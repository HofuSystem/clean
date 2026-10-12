<?php

namespace App\Console\Commands;

use Core\Orders\Models\AnalyticsOutbox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

class SendAnalyticsOutbox extends Command
{
    protected $signature = 'analytics:send-outbox {--limit=100}';
    protected $description = 'Send ready GA4 events from the transactional analytics outbox';

    public function handle(): int
    {
        $rows = AnalyticsOutbox::query()
            ->whereIn('destination', ['ga4', 'branch'])
            ->where('status', 'pending')
            ->where(fn ($query) => $query->whereNull('available_at')->orWhere('available_at', '<=', now()))
            ->orderBy('id')
            ->limit(max(1, (int) $this->option('limit')))
            ->get();

        foreach ($rows as $row) {
            $payload = $row->payload ?? [];
            $platform = $payload['platform'] ?? null;
            $eventName = $payload['event_name'] ?? null;
            if (! is_string($eventName) || $eventName === '') {
                $row->update(['status' => 'blocked_invalid_event', 'last_error' => 'Event name is missing.']);
                continue;
            }

            if ($eventName === 'purchase' && (! isset($payload['value'], $payload['tax'], $payload['shipping'], $payload['items'])
                || ! array_key_exists('wallet_paid_amount', $payload)
                || ! array_key_exists('wallet_promo_amount', $payload)
                || ! array_key_exists('loyalty_points_used', $payload))) {
                $row->update(['status' => 'blocked_financial_snapshot', 'last_error' => 'Purchase requires complete backend financial and wallet snapshots.']);
                continue;
            }

            try {
                if ($row->destination === 'ga4') {
                    $stream = config("analytics.ga4.{$platform}");
                    if (! in_array($platform, ['ios', 'android'], true)
                        || empty($payload['app_instance_id'])
                        || empty($stream['firebase_app_id'])
                        || empty($stream['api_secret'])) {
                        $row->update([
                            'status' => 'blocked_missing_config_or_identity',
                            'last_error' => 'GA4 requires a supported platform, app_instance_id, Firebase app id, and Measurement Protocol secret.',
                        ]);
                        continue;
                    }

                    $parameters = $payload;
                    unset($parameters['schema_version'], $parameters['event_name'], $parameters['app_instance_id'], $parameters['platform'], $parameters['developer_identity'], $parameters['branch_first'], $parameters['branch_latest']);
                    $eventTimestamp = ($row->created_at ?? now())->getTimestamp() * 1_000_000;
                    $response = Http::acceptJson()->asJson()->timeout(10)
                        ->withOptions(['query' => [
                            'firebase_app_id' => $stream['firebase_app_id'],
                            'api_secret' => $stream['api_secret'],
                        ]])->post('https://www.google-analytics.com/mp/collect', [
                            'app_instance_id' => $payload['app_instance_id'],
                            'timestamp_micros' => $eventTimestamp,
                            'events' => [[
                                'name' => $eventName,
                                'params' => $parameters,
                            ]],
                        ]);
                } else {
                    $branchKey = config('analytics.branch.' . ($platform === 'ios' ? 'ios_key' : 'android_key'));
                    if (! in_array($platform, ['ios', 'android'], true)
                        || empty($payload['developer_identity'])
                        || empty($branchKey)) {
                        $row->update([
                            'status' => 'blocked_missing_config_or_identity',
                            'last_error' => 'Branch requires a platform app key and the internal customer developer_identity.',
                        ]);
                        continue;
                    }

                    $standardPurchase = $eventName === 'purchase';
                    $customData = $payload;
                    unset($customData['schema_version'], $customData['event_name'], $customData['developer_identity'], $customData['app_instance_id'], $customData['platform']);
                    if ($standardPurchase) {
                        unset($customData['items']);
                    }
                    $branchPayload = [
                        'branch_key' => $branchKey,
                        'name' => $standardPurchase ? 'PURCHASE' : $eventName,
                        'user_data' => [
                            'developer_identity' => $payload['developer_identity'],
                            'os' => $platform === 'ios' ? 'iOS' : 'Android',
                        ],
                        'custom_data' => $customData,
                    ];
                    if (isset($payload['value'])) {
                        $branchPayload['event_data'] = array_filter([
                            'transaction_id' => $payload['transaction_id'] ?? null,
                            'revenue' => $payload['value'],
                            'currency' => $payload['currency'] ?? 'SAR',
                            'shipping' => $payload['shipping'] ?? null,
                            'tax' => $payload['tax'] ?? null,
                        ], static fn ($value) => $value !== null);
                    }
                    if ($standardPurchase) {
                        $branchPayload['content_items'] = array_map(static fn (array $item) => [
                            '$content_schema' => 'COMMERCE_PRODUCT',
                            '$canonical_identifier' => (string) ($item['item_id'] ?? ''),
                            '$product_name' => $item['item_name'] ?? 'Product',
                            '$price' => (float) ($item['price'] ?? 0),
                            '$quantity' => (float) ($item['quantity'] ?? 0),
                        ], $payload['items'] ?? []);
                    }
                    $response = Http::acceptJson()->asJson()->timeout(10)->post(
                        'https://api2.branch.io/v2/event/' . ($standardPurchase ? 'standard' : 'custom'),
                        $branchPayload
                    );
                }

                if (! $response->successful()) {
                    throw new \RuntimeException('GA4 Measurement Protocol returned HTTP ' . $response->status());
                }

                $row->update(['status' => 'sent', 'sent_at' => now(), 'attempts' => $row->attempts + 1, 'last_error' => null]);
            } catch (Throwable $exception) {
                $attempts = $row->attempts + 1;
                $row->update([
                    'status' => $attempts >= 10 ? 'dead_letter' : 'pending',
                    'attempts' => $attempts,
                    'available_at' => $attempts >= 10 ? null : now()->addMinutes(min(60, 2 ** min($attempts, 6))),
                    'last_error' => mb_substr($exception->getMessage(), 0, 2000),
                ]);
            }
        }

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Core\Orders\Models\AnalyticsOutbox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

class SendAnalyticsOutbox extends Command
{
    protected $signature = 'analytics:send-outbox {--limit=100}';
    protected $description = 'Send ready GA4 and Branch events from the transactional analytics outbox';

    public function handle(): int
    {
        // Recover leases left behind by a process crash, then claim rows one by one
        // so overlapping workers cannot send the same event concurrently.
        AnalyticsOutbox::query()->where('status', 'sending')
            ->where(fn ($query) => $query->whereNull('locked_at')->orWhere('locked_at', '<', now()->subMinutes(10)))
            ->update(['status' => 'pending', 'locked_at' => null, 'available_at' => now()]);

        $ids = AnalyticsOutbox::query()->whereIn('destination', ['ga4', 'branch'])
            ->where('status', 'pending')
            ->where(fn ($query) => $query->whereNull('available_at')->orWhere('available_at', '<=', now()))
            ->orderBy('id')->limit(max(1, (int) $this->option('limit')))->pluck('id');

        foreach ($ids as $id) {
            $claimed = AnalyticsOutbox::query()->whereKey($id)->where('status', 'pending')
                ->update(['status' => 'sending', 'locked_at' => now()]);
            if ($claimed !== 1) {
                continue;
            }
            $row = AnalyticsOutbox::find($id);
            if (! $row) {
                continue;
            }
            $payload = $row->payload ?? [];
            $eventName = $payload['event_name'] ?? null;
            $platform = $payload['platform'] ?? null;

            if (! is_string($eventName) || $eventName === '') {
                $this->block($row, 'blocked_invalid_event', 'Event name is missing.');
                continue;
            }
            if ($row->destination === 'branch' && ! in_array($eventName, ['order_created', 'purchase'], true)) {
                $this->block($row, 'blocked_branch_event_not_allowed', 'Branch receives only order_created and purchase.');
                continue;
            }
            if ($eventName === 'purchase' && (! isset($payload['value'], $payload['tax'], $payload['shipping'], $payload['items'])
                || ! array_key_exists('wallet_paid_amount', $payload)
                || ! array_key_exists('wallet_promo_amount', $payload)
                || ! array_key_exists('loyalty_points_used', $payload)
                || ! array_key_exists('funded_wallet_used', $payload)
                || ! array_key_exists('promo_wallet_used', $payload)
                || ! array_key_exists('loyalty_value_used', $payload))) {
                $this->block($row, 'blocked_financial_snapshot', 'Purchase requires a complete backend financial snapshot.');
                continue;
            }

            try {
                if ($row->destination === 'ga4') {
                    $stream = config("analytics.ga4.{$platform}");
                    if (! in_array($platform, ['ios', 'android'], true)) {
                        $this->block($row, 'blocked_unsupported_platform', 'GA4 Measurement Protocol requires an iOS or Android app stream.');
                        continue;
                    }
                    if (empty($payload['app_instance_id'])) {
                        $this->block($row, 'missing_app_instance_id', 'The event has no app_instance_id.');
                        continue;
                    }
                    if (empty($stream['firebase_app_id']) || empty($stream['api_secret'])) {
                        $this->block($row, 'blocked_missing_config', 'GA4 Firebase app id or Measurement Protocol secret is missing.');
                        continue;
                    }
                    $parameters = $payload;
                    unset($parameters['event_name'], $parameters['app_instance_id'], $parameters['user_id'], $parameters['platform'], $parameters['developer_identity'], $parameters['branch_first'], $parameters['branch_latest']);
                    $request = [
                        'app_instance_id' => $payload['app_instance_id'],
                        'timestamp_micros' => ($row->created_at ?? now())->getTimestamp() * 1_000_000,
                        'events' => [['name' => $eventName, 'params' => $parameters]],
                    ];
                    if (! empty($payload['user_id'])) {
                        $request['user_id'] = (string) $payload['user_id'];
                    }
                    $response = Http::acceptJson()->asJson()->timeout(10)->withOptions(['query' => [
                        'firebase_app_id' => $stream['firebase_app_id'], 'api_secret' => $stream['api_secret'],
                    ]])->post('https://www.google-analytics.com/mp/collect', $request);
                } else {
                    $branchKey = config('analytics.branch.' . ($platform === 'ios' ? 'ios_key' : 'android_key'));
                    if (! in_array($platform, ['ios', 'android'], true) || empty($payload['developer_identity']) || empty($branchKey)) {
                        $this->block($row, 'blocked_branch_config', 'Branch requires an app key and internal developer identity.');
                        continue;
                    }
                    $standardPurchase = $eventName === 'purchase';
                    $customData = $payload;
                    unset($customData['schema_version'], $customData['event_name'], $customData['event_id'], $customData['developer_identity'], $customData['app_instance_id'], $customData['platform'], $customData['branch_first'], $customData['branch_latest'], $customData['user_id']);
                    if ($standardPurchase) {
                        unset($customData['items']);
                    }
                    $branchPayload = [
                        'branch_key' => $branchKey,
                        'name' => $standardPurchase ? 'PURCHASE' : 'order_created',
                        'user_data' => ['developer_identity' => $payload['developer_identity'], 'os' => $platform === 'ios' ? 'iOS' : 'Android'],
                        'custom_data' => ['schema_version' => 2, 'event_source' => 'backend', 'event_id' => $payload['event_id'] ?? $row->event_key, ...$customData],
                    ];
                    if (isset($payload['value'])) {
                        $branchPayload['event_data'] = array_filter([
                            'transaction_id' => $payload['transaction_id'] ?? null, 'revenue' => $payload['value'],
                            'currency' => $payload['currency'] ?? 'SAR', 'shipping' => $payload['shipping'] ?? null,
                            'tax' => $payload['tax'] ?? null,
                        ], static fn ($value) => $value !== null);
                    }
                    if ($standardPurchase) {
                        $branchPayload['content_items'] = array_map(static fn (array $item) => [
                            '$content_schema' => 'COMMERCE_PRODUCT', '$canonical_identifier' => (string) ($item['item_id'] ?? ''),
                            '$product_name' => $item['item_name'] ?? 'Product', '$price' => (float) ($item['price'] ?? 0),
                            '$quantity' => (float) ($item['quantity'] ?? 0),
                        ], $payload['items'] ?? []);
                    }
                    $response = Http::acceptJson()->asJson()->timeout(10)->post(
                        'https://api2.branch.io/v2/event/' . ($standardPurchase ? 'standard' : 'custom'), $branchPayload
                    );
                }

                if (! $response->successful()) {
                    throw new \RuntimeException(ucfirst($row->destination) . ' returned HTTP ' . $response->status());
                }
                $row->update(['status' => 'sent', 'sent_at' => now(), 'locked_at' => null, 'attempts' => $row->attempts + 1, 'last_error' => null]);
            } catch (Throwable $exception) {
                $attempts = $row->attempts + 1;
                $deadLetter = $attempts >= 10;
                $row->update([
                    'status' => $deadLetter ? 'dead_letter' : 'pending', 'locked_at' => null, 'attempts' => $attempts,
                    'available_at' => $deadLetter ? null : now()->addMinutes(min(60, 2 ** min($attempts, 6))),
                    'last_error' => mb_substr($exception->getMessage(), 0, 2000),
                ]);
                if ($deadLetter) {
                    report(new \RuntimeException("Analytics outbox dead-letter event {$row->id} ({$row->destination}/{$eventName})"));
                }
            }
        }

        return self::SUCCESS;
    }

    private function block(AnalyticsOutbox $row, string $status, string $reason): void
    {
        $row->update(['status' => $status, 'locked_at' => null, 'last_error' => $reason]);
    }
}

<?php

namespace Core\Orders\Services;

use Core\Orders\Models\AnalyticsOutbox;
use Core\Orders\Models\Order;
use Throwable;

class AnalyticsOutboxService
{
    /**
     * Store one event per destination. The unique key makes callbacks and status
     * retries idempotent while allowing GA4 and Branch to be delivered separately.
     */
    public function record(string $eventName, Order $order, array $parameters = [], ?string $sourceKey = null): void
    {
        $context = $order->analyticsContext;
        $blockedReason = $parameters['_analytics_blocked_reason'] ?? null;
        unset($parameters['_analytics_blocked_reason']);
        $eventId = (string) ($parameters['event_id'] ?? sprintf(
            '%s:%s:%s',
            $eventName,
            $order->id,
            $sourceKey ?? '0'
        ));
        if (strlen($eventId) > 180) {
            $eventId = sprintf('%s:%s:%s', $eventName, $order->id, hash('sha256', $eventId));
        }
        $parameters['event_id'] = $eventId;
        $payload = array_filter([
            'schema_version' => 2,
            'event_source' => 'backend',
            // Keep the real order service line. The platform supports multiple
            // order types, so analytics must not label every order as laundry.
            'main_section' => (string) $order->type,
            'event_name' => $eventName,
            'order_id' => (string) $order->id,
            'order_type' => (string) $order->type,
            'order_mode' => $this->orderMode($order),
            'city_id' => $order->city_id !== null ? (string) $order->city_id : null,
            'user_id' => $order->client_id !== null ? (string) $order->client_id : null,
            'transaction_id' => (string) $order->id,
            'currency' => 'SAR',
            'developer_identity' => $order->client_id !== null ? (string) $order->client_id : null,
            'app_instance_id' => $context?->app_instance_id,
            'app_version' => $context?->app_version,
            'session_id' => $eventName === 'order_created' ? $context?->session_id : null,
            'platform' => $context?->platform,
            'branch_first' => $context?->branch_first,
            'branch_latest' => $context?->branch_latest,
            ...$parameters,
        ], static fn ($value) => $value !== null);

        foreach (['ga4', 'branch'] as $destination) {
            if ($destination === 'branch' && ! in_array($eventName, ['order_created', 'purchase'], true)) {
                continue;
            }

            $key = $destination . ':' . $eventId;
            $status = 'pending';
            $lastError = $blockedReason;
            if ($blockedReason) {
                $status = 'blocked_financial_snapshot';
            } elseif ($eventName === 'purchase' && ! isset($parameters['value'], $parameters['tax'], $parameters['shipping'], $parameters['items'])) {
                $status = 'blocked_financial_snapshot';
                $lastError = 'Purchase requires backend-calculated value, tax, shipping, and item snapshots.';
            }
            if ($destination === 'branch' && empty($payload['developer_identity'])) {
                $status = 'blocked_missing_identity';
                $lastError = 'Branch Events API requires developer_identity, browser_fingerprint_id, or an advertising identifier.';
            }
            try {
                $outboxEvent = AnalyticsOutbox::query()->firstOrCreate(
                    ['event_key' => $key],
                    [
                        'event_name' => $eventName,
                        'destination' => $destination,
                        'order_id' => $order->id,
                        'payload' => $payload,
                        // Keep events visible when required mapping/identity is absent
                        // instead of silently sending malformed or un-attributable data.
                        'status' => $status,
                        'last_error' => $lastError,
                        'available_at' => now(),
                    ]
                );
                if ($eventName === 'purchase'
                    && ! $blockedReason
                    && isset($parameters['value'], $parameters['tax'], $parameters['shipping'], $parameters['items'])
                    && $outboxEvent->status === 'blocked_financial_snapshot') {
                    $outboxEvent->update([
                        'payload' => $payload,
                        'status' => 'pending',
                        'last_error' => null,
                        'attempts' => 0,
                        'available_at' => now(),
                    ]);
                }
            } catch (Throwable $exception) {
                // Analytics storage must never prevent a customer's order or payment.
                report($exception);
            }
        }
    }

    /**
     * Record a successful customer-level event that is not tied to an order.
     * Events without app attribution remain visible as blocked outbox rows.
     */
    public function recordUserEvent(
        string $eventName,
        int $userId,
        array $context,
        array $parameters = [],
        ?string $sourceKey = null
    ): void {
        $eventId = $eventName . ':' . $userId . ':' . ($sourceKey ?? '0');
        if (strlen($eventId) > 170) {
            $eventId = $eventName . ':' . $userId . ':' . hash('sha256', $eventId);
        }

        $platform = $context['platform'] ?? null;
        $payload = array_filter([
            'schema_version' => 2,
            'event_source' => 'backend',
            'event_name' => $eventName,
            'event_id' => $eventId,
            'user_id' => (string) $userId,
            'platform' => $platform,
            'app_instance_id' => $context['app_instance_id'] ?? null,
            'app_version' => $context['app_version'] ?? null,
            'currency' => 'SAR',
            ...$parameters,
        ], static fn ($value) => $value !== null);

        $status = 'pending';
        $lastError = null;
        if (! in_array($platform, ['ios', 'android'], true)) {
            $status = 'blocked_unsupported_platform';
            $lastError = 'A supported app platform was not provided.';
        } elseif (empty($payload['app_instance_id'])) {
            $status = 'missing_app_instance_id';
            $lastError = 'The event has no app_instance_id.';
        }

        try {
            AnalyticsOutbox::query()->firstOrCreate(
                ['event_key' => 'ga4:' . $eventId],
                [
                    'event_name' => $eventName,
                    'destination' => 'ga4',
                    'payload' => $payload,
                    'status' => $status,
                    'last_error' => $lastError,
                    'available_at' => now(),
                ]
            );
        } catch (Throwable $exception) {
            // Analytics persistence must not roll back a wallet operation.
            report($exception);
        }
    }

    private function orderMode(Order $order): string
    {
        if ($order->type === 'fastorder') {
            return 'quick';
        }

        $hasEconomicBag = $order->items()
            ->whereHas('product.category', static fn ($query) => $query->where('slug', 'economic-bags'))
            ->exists();

        return $hasEconomicBag ? 'economic_bag' : 'normal';
    }
}

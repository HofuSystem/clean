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
        $payload = array_filter([
            'schema_version' => 2,
            'event_name' => $eventName,
            'order_id' => (string) $order->id,
            'order_type' => $order->type,
            'transaction_id' => (string) $order->id,
            'currency' => 'SAR',
            'developer_identity' => $order->client_id !== null ? (string) $order->client_id : null,
            'app_instance_id' => $context?->app_instance_id,
            'session_id' => $eventName === 'order_created' ? $context?->session_id : null,
            'platform' => $context?->platform,
            'branch_first' => $context?->branch_first,
            'branch_latest' => $context?->branch_latest,
            ...$parameters,
        ], static fn ($value) => $value !== null);

        foreach (['ga4', 'branch'] as $destination) {
            $key = hash('sha256', implode(':', [$destination, $sourceKey ?? 'order', $order->id, $eventName]));
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
}

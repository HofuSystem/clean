<?php

namespace Core\Users\Observers;

use Core\Users\Models\Point;
use Core\Users\Models\User;
use Core\Users\Services\PointsService;

class PointObserver
{
    /**
     * Handle the Point "creating" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function creating(Point $point)
    {
        if (!$point->creator_id) {
            $actor = auth('web')->user() ?? auth('api')->user();
            if ($actor) {
                $point->creator_id = $actor->id;
            }
        }
    }
    /**
     * Handle the Point "created" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function created(Point $point)
    {
        // Order-linked point events carry order context and are emitted by the
        // order services. Standalone admin credits/debits use this ledger event.
        if ($point->order_id || ! $point->user_id || ! in_array($point->operation, ['deposit', 'withdraw'], true)) {
            return;
        }

        $context = request()->input('analytics_context', []);
        $context = is_array($context) ? $context : [];

        app(\Core\Orders\Services\AnalyticsOutboxService::class)->recordUserEvent(
            $point->operation === 'deposit' ? 'loyalty_earned' : 'loyalty_redeemed',
            (int) $point->user_id,
            $context,
            [
                ($point->operation === 'deposit' ? 'points_earned' : 'points_redeemed') => (float) $point->amount,
                'loyalty_transaction_id' => (string) $point->id,
                'source' => 'standalone_ledger',
            ],
            'loyalty-transaction:' . $point->id
        );
    }

    /**
     * Handle the Point "updating" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function updating(Point $point)
    {

    }
    /**
     * Handle the Point "updated" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function updated(Point $point)
    {
        
    }
    /**
     * Handle the Point "saving" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function saving(Point $point)
    {

    }
    /**
     * Handle the Point "saved" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function saved(Point $point)
    {
        PointsService::updateUserPoints($point->user_id);
    }

    /**
     * Handle the Point "deleted" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function deleted(Point $point)
    {
        PointsService::updateUserPoints($point->user_id);
    }

    /**
     * Handle the Point "restored" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function restored(Point $point)
    {
        PointsService::updateUserPoints($point->user_id);
    }

    /**
     * Handle the Point "force deleted" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function forceDeleted(Point $point)
    {
        PointsService::updateUserPoints($point->user_id);
    }
}

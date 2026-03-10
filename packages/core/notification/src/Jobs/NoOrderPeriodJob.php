<?php

namespace Core\Notification\Jobs;

use Core\Notification\Models\Notification;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;
use Core\Wallet\Models\WalletTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NoOrderPeriodJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger(__CLASS__ . ' was running ' . now());
        try {
            // Get notification messages from settings
            $notificationMessagesJson = SettingsService::getDataBaseSetting('no_order_notifications');
          
            if (empty($notificationMessagesJson)) {
                logger(__CLASS__ . ' - No notification messages configured in settings');
                return;
            }
            
            $notificationMessages = json_decode(json_encode($notificationMessagesJson), true);

            if (empty($notificationMessages)) {
                logger(__CLASS__ . ' - No active notification messages found');
                return;
            }

            foreach ($notificationMessages as $notificationMessage) {
                if ($notificationMessage['is_active'] ?? false) {
                    $this->sendNotificationForPeriod($notificationMessage);
                }
            }
        } catch (\Throwable $th) {
            logger(__CLASS__ . ' was failed ' . now() . ' ' . $th->getMessage());
        }
    }

    /**
     * Send notification for specific period
     */
    private function sendNotificationForPeriod(array $notificationMessage): void
    {
      logger(__CLASS__ . " - Sending notification for {$notificationMessage['days']} days");
        try {
            $days = $notificationMessage['days'];
            // Find users who registered exactly $days ago and have not placed any orders yet
            $users = User::whereDate('created_at', now()->subDays($days)->toDateString())
                ->whereDoesntHave('orders')
                ->get();
            logger(__CLASS__ . " - Found {$users->count()} users for {$days} days period");
             $users = $users->pluck('id')->toArray();

             if (!empty($users) and !empty($notificationMessage['notification_title']) and !empty($notificationMessage['notification_body'])) {
                 // Create notification
                foreach ($users as $userId) {
                    Notification::create([
                        'types'    => json_encode(['apps']),
                        'for'      => 'users',
                        'for_data' => json_encode([$userId]),
                        'title'    => $notificationMessage['notification_title'],
                        'body'     => $notificationMessage['notification_body'],
                        'media'    => null,
                    ]);
                }

                 // Add money to user wallets if specified
                 $addedAmount = (float) ($notificationMessage['added_points'] ?? 0);
                 $moneyExpiryDays = (int) ($notificationMessage['money_expiry_days'] ?? 30);
                 $notes = $notificationMessage['notes'] ?? '';
                 
                 if ($addedAmount > 0) {
                     foreach ($users as $userId) {
                         $user = User::find($userId);
                         if ($user) {
                             logger(__CLASS__ . " - Adding {$addedAmount} to wallet for user {$userId}, expiring in {$moneyExpiryDays} days");
                             
                             // Create wallet transaction to add money to user's wallet with expiry
                             $user->walletTransactions()->firstOrCreate([
                                 'amount' => $addedAmount,
                                 'type' => 'deposit',
                                 'transaction_type' => 'reward',
                             ],[
                              'status' => 'accepted',
                              'expired_at' => now()->addDays($moneyExpiryDays),
                              'notes' => $notes ? $notes : ($notificationMessage['notification_title'] . ' - Reward Money'),
                             ]);
                         }
                     }
                     
                     logger(__CLASS__ . " - Added {$addedAmount} to wallet for " . count($users) . " users, expiring in {$moneyExpiryDays} days");
                 }
             }
           
        } catch (\Throwable $th) {
            logger(__CLASS__ . " - Failed to send notification for {$notificationMessage['days']} days: " . $th->getMessage());
        }
    }
}

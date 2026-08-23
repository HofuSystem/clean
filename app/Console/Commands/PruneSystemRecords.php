<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PruneSystemRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-system-records {--days=30 : Number of days of records to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old system logs, routes records, and temporary database entries to optimize performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);
        $this->info("Starting system records cleanup (pruning records older than {$days} days: {$cutoffDate->toDateTimeString()})...");

        // 1. Prune routes_records
        if (Schema::hasTable('routes_records')) {
            $deleted = DB::table('routes_records')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
            $this->info("✓ Pruned {$deleted} records from 'routes_records' table.");
        }

        // 2. Prune failed_jobs older than $days
        if (Schema::hasTable('failed_jobs')) {
            $deleted = DB::table('failed_jobs')
                ->where('failed_at', '<', $cutoffDate)
                ->delete();
            $this->info("✓ Pruned {$deleted} records from 'failed_jobs' table.");
        }

        // 3. Prune old FCM notification delivery logs (notification_tokens) older than $days
        if (Schema::hasTable('notification_tokens')) {
            $deleted = DB::table('notification_tokens')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
            $this->info("✓ Pruned {$deleted} FCM delivery log records from 'notification_tokens'.");
        }

        // 4. Prune expired banner vision logs (BannerNotification) older than $days while keeping user notifications intact
        if (Schema::hasTable('users_notifications')) {
            $deleted = DB::table('users_notifications')
                ->where('notifications_type', 'Core\Notification\Models\BannerNotification')
                ->where(function ($q) use ($cutoffDate) {
                    $q->where('next_vision_date', '<', $cutoffDate)
                      ->orWhereNull('next_vision_date');
                })
                ->delete();
            $this->info("✓ Pruned {$deleted} expired banner tracking records from 'users_notifications'.");
        }

        // 5. Prune Telescope tables if present
        if (Schema::hasTable('telescope_entries')) {
            try {
                $deleted = DB::table('telescope_entries')
                    ->where('created_at', '<', now()->subDays(3))
                    ->delete();
                $this->info("✓ Pruned {$deleted} records from 'telescope_entries' table.");
            } catch (\Throwable $e) {
                $this->warn("Telescope entries prune skipped: " . $e->getMessage());
            }
        }

        $this->info("System cleanup completed successfully!");
        return Command::SUCCESS;
    }
}

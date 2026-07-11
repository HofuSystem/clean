<?php

namespace Core\Users\Commands;

use Core\Users\Models\User;
use Illuminate\Console\Command;

class DeleteNamelessUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-nameless-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users without a name who were created more than 2 hours ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find users with null or empty fullname created more than 2 hours ago.
        // We include withTrashed() to ensure we permanently clean up soft-deleted nameless users too.
        $users = User::withTrashed()
            ->where(function ($query) {
                $query->whereNull('fullname')
                    ->orWhere('fullname', '');
            })
            ->where('created_at', '<=', now()->subHours(2))
            ->get();

        $count = $users->count();

        foreach ($users as $user) {
            // Delete related models to prevent foreign key constraint violations
            $user->devices()->delete();
            $user->points()->delete();
            $user->addresses()->delete();
            if ($user->profile) {
                $user->profile->delete();
            }

            // Force delete the user
            $user->forceDelete();
        }

        $this->info("Successfully deleted {$count} nameless users.");
    }
}

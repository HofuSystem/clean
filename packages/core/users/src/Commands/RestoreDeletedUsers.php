<?php

namespace Core\Users\Commands;

use Core\Users\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestoreDeletedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:restore-deleted-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loop on users from clean_users database and insert them into the main database if they were deleted';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve the mysql2 configuration
        $mysql2Config = config('database.connections.mysql2');
        if (!$mysql2Config) {
            $this->error('Secondary mysql2 database connection configuration not found.');
            return 1;
        }

        // If secondary connection credentials are not explicitly configured in env,
        // fall back to the primary connection's host, port, username, and password.
        $mysqlConfig = config('database.connections.mysql');
        if ($mysqlConfig) {
            if (!env('DB_TWO_USERNAME')) {
                $mysql2Config['username'] = $mysqlConfig['username'];
            }
            if (!env('DB_TWO_PASSWORD')) {
                $mysql2Config['password'] = $mysqlConfig['password'];
            }
            if (!env('DB_TWO_HOST')) {
                $mysql2Config['host'] = $mysqlConfig['host'];
                $mysql2Config['port'] = $mysqlConfig['port'];
            }
        }

        // Ensure the database is clean_users if it defaults to laravel or is not set
        if (!env('DB_TWO_DATABASE') || empty($mysql2Config['database']) || $mysql2Config['database'] === 'laravel') {
            $mysql2Config['database'] = 'clean_users';
        }

        // Apply updated config
        config(['database.connections.mysql2' => $mysql2Config]);

        $this->info("Connecting to database: {$mysql2Config['database']} via mysql2 connection...");

        try {
            // Retrieve all users from the 2nd database's users table
            $cleanUsers = DB::connection('mysql2')->table('users')->get();
        } catch (\Exception $e) {
            $this->error('Failed to connect via mysql2 or retrieve users: ' . $e->getMessage());
            return 1;
        }

        $total = $cleanUsers->count();
        $this->info("Found {$total} users in clean_users database.");

        $insertedCount = 0;
        $restoredCount = 0;

        // Get the column listing of the main database's users table to filter out mismatching columns
        $mainColumns = Schema::getColumnListing('users');

        // Fetch or create client roles for all applicable guards
        $clientRoles = \Spatie\Permission\Models\Role::where('name', 'client')->get();
        if ($clientRoles->isEmpty()) {
            $clientRoles = collect([
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']),
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'client', 'guard_name' => 'api']),
            ]);
        }

        foreach ($cleanUsers as $cleanUser) {
            // Check if the user exists in the main database (including soft-deleted ones)
            $existingUser = User::withTrashed()->find($cleanUser->id);

            if (!$existingUser) {
                // User doesn't exist at all, let's insert it
                $userData = (array) $cleanUser;

                // Filter keys to only include columns that exist in the main database's users table
                $filteredData = array_intersect_key($userData, array_flip($mainColumns));

                DB::table('users')->insert($filteredData);
                $this->info("Inserted user ID {$cleanUser->id} with fullname '{$cleanUser->fullname}'");
                $insertedCount++;

                // Assign client role to the newly inserted user
                $userModel = User::find($cleanUser->id);
                if ($userModel) {
                    $userModel->assignRole($clientRoles);
                }
            } elseif ($existingUser->trashed()) {
                // User exists but was soft-deleted
                $existingUser->restore();
                $this->info("Restored soft-deleted user ID {$cleanUser->id} with fullname '{$cleanUser->fullname}'");
                $restoredCount++;

                // Assign client role to the restored user
                $existingUser->assignRole($clientRoles);
            } else {
                // User exists and is active, ensure they have the 'client' role
                $existingUser->assignRole($clientRoles);
            }
        }

        $this->info("Execution complete. Inserted: {$insertedCount}, Restored: {$restoredCount}.");
        return 0;
    }
}

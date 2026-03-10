<?php

namespace Database\Seeders;

use Core\MediaCenter\Models\Media;
use Core\Users\Models\Role;
use Core\Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LatestAppearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        

        // Get the latest route record for each user
        $latestRoutes = DB::table('routes_records')
            ->select('user_id', DB::raw('MAX(created_at) as latest_created_at'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->get();

        // Update users' appear_at field with their latest route created_at
        foreach ($latestRoutes as $route) {
            User::where('id', $route->user_id)
                ->update(['appear_at' => $route->latest_created_at]);
        }

    }
}

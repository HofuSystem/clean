<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Align DB slugs with SEO redirects in routes/web.php to prevent 404s
        DB::table('blogs')->where('id', 1)->update(['slug' => 'laundry-pickup-delivery-riyadh']);
        DB::table('blogs')->where('id', 10)->update(['slug' => 'on-time-laundry-pickup-delivery-riyadh']);
        DB::table('blogs')->where('id', 13)->update(['slug' => 'laundry-fabric-care-prices-riyadh']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('blogs')->where('id', 1)->update(['slug' => 'the-sparkle-you-deserve-why-a-professional-cleaning-service-is-a-game-changer']);
        DB::table('blogs')->where('id', 10)->update(['slug' => 'pickup-delivery-on-time']);
        DB::table('blogs')->where('id', 13)->update(['slug' => 'laundry-prices-riyadh']);
    }
};

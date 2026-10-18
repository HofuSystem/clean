<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_slug_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('old_slug')->unique();
            // A null target marks a permanently deleted article (HTTP 410).
            $table->string('new_slug')->nullable()->index();
            $table->timestamps();
        });

        // Preserve the three legacy SEO URLs changed by the 2026-10-12 migration,
        // but only when the article still has the expected current slug.
        $legacySlugs = [
            1 => ['the-sparkle-you-deserve-why-a-professional-cleaning-service-is-a-game-changer', 'laundry-pickup-delivery-riyadh'],
            10 => ['pickup-delivery-on-time', 'on-time-laundry-pickup-delivery-riyadh'],
            13 => ['laundry-prices-riyadh', 'laundry-fabric-care-prices-riyadh'],
        ];

        foreach ($legacySlugs as $blogId => [$oldSlug, $currentSlug]) {
            $articleStillUsesExpectedSlug = DB::table('blogs')
                ->where('id', $blogId)
                ->where('slug', $currentSlug)
                ->exists();
            $oldSlugIsAlreadyInUse = DB::table('blogs')->where('slug', $oldSlug)->exists();

            if ($articleStillUsesExpectedSlug && !$oldSlugIsAlreadyInUse) {
                DB::table('blog_slug_redirects')->insert([
                    'old_slug' => $oldSlug,
                    'new_slug' => $currentSlug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_slug_redirects');
    }
};

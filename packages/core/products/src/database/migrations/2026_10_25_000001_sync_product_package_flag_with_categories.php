<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')
            ->select(['id', 'is_package'])
            ->orderBy('id')
            ->each(function ($category): void {
                DB::table('products')
                    ->where('category_id', $category->id)
                    ->update(['is_package' => (bool) $category->is_package]);
            });
    }

    public function down(): void
    {
        // The previous manually selected values cannot be reconstructed safely.
    }
};

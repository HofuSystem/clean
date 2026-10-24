<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analytics_outbox', function (Blueprint $table) {
            $table->timestamp('locked_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('analytics_outbox', function (Blueprint $table) {
            $table->dropIndex(['locked_at']);
            $table->dropColumn('locked_at');
        });
    }
};

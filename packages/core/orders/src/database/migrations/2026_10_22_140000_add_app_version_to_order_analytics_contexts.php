<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_analytics_contexts', function (Blueprint $table) {
            $table->string('app_version', 100)->nullable()->after('app_instance_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('order_analytics_contexts', function (Blueprint $table) {
            $table->dropIndex(['app_version']);
            $table->dropColumn('app_version');
        });
    }
};

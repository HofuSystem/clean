<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('coverage_notifications') && !Schema::hasColumn('coverage_notifications', 'address_id')) {
            Schema::table('coverage_notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('address_id')->nullable()->after('user_id');
                $table->foreign('address_id')->references('id')->on('addresses')->nullOnDelete();
                $table->index(['user_id', 'address_id', 'status'], 'coverage_notifications_user_address_status_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('coverage_notifications') && Schema::hasColumn('coverage_notifications', 'address_id')) {
            Schema::table('coverage_notifications', function (Blueprint $table) {
                $table->dropIndex('coverage_notifications_user_address_status_index');
                $table->dropForeign(['address_id']);
                $table->dropColumn('address_id');
            });
        }
    }
};

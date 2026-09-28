<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users_notifications', function (Blueprint $table) {
            $table->string('eligibility_status')->nullable()->after('status')->index();
            $table->string('eligibility_reason')->nullable()->after('eligibility_status');
            $table->unsignedInteger('accepted_devices_count')->default(0)->after('eligibility_reason');
            $table->unsignedInteger('failed_devices_count')->default(0)->after('accepted_devices_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_notifications', function (Blueprint $table) {
            $table->dropIndex(['eligibility_status']);
            $table->dropColumn([
                'eligibility_status',
                'eligibility_reason',
                'accepted_devices_count',
                'failed_devices_count',
            ]);
        });
    }
};

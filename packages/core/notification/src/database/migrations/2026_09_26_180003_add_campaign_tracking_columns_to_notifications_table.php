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
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('purpose')->nullable()->after('for_data')->index();
            $table->string('processing_status')->nullable()->after('purpose')->index();
            $table->timestamp('started_at')->nullable()->after('processing_status');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->unsignedInteger('targeted_users_count')->default(0)->after('completed_at');
            $table->unsignedInteger('eligible_users_count')->default(0)->after('targeted_users_count');
            $table->unsignedInteger('eligible_devices_count')->default(0)->after('eligible_users_count');
            $table->unsignedInteger('accepted_by_fcm_count')->default(0)->after('eligible_devices_count');
            $table->unsignedInteger('permanent_failed_count')->default(0)->after('accepted_by_fcm_count');
            $table->unsignedInteger('transient_failed_count')->default(0)->after('permanent_failed_count');
            $table->unsignedInteger('received_count')->default(0)->after('transient_failed_count');
            $table->unsignedInteger('opened_count')->default(0)->after('received_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['purpose']);
            $table->dropIndex(['processing_status']);
            $table->dropColumn([
                'purpose',
                'processing_status',
                'started_at',
                'completed_at',
                'targeted_users_count',
                'eligible_users_count',
                'eligible_devices_count',
                'accepted_by_fcm_count',
                'permanent_failed_count',
                'transient_failed_count',
                'received_count',
                'opened_count',
            ]);
        });
    }
};

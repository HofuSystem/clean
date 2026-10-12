<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasDuplicateInstallations = DB::table('devices')
            ->select('installation_id')
            ->whereNotNull('installation_id')
            ->groupBy('installation_id')
            ->havingRaw('COUNT(*) > 1')
            ->limit(1)
            ->exists();

        if ($hasDuplicateInstallations) {
            throw new RuntimeException(
                'Cannot add a unique index to devices.installation_id: duplicate non-null values exist. '
                . 'Resolve duplicate device records while preserving ownership, then rerun migrations.'
            );
        }

        Schema::table('devices', function (Blueprint $table) {
            $table->unique('installation_id', 'devices_installation_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropUnique('devices_installation_id_unique');
        });
    }
};

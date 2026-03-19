<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('line_of_business')->nullable()->after('fullname');
        });

        Schema::table('company_branches', function (Blueprint $table) {
            $table->string('lat')->nullable()->after('location');
            $table->string('lng')->nullable()->after('lat');
            $table->unsignedBigInteger('city_id')->nullable()->after('lng');
            $table->unsignedBigInteger('district_id')->nullable()->after('city_id');
            $table->unsignedBigInteger('user_id')->nullable()->after('district_id');
            $table->boolean('is_default')->default(0)->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('line_of_business');
        });

        Schema::table('company_branches', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng', 'city_id', 'district_id', 'user_id', 'is_default']);
        });
    }
};

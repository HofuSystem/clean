<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('commercial_registration')->nullable()->after('iban');
            $table->string('tax_number')->nullable()->after('commercial_registration');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['commercial_registration', 'tax_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('commercial_registration')->nullable();
            $table->string('tax_number')->nullable();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['commercial_registration', 'tax_number']);
        });
    }
};

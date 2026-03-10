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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 25);
            $table->string('name', 100);
            $table->string('script', 100);
            $table->string('native', 100);
            $table->string('flag', 100)->nullable();
            $table->string('regional', 100)->nullable();
            $table->enum('dir', ['rtl', 'ltr'])->default('ltr');
            $table->boolean('active')->default(false);
            $table->boolean('default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
};

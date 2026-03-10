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
        Schema::create('media_center', function (Blueprint $table) {
            $table->id();
            $table->string('file_type')->default(0);
            $table->string('file_name')->default(0);
            $table->double('size')->default(0);
            $table->string('title')->default(0);
            $table->string('alt')->default(0);
            $table->string('dimensions')->default(0);
            $table->bigInteger('user_id')->nullable();

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
        Schema::dropIfExists('media_center');
    }
};

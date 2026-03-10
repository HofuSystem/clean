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
        Schema::create('report_reasons', function (Blueprint $table) {
            $table->id();
            $table->integer("ordering")->nullable();

            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('report_reason_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255);
            $table->string("desc",1000)->nullable();

            $table->string('locale')->index();
            $table->unsignedBigInteger('report_reason_id');
            $table->unique(['report_reason_id', 'locale']);
            $table->foreign('report_reason_id')->references('id')->on('report_reasons')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_reasons');
        Schema::dropIfExists('report_reason_translations');

    }
};

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
        Schema::create('work_steps', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 255)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('work_step_translations', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('work_step_id');
            $table->unique(['work_step_id', 'locale']);
            $table->foreign('work_step_id')->references('id')->on('work_steps')
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
        Schema::dropIfExists('work_step_translations');
        Schema::dropIfExists('work_steps');
    }
};


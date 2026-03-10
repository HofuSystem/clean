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
        Schema::create('comparisons', function (Blueprint $table) {
            $table->id();
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

        Schema::create('comparison_translations', function (Blueprint $table) {
            $table->id();
            $table->string('point', 255)->nullable();
            $table->text('us_text')->nullable();
            $table->text('them_text')->nullable();
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('comparison_id');
            $table->unique(['comparison_id', 'locale']);
            $table->foreign('comparison_id')->references('id')->on('comparisons')
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
        Schema::dropIfExists('comparison_translations');
        Schema::dropIfExists('comparisons');
    }
};


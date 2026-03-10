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
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string("icon",255);  
            $table->boolean("is_active")->nullable();  
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::create('feature_translations', function (Blueprint $table) {
            $table->id();
            $table->string("title",255);  
            $table->string("description",1000);  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('feature_id');
            $table->unique(['feature_id', 'locale']);
            $table->foreign('feature_id')->references('id')->on('features')
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
        Schema::dropIfExists('features');
        Schema::dropIfExists('feature_translations');
    
    }
};

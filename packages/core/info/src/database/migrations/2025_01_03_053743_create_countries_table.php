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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string("slug",255)->unique();  
            $table->string("phonecode",255)->nullable();  
            $table->string("short_name",255)->nullable();  
            $table->string("flag",255)->nullable();  
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::create('country_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255);  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('country_id');
            $table->unique(['country_id', 'locale']);
            $table->foreign('country_id')->references('id')->on('countries')
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
        Schema::dropIfExists('countries');
        Schema::dropIfExists('country_translations');
    
    }
};

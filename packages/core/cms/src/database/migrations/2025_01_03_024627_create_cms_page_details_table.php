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
        Schema::create('cms_page_details', function (Blueprint $table) {
            $table->id();
            $table->string("image",255)->nullable();  
            $table->string("tablet_image",255)->nullable();  
            $table->string("mobile_image",255)->nullable();  
            $table->string("icon",255)->nullable();  
            $table->string("video",255)->nullable();  
            $table->string("link",255)->nullable();  
            $table->unsignedBigInteger("cms_pages_id")->nullable();
            $table->foreign("cms_pages_id")->references("id")->on("cms_pages")
              ->cascadeOnDelete();
    
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::create('cms_page_detail_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255)->nullable();  
            $table->string("description",1000)->nullable();  
            $table->string("intro",1000)->nullable();  
            $table->string("point",1000)->nullable();  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('cms_page_detail_id');
            $table->unique(['cms_page_detail_id', 'locale']);
            $table->foreign('cms_page_detail_id')->references('id')->on('cms_page_details')
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
        Schema::dropIfExists('cms_page_details');
        Schema::dropIfExists('cms_page_detail_translations');
    
    }
};

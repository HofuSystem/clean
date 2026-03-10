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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string("slug",255)->unique();  
            $table->boolean("is_parent")->default(false);  
            $table->boolean("is_multi_upload")->default(false);  
            $table->boolean("have_point")->default(false);  
            $table->boolean("have_name")->default(false);  
            $table->boolean("have_description")->default(false);  
            $table->boolean("have_intro")->default(false);  
            $table->boolean("have_image")->default(false);  
            $table->boolean("have_tablet_image")->default(false);  
            $table->boolean("have_mobile_image")->default(false);  
            $table->boolean("have_icon")->default(false);  
            $table->boolean("have_video")->default(false);  
            $table->boolean("have_link")->default(false);  
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::create('cms_page_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255);  
            $table->string('locale')->index();
            $table->unsignedBigInteger('cms_page_id');
            $table->unique(['cms_page_id', 'locale']);
            $table->foreign('cms_page_id')->references('id')->on('cms_pages')
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
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('cms_page_translations');
    
    }
};

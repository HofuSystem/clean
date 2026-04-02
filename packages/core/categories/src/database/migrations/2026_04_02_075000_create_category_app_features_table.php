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
        Schema::create('category_app_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("category_id")->nullable();
            $table->foreign("category_id")->references("id")->on("categories")
                ->cascadeOnDelete();
            
            $table->enum("section", ["mainFeature", "reviewsCount", "reviewsRate", "intro", "secFeaures", "whyus", "included"]);
            $table->text("image")->nullable();
            $table->string("value")->nullable();
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('category_app_feature_translations', function (Blueprint $table) {
            $table->id();
            $table->string("title", 255);
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('category_app_feature_id');
            $table->unique(['category_app_feature_id', 'locale'], 'cat_app_feat_id_locale_unique');
            $table->foreign('category_app_feature_id', 'cat_app_feat_id_foreign')->references('id')->on('category_app_features')
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
        Schema::dropIfExists('category_app_features');
        Schema::dropIfExists('category_app_feature_translations');
    }
};

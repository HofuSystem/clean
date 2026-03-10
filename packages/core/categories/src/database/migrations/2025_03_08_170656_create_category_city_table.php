<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('category_cities', function (Blueprint $table) {
            $table->unsignedBigInteger("category_id");
            $table->foreign("category_id")->references("id")->on("categories")
              ->cascadeOnDelete();
              $table->unsignedBigInteger("city_id");
            $table->foreign("city_id")->references("id")->on("cities")
              ->cascadeOnDelete();
            $table->primary(['category_id','city_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_cities');
    }
};
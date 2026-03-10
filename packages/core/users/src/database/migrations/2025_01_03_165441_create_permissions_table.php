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
      
        Schema::create('permission_translations', function (Blueprint $table) {
            $table->id();
            $table->string("title",255);  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('permission_id');
            $table->unique(['permission_id', 'locale']);
            $table->foreign('permission_id')->references('id')->on('permissions')
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
        Schema::dropIfExists('permission_translations');
    
    }
};

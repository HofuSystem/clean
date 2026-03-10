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
       
        Schema::create('role_translations', function (Blueprint $table) {
            $table->id();
            $table->string("title",255);  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('role_id');
            $table->unique(['role_id', 'locale']);
            $table->foreign('role_id')->references('id')->on('roles')
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
        Schema::dropIfExists('role_translations');
    
    }
};

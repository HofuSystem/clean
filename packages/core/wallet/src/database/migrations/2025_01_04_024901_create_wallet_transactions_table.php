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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum("type",["deposit","withdraw"]);
            $table->double("amount")->unsigned();
            $table->double("wallet_before")->unsigned();
            $table->double("wallet_after")->unsigned()->nullable();
            $table->enum("status",["pending","accepted","rejected"]);
            $table->string("transaction_id",255)->nullable();
            $table->string("bank_name",255)->nullable();
            $table->string("account_number",255)->nullable();
            $table->string("iban_number",255)->nullable();
            $table->unsignedBigInteger("user_id")->nullable();
            $table->foreign("user_id")->references("id")->on("users")
              ->nullOnDelete();
            $table->unsignedBigInteger("added_by_id")->nullable();
            $table->foreign("added_by_id")->references("id")->on("users")
              ->nullOnDelete();
            $table->unsignedBigInteger("package_id")->nullable();
            $table->foreign("package_id")->references("id")->on("wallet_packages")
              ->nullOnDelete();

            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

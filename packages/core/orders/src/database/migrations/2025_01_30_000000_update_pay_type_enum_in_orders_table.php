<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the existing enum constraint
        DB::statement("ALTER TABLE orders MODIFY COLUMN pay_type ENUM('card', 'cash', 'wallet', 'contract') NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to the original enum values
        DB::statement("ALTER TABLE orders MODIFY COLUMN pay_type ENUM('card', 'cash', 'wallet') NULL");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Company Permissions
        if (!Schema::hasTable('company_permissions')) {
            Schema::create('company_permissions', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->timestamps();
                $table->softDeletes();
                $table->unsignedBigInteger('creator_id')->nullable();
                $table->unsignedBigInteger('updater_id')->nullable();
                
                $table->foreign('creator_id')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updater_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 1.1 Company Permission Translations
        if (!Schema::hasTable('company_permission_translations')) {
            Schema::create('company_permission_translations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('locale')->index();
                $table->unsignedBigInteger('company_permission_id');
                $table->unique(['company_permission_id', 'locale'], 'cp_trans_unique');
                $table->foreign('company_permission_id', 'cp_trans_foreign')
                    ->references('id')->on('company_permissions')
                    ->cascadeOnDelete();
            });
        }

        // 2. Company Employees (Pivot)
        if (!Schema::hasTable('company_employees')) {
            Schema::create('company_employees', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('company_permissions')->onDelete('cascade');
                $table->foreign('branch_id')->references('id')->on('company_branches')->onDelete('set null');
            });
        }

        // 3. Update Orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('client_id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            }
            if (!Schema::hasColumn('orders', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('company_id');
                $table->foreign('branch_id')->references('id')->on('company_branches')->onDelete('set null');
            }
            if (!Schema::hasColumn('orders', 'b2b_type')) {
                $table->enum('b2b_type', ['company', 'client'])->default('client')->after('branch_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['company_id', 'branch_id', 'b2b_type']);
        });
        Schema::dropIfExists('company_employees');
        Schema::dropIfExists('company_permission_translations');
        Schema::dropIfExists('company_permissions');
    }
};

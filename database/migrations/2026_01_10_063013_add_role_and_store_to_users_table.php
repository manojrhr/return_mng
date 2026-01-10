<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                // Make nullable initially - boot method will set it
                $table->foreignId('role_id')->nullable()->after('id')->constrained();
            }
            if (!Schema::hasColumn('users', 'store_id')) {
                $table->foreignId('store_id')->nullable()->after('role_id')->constrained()->onDelete('cascade');
            }
            // If company_id exists, drop it
            if (Schema::hasColumn('users', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['store_id']);
            $table->dropColumn(['role_id', 'store_id']);
        });
    }
};

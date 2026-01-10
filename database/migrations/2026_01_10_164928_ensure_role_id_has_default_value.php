<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'role_id')) {
            // First, ensure roles exist
            if (!\DB::table('roles')->exists()) {
                // Create default roles if they don't exist
                \DB::table('roles')->insert([
                    ['name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'store_user', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'warehouse_user', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'client_user', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }
            
            // Get default role (client_user) or first available role
            $defaultRole = \DB::table('roles')
                ->where('name', 'client_user')
                ->first() 
                ?? \DB::table('roles')->first();
            
            if ($defaultRole) {
                // Update any existing users without role_id
                \DB::table('users')
                    ->whereNull('role_id')
                    ->update(['role_id' => $defaultRole->id]);
            }
            
            // Make the column nullable to allow inserts without role_id
            // The User model's boot method will set it automatically
            try {
                \DB::statement('ALTER TABLE `users` MODIFY `role_id` BIGINT UNSIGNED NULL');
            } catch (\Exception $e) {
                // Column might already be nullable, or we're using a different DB
                // Try Laravel's way
                Schema::table('users', function (Blueprint $table) {
                    $table->foreignId('role_id')->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to not nullable
        if (Schema::hasColumn('users', 'role_id')) {
            $defaultRole = Role::where('name', 'client_user')->first() 
                ?? Role::first();
            
            if ($defaultRole) {
                // Set default for any NULL values before making it NOT NULL
                \DB::table('users')
                    ->whereNull('role_id')
                    ->update(['role_id' => $defaultRole->id]);
            }
            
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable(false)->change();
            });
        }
    }
};

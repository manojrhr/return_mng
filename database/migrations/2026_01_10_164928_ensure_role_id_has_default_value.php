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
            
            $defaultRoleId = $defaultRole ? $defaultRole->id : 1;
            
            if ($defaultRole) {
                // Update any existing users without role_id
                \DB::table('users')
                    ->whereNull('role_id')
                    ->update(['role_id' => $defaultRoleId]);
            }
            
            // CRITICAL: Make the column nullable AND set a default value
            // This ensures MySQL won't reject inserts even if boot method fails
            $driver = \DB::connection()->getDriverName();
            
            if ($driver === 'mysql') {
                // For MySQL, we need to drop foreign key first, modify column, then re-add foreign key
                try {
                    // Drop foreign key constraint if it exists
                    $foreignKeys = \DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'users' 
                        AND COLUMN_NAME = 'role_id' 
                        AND REFERENCED_TABLE_NAME IS NOT NULL
                    ");
                    
                    foreach ($foreignKeys as $fk) {
                        \DB::statement("ALTER TABLE `users` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                    }
                    
                    // Modify column to be nullable with default
                    \DB::statement("ALTER TABLE `users` MODIFY `role_id` BIGINT UNSIGNED NULL DEFAULT {$defaultRoleId}");
                    
                    // Re-add foreign key constraint
                    \DB::statement("ALTER TABLE `users` ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)");
                } catch (\Exception $e) {
                    // If foreign key doesn't exist or other error, just modify the column
                    \DB::statement("ALTER TABLE `users` MODIFY `role_id` BIGINT UNSIGNED NULL DEFAULT {$defaultRoleId}");
                }
            } else {
                // For other databases, use Laravel's Schema
                try {
                    Schema::table('users', function (Blueprint $table) use ($defaultRoleId) {
                        $table->foreignId('role_id')->nullable()->default($defaultRoleId)->change();
                    });
                } catch (\Exception $e) {
                    // Fallback: just make it nullable
                    Schema::table('users', function (Blueprint $table) {
                        $table->foreignId('role_id')->nullable()->change();
                    });
                }
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

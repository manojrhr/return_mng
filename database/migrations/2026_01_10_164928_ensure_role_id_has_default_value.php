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
        // Get default role (client_user) or first available role
        $defaultRole = Role::where('name', 'client_user')->first() 
            ?? Role::first();
        
        if ($defaultRole && Schema::hasColumn('users', 'role_id')) {
            // Update any existing users without role_id
            \DB::table('users')
                ->whereNull('role_id')
                ->update(['role_id' => $defaultRole->id]);
            
            // Make the column nullable temporarily to allow inserts without role_id
            // The User model's boot method will set it automatically
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable()->change();
            });
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

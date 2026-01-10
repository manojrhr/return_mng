<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Create Super Admin User
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // Provide the missing value here
            'store_id' => null,
        ]);

    }
}

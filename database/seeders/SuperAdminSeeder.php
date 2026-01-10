<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;              // ✅ REQUIRED
use App\Models\Role;              // ✅ REQUIRED
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'super_admin')->first()->id,
                'store_id' => null,
            ]);
        }
    }
}

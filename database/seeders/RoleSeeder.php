<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'super_admin',
            'store_user',
            'warehouse_user',
            'client_user'
        ] as $role) {
            Role::create(['name' => $role]);
        }
    }
}

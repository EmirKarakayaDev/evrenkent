<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['super_admin', 'dergi_editoru', 'yazar', 'okur'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}

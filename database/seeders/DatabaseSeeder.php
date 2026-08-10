<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $superAdmin = User::factory()->create([
            'name' => 'Süper Admin',
            'email' => 'admin@evrenkent.test',
            'password' => bcrypt('password'),
        ]);
        $superAdmin->assignRole('super_admin');

        $editor = User::factory()->create([
            'name' => 'Dergi Editörü',
            'email' => 'editor@evrenkent.test',
            'password' => bcrypt('password'),
        ]);
        $editor->assignRole('dergi_editoru');

        $author = User::factory()->create([
            'name' => 'Yazar',
            'email' => 'author@evrenkent.test',
            'password' => bcrypt('password'),
        ]);
        $author->assignRole('yazar');

        $reader = User::factory()->create([
            'name' => 'Okur',
            'email' => 'reader@evrenkent.test',
            'password' => bcrypt('password'),
        ]);
        $reader->assignRole('okur');
    }
}

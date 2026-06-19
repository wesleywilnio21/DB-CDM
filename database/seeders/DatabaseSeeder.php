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
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin User',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
            ]
        );

        // Seed development/dummy data
        $this->call(DummyDataSeeder::class);
    }
}

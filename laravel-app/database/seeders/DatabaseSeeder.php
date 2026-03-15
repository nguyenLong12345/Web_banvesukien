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
        // Create test user
        User::create([
            'user_id' => 'PKA0001',
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'reset_token' => '',
            'reset_expire' => null,
        ]);

        // Create admin user
        \App\Models\Admin::create([
            'username' => 'admin',
            'password' => bcrypt('admin123'),
        ]);
    }
}

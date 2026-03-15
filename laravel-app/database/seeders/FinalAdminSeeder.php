<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FinalAdminSeeder extends Seeder
{
    /**
     * Setup final admin account - only one admin
     */
    public function run(): void
    {
        echo "→ Setting up admin account...\n";

        // Delete all existing admins
        DB::table('admins')->truncate();
        
        // Create single admin account
        DB::table('admins')->insert([
            'id' => 1,
            'username' => 'admin',
            'email' => 'admin@ticketbox.com',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✓ Admin account created\n";
        echo "  Username: admin\n";
        echo "  Password: admin123\n\n";

        echo "=== ADMIN ACCOUNT READY ===\n";
        echo "Login at: http://localhost:8000/admin/login\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    }
}

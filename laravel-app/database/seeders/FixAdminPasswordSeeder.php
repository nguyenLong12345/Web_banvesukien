<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FixAdminPasswordSeeder extends Seeder
{
    /**
     * Fix admin password and create test accounts
     */
    public function run(): void
    {
        echo "→ Fixing admin password...\n";

        // Update existing admin with new password
        DB::table('admins')
            ->where('username', 'admin')
            ->update([
                'password' => Hash::make('admin123')
            ]);

        echo "✓ Admin password updated\n";
        echo "  Username: admin\n";
        echo "  Password: admin123\n\n";

        // Check if test admin exists
        $testAdmin = DB::table('admins')->where('username', 'testadmin')->first();
        
        if (!$testAdmin) {
            DB::table('admins')->insert([
                'username' => 'testadmin',
                'password' => Hash::make('password')
            ]);
            echo "✓ Created test admin account\n";
            echo "  Username: testadmin\n";
            echo "  Password: password\n\n";
        }

        echo "=== ADMIN ACCOUNTS ===\n";
        $admins = DB::table('admins')->get(['id', 'username']);
        foreach ($admins as $admin) {
            echo "- ID: {$admin->id}, Username: {$admin->username}\n";
        }

        echo "\n✓ Admin accounts ready!\n";
        echo "\nLogin at: http://localhost/laravel-app/public/admin/login\n";
    }
}

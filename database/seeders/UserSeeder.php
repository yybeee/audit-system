<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat department
        $dept = Department::firstOrCreate(
            ['code' => 'IT'],
            [
                'name' => 'IT Department',
                'description' => 'Information Technology',
                'is_active' => true
            ]
        );

        // Buat user super admin
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'department_id' => $dept->id,
                'is_active' => true
            ]
        );

        echo "✅ User admin berhasil dibuat!\n";
        echo "Username: admin\n";
        echo "Password: password\n";
    }
}
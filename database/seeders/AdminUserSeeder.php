<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@himmah.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '0501234567',
        ]);

        // Create new admin user with encrypted password
        User::create([
            'name' => 'أحمد المدير',
            'email' => 'ahmed@himmah.com',
            'password' => Hash::make('Himmah2024!'),
            'role' => 'admin',
            'phone' => '0509876543',
        ]);

        // Create test users
        User::create([
            'name' => 'محمد أحمد',
            'email' => 'user@himmah.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'phone' => '0507654321',
        ]);

        User::create([
            'name' => 'فاطمة علي',
            'email' => 'fatima@himmah.com',
            'password' => Hash::make('fatima123'),
            'role' => 'user',
            'phone' => '0501111111',
        ]);

        User::create([
            'name' => 'عبدالله خالد',
            'email' => 'abdullah@himmah.com',
            'password' => Hash::make('abdullah123'),
            'role' => 'user',
            'phone' => '0502222222',
        ]);

        User::create([
            'name' => 'سارة محمد',
            'email' => 'sara@himmah.com',
            'password' => Hash::make('sara123'),
            'role' => 'user',
            'phone' => '0503333333',
        ]);

        User::create([
            'name' => 'علي حسن',
            'email' => 'ali@himmah.com',
            'password' => Hash::make('ali123'),
            'role' => 'user',
            'phone' => '0504444444',
        ]);
    }
}


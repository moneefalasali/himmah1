<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم معلم تجريبي
        User::create([
            'name' => 'أحمد المعلم',
            'email' => 'instructor@example.com',
            'password' => Hash::make('password'),
            'phone' => '0501234567',
            'role' => 'instructor',
            'is_instructor' => true,
        ]);

        $this->command->info('تم إنشاء مستخدم معلم تجريبي بنجاح!');
        $this->command->info('البريد الإلكتروني: instructor@example.com');
        $this->command->info('كلمة المرور: password');
    }
} 
<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // الأدمن
        User::updateOrCreate(
            ['email' => 'admin@himmah.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '0501234567',
            ]
        );

        // المعلمون
        User::updateOrCreate(
            ['email' => 'teacher@himmah.com'],
            [
                'name' => 'د. أحمد سعيد',
                'password' => Hash::make('teacher123'),
                'role' => 'teacher',
                'phone' => '0509876543',
            ]
        );

        User::updateOrCreate(
            ['email' => 'ali_teacher@himmah.com'],
            [
                'name' => 'أ. علي حسن',
                'password' => Hash::make('teacher123'),
                'role' => 'teacher',
                'phone' => '0504444444',
            ]
        );

        // الطلاب
        User::updateOrCreate(
            ['email' => 'student@himmah.com'],
            [
                'name' => 'محمد أحمد',
                'password' => Hash::make('student123'),
                'role' => 'user',
                'phone' => '0507654321',
            ]
        );
    }
}

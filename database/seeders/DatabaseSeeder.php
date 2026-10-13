<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with safe local demo accounts.
     */
    public function run(): void
    {
        // 1. Administrator Demo Account
        User::updateOrCreate(
            ['email' => 'admin@schoolsphere.test'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 2. Teacher Demo Account
        User::updateOrCreate(
            ['email' => 'teacher@schoolsphere.test'],
            [
                'name' => 'Sarah Jenkins',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 3. Student Demo Account
        User::updateOrCreate(
            ['email' => 'student@schoolsphere.test'],
            [
                'name' => 'Alexander Hayes',
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 4. Parent / Guardian Demo Account
        User::updateOrCreate(
            ['email' => 'parent@schoolsphere.test'],
            [
                'name' => 'Marcus Hayes',
                'password' => Hash::make('password'),
                'role' => 'parent',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 5. Deactivated User Account (for testing status restrictions)
        User::updateOrCreate(
            ['email' => 'inactive@schoolsphere.test'],
            [
                'name' => 'Deactivated User',
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'inactive',
                'email_verified_at' => now(),
            ]
        );
    }
}

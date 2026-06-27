<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $staffRole = Role::where('slug', 'staff')->first();

        // Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        )->roles()->sync([$superAdminRole->id]);

        // Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        )->roles()->sync([$adminRole->id]);

        // Staff
        User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff Member',
                'email' => 'staff@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        )->roles()->sync([$staffRole->id]);

        // Create additional test users
        $memberRole = Role::where('slug', 'member')->first();
        $volunteerRole = Role::where('slug', 'volunteer')->first();
        $donorRole = Role::where('slug', 'donor')->first();

        for ($i = 1; $i <= 5; $i++) {
            User::firstOrCreate(
                ['email' => "member{$i}@example.com"],
                [
                    'name' => "Member {$i}",
                    'email' => "member{$i}@example.com",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            )->roles()->sync([$memberRole->id]);
        }

        for ($i = 1; $i <= 3; $i++) {
            User::firstOrCreate(
                ['email' => "volunteer{$i}@example.com"],
                [
                    'name' => "Volunteer {$i}",
                    'email' => "volunteer{$i}@example.com",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            )->roles()->sync([$volunteerRole->id]);
        }

        for ($i = 1; $i <= 3; $i++) {
            User::firstOrCreate(
                ['email' => "donor{$i}@example.com"],
                [
                    'name' => "Donor {$i}",
                    'email' => "donor{$i}@example.com",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            )->roles()->sync([$donorRole->id]);
        }
    }
}

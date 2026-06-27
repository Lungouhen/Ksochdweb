<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions',
                'is_admin' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrative access to manage content and members',
                'is_admin' => true,
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Staff member with limited administrative access',
                'is_admin' => false,
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Regular organization member',
                'is_admin' => false,
            ],
            [
                'name' => 'Volunteer',
                'slug' => 'volunteer',
                'description' => 'Volunteer with event participation access',
                'is_admin' => false,
            ],
            [
                'name' => 'Donor',
                'slug' => 'donor',
                'description' => 'Donor with donation history access',
                'is_admin' => false,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }
    }
}

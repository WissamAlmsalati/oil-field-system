<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@almansoori.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        
        // Assign Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole && !$admin->hasRole('Admin')) {
            $admin->assignRole($adminRole);
        }

        // Create or update manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@almansoori.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        
        // Assign Manager role
        $managerRole = Role::where('name', 'Manager')->first();
        if ($managerRole && !$manager->hasRole('Manager')) {
            $manager->assignRole($managerRole);
        }

        // Create or update regular user
        $user = User::firstOrCreate(
            ['email' => 'user@almansoori.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        
        // Assign User role
        $userRole = Role::where('name', 'User')->first();
        if ($userRole && !$user->hasRole('User')) {
            $user->assignRole($userRole);
        }

        $this->command->info('Admin users created/updated and roles assigned successfully!');
    }
}

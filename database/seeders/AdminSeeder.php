<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@almansoori.com',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@almansoori.com',
            'password' => Hash::make('password123'),
            'role' => 'Manager',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@almansoori.com',
            'password' => Hash::make('password123'),
            'role' => 'User',
            'email_verified_at' => now(),
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@gopet.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        \App\Models\User::create([
            'name' => 'Owner User',
            'email' => 'owner@gopet.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'owner',
            'email_verified_at' => now(),
        ]);
    }
}

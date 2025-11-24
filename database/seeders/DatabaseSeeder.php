<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // SEED ADMIN
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // SEED CUSTOMER
        User::create([
            'name' => 'Customer Utama',
            'username' => 'customer',
            'email' => 'customer@example.com',
            'phone' => '089876543210',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
    }
}

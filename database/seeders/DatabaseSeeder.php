<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
     public function run(): void
    {
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Billing Clerk',
            'email' => 'billing@example.com',
            'password' => Hash::make('password'),
            'role' => 'billing_clerk',
        ]);

        User::create([
            'name' => 'Receivable Clerk',
            'email' => 'receivable@example.com',
            'password' => Hash::make('password'),
            'role' => 'receivable_clerk',
        ]);
    }
}

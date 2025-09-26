<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;       // ✅ Import User model
use Illuminate\Support\Facades\Hash; // ✅ Import Hash facade

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
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

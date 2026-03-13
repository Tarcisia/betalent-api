<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@betalent.local',
                'password' => '123456',
                'role' => 'ADMIN',
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@betalent.local',
                'password' => '123456',
                'role' => 'MANAGER',
            ],
            [
                'name' => 'Finance',
                'email' => 'finance@betalent.local',
                'password' => '123456',
                'role' => 'FINANCE',
            ],
            [
                'name' => 'User',
                'email' => 'user@betalent.local',
                'password' => '123456',
                'role' => 'USER',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                    'role' => $user['role'],
                ]
            );
        }
    }
}
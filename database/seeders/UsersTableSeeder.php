<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (!$password) {
            throw new RuntimeException('ADMIN_PASSWORD is not configured.');
        }

        $data = [
            'name' => 'Admin',
            'email' => env('ADMIN_EMAIL') ?: 'admin@example.com',
            'password' => Hash::make($password),
            'is_admin' => 1,
            'role' => 'admin',
            'is_banned' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('users')->insert($data);
    }
}

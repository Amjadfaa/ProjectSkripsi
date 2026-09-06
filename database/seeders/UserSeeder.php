<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Administrator
        User::firstOrCreate(
            ['email' => 'admin@monitoring-pas.test'],
            [
                'name'       => 'Administrator',
                'password'   => Hash::make('password'),
                'role'       => 'administrator',
            ]
        );

        // Akun Operator
        User::firstOrCreate(
            ['email' => 'operator@monitoring-pas.test'],
            [
                'name'       => 'Petugas Operator',
                'password'   => Hash::make('password'),
                'role'       => 'operator',
            ]
        );
    }
}
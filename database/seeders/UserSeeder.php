<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'       => 'Administrator',
            'email'      => 'admin@monitoring-pas.test',
            'password'   => Hash::make('password'),
            'role'       => 'administrator',
        ]);

        User::create([
            'name'       => 'Verifikator',
            'email'      => 'verifikator@monitoring-pas.test',
            'password'   => Hash::make('password'),
            'role'       => 'verifikator',
        ]);

        User::create([
            'name'       => 'Pemohon PT ABC',
            'email'      => 'pemohon@monitoring-pas.test',
            'password'   => Hash::make('password'),
            'role'       => 'pemohon',
            'perusahaan' => 'PT ABC',
        ]);
    }
}
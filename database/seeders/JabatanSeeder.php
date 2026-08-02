<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Manager',
            'Supervisor',
            'Staff',
            'Teknisi',
            'Security',
            'AVSEC',
            'Driver',
            'Ground Handling',
            'Operator',
            'Direktur',
        ];

        foreach ($data as $nama) {
            Jabatan::updateOrCreate(['nama_jabatan' => $nama]);
        }
    }
}

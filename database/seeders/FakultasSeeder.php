<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fakultas;

class FakultasSeeder extends Seeder
{
    /**
     * Seed the fakultas table with fixed faculty values.
     * These are the only valid faculties allowed during import.
     */
    public function run(): void
    {
        $fakultasList = [
            'FTE',
            'FRI',
            'FIF',
            'FEB',
            'FKS',
            'FIK',
            'FIT',
            'TUP',
            'TUS',
        ];

        foreach ($fakultasList as $nama) {
            Fakultas::firstOrCreate(['nama_fakultas' => $nama]);
        }
    }
}

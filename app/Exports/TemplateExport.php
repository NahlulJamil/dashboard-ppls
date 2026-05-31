<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Program',
            'Sub Program',
            'Fakultas',
            'Program Studi',
            'NIM',
            'Nama Mahasiswa',
            'Tahun Ajaran',
            'Semester',
            'Semester TA',
            'Program Kegiatan',
            'Penyelenggara',
            'Mitra',
            'Dosen Pembimbing',
            'Jumlah SKS',
        ];
    }

    public function array(): array
    {
        // One example row to guide the user
        return [
            [
                'Asistensi Mengajar',
                'Kampus Mengajar',
                'FIF',
                'S1 Informatika',
                '1301210001',
                'CONTOH NAMA MAHASISWA',
                '2024/2025',
                'GENAP',
                '2024/2025 S2',
                'Nama Kegiatan',
                'Eksternal',
                'Nama Instansi Mitra',
                'NAMA DOSEN PEMBIMBING',
                '20',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '7B1113'],
                ],
            ],
            2 => [
                'font' => ['italic' => true, 'color' => ['rgb' => '999999']],
            ],
        ];
    }
}

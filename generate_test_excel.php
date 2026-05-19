<?php

/**
 * Generate test Excel file with errors for popup demo.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;

class TestErrorExport implements FromArray
{
    public function array(): array
    {
        return [
            // Header row
            ['Program', 'Sub Program', 'Fakultas', 'Program Studi', 'NIM', 'Nama Mahasiswa', 'Tahun Ajaran', 'Semester', 'Semester TA', 'Program Kegiatan', 'Penyelenggara', 'Mitra', 'Dosen Pembimbing Akademik', 'Jumlah Konversi SKS'],
            // Row 2: Valid data
            ['Asistensi Mengajar', 'Kampus Mengajar', 'FEB', 'S1 Manajemen', '1401184350', 'ADZRA HELGA', '2020/2021', 'GENAP', '2020/2021 S2', 'Marketing', 'Eksternal', 'Kemendikbud RI', 'TRIAJI', '20'],
            // Row 3: Typo penyelenggara
            ['Asistensi Mengajar', 'Kampus Mengajar', 'FEB', 'S1 Akuntansi', '1401184351', 'BUDI SANTOSO', '2020/2021', 'GENAP', '2020/2021 S2', 'Data Science', 'Eksternnal', 'Google Indonesia', 'DOSEN A', '18'],
            // Row 4: Fakultas tidak valid
            ['Asistensi Mengajar', 'Kampus Mengajar', 'FXX', 'S1 Teknik', '1401184352', 'CITRA DEWI', '2020/2021', 'GANJIL', '2020/2021 S1', 'Web Dev', 'Internal', 'Microsoft', 'DOSEN B', '15'],
            // Row 5: Semester tidak valid
            ['Asistensi Mengajar', 'Kampus Mengajar', 'FIF', 'S1 Informatika', '1401184353', 'DINA PUTRI', '2020/2021', 'Genap', '2020/2021 S2', 'AI Research', 'Eksternal', 'AWS Indonesia', 'DOSEN C', '20'],
            // Row 6: NIM bukan angka
            ['Asistensi Mengajar', 'Kampus Mengajar', 'FIT', 'S1 Desain', 'ABC123', 'EKO PRASETYO', '2020/2021', 'GANJIL', '2020/2021 S1', 'UI Design', 'Internal', 'Tokopedia', 'DOSEN D', '12'],
        ];
    }
}

Excel::store(new TestErrorExport, 'test_errors.xlsx', 'local');

$path = storage_path('app/private/test_errors.xlsx');
if (!file_exists($path)) {
    $path = storage_path('app/test_errors.xlsx');
}
echo "Test Excel created at: {$path}\n";
echo "File exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";

<?php

namespace App\Imports;

use App\Models\DataPlps;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\Kegiatan;
use App\Models\Mitra;
use App\Models\Program;
use App\Models\SubProgram;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Exception;

class DataPlpsImport implements ToCollection
{
    public $errors = [];

    /**
     * Mapping kolom Excel (0-indexed):
     * 0: Program           (e.g. "Asistensi Mengajar")
     * 1: Sub Program        (e.g. "Kampus Mengajar")
     * 2: Fakultas           (e.g. "FEB")
     * 3: Program Studi      (e.g. "S1 Manajemen")
     * 4: NIM                (e.g. "1401184350")
     * 5: Nama Mahasiswa     (e.g. "ADZRA HELGA ENGRASIA GUSHENDRI")
     * 6: Tahun Ajaran       (e.g. "2020/2021")
     * 7: Semester           (e.g. "GENAP")
     * 8: Semester TA        (e.g. "2020/2021 S2")
     * 9: Program Kegiatan   (e.g. "Marketing Communications")
     * 10: Penyelenggara     (e.g. "Eksternal")
     * 11: Mitra             (e.g. "Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi...")
     * 12: Dosen Pembimbing  (e.g. "TRIAJI PRIO PRATOMO")
     * 13: Jumlah SKS        (e.g. 20)
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Skip header row (baris pertama = template/keterangan kolom)
            if ($index == 0) continue;

            $line = $index + 1;

            try {
                // === NORMALISASI INPUT ===
                $programInput      = $this->normalize($row[0] ?? '');
                $subProgramInput   = $this->normalize($row[1] ?? '');
                $fakultasInput     = $this->normalizeUpper($row[2] ?? '');
                $prodiInput        = $this->normalize($row[3] ?? '');
                $nimInput          = trim($row[4] ?? '');
                $namaInput         = $this->normalize($row[5] ?? '');
                $tahunAjaranInput  = trim($row[6] ?? '');
                $semesterInput     = $this->normalizeUpper($row[7] ?? '');
                $semesterTaInput   = trim($row[8] ?? '');
                $kegiatanInput     = $this->normalize($row[9] ?? '');
                $penyelenggaraInput = $this->normalize($row[10] ?? '');
                $mitraInput        = $this->normalize($row[11] ?? '');
                $dosenInput        = $this->normalize($row[12] ?? '');
                $sksInput          = trim($row[13] ?? '');

                // === VALIDASI WAJIB ===
                if (empty($nimInput)) {
                    throw new Exception("NIM tidak boleh kosong");
                }
                if (!is_numeric($nimInput)) {
                    throw new Exception("NIM harus angka, ditemukan: \"{$nimInput}\"");
                }
                if (empty($namaInput)) {
                    throw new Exception("Nama Mahasiswa tidak boleh kosong");
                }
                if (empty($sksInput) || !is_numeric($sksInput)) {
                    throw new Exception("Jumlah SKS harus angka, ditemukan: \"{$sksInput}\"");
                }

                // === VALIDASI ENUM: Semester ===
                $validSemesters = ['GANJIL', 'GENAP'];
                if (!in_array($semesterInput, $validSemesters)) {
                    throw new Exception("Semester harus GANJIL/GENAP, ditemukan: \"{$semesterInput}\"");
                }

                // === VALIDASI ENUM: Penyelenggara ===
                $penyelenggaraNormalized = $this->validatePenyelenggara($penyelenggaraInput);

                // === VALIDASI FAKULTAS (harus match dengan data seeder) ===
                $fakultas = Fakultas::whereRaw('LOWER(nama_fakultas) = ?', [mb_strtolower($fakultasInput)])->first();
                if (!$fakultas) {
                    $validFakultas = Fakultas::pluck('nama_fakultas')->implode(', ');
                    throw new Exception("Fakultas \"{$fakultasInput}\" tidak valid. Fakultas yang tersedia: {$validFakultas}");
                }

                // === PRODI (firstOrCreate, scoped ke fakultas) ===
                $prodi = Prodi::firstOrCreate(
                    [
                        'nama_prodi' => $prodiInput,
                        'fakultas_id' => $fakultas->id,
                    ]
                );

                // === PROGRAM (firstOrCreate + unique) ===
                $program = $this->safeFirstOrCreate(
                    Program::class,
                    'nama_program',
                    $programInput
                );

                // === SUB PROGRAM (firstOrCreate + scoped ke program) ===
                $subProgram = SubProgram::firstOrCreate(
                    [
                        'nama_sub_program' => $subProgramInput,
                        'program_id' => $program->id,
                    ]
                );

                // === MAHASISWA (updateOrCreate by NIM) ===
                $mahasiswa = Mahasiswa::updateOrCreate(
                    ['nim' => $nimInput],
                    [
                        'nama' => $namaInput,
                        'prodi_id' => $prodi->id,
                    ]
                );

                // === KEGIATAN (fuzzy firstOrCreate + unique) ===
                $kegiatan = $this->fuzzyFirstOrCreate(
                    Kegiatan::class,
                    'nama_kegiatan',
                    $kegiatanInput
                );

                // === MITRA (fuzzy firstOrCreate + unique) ===
                $mitra = $this->fuzzyFirstOrCreate(
                    Mitra::class,
                    'nama_mitra',
                    $mitraInput
                );

                // === CEK DUPLIKAT ===
                $exists = DataPlps::where('nim', $nimInput)
                    ->where('kegiatan_id', $kegiatan->id)
                    ->where('mitra_id', $mitra->id)
                    ->where('semester', $semesterInput)
                    ->where('tahun_ajaran', $tahunAjaranInput)
                    ->exists();

                if ($exists) {
                    throw new Exception("Data duplikat (NIM: {$nimInput}, Kegiatan: {$kegiatanInput}, Semester: {$semesterInput}, TA: {$tahunAjaranInput})");
                }

                // === SIMPAN DATA PLPS ===
                DataPlps::create([
                    'program_id' => $program->id,
                    'sub_program_id' => $subProgram->id,
                    'nim' => $nimInput,
                    'kegiatan_id' => $kegiatan->id,
                    'mitra_id' => $mitra->id,
                    'sks' => (int) $sksInput,
                    'semester' => $semesterInput,
                    'tahun_ajaran' => $tahunAjaranInput,
                    'semester_ta' => $semesterTaInput,
                    'penyelenggara' => $penyelenggaraNormalized,
                    'dosen_pembimbing' => !empty($dosenInput) ? $dosenInput : null,
                ]);

            } catch (Exception $e) {
                $this->errors[] = "Baris {$line}: " . $e->getMessage();
            }
        }

        if (count($this->errors) > 0) {
            throw new Exception(implode("\n", $this->errors));
        }
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Trim whitespace dan normalize string (leading/trailing spaces, double spaces).
     */
    private function normalize(?string $value): string
    {
        if ($value === null) return '';
        return preg_replace('/\s+/', ' ', trim($value));
    }

    /**
     * Normalize dan uppercase.
     */
    private function normalizeUpper(?string $value): string
    {
        return mb_strtoupper($this->normalize($value));
    }

    /**
     * Validasi Penyelenggara (enum: Eksternal/Internal).
     * Case-insensitive match, return format baku.
     */
    private function validatePenyelenggara(string $input): string
    {
        $map = [
            'eksternal' => 'Eksternal',
            'internal' => 'Internal',
        ];

        $key = mb_strtolower($input);

        if (isset($map[$key])) {
            return $map[$key];
        }

        // Fuzzy check untuk typo
        foreach ($map as $valid => $formatted) {
            similar_text($key, $valid, $percent);
            if ($percent >= 75) {
                throw new Exception(
                    "Penyelenggara \"{$input}\" kemungkinan typo dari \"{$formatted}\". " .
                    "Nilai yang valid: Eksternal, Internal"
                );
            }
        }

        throw new Exception(
            "Penyelenggara \"{$input}\" tidak valid. Nilai yang valid: Eksternal, Internal"
        );
    }

    /**
     * Case-insensitive firstOrCreate.
     * Cari existing record dengan lowercase match, kalau tidak ada buat baru.
     */
    private function safeFirstOrCreate(string $modelClass, string $column, string $value)
    {
        $value = $this->normalize($value);

        if (empty($value)) {
            throw new Exception("Kolom {$column} tidak boleh kosong");
        }

        // Exact match (case-insensitive)
        $existing = $modelClass::whereRaw("LOWER({$column}) = ?", [mb_strtolower($value)])->first();

        if ($existing) {
            return $existing;
        }

        // Tidak ada match — create baru
        return $modelClass::create([$column => $value]);
    }

    /**
     * Fuzzy firstOrCreate untuk data dinamis (Mitra, Kegiatan).
     *
     * Flow:
     * 1. Exact match (case-insensitive) → pakai existing
     * 2. Fuzzy match (>= 80% similar) → REJECT dengan saran perbaikan
     * 3. Tidak ada match sama sekali → create baru
     *
     * Ini mencegah typo seperti "Kemendikbud Ristek" vs "Kemendikbudristek"
     * dari bikin record baru yang ngerusak filter.
     */
    private function fuzzyFirstOrCreate(string $modelClass, string $column, string $value, float $threshold = 80.0)
    {
        $value = $this->normalize($value);

        if (empty($value)) {
            throw new Exception("Kolom {$column} tidak boleh kosong");
        }

        $lowerValue = mb_strtolower($value);

        // 1. Exact match (case-insensitive)
        $exact = $modelClass::whereRaw("LOWER({$column}) = ?", [$lowerValue])->first();
        if ($exact) {
            return $exact;
        }

        // 2. Fuzzy match — cek semua existing records
        $allRecords = $modelClass::all();
        foreach ($allRecords as $record) {
            $existingValue = $record->$column;
            similar_text($lowerValue, mb_strtolower($existingValue), $percent);

            if ($percent >= $threshold) {
                throw new Exception(
                    "Kemungkinan typo: \"{$value}\" mirip dengan \"{$existingValue}\" " .
                    "({$percent}% kemiripan). Perbaiki data di Excel jika memang sama, " .
                    "atau abaikan jika memang berbeda."
                );
            }
        }

        // 3. Tidak mirip apa-apa — create baru
        return $modelClass::create([$column => $value]);
    }
}

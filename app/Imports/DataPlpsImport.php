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
use Illuminate\Support\Facades\DB;
use Exception;

class DataPlpsImport implements ToCollection
{
    public $errors = [];
    public $validateOnly = false;
    public $validRowCount = 0;
    public $processedKeys = [];
    public $isChunk = false;

    public function __construct(bool $validateOnly = false, bool $isChunk = false, array $processedKeys = [])
    {
        $this->validateOnly = $validateOnly;
        $this->isChunk = $isChunk;
        $this->processedKeys = $processedKeys;
    }

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
        DB::transaction(function () use ($rows) {
            // === PRE-FETCH CACHES FOR PERFORMANCE ===
            $programInputs = [];
            $subProgramInputs = [];
            $fakultasInputs = [];
            $prodiInputs = [];
            $nims = [];
            $kegiatanInputs = [];
            $mitraInputs = [];

            foreach ($rows as $index => $row) {
                if ($this->isChunk) {
                    if ($index == 1) continue;
                } else {
                    if ($index == 0) continue;
                }

                $programInputs[] = $this->normalize($row[0] ?? '');
                $subProgramInputs[] = $this->normalize($row[1] ?? '');
                $fakInput = $this->normalizeUpper($row[2] ?? '');
                if ($fakInput === 'FKB') {
                    $fakInput = 'FKS';
                }
                $fakultasInputs[] = $fakInput;
                $prodiInputs[] = $this->normalize($row[3] ?? '');
                $nims[] = trim($row[4] ?? '');
                $kegiatanInputs[] = $this->normalize($row[9] ?? '');
                $mitraInputs[] = $this->normalize($row[11] ?? '');
            }

            $programInputs = array_values(array_unique(array_filter($programInputs)));
            $subProgramInputs = array_values(array_unique(array_filter($subProgramInputs)));
            $fakultasInputs = array_values(array_unique(array_filter($fakultasInputs)));
            $prodiInputs = array_values(array_unique(array_filter($prodiInputs)));
            $nims = array_values(array_unique(array_filter($nims)));
            $kegiatanInputs = array_values(array_unique(array_filter($kegiatanInputs)));
            $mitraInputs = array_values(array_unique(array_filter($mitraInputs)));

            // Load reference models in bulk (reduces DB queries by 99%)
            $fakultasCache = Fakultas::whereIn(DB::raw('LOWER(nama_fakultas)'), array_map('mb_strtolower', $fakultasInputs))
                ->get()
                ->keyBy(fn($item) => mb_strtolower($item->nama_fakultas));

            $prodiCache = Prodi::whereIn(DB::raw('LOWER(nama_prodi)'), array_map('mb_strtolower', $prodiInputs))
                ->get()
                ->groupBy(fn($item) => mb_strtolower($item->nama_prodi));

            $programCache = Program::whereIn(DB::raw('LOWER(nama_program)'), array_map('mb_strtolower', $programInputs))
                ->get()
                ->keyBy(fn($item) => mb_strtolower($item->nama_program));

            $subProgramCache = SubProgram::whereIn(DB::raw('LOWER(nama_sub_program)'), array_map('mb_strtolower', $subProgramInputs))
                ->get()
                ->groupBy(fn($item) => mb_strtolower($item->nama_sub_program));

            $mahasiswaCache = Mahasiswa::whereIn('nim', $nims)
                ->get()
                ->keyBy('nim');

            // Load all Kegiatan and Mitra into memory to avoid repeated queries during fuzzy checks
            $allKegiatan = Kegiatan::all()->keyBy(fn($item) => mb_strtolower($item->nama_kegiatan));
            $allMitra = Mitra::all()->keyBy(fn($item) => mb_strtolower($item->nama_mitra));

            // Load existing DataPlps records matching NIMs in this chunk to prevent duplicate queries
            $existingPlps = [];
            if (!empty($nims)) {
                $existingPlps = DataPlps::whereIn('nim', $nims)
                    ->select('nim', 'program_id', 'semester', 'tahun_ajaran')
                    ->get()
                    ->map(fn($item) => "{$item->nim}_{$item->program_id}_{$item->semester}_{$item->tahun_ajaran}")
                    ->flip()
                    ->toArray();
            }

            foreach ($rows as $index => $row) {
                if ($this->isChunk) {
                    $line = $index; // For custom chunk collection, keys are exact Excel row numbers
                    if ($line == 1) continue; // Skip header row
                } else {
                    if ($index == 0) continue; // Skip header row
                    $line = $index + 1;
                }

                try {
                    // === NORMALISASI INPUT ===
                    $programInput      = $this->normalize($row[0] ?? '');
                    $subProgramInput   = $this->normalize($row[1] ?? '');
                    $fakultasInput     = $this->normalizeUpper($row[2] ?? '');
                    if ($fakultasInput === 'FKB') {
                        $fakultasInput = 'FKS';
                    }
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

                    // === DETEKSI BARIS KOSONG ===
                    // Jika semua kolom penting bernilai kosong, abaikan baris ini (biasanya baris kosong sisa hapusan di Excel)
                    if (
                        empty($programInput) && 
                        empty($subProgramInput) && 
                        empty($fakultasInput) && 
                        empty($prodiInput) && 
                        empty($nimInput) && 
                        empty($namaInput) &&
                        empty($tahunAjaranInput) &&
                        empty($semesterInput) &&
                        empty($kegiatanInput) &&
                        empty($mitraInput)
                    ) {
                        break;
                    }

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
                    $lowerFakultas = mb_strtolower($fakultasInput);
                    $fakultas = $fakultasCache->get($lowerFakultas);
                    if (!$fakultas) {
                        $validFakultas = Fakultas::pluck('nama_fakultas')->implode(', ');
                        throw new Exception("Fakultas \"{$fakultasInput}\" tidak valid. Fakultas yang tersedia: {$validFakultas}");
                    }

                    // === PRODI (case-insensitive lookup, scoped ke fakultas) ===
                    $lowerProdi = mb_strtolower($prodiInput);
                    $prodisWithSameName = $prodiCache->get($lowerProdi);
                    $prodi = $prodisWithSameName ? $prodisWithSameName->firstWhere('fakultas_id', $fakultas->id) : null;
                    if (!$prodi) {
                        $prodi = Prodi::create([
                            'nama_prodi' => $prodiInput,
                            'fakultas_id' => $fakultas->id,
                        ]);
                        if (!$prodisWithSameName) {
                            $prodisWithSameName = collect();
                            $prodiCache->put($lowerProdi, $prodisWithSameName);
                        }
                        $prodisWithSameName->push($prodi);
                    }

                    // === PROGRAM (case-insensitive cache check / creation) ===
                    $lowerProgram = mb_strtolower($programInput);
                    if (empty($programInput)) {
                        throw new Exception("Kolom Program tidak boleh kosong");
                    }
                    $program = $programCache->get($lowerProgram);
                    if (!$program) {
                        $program = Program::create(['nama_program' => $programInput]);
                        $programCache->put($lowerProgram, $program);
                    }

                    // === SUB PROGRAM (case-insensitive lookup, scoped ke program) ===
                    $lowerSubProgram = mb_strtolower($subProgramInput);
                    $subProgramsWithSameName = $subProgramCache->get($lowerSubProgram);
                    $subProgram = $subProgramsWithSameName ? $subProgramsWithSameName->firstWhere('program_id', $program->id) : null;
                    if (!$subProgram) {
                        $existingSub = $subProgramsWithSameName ? $subProgramsWithSameName->first() : null;
                        if ($existingSub) {
                            $subProgram = $existingSub;
                        } else {
                            $subProgram = SubProgram::create([
                                'nama_sub_program' => $subProgramInput,
                                'program_id' => $program->id,
                            ]);
                        }
                        if (!$subProgramsWithSameName) {
                            $subProgramsWithSameName = collect();
                            $subProgramCache->put($lowerSubProgram, $subProgramsWithSameName);
                        }
                        $subProgramsWithSameName->push($subProgram);
                    }

                    // === MAHASISWA (updateOrCreate by NIM) ===
                    $mahasiswa = $mahasiswaCache->get($nimInput);
                    if ($mahasiswa) {
                        if ($mahasiswa->nama !== $namaInput || $mahasiswa->prodi_id !== $prodi->id) {
                            $mahasiswa->update([
                                'nama' => $namaInput,
                                'prodi_id' => $prodi->id,
                            ]);
                        }
                    } else {
                        $mahasiswa = Mahasiswa::create([
                            'nim' => $nimInput,
                            'nama' => $namaInput,
                            'prodi_id' => $prodi->id,
                        ]);
                        $mahasiswaCache->put($nimInput, $mahasiswa);
                    }

                    // === KEGIATAN (fuzzy lookup / creation) ===
                    $kegiatan = null;
                    if (!empty($kegiatanInput)) {
                        $lowerKegiatan = mb_strtolower($kegiatanInput);
                        $kegiatan = $allKegiatan->get($lowerKegiatan);
                        if (!$kegiatan) {
                            // Fuzzy match
                            foreach ($allKegiatan as $record) {
                                $existingValue = $record->nama_kegiatan;
                                similar_text($lowerKegiatan, mb_strtolower($existingValue), $percent);
                                if ($percent >= 80.0) {
                                    throw new Exception(
                                        "Kemungkinan typo: \"{$kegiatanInput}\" mirip dengan \"{$existingValue}\" " .
                                        "({$percent}% kemiripan). Perbaiki data di Excel agar penulisan sama persis."
                                    );
                                }
                            }
                            $kegiatan = Kegiatan::create(['nama_kegiatan' => $kegiatanInput]);
                            $allKegiatan->put($lowerKegiatan, $kegiatan);
                        }
                    }

                    // === MITRA (fuzzy lookup / creation) ===
                    $lowerMitra = mb_strtolower($mitraInput);
                    if (empty($mitraInput)) {
                        throw new Exception("Kolom Mitra tidak boleh kosong");
                    }
                    $mitra = $allMitra->get($lowerMitra);
                    if (!$mitra) {
                        // Fuzzy match
                        foreach ($allMitra as $record) {
                            $existingValue = $record->nama_mitra;
                            similar_text($lowerMitra, mb_strtolower($existingValue), $percent);
                            if ($percent >= 80.0) {
                                throw new Exception(
                                    "Kemungkinan typo: \"{$mitraInput}\" mirip dengan \"{$existingValue}\" " .
                                    "({$percent}% kemiripan). Perbaiki data di Excel agar penulisan sama persis."
                                );
                            }
                        }
                        $mitra = Mitra::create(['nama_mitra' => $mitraInput]);
                        $allMitra->put($lowerMitra, $mitra);
                    }

                    // === CEK DUPLIKAT ===
                    $duplicateKey = "{$nimInput}_{$program->id}_{$semesterInput}_{$tahunAjaranInput}";

                    if (isset($this->processedKeys[$duplicateKey])) {
                        throw new Exception("Data duplikat di dalam file yang sama pada baris ke-{$this->processedKeys[$duplicateKey]} (NIM: {$nimInput}, Program: {$programInput}, Semester: {$semesterInput}, TA: {$tahunAjaranInput})");
                    }
                    $this->processedKeys[$duplicateKey] = $line;

                    if (isset($existingPlps[$duplicateKey])) {
                        throw new Exception("Data duplikat (NIM: {$nimInput}, Program: {$programInput}, Semester: {$semesterInput}, TA: {$tahunAjaranInput})");
                    }

                    if (!$this->validateOnly) {
                        // === SIMPAN DATA PLPS ===
                        DataPlps::create([
                            'program_id' => $program->id,
                            'sub_program_id' => $subProgram->id,
                            'nim' => $nimInput,
                            'kegiatan_id' => $kegiatan ? $kegiatan->id : null,
                            'mitra_id' => $mitra->id,
                            'sks' => (int) $sksInput,
                            'semester' => $semesterInput,
                            'tahun_ajaran' => $tahunAjaranInput,
                            'semester_ta' => $semesterTaInput,
                            'penyelenggara' => $penyelenggaraNormalized,
                            'dosen_pembimbing' => !empty($dosenInput) ? $dosenInput : null,
                        ]);
                        $existingPlps[$duplicateKey] = true;
                    }

                    $this->validRowCount++;

                } catch (Exception $e) {
                    $this->errors[] = "Baris {$line}: " . $e->getMessage();
                }
            }

            if (count($this->errors) > 0) {
                throw new Exception(implode("\n", $this->errors));
            }
        }); // end DB::transaction
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
}

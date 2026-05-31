<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration untuk membersihkan data duplikat di tabel lookup:
 * - prodis (duplikat berdasarkan nama_prodi, case-insensitive)
 * - sub_programs (duplikat berdasarkan nama_sub_program, case-insensitive)
 * - programs (duplikat berdasarkan nama_program, case-insensitive)
 * - kegiatans (duplikat berdasarkan nama_kegiatan, case-insensitive)
 * - mitras (duplikat berdasarkan nama_mitra, case-insensitive)
 *
 * Logika: Untuk setiap grup duplikat, simpan record dengan ID terkecil,
 * lalu update semua referensi di tabel terkait ke ID tersebut,
 * kemudian hapus record duplikat.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Cleanup duplikat PRODI
        $this->cleanupDuplicates(
            table: 'prodis',
            nameColumn: 'nama_prodi',
            referenceTables: [
                ['table' => 'mahasiswas', 'column' => 'prodi_id'],
            ]
        );

        // 2. Cleanup duplikat SUB PROGRAM
        $this->cleanupDuplicates(
            table: 'sub_programs',
            nameColumn: 'nama_sub_program',
            referenceTables: [
                ['table' => 'data_plps', 'column' => 'sub_program_id'],
            ]
        );

        // 3. Cleanup duplikat PROGRAM
        $this->cleanupDuplicates(
            table: 'programs',
            nameColumn: 'nama_program',
            referenceTables: [
                ['table' => 'data_plps', 'column' => 'program_id'],
                ['table' => 'sub_programs', 'column' => 'program_id'],
            ]
        );

        // 4. Cleanup duplikat KEGIATAN
        $this->cleanupDuplicates(
            table: 'kegiatans',
            nameColumn: 'nama_kegiatan',
            referenceTables: [
                ['table' => 'data_plps', 'column' => 'kegiatan_id'],
            ]
        );

        // 5. Cleanup duplikat MITRA
        $this->cleanupDuplicates(
            table: 'mitras',
            nameColumn: 'nama_mitra',
            referenceTables: [
                ['table' => 'data_plps', 'column' => 'mitra_id'],
            ]
        );
    }

    /**
     * Cari duplikat berdasarkan LOWER(nameColumn), pertahankan ID terkecil,
     * update semua referensi, lalu hapus record duplikat.
     */
    private function cleanupDuplicates(string $table, string $nameColumn, array $referenceTables): void
    {
        // Cari nama-nama yang punya lebih dari 1 record
        $duplicates = DB::table($table)
            ->select(DB::raw("LOWER({$nameColumn}) as lower_name"))
            ->groupBy(DB::raw("LOWER({$nameColumn})"))
            ->havingRaw('COUNT(*) > 1')
            ->pluck('lower_name');

        foreach ($duplicates as $lowerName) {
            // Ambil semua record dengan nama ini
            $records = DB::table($table)
                ->whereRaw("LOWER({$nameColumn}) = ?", [$lowerName])
                ->orderBy('id')
                ->get();

            // ID pertama (terkecil) = yang dipertahankan
            $keepId = $records->first()->id;
            $duplicateIds = $records->pluck('id')->filter(fn($id) => $id !== $keepId)->toArray();

            if (empty($duplicateIds)) continue;

            // Update semua referensi ke ID yang dipertahankan
            foreach ($referenceTables as $ref) {
                DB::table($ref['table'])
                    ->whereIn($ref['column'], $duplicateIds)
                    ->update([$ref['column'] => $keepId]);
            }

            // Hapus record duplikat
            DB::table($table)->whereIn('id', $duplicateIds)->delete();
        }
    }

    public function down(): void
    {
        // Data cleanup tidak bisa di-rollback
    }
};

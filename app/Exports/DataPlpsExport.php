<?php

namespace App\Exports;

use App\Models\DataPlps;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class DataPlpsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

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
            'Kegiatan',
            'Penyelenggara',
            'Mitra',
            'Dosen Pembimbing',
            'Jumlah Konversi SKS',
        ];
    }

    public function map($row): array
    {
        return [
            $row->program->nama_program ?? '-',
            $row->subProgram->nama_sub_program ?? '-',
            $row->mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
            $row->mahasiswa->prodi->nama_prodi ?? '-',
            $row->nim,
            $row->mahasiswa->nama ?? '-',
            $row->tahun_ajaran,
            $row->semester,
            $row->semester_ta,
            $row->kegiatan->nama_kegiatan ?? '-',
            $row->penyelenggara,
            $row->mitra->nama_mitra ?? '-',
            $row->dosen_pembimbing ?? '-',
            $row->sks,
        ];
    }
}

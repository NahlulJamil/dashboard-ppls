<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPlps extends Model
{
    protected $table = 'data_plps';

    protected $fillable = [
        'program_id',
        'sub_program_id',
        'nim',
        'kegiatan_id',
        'mitra_id',
        'sks',
        'semester',
        'tahun_ajaran',
        'semester_ta',
        'penyelenggara',
        'dosen_pembimbing',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function subProgram()
    {
        return $this->belongsTo(SubProgram::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }
}

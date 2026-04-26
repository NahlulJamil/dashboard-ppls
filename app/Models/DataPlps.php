<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPlps extends Model
{
    protected $table = 'data_plps';

    protected $fillable = [
        'nim',
        'kegiatan_id',
        'instansi_id',
        'sks',
        'semester',
        'tahun',
        'tahun_ajaran',
        'penyelenggara',
        'program_owner'
    ];

    public function mahasiswa() {
        return $this->belongsTo(Mahasiswa::class, 'nim');
    }

    public function kegiatan() {
        return $this->belongsTo(Kegiatan::class);
    }

    public function instansi() {
        return $this->belongsTo(Instansi::class);
    }
}

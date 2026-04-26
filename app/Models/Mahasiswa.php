<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Prodi;
use App\Models\DataPlps;

class Mahasiswa extends Model
{
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nim', 'nama', 'prodi_id'];

    public function prodi() {
        return $this->belongsTo(prodi::class);
    }

    public function dataPlps() {
        return $this->hasMany(DataPlps::class, 'nim');
    }
}

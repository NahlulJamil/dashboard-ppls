<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $fillable = ['nama_kegiatan'];

    public function dataPlps() {
        return $this->hasMany(DataPlps::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    protected $fillable = ['nama_mitra'];

    public function dataPlps()
    {
        return $this->hasMany(DataPlps::class);
    }
}

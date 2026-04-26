<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    protected $fillable = ['nama_instansi'];

    public function dataPlps() {
        return $this->hasMany(DataPlps::class);
    }
}

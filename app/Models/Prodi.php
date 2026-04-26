<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    public function fakultas() {
        return $this->belongsTo(Fakultas::class);
    }
}

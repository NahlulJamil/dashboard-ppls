<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = ['nama_program'];

    public function subPrograms()
    {
        return $this->hasMany(SubProgram::class);
    }

    public function dataPlps()
    {
        return $this->hasMany(DataPlps::class);
    }
}

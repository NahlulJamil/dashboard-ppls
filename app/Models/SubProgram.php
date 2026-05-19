<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubProgram extends Model
{
    protected $fillable = ['nama_sub_program', 'program_id'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function dataPlps()
    {
        return $this->hasMany(DataPlps::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    protected $fillable = ['filename', 'admin_id', 'rows_count'];

    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id', 'id_admin');
    }
}

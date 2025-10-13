<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderCoa extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_header',
        'nama_header',
        'level',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(HeaderCOA::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(HeaderCOA::class, 'parent_id');
    }

    public function coas()
    {
        return $this->hasMany(COA::class)->orderBy('kode_akun');
    }
}

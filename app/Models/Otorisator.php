<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otorisator extends Model
{
    use HasFactory;

    protected $table = 'otorisators';

    protected $fillable = [
        'nama_otorisator',
        'jabatan_otorisator',
    ];
}

<?php

namespace App\Models;

use Database\Factories\OtorisatorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @use HasFactory<OtorisatorFactory>
 */
class Otorisator extends Model
{
    use HasFactory;

    protected $table = 'otorisators';

    protected $fillable = [
        'nama_otorisator',
        'jabatan_otorisator',
    ];
}

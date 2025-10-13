<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $table = 'periodes'; // Specify the correct table name

    protected $fillable = [
        'nama_periode',
        'tanggal_awal',
        'tanggal_akhir',
        'is_rekap',
    ];
}

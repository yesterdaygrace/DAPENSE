<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeracaSaldo extends Model
{
    use HasFactory;

    protected $fillable = [
        'coa_id',
        'periode_id',
        'month',
        'debit',
        'kredit',
        'balance',
        'saldo_awal',
    ];

    public function coa()
    {
        return $this->belongsTo(COA::class, 'coa_id', 'kode_akun');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }
}

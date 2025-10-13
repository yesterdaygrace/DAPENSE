<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoAwal extends Model
{
    use HasFactory;

    protected $table = 'saldo_awal';

    protected $fillable = [
        'coa_id',
        'tanggal_saldo',
        'debit',
        'kredit',
        'periode_id',
    ];

    public function coa()
    {
        return $this->belongsTo(COA::class, 'coa_id');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }
}

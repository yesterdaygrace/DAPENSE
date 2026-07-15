<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnaling extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal_jurnal',
        'nomor_bukti',
        'keterangan',
        'kategori_jurnal',
        'debit',
        'kredit',
        'coa_id',
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

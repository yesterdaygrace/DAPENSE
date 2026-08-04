<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function coa(): BelongsTo
    {
        return $this->belongsTo(COA::class, 'coa_id', 'kode_akun');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }
}

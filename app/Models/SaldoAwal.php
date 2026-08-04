<?php

namespace App\Models;

use Database\Factories\SaldoAwalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @use HasFactory<SaldoAwalFactory>
 */
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

    public function coa(): BelongsTo
    {
        return $this->belongsTo(COA::class, 'coa_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }
}

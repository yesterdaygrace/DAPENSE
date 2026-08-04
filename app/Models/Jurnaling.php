<?php

namespace App\Models;

use Database\Factories\JurnalingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @use HasFactory<JurnalingFactory>
 *
 * @property float $debit Debit amount
 * @property float $kredit Kredit amount
 * @property-read float|null $total_debit Sum of debit from aggregate queries
 * @property-read float|null $total_kredit Sum of kredit from aggregate queries
 * @property-read int|null $total_entries Count from aggregate queries
 * @property mixed $trend Computed trend value from dashboard queries
 * @property-read mixed $deb Debit alias from neraca saldo queries
 * @property-read mixed $kred Kredit alias from neraca saldo queries
 * @property-read string $month Month key from DATE_FORMAT queries
 * @property-read mixed $coa The related COA model
 */
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

    protected function casts(): array
    {
        return [
            'debit' => 'decimal:2',
            'kredit' => 'decimal:2',
        ];
    }

    public function coa(): BelongsTo
    {
        return $this->belongsTo(COA::class, 'coa_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }
}

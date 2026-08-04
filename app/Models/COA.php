<?php

namespace App\Models;

use Database\Factories\COAFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @use HasFactory<COAFactory>
 */
class COA extends Model
{
    use HasFactory;

    protected $table = 'coas'; // Ensure this matches your table name

    protected $fillable = [
        'kode_akun', 'nama_akun', 'saldo_normal', 'kategori', 'level', 'header_coa_id',
    ];

    public function headerCoa(): BelongsTo
    {
        return $this->belongsTo(HeaderCOA::class, 'header_coa_id');
    }

    public function jurnalings(): HasMany
    {
        return $this->hasMany(Jurnaling::class, 'coa_id', 'id'); // Adjust foreign key if needed
    }
}

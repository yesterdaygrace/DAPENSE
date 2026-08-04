<?php

namespace App\Models;

use Database\Factories\HeaderCoaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @use HasFactory<HeaderCoaFactory>
 *
 * @property int $id
 * @property string $kode_header
 * @property string $nama_header
 * @property int $level
 * @property int|null $parent_id
 */
class HeaderCOA extends Model
{
    use HasFactory;

    protected $table = 'header_coas';

    protected $fillable = [
        'kode_header',
        'nama_header',
        'level',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(HeaderCOA::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(HeaderCOA::class, 'parent_id');
    }

    public function coas(): HasMany
    {
        return $this->hasMany(COA::class, 'header_coa_id')->orderBy('kode_akun');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COA extends Model
{
    use HasFactory;

    protected $table = 'coas'; // Ensure this matches your table name

    protected $fillable = [
        'kode_akun', 'nama_akun', 'saldo_normal', 'kategori', 'level', 'header_coa_id'
    ];

    public function headerCoa()
    {
        return $this->belongsTo(HeaderCOA::class);
    }

    public function jurnalings()
    {
        return $this->hasMany(Jurnaling::class, 'coa_id', 'id'); // Adjust foreign key if needed
    }
}

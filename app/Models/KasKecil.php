<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKecil extends Model
{
    use HasFactory;

    protected $table = 'kas_kecil';

    protected $fillable = [
        'tanggal',
        'jenis',
        'kategori',
        'keterangan',
        'nominal',
        'nomor_bukti',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getNominalRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->nominal, 0, ',', '.');
    }
}

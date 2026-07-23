<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiAdministrasi extends Model
{
    use HasFactory;

    protected $table = 'transaksi_administrasi';

    protected $fillable = [
        'id_berkas',
        'tanggal_transaksi',
        'jenis_transaksi',
        'arah_transaksi',
        'kategori',
        'nominal',
        'status_transaksi',
        'metode_pembayaran',
        'nomor_referensi',
        'bukti_pembayaran',
        'diperiksa_oleh',
        'tanggal_verifikasi',
        'catatan_verifikasi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'nominal' => 'decimal:2',
    ];

    public function berkas()
    {
        return $this->belongsTo(Berkas::class, 'id_berkas', 'id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diperiksa_oleh', 'id');
    }

    public function jurnal()
    {
        return $this->hasOne(JurnalUmum::class, 'transaksi_id', 'id');
    }

    public function getNominalRupiahAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}

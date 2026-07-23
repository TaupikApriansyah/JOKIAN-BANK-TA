<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    protected $table = 'jurnal_umum';

    protected $fillable = [
        'transaksi_id',
        'user_id',
        'nomor_jurnal',
        'tanggal_jurnal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_jurnal' => 'date',
    ];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiAdministrasi::class, 'transaksi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(DetailJurnal::class, 'jurnal_id');
    }
}

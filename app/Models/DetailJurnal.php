<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailJurnal extends Model
{
    protected $table = 'detail_jurnal';

    protected $fillable = [
        'jurnal_id',
        'akun_id',
        'debit',
        'kredit',
        'keterangan',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    public function jurnal()
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_id');
    }

    public function akun()
    {
        return $this->belongsTo(AkunAkuntansi::class, 'akun_id');
    }
}

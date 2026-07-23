<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkunAkuntansi extends Model
{
    protected $table = 'akun_akuntansi';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'kelompok',
        'saldo_normal',
        'status',
    ];

    public function detailJurnals()
    {
        return $this->hasMany(DetailJurnal::class, 'akun_id');
    }
}

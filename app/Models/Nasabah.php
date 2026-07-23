<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Nasabah extends Model
{
    protected $table = 'nasabah';

    protected $fillable = [
        'nama_nasabah',
        'nik',
        'alamat',
        'no_telepon',
        'created_by',
    ];

    // CS pembuat
    public function cs()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke Berkas
    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'id_nasabah');
    }
}

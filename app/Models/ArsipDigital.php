<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArsipDigital extends Model
{
    use HasFactory;

    protected $table = 'arsip_digital';

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'berkas_id',
        'nama_file',
        'path_file',
        'tanggal_upload',
        'jenis_dokumen',
        'status_arsip',
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
    ];

    /**
     * Relasi ke tabel berkas
     * arsip_digital.berkas_id → berkas.id
     */
    public function berkas()
    {
        return $this->belongsTo(Berkas::class, 'berkas_id', 'id');
    }
}

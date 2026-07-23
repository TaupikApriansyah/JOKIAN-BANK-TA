<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI TABEL
    |--------------------------------------------------------------------------
    */
    protected $table = 'berkas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'id_nasabah',
        'user_id',
        'jenis_layanan',
        'tanggal_masuk',
        'estimasi_selesai',
        'status_berkas',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTING
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'tanggal_masuk'    => 'date',
        'estimasi_selesai' => 'date',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // ======================
    // Berkas -> Nasabah
    // ======================
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'id_nasabah', 'id');
    }

    // ======================
    // Berkas -> User (CS/Admin)
    // ======================
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // ======================
    // Berkas -> Semua Tracking Status
    // ======================
    public function trackings()
    {
        return $this->hasMany(TrackingStatus::class, 'berkas_id', 'id')
                    ->orderBy('tanggal_update', 'desc');
    }

    // ======================
    // Berkas -> Tracking Terakhir (Paling Baru)
    // ======================
    public function latestTracking()
    {
        return $this->hasOne(TrackingStatus::class, 'berkas_id', 'id')
                    ->latestOfMany('tanggal_update');
    }

    // ======================
    // Berkas -> Arsip Digital
    // ======================
    public function arsips()
    {
        return $this->hasMany(ArsipDigital::class, 'berkas_id', 'id');
    }

    // ======================
    // Berkas -> Transaksi Administrasi
    // ======================
    public function transaksis()
    {
        return $this->hasMany(TransaksiAdministrasi::class, 'id_berkas', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR (TAMBAHAN)
    |--------------------------------------------------------------------------
    */

    // ======================
    // Total Nominal Transaksi
    // ======================
    public function getTotalTransaksiAttribute()
    {
        return $this->transaksis()->sum('nominal');
    }

    // ======================
    // Warna Badge Status
    // ======================
    public function getStatusColorAttribute()
    {
        return match ($this->status_berkas) {
            'Diterima' => 'primary',
            'Diproses' => 'warning',
            'Selesai'  => 'success',
            'Ditolak'  => 'danger',
            default    => 'secondary',
        };
    }

    // ======================
    // Status Tracking Terakhir (Optional Helper)
    // ======================
    public function getLatestStatusAttribute()
    {
        return $this->latestTracking?->status ?? '-';
    }
}

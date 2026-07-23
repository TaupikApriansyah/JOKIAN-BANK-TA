<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingStatus extends Model
{
    protected $table = 'tracking_status';
    protected $primaryKey = 'id';   // ✅ FIX: pakai id
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'berkas_id',
        'user_id',
        'status',
        'tanggal_update',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_update' => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    // RELASI
    public function berkas()
    {
        return $this->belongsTo(Berkas::class, 'berkas_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // AUTO UPDATE STATUS BERKAS
    protected static function boot()
    {
        parent::boot();

        static::created(function ($tracking) {
            if ($tracking->berkas) {
                $tracking->berkas->update([
                    'status_berkas' => $tracking->status
                ]);
            }
        });
    }
}

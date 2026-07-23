<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 🔗 Relasi
public function berkas()
{
    return $this->hasMany(Berkas::class, 'user_id', 'id');
}

public function tracking()
{
    return $this->hasMany(TrackingStatus::class, 'user_id', 'id');
}

public function jurnalDiposting()
{
    return $this->hasMany(JurnalUmum::class, 'user_id', 'id');
}
}
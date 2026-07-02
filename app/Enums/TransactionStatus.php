<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Draft = 'draft';
    case MenungguVerifikasi = 'menunggu_verifikasi';
    case Disetujui = 'disetujui';
    case Dikembalikan = 'dikembalikan';
    case Ditolak = 'ditolak';
    case Dibatalkan = 'dibatalkan';
    case Dikoreksi = 'dikoreksi';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::MenungguVerifikasi => 'Menunggu Verifikasi',
            self::Disetujui => 'Disetujui',
            self::Dikembalikan => 'Dikembalikan untuk Perbaikan',
            self::Ditolak => 'Ditolak',
            self::Dibatalkan => 'Dibatalkan',
            self::Dikoreksi => 'Dikoreksi',
        };
    }
}

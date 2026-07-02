<?php

namespace App\Enums;

enum TransactionCorrectionStatus: string
{
    case MenungguVerifikasi = 'menunggu_verifikasi';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::MenungguVerifikasi => 'Menunggu Verifikasi',
            self::Disetujui => 'Disetujui',
            self::Ditolak => 'Ditolak',
        };
    }
}

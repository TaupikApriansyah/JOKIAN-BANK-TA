<?php

namespace App\Enums;

enum CaseStatus: string
{
    case Baru = 'baru';
    case MenungguDokumen = 'menunggu_dokumen';
    case Diproses = 'diproses';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Baru => 'Baru',
            self::MenungguDokumen => 'Menunggu Dokumen',
            self::Diproses => 'Diproses',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
        };
    }
}

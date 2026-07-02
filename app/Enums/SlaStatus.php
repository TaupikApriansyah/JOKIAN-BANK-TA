<?php

namespace App\Enums;

enum SlaStatus: string
{
    case Aman = 'aman';
    case Mendekati = 'mendekati';
    case Terlambat = 'terlambat';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Aman => 'Aman',
            self::Mendekati => 'Mendekati SLA',
            self::Terlambat => 'Terlambat',
            self::Selesai => 'Selesai',
        };
    }
}

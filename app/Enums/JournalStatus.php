<?php

namespace App\Enums;

enum JournalStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Menunggu Posting',
            self::Posted => 'Sudah Diposting',
        };
    }
}

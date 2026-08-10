<?php

namespace App\Enums;

enum NoteType: string
{
    case Defter = 'defter';
    case Not = 'not';
    case Alinti = 'alinti';

    public function label(): string
    {
        return match ($this) {
            self::Defter => 'Defter',
            self::Not => 'Not',
            self::Alinti => 'Alıntı',
        };
    }
}

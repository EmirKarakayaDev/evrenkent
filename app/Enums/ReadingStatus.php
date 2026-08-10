<?php

namespace App\Enums;

enum ReadingStatus: string
{
    case Listede = 'listede';
    case Tamamlandi = 'tamamlandi';

    public function label(): string
    {
        return match ($this) {
            self::Listede => 'Listede',
            self::Tamamlandi => 'Tamamlandı',
        };
    }
}

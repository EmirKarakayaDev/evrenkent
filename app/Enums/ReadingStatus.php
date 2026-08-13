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

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Listede => 'bg-slate-100 text-slate-600 ring-slate-300',
            self::Tamamlandi => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        };
    }
}

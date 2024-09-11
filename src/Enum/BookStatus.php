<?php

namespace App\Enum;

enum BookStatus: string
{
    case Available = 'available';
    case Borrowed = 'borrowwed';
    case Unavailable = 'unavailable';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::Borrowed => 'Emprunté',
            self::Unavailable => 'Indisponible',
        };
    }
}
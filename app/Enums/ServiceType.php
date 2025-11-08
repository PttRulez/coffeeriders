<?php

namespace App\Enums;

enum ServiceType: string
{
    case Cycling  = 'cycling';
    case BikeRent = 'bike_rent';

    public function label(): string
    {
        return match ($this) {
            self::Cycling  => 'Сайклинг',
            self::BikeRent => 'Аренда велосипедов',
        };
    }
}
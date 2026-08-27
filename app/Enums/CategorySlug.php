<?php

namespace App\Enums;

enum CategorySlug: string
{
    case FILMS = 'filmi';
    case SERIALS = 'seriali';
    case MULTS = 'multfilmi ';
    case MULTSERIALS = 'multseriali';


    public function isSerialType(): bool
    {
        return $this === self::SERIALS || $this === self::MULTSERIALS;
    }

}

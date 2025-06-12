<?php

namespace App\Enums;

enum ColorEnum: int
{
    case EMPTY =  0;
    case RED = 1;
    case GREEN = 2;
    case FILLED = 3;

    public function getEmoji(): string
    {
        $emoji = match ($this) {
            self::RED=>'❤️',
            self::GREEN=>'💚'
        };
        return $emoji;
    }

}

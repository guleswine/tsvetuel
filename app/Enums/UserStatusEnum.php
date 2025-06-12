<?php

namespace App\Enums;

enum UserStatusEnum: int
{
    case LOBBY = 1;
    case SEARCH = 2;
    case GAME = 3;
}

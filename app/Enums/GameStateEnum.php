<?php

namespace App\Enums;

enum GameStateEnum: int
{
    case MAKE_MOVE = 0;
    CASE CHOOSE_MY_FIGURE = 1;
    CASE CHOOSE_DESTRUCTION_CELL = 2;
    case CHOOSE_RECOVERY_CELL = 3;
}

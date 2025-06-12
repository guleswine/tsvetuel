<?php

namespace App\Handlers;

use App\Enums\UserStatusEnum;
use App\Models\User;

class MainHandler
{
    public static function processing(User $user,string $message,bool $first_time=false): void
    {
        switch ($user->status){
            case UserStatusEnum::SEARCH:
                SearchHandler::processing($user,$message);
                break;
            case UserStatusEnum::GAME:
                GameHandler::processing($user,$message);
                break;
            default:
                LobbyHandler::processing($user,$message);
                break;
        }
    }
}

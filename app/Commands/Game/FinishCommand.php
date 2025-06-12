<?php

namespace App\Commands\Game;

use App\Commands\BaseCommand;
use App\Models\User;
use App\Services\Game\GameService;

class FinishCommand extends BaseCommand
{

    protected static string $command_name_path = 'commands.finish';



    public function execute(User $user, GameService $game_service, string $message): void
    {
        if($game_service->isHasWinner()){
            $game_service->finish();
        }
    }
}

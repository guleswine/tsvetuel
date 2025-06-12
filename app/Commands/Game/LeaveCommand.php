<?php

namespace App\Commands\Game;

use App\Commands\BaseCommand;
use App\Commands\CommandInterface;
use App\Commands\ComputerCommand;
use App\Commands\FoundEnemyCommand;
use App\Commands\HelpCommand;
use App\Enums\UserStatusEnum;
use App\Models\User;
use App\Responses\MessageSender;
use App\Services\Game\GameService;

class LeaveCommand extends BaseCommand
{

    protected static string $command_name_path = 'commands.cancel';


    public function execute(User $user, GameService $game_service, string $message): void
    {
        $enemy_user = $game_service->getEnemyUser($user);
        $game_service->cancelGame();
        $user->game_id = null;
        $user->status = UserStatusEnum::LOBBY;
        $user->save();
        $enemy_user->game_id = null;
        $enemy_user->status = UserStatusEnum::LOBBY;
        $enemy_user->save();
        $buttons = [[
            FoundEnemyCommand::getCommand(),
            HelpCommand::getCommand()
        ],[
            ComputerCommand::getCommand()
        ]];
        new MessageSender(__('game.you_cancel_game'),$buttons)->send($user);
        new MessageSender(__('game.player_cancel_game',['name'=>$user->name]),$buttons)->send($enemy_user);
    }
}

<?php

namespace App\Commands;

use App\Commands\CommandInterface;
use App\Commands\Search\CancelCommand;
use App\Enums\UserStatusEnum;
use App\Models\User;
use App\Responses\MessageSender;
use App\Services\Game\GameService;

class FoundEnemyCommand extends BaseCommand
{

    protected static string $command_name_path = 'commands.found_enemy';


    public function execute(User $user, string $message): void
    {
        $enemyUser = self::findEnemy();
        if ($enemyUser){
            $game_service = GameService::make($enemyUser,$user);
            $game_service->start($enemyUser,$user);
        }else{
            $user->status = UserStatusEnum::SEARCH;
            $user->save();
            new MessageSender(__('info.search_message'))->setButtons([[
                HelpCommand::getCommand(),
                CancelCommand::getCommand()
            ]])->send($user);
        }
    }

    public static function findEnemy(): User|null
    {
        $enemy = User::where('status',UserStatusEnum::SEARCH)->first();
        return $enemy;
    }
}

<?php

namespace App\Commands\Search;

use App\Commands\BaseCommand;
use App\Commands\CommandInterface;
use App\Commands\FoundEnemyCommand;
use App\Commands\HelpCommand;
use App\Enums\UserStatusEnum;
use App\Models\User;
use App\Responses\MessageSender;

class CancelCommand  extends BaseCommand
{
    protected static string $command_name_path = 'commands.cancel';


    public function execute(User $user, string $message): void
    {
        $user->status = UserStatusEnum::LOBBY;
        $user->save();
        new MessageSender(__('info.cancel_search'))->setButtons([[
            FoundEnemyCommand::getCommand(),
            HelpCommand::getCommand()
        ]])->send($user);
    }
}

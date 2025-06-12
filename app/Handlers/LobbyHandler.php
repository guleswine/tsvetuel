<?php

namespace App\Handlers;

use App\Commands\ComputerCommand;
use App\Commands\FoundEnemyCommand;
use App\Commands\HelpCommand;
use App\Commands\ResultsCommand;
use App\Models\User;
use App\Responses\MessageSender;

class LobbyHandler
{


    public static function processing(User $user,string $message)
    {
        $commands = [
            new HelpCommand(),
            new FoundEnemyCommand(),
            new ComputerCommand(),
            new ResultsCommand()
        ];
        foreach ($commands as $command) {
            if ($command->matches($message)) {
                $command->execute($user, $message);
                return;
            }
        }
            new MessageSender()
                ->addText(__('info.default_message'))
                ->setButtons(LobbyHandler::defaultButtons())
                ->send($user);

    }

    public static function defaultButtons()
    {
        return [
            [
                FoundEnemyCommand::getCommand(),
                ComputerCommand::getCommand()
            ],
            [
                ResultsCommand::getCommand(),
                HelpCommand::getCommand()
            ]
        ];
    }
}

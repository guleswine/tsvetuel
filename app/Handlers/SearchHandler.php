<?php

namespace App\Handlers;

use App\Commands\HelpCommand;
use App\Commands\Search\CancelCommand;
use App\Models\User;
use App\Responses\MessageSender;


class SearchHandler
{
    public static function processing(User $user,string $message)
    {
        $commands = [
            new CancelCommand(),
        ];
        foreach ($commands as $command) {
            if ($command->matches($message)) {
                $command->execute($user, $message);
                return;
            }
        }
        new MessageSender(__('info.search_message'))->setButtons([[
            HelpCommand::getCommand(),
            CancelCommand::getCommand()
        ]])->send($user);

    }


}

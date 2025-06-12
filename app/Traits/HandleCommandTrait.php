<?php

namespace App\Traits;

trait HandleCommandTrait
{


    public static function handleCommand($commands,$message)
    {
        foreach ($commands as $command) {
            if ($command->matches($message)) {
                $command->execute($user,$game_service, $message);
                return;
            }
        }
    }
}

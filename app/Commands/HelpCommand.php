<?php

namespace App\Commands;

use App\Models\User;
use App\Responses\MessageSender;

class HelpCommand extends BaseCommand
{
    protected static string $command_name_path = 'commands.help';


    public function execute(User $user, string $message): void
    {
        new MessageSender(__('info.help_message'))
            ->addAttachments(['type'=>'photo','path'=>'/img/guide.png'])->send($user);
    }
}

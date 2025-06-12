<?php

namespace App\Commands;

use App\Enums\SourceEnum;
use App\Enums\UserStatusEnum;
use App\Models\User;
use App\Services\Game\GameService;

class ComputerCommand extends BaseCommand
{

    protected static string $command_name_path = 'commands.computer';



    public function execute(User $user, string $message): void
    {
        $computer = User::firstOrCreate(
            ['first_name'=>'Компьютер','source_id'=>SourceEnum::COMPUTER],
            ['status'=>UserStatusEnum::GAME,'source_user_id'=>0,'active_at'=>now()]);
        $game_service = GameService::make($user,$computer);
        $game_service->start($user,$computer);
    }
}

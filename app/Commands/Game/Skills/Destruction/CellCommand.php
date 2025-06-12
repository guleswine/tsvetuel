<?php

namespace App\Commands\Game\Skills\Destruction;

use App\Commands\BaseCommand;
use App\Enums\ColorEnum;
use App\Enums\GameStateEnum;
use App\Enums\SkillEnum;
use App\Models\User;
use App\Services\Game\GameService;
use App\Services\NotifyService;

class CellCommand extends \App\Commands\Game\CellCommand
{


    public function execute(User $user, GameService $game_service, string $message): void
    {
        $game_service->setCellColor($message,ColorEnum::FILLED)
            ->increaceUsedSkills()
            ->setState(GameStateEnum::MAKE_MOVE)
            ->saveGame();
        $player = $game_service->getMyPlayer($user);
        $player->skills=SkillEnum::DESTRUCTION->remove($player->skills);
        $player->save();
        NotifyService::sendGameInfo($game_service,$user,
            __('game.you_use_skill',['name'=>SkillEnum::DESTRUCTION->getName()]).PHP_EOL.
            __('game.skills.destruction.header',['cell'=>$message])
        );
        NotifyService::sendGameInfo($game_service,$game_service->getEnemyUser($user),
            __('game.enemy_use_skill',['name'=>SkillEnum::DESTRUCTION->getName()]));
    }
}

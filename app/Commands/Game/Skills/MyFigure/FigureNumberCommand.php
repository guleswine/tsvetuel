<?php

namespace App\Commands\Game\Skills\MyFigure;

use App\Commands\BaseCommand;
use App\Enums\GameStateEnum;
use App\Enums\SkillEnum;
use App\Models\User;
use App\Services\Game\GameService;
use App\Services\NotifyService;

class FigureNumberCommand extends BaseCommand
{
    public function matches(string $message): bool
    {
        if (!ctype_digit($message)) {return false;}
        $cell = (int)$message;
        if(1<=$cell and $cell<=12) {
            return true;
        }else{
            return false;
        }
    }

    public function execute(User $user, GameService $game_service, string $message): void
    {
        $figure = (int)$message+50;
        $player = $game_service->getMyPlayer($user);
        $player->replaceFirstFigure($figure);
        $player->skills=SkillEnum::MY_FIGURE->remove($player->skills);
        $player->save();

        $game_service->increaceUsedSkills()
            ->setState(GameStateEnum::MAKE_MOVE)
            ->saveGame();
        NotifyService::sendGameInfo($game_service,$user,
            __('game.you_use_skill',['name'=>SkillEnum::MY_FIGURE->getName()]));
        NotifyService::sendGameInfo($game_service,$game_service->getEnemyUser($user),
            __('game.enemy_use_skill',['name'=>SkillEnum::MY_FIGURE->getName()]));
    }
}

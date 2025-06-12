<?php

namespace App\Commands\Game\Skills\SkipFigure;

use App\Commands\Game\Skills\SkillCommand;
use App\Enums\SkillEnum;
use App\Models\User;
use App\Services\Game\FieldService;
use App\Services\Game\FigureService;
use App\Services\Game\GameService;
use App\Services\NotifyService;

class SkipFigureCommand extends SkillCommand
{

    protected SkillEnum $skill = SkillEnum::SKIP_FIGURE;


    public function useSkill(User $user, GameService $game_service, string $message): void
    {
        $game_service->increaceUsedSkills()->saveGame();
        $player = $game_service->getMyPlayer($user);
        $player->shiftFigure();
        $player->skills=SkillEnum::SKIP_FIGURE->remove($player->skills);
        $not_filled = FieldService::getNotFilledCellsCount($game_service->getField());
        if (0<$not_filled and $not_filled<=5 ){
            $strongest_chance = 50/$not_filled;
        }else{
            $strongest_chance = 0;
        }
        $player->pushFigure(FigureService::getFigure($player->getFigures(),$strongest_chance));
        $player->save();
        NotifyService::sendGameInfo($game_service,$user,
            __('game.you_use_skill',['name'=>$this->skill->getName()]));
        NotifyService::sendGameInfo($game_service,$game_service->getEnemyUser($user),
            __('game.enemy_use_skill',['name'=>$this->skill->getName()]));
    }
}

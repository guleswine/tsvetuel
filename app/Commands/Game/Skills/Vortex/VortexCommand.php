<?php

namespace App\Commands\Game\Skills\Vortex;

use App\Commands\Game\Skills\SkillCommand;
use App\Enums\SkillEnum;
use App\Models\User;
use App\Services\Game\FieldService;
use App\Services\Game\GameService;
use App\Services\NotifyService;

class VortexCommand extends SkillCommand
{

    protected SkillEnum $skill = SkillEnum::VORTEX;

    public function useSkill(User $user, GameService $game_service, string $message): void
    {
        $game_service->increaceUsedSkills()
            ->setField(FieldService::shuffleField($game_service->getField()))
            ->saveGame();
        $player = $game_service->getMyPlayer($user);
        $player->skills=SkillEnum::VORTEX->remove($player->skills);
        $player->save();
        NotifyService::sendGameInfo($game_service,$user,
            __('game.you_use_skill',['name'=>$this->skill->getName()]));
        NotifyService::sendGameInfo($game_service,$game_service->getEnemyUser($user),
            __('game.enemy_use_skill',['name'=>$this->skill->getName()]));
    }
}

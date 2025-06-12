<?php

namespace App\Commands\Game\Skills\Destruction;

use App\Commands\Game\Skills\SkillCommand;
use App\Enums\GameStateEnum;
use App\Enums\SkillEnum;
use App\Models\User;
use App\Responses\MessageSender;
use App\Services\Game\GameService;

class DesctructionCommand extends SkillCommand
{

    protected SkillEnum $skill = SkillEnum::DESTRUCTION;


    public function useSkill(User $user, GameService $game_service, string $message): void
    {
        $game_service->setState(GameStateEnum::CHOOSE_DESTRUCTION_CELL)->saveGame();
        new MessageSender(__('game.skills.destruction.choose'))->send($user);
    }
}

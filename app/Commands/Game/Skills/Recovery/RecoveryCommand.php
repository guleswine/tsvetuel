<?php

namespace App\Commands\Game\Skills\Recovery;

use App\Commands\Game\Skills\SkillCommand;
use App\Enums\GameStateEnum;
use App\Enums\SkillEnum;
use App\Models\User;
use App\Responses\MessageSender;
use App\Services\Game\GameService;

class RecoveryCommand extends SkillCommand
{
    protected SkillEnum $skill = SkillEnum::RECOVERY;

    public function useSkill(User $user, GameService $game_service, string $message): void
    {
        $game_service->setState(GameStateEnum::CHOOSE_RECOVERY_CELL)->saveGame();
        new MessageSender(__('game.skills.recovery.choose'))->send($user);
    }
}

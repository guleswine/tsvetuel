<?php

namespace App\Commands\Game\Skills;

use App\Commands\BaseCommand;
use App\Enums\GameStateEnum;
use App\Enums\SkillEnum;
use App\Models\User;
use App\Responses\MessageSender;
use App\Services\Game\GameService;

abstract class SkillCommand extends BaseCommand
{
    protected SkillEnum $skill;

    public function matches(string $message): bool
    {
        $clean_message = mb_trim(mb_strtolower($message));
        return mb_strtolower($this->skill->getName()) === $clean_message;
    }

    public function execute(User $user, GameService $game_service, string $message): void
    {
        $player = $game_service->getMyPlayer($user);
        $skills = $player->skills;
        if ($this->skill->isAvailable($skills)){
            if($game_service->isExceededSkillUsage()){
                new MessageSender(__('game.you_cant_use_more_skills'))->send($user);
            }else{
                $this->useSkill($user,$game_service,$message);
            }

        }else{
            new MessageSender(__('game.you_cant_use_skill'))->send($user);
        }
    }

    public function useSkill(User $user, GameService $game_service, string $message): void
    {

    }
}

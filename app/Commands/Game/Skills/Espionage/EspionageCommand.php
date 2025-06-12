<?php

namespace App\Commands\Game\Skills\Espionage;

use App\Commands\Game\Skills\SkillCommand;
use App\Enums\SkillEnum;
use App\Models\User;
use App\Responses\MessageSender;
use App\Services\Game\FigureService;
use App\Services\Game\GameService;

class EspionageCommand extends SkillCommand
{

    protected SkillEnum $skill = SkillEnum::ESPIONAGE;



    public function useSkill(User $user, GameService $game_service, string $message): void
    {
        $game_service->increaceUsedSkills()->saveGame();
        $player = $game_service->getMyPlayer($user);
        $enemy_player = $game_service->getEnemyPlayer($user);
        $player->skills=SkillEnum::ESPIONAGE->remove($player->skills);
        $player->save();
        new MessageSender()
            ->addText(__('game.you_use_skill',['name'=>$this->skill->getName()]))
            ->addText(__('game.skills.espionage.header'),PHP_EOL.PHP_EOL)
            ->addText(FigureService::printFigures($enemy_player->getFigures(3)),PHP_EOL)
            ->send($user);

        new MessageSender(__('game.enemy_use_skill',['name'=>$this->skill->getName()]))
            ->send($game_service->getEnemyUser($user));
    }

}

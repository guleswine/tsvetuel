<?php

namespace App\Commands\Game\Skills\MyFigure;

use App\Commands\Game\Skills\SkillCommand;
use App\Enums\GameStateEnum;
use App\Enums\SkillEnum;
use App\Models\User;
use App\Responses\MessageSender;
use App\Services\Game\FigureService;
use App\Services\Game\GameService;

class MyFigureCommand extends SkillCommand
{

    protected SkillEnum $skill = SkillEnum::MY_FIGURE;
    public function useSkill(User $user, GameService $game_service, string $message): void
    {
        $game_service->setState(GameStateEnum::CHOOSE_MY_FIGURE)->saveGame();
        $figures = FigureService::getSmallFigures();

        new MessageSender(__('game.skills.choice.choose'))
            ->setButtons([['1','2','3'],['4','5','6']])->inline()
            ->addText(FigureService::printFigures(array_slice($figures,0,3)),PHP_EOL)
            ->addText(FigureService::printFigures(array_slice($figures,3,3)),PHP_EOL.PHP_EOL)
            ->send($user);
        new MessageSender(__('game.skills.choice.choose'))
            ->setButtons([['7','8','9'],['10','11','12']])->inline()
            ->addText(FigureService::printFigures(array_slice($figures,6,3)),PHP_EOL)
            ->addText(FigureService::printFigures(array_slice($figures,9,3)),PHP_EOL.PHP_EOL)
            ->send($user);
    }
}

<?php

namespace App\Services;

use App\Commands\Game\FinishCommand;
use App\Commands\Game\LeaveCommand;
use App\Commands\HelpCommand;
use App\Models\User;
use App\Responses\MessageSender;
use App\Services\Game\FieldService;
use App\Services\Game\FigureService;
use App\Services\Game\GameService;
use App\Services\Game\SkillService;

class NotifyService
{


    public static function sendGameInfo(GameService $game_service, User $user,string $prefix_message = '')
    {
        $MS = new MessageSender();
        if ($prefix_message){
            $MS->addPrefixText($prefix_message,PHP_EOL.PHP_EOL);
        }
        if ($game_service->isHasWinner()) {
            $MS->addText(__('game.can_early_finish').PHP_EOL);
        }
        $player = $game_service->getMyPlayer($user);
        $enemy_player = $game_service->getEnemyPlayer($user);

        $MS->addText(__('game.your_score', ['emoji' => $player->color->getEmoji(), 'score' => $player->score]));
        if($player->combo>1) $MS->addText(__('game.combo',['combo' => $player->combo]));
        $MS->addText(__('game.enemy_score', ['emoji' => $enemy_player->color->getEmoji(), 'score' => $enemy_player->score]),PHP_EOL);
        if($enemy_player->combo>1) __('game.combo', ['combo' => $enemy_player->combo]);

        if($game_service->isMyTurn($user)){
            $MS->addText(__('game.your_turn'),PHP_EOL.PHP_EOL);
            $MS->addText(__('game.your_figures'),PHP_EOL);
            $MS->addText(FigureService::printFigures($player->getFigures(2)),PHP_EOL);
        }else{
            $MS->addText(__('game.enemy_turn'),PHP_EOL.PHP_EOL);
            $MS->addText(FigureService::printFigure($enemy_player->getCurrentFigure()),PHP_EOL);
        }
        $MS->setButtons(FieldService::convertToButtons($game_service->getField()));
        $MS->mergeButtons(SkillService::getSkillsButtons($player->skills));
        $MS->addButtons([
            HelpCommand::getCommand(),
            LeaveCommand::getCommand()
        ]);
        if ($game_service->isHasWinner()) {
            $MS->addButtons([FinishCommand::getCommand()]);
        }
        $MS->send($user);

    }
}

<?php

namespace App\Handlers;

use App\Commands\Game\CellCommand;
use App\Commands\Game\FinishCommand;
use App\Commands\Game\LeaveCommand;
use App\Commands\Game\Skills\Destruction\DesctructionCommand;
use App\Commands\Game\Skills\Espionage\EspionageCommand;
use App\Commands\Game\Skills\MyFigure\FigureNumberCommand;
use App\Commands\Game\Skills\MyFigure\MyFigureCommand;
use App\Commands\Game\Skills\Recovery\RecoveryCommand;
use App\Commands\Game\Skills\SkipFigure\SkipFigureCommand;
use App\Commands\Game\Skills\Vortex\VortexCommand;
use App\Enums\GameStateEnum;
use App\Models\User;
use App\Responses\MessageSender;
use App\Services\Game\GameService;
use App\Services\NotifyService;

class GameHandler
{

    public static function processing(User $user,string $message): void
    {
        $game_service = new GameService($user->game_id);
        $commands = [
            new LeaveCommand(),
            new FinishCommand(),
        ];
        foreach ($commands as $command) {
            if ($command->matches($message)) {
                $command->execute($user, $game_service,$message);
                return;
            }
        }


        if (!$game_service->isMyTurn($user)){
            new MessageSender(__('game.wait'))->send($user);
            return;
        }
        if ($game_service->getState()!=GameStateEnum::MAKE_MOVE){
            self::stateProcessing($user,$game_service,$message);
            return;
        }
        $commands = [
            new CellCommand(),
            new DesctructionCommand(),
            new EspionageCommand(),
            new MyFigureCommand(),
            new RecoveryCommand(),
            new SkipFigureCommand(),
            new VortexCommand()
        ];
        foreach ($commands as $command) {
            if ($command->matches($message)) {
                $command->execute($user,$game_service, $message);
                return;
            }
        }
        NotifyService::sendGameInfo($game_service,$user);
    }

    public static function stateProcessing(User $user,GameService $game_service,$message): void
    {
        switch ($game_service->getState()){
            case GameStateEnum::CHOOSE_MY_FIGURE:
                $command = new FigureNumberCommand();
                if($command->matches($message)){
                    $command->execute($user,$game_service,$message);
                }
                break;
            case GameStateEnum::CHOOSE_DESTRUCTION_CELL:
                $command = new \App\Commands\Game\Skills\Destruction\CellCommand();
                if($command->matches($message)){
                    $command->execute($user,$game_service,$message);
                }
                break;
            case GameStateEnum::CHOOSE_RECOVERY_CELL:
                $command = new \App\Commands\Game\Skills\Recovery\CellCommand();
                if($command->matches($message)){
                    $command->execute($user,$game_service,$message);
                }
                break;
        }
    }
}

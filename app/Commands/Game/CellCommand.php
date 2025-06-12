<?php

namespace App\Commands\Game;

use App\Commands\BaseCommand;
use App\Commands\CommandInterface;
use App\Enums\SourceEnum;
use App\Models\User;
use App\Services\Game\FieldService;
use App\Services\Game\GameService;
use App\Services\NotifyService;

class CellCommand extends BaseCommand
{



    public function matches(string $message): bool
    {
        if (!ctype_digit($message)) {return false;}
        $cell = (int)$message;
        if(1<=$cell and $cell<=FieldService::totalCells()) {
            return true;
        }else{
            return false;
        }
    }

    public function execute(User $user, GameService $game_service, string $message): void
    {
        $points = $game_service->makeMove($user, $message);
        $game_service->changeUserMove($user)->saveGame();
        if ($game_service->isFieldFilled()) {
            $game_service->finish();
        } else {
            $enemy_user = $game_service->getEnemyUser($user);
            $first_prefix =__('game.your_move',['cell' => $message]);
            $second_prefix = __('game.enemy_move',['cell'=>$message]);
            if ($points<>0){
                $first_prefix .= trans_choice('game.add_you_earn_score',abs($points),['points'=>$points]);
                $second_prefix .= trans_choice('game.add_enemy_earn_score',abs($points),['points'=>$points]);
            }
            NotifyService::sendGameInfo($game_service,$user,$first_prefix);
            NotifyService::sendGameInfo($game_service,$enemy_user,$second_prefix);
            if ($enemy_user->source_id ==SourceEnum::COMPUTER){
                sleep(random_int(3,6));
                $game_service->makeComputerStep($enemy_user);
            }
        }
    }
}

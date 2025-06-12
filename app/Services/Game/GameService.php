<?php

namespace App\Services\Game;

use App\Commands\HelpCommand;
use App\Commands\Search\CancelCommand;
use App\Enums\ColorEnum;
use App\Enums\GameStateEnum;
use App\Enums\ResultEnum;
use App\Enums\SkillEnum;
use App\Enums\UserStatusEnum;
use App\Models\Game;
use App\Models\Player;
use App\Models\Result;
use App\Models\User;
use App\Responses\MessageSender;
use App\Services\NotifyService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GameService
{
    private Game $game;
    private Collection $players;


    public function __construct(int|Game $game)
    {
        if(is_int($game)) {
            $this->game = Game::find($game);
        }else{
            $this->game = $game;
        }
        $this->players = new Collection();

    }


    private function loadPlayers()
    {
        $this->players = Player::where('game_id', $this->game->id)->get();
    }

    public function cancelGame(): void
    {
        Player::where('game_id', $this->game->id)->delete();
        $this->game->delete();

    }


    public function increaceUsedSkills(): GameService
    {
        $this->game->used_skills++;
        return $this;
    }

    public function saveGame(): void
    {
        $this->game->save();
    }

    public function changeUserMove(User $user): GameService
    {
        $enemy_user = $this->getEnemyUser($user);
        if ($enemy_user){
            $this->game->move_user_id = $enemy_user->id;
        }
        $this->game->state = GameStateEnum::MAKE_MOVE;
        $this->game->used_skills = 0;
        return $this;
    }

    public function isFieldFilled(): int
    {
        return (FieldService::getNotFilledCellsCount($this->getField()) ==0);
    }

    public function isHasWinner(): bool
    {
        $players = $this->getPlayers();
        $first_player = $players->get(0);
        $second_player = $players->get(1);
        $combo = max($first_player->combo, $second_player->combo);
        $score_difference = abs($first_player->score - $second_player->score);
        $cells = FieldService::getNotFilledCells($this->getField());
        $potential_points = 0;
        foreach ($cells as $cell=>$color) {
            $potential_points +=$cell+$combo;
            $combo++;
        }
        return $score_difference>$potential_points;
    }

    /**
     * @return Collection<int,Player>
     */
    public function getPlayers(): Collection
    {
        if ($this->players->isEmpty()){
            $this->loadPlayers();
        }
        return $this->players;
    }

    public function finish()
    {
        $first_player = $this->getPlayers()->get(0);
        $second_player = $this->getPlayers()->get(1);
        //$second_user = $second_player->user;
        User::where('game_id', $this->game->id)->update(['game_id'=>null,'status'=>UserStatusEnum::LOBBY]);
        Player::where('game_id', $this->game->id)->delete();
        $this->game->delete();
        $this->game->field = [];
        $first_user = $first_player->user;
        $second_user = $second_player->user;
        if($first_player->score == $second_player->score){
            $this->saveResult($first_player->user_id,$first_player->score,$second_player->user_id,ResultEnum::DRAW);
            $this->saveResult($second_player->user_id,$second_player->score,$first_player->user_id,ResultEnum::DRAW);
            new MessageSender(__('game.you_draw'))
                ->addText(__('game.your_score',['emoji' => $first_player->color->getEmoji(), 'score' => $first_player->score]),PHP_EOL)
                ->send($first_user);
            new MessageSender(__('game.you_draw'))
                ->addText(__('game.your_score',['emoji' => $second_player->color->getEmoji(), 'score' => $second_player->score]),PHP_EOL)
                ->send($second_user);

        }else{
            [$winner_player, $loser_player, $winner_user, $loser_user] =
                    $first_player->score > $second_player->score
                    ? [$first_player, $second_player, $first_user, $second_user]
                    : [$second_player, $first_player, $second_user, $first_user];
            $this->saveResult($winner_player->user_id,$winner_player->score,$loser_player->user_id,ResultEnum::WIN);
            $this->saveResult($loser_player->user_id,$loser_player->score,$winner_player->user_id,ResultEnum::LOSE);

            $winner_user->level++;
            $winner_user->save();

            $MS = new MessageSender(__('game.you_win'));
            if ($winner_user->level <=7){
                $MS->addText(__('game.new_skill'),PHP_EOL);
            }
            $MS->addText(__('game.your_score',['emoji' => $winner_player->color->getEmoji(), 'score' => $winner_player->score]),PHP_EOL);
            $MS->send($winner_user);

            new MessageSender(__('game.you_lose'))
                ->addText(__('game.your_score',['emoji' => $loser_player->color->getEmoji(), 'score' => $loser_player->score]),PHP_EOL)
                ->send($loser_user);
        }

    }

    public function saveResult(int $user_id,int $score,int $enemy_user_id,ResultEnum $result)
    {
        Result::create([
            'user_id' => $user_id,
            'score' => $score,
            'enemy_user_id'=>$enemy_user_id,
            'result' => $result,
            'version'=>config('game.version')
        ]);
    }

    public function getState(): GameStateEnum
    {
        return $this->game->state;
    }



    public function setState(GameStateEnum $state): GameService
    {
        $this->game->state = $state;
        return $this;
    }

    public function isExceededSkillUsage(): bool
    {
        if ($this->game->used_skills>=2){
            return true;
        }else{
            return false;
        }
    }

    public function getCurrentUser(): User
    {
        return User::find($this->game->move_user_id);
    }

    public function makeComputerStep(User $user)
    {
        $player = $this->getMyPlayer($user);
        $enemy = $this->getEnemyPlayer($user);
        $figure = $player->getCurrentFigure();
        $field = $this->getField();
        $not_filled = FieldService::getNotFilledCellsCount($field);
        $best_cell = '1';
        $best_score = 0;
        foreach (FieldService::iterateCells($field) as $cell=>$color){
            $score = $this->calcBenefitStep($player,$enemy,$cell,$figure,$not_filled);
            if ($score > $best_score) {
                $best_cell = $cell;
                $best_score = $score;
            }
        }

        $points = $this->makeMove($user,$best_cell);
        $this->changeUserMove($user)->saveGame();
        if ($this->isFieldFilled()) {
            $this->finish();
        }else{
            $first_prefix =__('game.your_move',['cell' => $best_cell]);
            $second_prefix = __('game.enemy_move',['cell'=>$best_cell]);
            if ($points<>0){
                $first_prefix .= trans_choice('game.add_you_earn_score',abs($points),['points'=>$points]);
                $second_prefix .= trans_choice('game.add_enemy_earn_score',abs($points),['points'=>$points]);
            }
            NotifyService::sendGameInfo($this,$user,$first_prefix);
            NotifyService::sendGameInfo($this,$this->getEnemyUser($user),$second_prefix);
        }

    }

    public static function make(User $first_user, User $second_user)
    {
        $game = Game::create([
            'move_user_id' => $first_user->id,
            'field'=>FieldService::makeField(),
            'version'=>config('game.version'),
            'state'=>GameStateEnum::MAKE_MOVE,
            'used_skills'=>0,
        ]);
        $game_service = new GameService($game);
        DB::transaction(function () use ($game, $first_user, $second_user) {
            $first_user->game_id = $game->id;
            $first_user->status = UserStatusEnum::GAME;
            $first_user->save();
            $second_user->game_id = $game->id;
            $second_user->status = UserStatusEnum::GAME;
            $second_user->save();
        });
        $first_player = Player::updateOrCreate([
            'user_id' => $first_user->id,
            'game_id'=>$game->id
        ],[
            'color'=>ColorEnum::RED,
            'combo'=>1,
            'skills'=>SkillEnum::getSkills($first_user->level),
            'figures'=>FigureService::getFigures(3,true),

        ]);
        $second_player = Player::updateOrCreate([
            'user_id' => $second_user->id,
            'game_id'=>$game->id
        ],[
            'color'=>ColorEnum::GREEN,
            'combo'=>1,
            'skills'=>SkillEnum::getSkills($second_user->level),
            'figures'=>FigureService::getFigures(3),

        ]);
        $game_service->players = Collection::make([$first_player,$second_player]);
        return $game_service;
    }


    public function start($first_user,$second_user)
    {
        $first_player = $this->getMyPlayer($first_user);
        $second_player = $this->getMyPlayer($second_user);
        $figure = $first_player->getCurrentFigure();

        new MessageSender(__('game.player_join',['name'=>$second_user->name]))
            ->addText(__('game.your_red'),PHP_EOL)
            ->addText(__('game.your_turn'),PHP_EOL)
            ->addText(__('game.your_figures'),PHP_EOL)
            ->addText(FigureService::printFigures($first_player->getFigures(2)),PHP_EOL)
            ->setButtons(FieldService::convertToButtons($this->getField()))
            ->mergeButtons(SkillService::getSkillsButtons($first_player->skills))
            ->addButtons([
                HelpCommand::getCommand(),
                CancelCommand::getCommand()
            ])->send($first_user);

        new MessageSender(__('game.player_found',['name'=>$first_user->name]))
            ->addText(__('game.your_green'),PHP_EOL)
            ->addText(__('game.enemy_turn'),PHP_EOL)
            ->addText(FigureService::printFigure($figure),PHP_EOL)
            ->setButtons(FieldService::convertToButtons($this->getField()))
            ->mergeButtons(SkillService::getSkillsButtons($second_player->skills))
            ->addButtons([
                HelpCommand::getCommand(),
                CancelCommand::getCommand()
            ])->send($second_user);


        return $this;
    }

    public function getField(): array
    {
        return $this->game->field;
    }

    public function setField($field): GameService
    {
        $this->game->field = $field;
        return $this;
    }

    public function getEnemyPlayer(User $user): Player|null
    {
        return $this->getPlayers()->where('user_id','<>',$user->id)->first();
    }

    public function getEnemyUser(User $user)
    {
        $player = $this->getEnemyPlayer($user);
        return User::find($player->user_id);
    }

    public function calcEarnedScores(array $changed_cells,Player $player): int
    {
        $score = 0;
        foreach ($changed_cells as $cell=>$color) {
            if ($color==ColorEnum::FILLED){
                $score+=(int)$cell;
                if ($player->combo>1){
                    $score += $player->combo;
                }
            }elseif ($color == ColorEnum::EMPTY){
                $score-=$player->combo;
            }
        }
        /*
        if ($player->combo>1){
            $score *= $player->combo;
        }
        */
        return $score;
    }

    public function makeMove(User $user,$cell): int
    {

        $player = $this->getMyPlayer($user);
        $figure = $player->shiftFigure();

        $changed_cells = $this->updateField($player->color,$cell,$figure);
        $points_received = $this->calcEarnedScores($changed_cells,$player);

        if (in_array(ColorEnum::FILLED,$changed_cells)){
            $player->combo++;
        }else{
            $player->combo=1;
        }
        $not_filled = FieldService::getNotFilledCellsCount($this->getField());
        if (0<$not_filled and $not_filled<=5 ){
            $strongest_chance = 50/$not_filled;
        }else{
            $strongest_chance = 0;
        }
        $player->pushFigure(FigureService::getFigure($player->getFigures(),$strongest_chance));
        $player->score += $points_received;
        $player->score = max($player->score,0);
        $player->save();
        return $points_received;
    }

    public function setCellColor($cell,ColorEnum $color): GameService
    {
        $cell_position = $this->getCellPosition($cell);
        $field = $this->game->field;
        $field[$cell_position['row']][$cell_position['col']] = [$cell=>$color->value];
        $this->game->field = $field;
        return $this;
    }

    public function calcBenefitStep(Player $player,Player $enemy,$cell,$figure,int $not_filled)
    {
        $score = 0;
        $figure_positions = FigureService::getFigurePosition($figure);
        $cell_position = $this->getCellPosition($cell);
        $field = $this->getField();
        foreach ($figure_positions as $position) {
            $field_cell_row = $cell_position['row'] + $position['str'];
            $field_cell_col = $cell_position['col'] + $position['clm'];
            if (isset($field[$field_cell_row][$field_cell_col])){
                $old_color = ColorEnum::from(current($field[$field_cell_row][$field_cell_col]));
                $new_color = FieldService::mixColor($old_color,$player->color);
                if ($figure==0){
                    $new_color = FieldService::mixColor($new_color,$player->color);
                }
                $current_cell = key($field[$field_cell_row][$field_cell_col]);
                if ($old_color != $new_color and $new_color==ColorEnum::FILLED){
                    $score+=(int)$current_cell+($player->combo>1?$player->combo:0);
                }elseif ($old_color != $new_color and $new_color==$player->color){
                    $score+=(int)$current_cell;
                }elseif ($old_color != $new_color and $new_color==ColorEnum::EMPTY){
                    if ($not_filled<intdiv(FieldService::totalCells(),2)){
                        $score-=$player->combo;
                    }else{
                        $score+=(int)floor($current_cell)-$player->combo;
                    }
                    //if($player->score<$enemy->score)

                }
                $field[$field_cell_row][$field_cell_col] = [$current_cell=>$new_color->value];
            }
        }
        return $score;
    }

    public function updateField($color,$cell,$figure): array
    {
        $changed_cells = [];
        $score = 0;
        $figure_positions = FigureService::getFigurePosition($figure);
        $cell_position = $this->getCellPosition($cell);
        $field = $this->game->field;
        foreach ($figure_positions as $position) {
            $field_cell_row = $cell_position['row'] + $position['str'];
            $field_cell_col = $cell_position['col'] + $position['clm'];
            if (isset($field[$field_cell_row][$field_cell_col])){
                $old_color = ColorEnum::from(current($field[$field_cell_row][$field_cell_col]));
                $new_color = FieldService::mixColor($old_color,$color);
                if ($figure==0){
                    $new_color = FieldService::mixColor($new_color,$color);
                }
                $current_cell = key($field[$field_cell_row][$field_cell_col]);
                if ($old_color != $new_color and $new_color==ColorEnum::FILLED){
                    $score+=(int)$current_cell;
                }elseif ($old_color != $new_color and $new_color==ColorEnum::EMPTY){
                    $score-=1;
                }
                if ($old_color != $new_color){
                    $changed_cells[$current_cell]=$new_color;
                }

                $field[$field_cell_row][$field_cell_col] = [$current_cell=>$new_color->value];
            }
        }
        $this->game->field = $field;
        return $changed_cells;
    }

    public function getCellPosition($cell)
    {
        foreach ($this->game->field as $r => $row) {
            foreach ($row as $c => $col) {
                if (key($col) == $cell) {
                    return ['row' => $r, 'col' => $c];
                }
            };
        }
        return false;
    }

    public function getMyPlayer(User $user): Player|null
    {
        return $this->getPlayers()->where('user_id',$user->id)->first();
    }
    public function isMyTurn($user): bool
    {
        return $user->id == $this->game->move_user_id;
    }


}

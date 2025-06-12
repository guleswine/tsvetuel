<?php

namespace App\Models;

use App\Enums\ColorEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Player
 *
 * @property array $field
 * @property int user_id
 * @property int game_id
 * @property int score
 * @property int combo
 * @property ColorEnum color
 * @property int skills
 */
class Player extends Model
{
    protected $fillable = ['user_id','game_id','figures','score','skills','color','combo'];


    public function getCurrentFigure(): int
    {
        $figure =(int)explode(',',$this->figures)[0];
        return  $figure;
    }

    public function getFigures(int|null $count = null): array
    {
        $array = explode(',',$this->figures);
        if ($count == null) {
            return $array;
        }else{
            return array_slice($array,0,$count);
        }
    }
    public function getNextFigure(): int
    {
        $figure =(int)explode(',',$this->figures)[1];
        return  $figure;
    }

    public function shiftFigure(): int
    {
        $array = explode(',',$this->figures);
        $figure =(int)array_shift($array);
        $this->figures = implode(',',$array);
        return  $figure;
    }

    public function pushFigure($figure): bool
    {
        $array = explode(',',$this->figures);
        array_push($array,$figure);
        $this->figures = implode(',',$array);
        return true;
    }

    public function replaceFirstFigure(int $figure): bool
    {
        $array = explode(',',$this->figures);
        $array[0] = $figure;
        $this->figures = implode(',',$array);
        return true;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    protected function casts(): array
    {
        return [
            'color' => ColorEnum::class,
        ];
    }
}

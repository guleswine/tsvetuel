<?php

namespace App\Models;

use App\Enums\GameStateEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * Game
 *
 * @property array $field
 * @property int move_user_id
 * @property GameStateEnum state
 * @property int used_skills
 * @property float version
 */

class Game extends Model
{
    protected $fillable = ['field','move_user_id','state','used_skills','version'];



    protected function casts(): array
    {
        return [
            'field' => 'array',
            'state'=>GameStateEnum::class
        ];
    }
}

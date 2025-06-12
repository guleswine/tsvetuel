<?php

namespace App\Models;

use App\Enums\ResultEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * Result
 *
 * @property int user_id
 * @property int enemy_user_id
 * @property int score
 * @property ResultEnum result
 * @property float version
 */

class Result extends Model
{
    protected $fillable = ['user_id','enemy_user_id','result','score','version'];


    protected function casts(): array
    {
        return [
            'result' => ResultEnum::class,
        ];
    }
}

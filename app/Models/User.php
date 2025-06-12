<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\SourceEnum;
use App\Enums\UserStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User
 *
 * @property int id
 * @property string name
 * @property string last_name
 * @property string first_name
 * @property SourceEnum source_id
 * @property int source_user_id
 * @property int game_id
 * @property int level
 * @property UserStatusEnum status
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const UPDATED_AT = null;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'last_name',
        'first_name',
        'source_id',
        'source_user_id',
        'active_at',
        'game_id',
        'status'
    ];

    public function getNameAttribute(): string
    {
        $name = $this->first_name.' '.$this->last_name;
        return trim($name);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function player(): HasOne
    {
        return $this->hasOne(Player::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => UserStatusEnum::class,
            'source_id'=>SourceEnum::class
        ];
    }
}

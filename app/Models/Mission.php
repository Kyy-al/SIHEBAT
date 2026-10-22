<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    protected $fillable = [
        'title',
        'description',
        'habit_slug',
        'target_count',
        'xp_reward',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'target_count' => 'integer',
        'xp_reward' => 'integer',
        'is_active' => 'boolean',
    ];

    public function userMissions(): HasMany
    {
        return $this->hasMany(UserMission::class);
    }
}

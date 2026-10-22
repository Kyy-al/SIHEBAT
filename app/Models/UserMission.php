<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMission extends Model
{
    protected $fillable = [
        'user_id',
        'mission_id',
        'week_start',
        'current_count',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'current_count' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function setWeekStartAttribute($value): void
    {
        $this->attributes['week_start'] = Carbon::parse($value)->format('Y-m-d');
    }

    public function getWeekStartAttribute($value): string
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function getProgressPercentAttribute(): int
    {
        if (! $this->mission || $this->mission->target_count === 0) {
            return 0;
        }

        return (int) min(100, round(($this->current_count / $this->mission->target_count) * 100));
    }
}

<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'habits_completed',
        'is_perfect_day',
        'is_draft',
        'xp_earned',
        'mood',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'habits_completed' => 'integer',
            'is_perfect_day' => 'boolean',
            'is_draft' => 'boolean',
            'xp_earned' => 'integer',
        ];
    }

    public function setDateAttribute($value): void
    {
        $this->attributes['date'] = $value instanceof CarbonInterface
            ? $value->format('Y-m-d')
            : (is_string($value) ? substr($value, 0, 10) : $value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * Get the completion percentage.
     */
    public function getCompletionPercentAttribute(): int
    {
        return (int) round(($this->habits_completed / 7) * 100);
    }

    /**
     * Get mood emoji.
     */
    public function getMoodEmojiAttribute(): string
    {
        return match ($this->mood) {
            'sangat_senang' => '😄',
            'senang' => '😊',
            'biasa' => '😐',
            'sedih' => '😢',
            'sangat_sedih' => '😭',
            default => '😐',
        };
    }
}

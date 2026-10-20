<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'xp_reward',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'xp_reward' => 'integer',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * Get the soft background color class for this habit.
     */
    public function getSoftColorAttribute(): string
    {
        return "bg-habit-{$this->color}-soft";
    }

    /**
     * Get hex color for this habit.
     */
    public function getColorHexAttribute(): string
    {
        return match ($this->color) {
            'bangun'   => '#FFB020',
            'ibadah'   => '#A78BFA',
            'olahraga' => '#FF7A59',
            'makan'    => '#4ADE80',
            'belajar'  => '#22C7D6',
            'sosial'   => '#FF8FB1',
            'tidur'    => '#6366F1',
            default    => '#38BDF8',
        };
    }
}

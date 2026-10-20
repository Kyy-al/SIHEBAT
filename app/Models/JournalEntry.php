<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'journal_id',
        'habit_id',
        'completed',
        'value',
        'xp_earned',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'value' => 'array',
            'xp_earned' => 'integer',
        ];
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}

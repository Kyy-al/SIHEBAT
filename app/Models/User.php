<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nis',
        'nip',
        'email',
        'role',
        'password',
        'avatar',
        'must_change_password',
        'xp',
        'level',
        'current_streak',
        'longest_streak',
        'last_journal_date',
        'streak_freezes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'last_journal_date' => 'date',
            'xp' => 'integer',
            'level' => 'integer',
            'current_streak' => 'integer',
            'longest_streak' => 'integer',
            'streak_freezes' => 'integer',
        ];
    }

    // ----- Role helpers -----

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    // ----- Relationships -----

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'classroom_student')
            ->withTimestamps();
    }

    public function homeroomClassrooms(): HasMany
    {
        return $this->hasMany(Classroom::class, 'homeroom_teacher_id');
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'badge_user')
            ->withPivot('earned_at');
    }

    // ----- Gamification helpers -----

    public function getLevelNameAttribute(): string
    {
        return match (true) {
            $this->level >= 20 => 'Legenda',
            $this->level >= 15 => 'Luar Biasa',
            $this->level >= 10 => 'Hebat',
            $this->level >= 5  => 'Berkembang',
            default             => 'Pemula',
        };
    }

    public function getXpToNextLevelAttribute(): int
    {
        return $this->level * 100; // 100 XP per level
    }

    public function getXpProgressAttribute(): int
    {
        $xpForCurrentLevel = ($this->level - 1) * 100;
        $xpInLevel = $this->xp - $xpForCurrentLevel;
        return (int) min(100, ($xpInLevel / max(1, $this->xp_to_next_level)) * 100);
    }

    /**
     * Get current classroom for the active academic year.
     */
    public function currentClassroom()
    {
        return $this->classrooms()
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->first();
    }
}

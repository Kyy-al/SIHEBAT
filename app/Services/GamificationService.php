<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Mission;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserMission;
use App\Models\XpLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /**
     * Evaluate completion of a single habit based on PRD 5.2.1 rules.
     */
    public function evaluateHabit(Habit $habit, array $inputs): array
    {
        $completed = false;
        $value = [];

        switch ($habit->slug) {
            case 'bangun-pagi':
                $time = $inputs['time'] ?? null;
                $isLate = ! empty($inputs['is_late']);
                $threshold = $habit->target_value ?: '05:00';
                if ($time || $isLate) {
                    $value = [
                        'time' => $time ?? ($isLate ? '06:30' : null),
                        'is_late' => $isLate || ($time && $time > $threshold),
                    ];
                    // Completed only if not late and time <= threshold
                    $completed = ! $value['is_late'] && ($value['time'] !== null && $value['time'] <= $threshold);
                }
                break;

            case 'beribadah':
                $done = ! empty($inputs['done']);
                $types = (array) ($inputs['types'] ?? []);
                $faith = $inputs['faith'] ?? null;
                $custom = $inputs['custom'] ?? null;
                if ($done || count($types) > 0 || ! empty(trim($custom ?? ''))) {
                    $completed = true;
                    $value = [
                        'done' => true,
                        'faith' => $faith,
                        'types' => $types,
                        'custom' => $custom,
                    ];
                }
                break;

            case 'berolahraga':
                $type = $inputs['type'] ?? null;
                $duration = (int) ($inputs['duration'] ?? 0);
                $minDuration = (int) ($habit->target_value ?: 30);
                if ($type && $duration > 0) {
                    $completed = ($duration >= $minDuration);
                    $value = ['type' => $type, 'duration' => $duration];
                }
                break;

            case 'makan-sehat':
                $items = (array) ($inputs['items'] ?? []);
                $minItems = (int) ($habit->target_value ?: 3);
                $completed = (count($items) >= $minItems);
                $value = ['items' => $items];
                break;

            case 'gemar-belajar':
                $duration = (int) ($inputs['duration'] ?? 0);
                $topic = $inputs['topic'] ?? '';
                $minDuration = (int) ($habit->target_value ?: 30);
                if ($duration > 0) {
                    $completed = ($duration >= $minDuration);
                    $value = ['duration' => $duration, 'topic' => $topic];
                }
                break;

            case 'bermasyarakat':
                $activities = (array) ($inputs['activities'] ?? []);
                $other = $inputs['other_activity'] ?? null;
                $minActivities = (int) ($habit->target_value ?: 1);
                $completed = (count($activities) >= $minActivities || ! empty(trim($other ?? '')));
                $value = [
                    'activities' => $activities,
                    'other_activity' => $other,
                ];
                break;

            case 'tidur-cepat':
                $time = $inputs['time'] ?? null;
                $maxTime = $habit->target_value ?: '21:00';
                if ($time) {
                    $value = ['time' => $time];
                    $completed = ($time <= $maxTime);
                }
                break;
        }

        $baseXp = (int) Setting::get('xp_per_habit', $habit->xp_reward ?: 10);
        $xp = $completed ? $baseXp : 0;

        return [
            'completed' => $completed,
            'value' => $value,
            'xp' => $xp,
        ];
    }

    /**
     * Process entire journal submission with real Gamification logic:
     * - Streak calculation (with Streak Freeze & multi-7 bonus)
     * - XP logs
     * - Level advancement
     * - Automated Badge awards
     * - Weekly Mission progress & rewards
     * - Prevents duplicate XP on same-day edits
     */
    public function processJournal(
        User $user,
        Carbon $date,
        array $submittedHabitsData,
        ?string $mood = null,
        ?string $notes = null,
        bool $isDraft = false
    ): array {
        return DB::transaction(function () use ($user, $date, $submittedHabitsData, $mood, $notes, $isDraft) {
            $habits = Habit::where('is_active', true)->orderBy('order')->get();

            // Check if existing journal exists and read previous XP to prevent double counting
            $existingJournal = Journal::where('user_id', $user->id)
                ->where('date', $date->format('Y-m-d'))
                ->first();

            $oldXp = ($existingJournal && ! $existingJournal->is_draft) ? $existingJournal->xp_earned : 0;

            // 1. Create or update Journal record
            $journal = Journal::updateOrCreate(
                ['user_id' => $user->id, 'date' => $date->format('Y-m-d')],
                [
                    'mood' => $mood,
                    'notes' => $notes,
                    'is_draft' => $isDraft,
                ]
            );

            $habitsCompleted = 0;
            $habitXpTotal = 0;

            foreach ($habits as $habit) {
                $habitInput = $submittedHabitsData[$habit->id] ?? [];
                $evaluation = $this->evaluateHabit($habit, $habitInput);

                if ($evaluation['completed']) {
                    $habitsCompleted++;
                    $habitXpTotal += $evaluation['xp'];
                }

                JournalEntry::updateOrCreate(
                    ['journal_id' => $journal->id, 'habit_id' => $habit->id],
                    [
                        'completed' => $evaluation['completed'],
                        'value' => $evaluation['value'],
                        'xp_earned' => $evaluation['xp'],
                    ]
                );
            }

            // If saving as Draft: do not award XP, streak, badges, or missions
            if ($isDraft) {
                $journal->update([
                    'habits_completed' => $habitsCompleted,
                    'is_perfect_day' => false,
                    'xp_earned' => 0,
                    'is_draft' => true,
                ]);

                return [
                    'journal' => $journal,
                    'habits_completed' => $habitsCompleted,
                    'xp_earned' => 0,
                    'is_perfect_day' => false,
                    'streak' => $user->current_streak,
                    'freeze_used' => false,
                    'streak_bonus_xp' => 0,
                    'new_badges' => [],
                    'completed_missions' => [],
                    'is_draft' => true,
                ];
            }

            // 2. Perfect Day Bonus (+30 XP default per PRD, configurable by admin)
            $isPerfectDay = ($habitsCompleted === 7);
            $perfectDayConfig = (int) Setting::get('xp_perfect_day', 30);
            $perfectDayBonus = $isPerfectDay ? $perfectDayConfig : 0;
            $totalXpToday = $habitXpTotal + $perfectDayBonus;

            $journal->update([
                'habits_completed' => $habitsCompleted,
                'is_perfect_day' => $isPerfectDay,
                'is_draft' => false,
                'xp_earned' => $totalXpToday,
            ]);

            // Calculate net XP difference to avoid double awarding on re-save
            $netXpDifference = $totalXpToday - $oldXp;

            // 3. Log Habit & Perfect Day XP
            if ($netXpDifference > 0) {
                XpLog::create([
                    'user_id' => $user->id,
                    'amount' => $netXpDifference,
                    'source' => ($isPerfectDay && $oldXp === 0) ? 'perfect_day' : 'habit',
                    'description' => "Capaian {$habitsCompleted} dari 7 kebiasaan".($isPerfectDay ? ' (Perfect Day)' : ''),
                    'date' => $date,
                ]);
            }

            // 4. Streak Calculation & Streak Freeze Logic
            $streakResult = $this->updateUserStreak($user, $date);
            $freezeUsed = $streakResult['freeze_used'];
            $streakBonusXp = $streakResult['bonus_xp'];

            if ($streakBonusXp > 0) {
                // Check if streak bonus was already logged today for this milestone
                $alreadyLoggedBonus = XpLog::where('user_id', $user->id)
                    ->where('source', 'streak_bonus')
                    ->where('date', $date->format('Y-m-d'))
                    ->exists();

                if (! $alreadyLoggedBonus) {
                    $netXpDifference += $streakBonusXp;
                    XpLog::create([
                        'user_id' => $user->id,
                        'amount' => $streakBonusXp,
                        'source' => 'streak_bonus',
                        'description' => "Bonus Konsistensi Streak kelipatan 7 hari ({$user->current_streak} hari)",
                        'date' => $date,
                    ]);
                }
            }

            // 5. Update user total XP and Level safely (no double-counting!)
            if ($netXpDifference > 0) {
                $user->increment('xp', $netXpDifference);
            } elseif ($netXpDifference < 0) {
                $user->decrement('xp', abs($netXpDifference));
            }

            $this->checkAndAdvanceLevel($user);

            // 6. Automated Badge Engine
            $newBadges = $this->checkAndAwardBadges($user, $journal);

            // 7. Weekly Missions Engine
            $completedMissions = $this->updateWeeklyMissions($user, $date);

            return [
                'journal' => $journal,
                'habits_completed' => $habitsCompleted,
                'xp_earned' => $totalXpToday,
                'is_perfect_day' => $isPerfectDay,
                'streak' => $user->fresh()->current_streak,
                'freeze_used' => $freezeUsed,
                'streak_bonus_xp' => $streakBonusXp,
                'new_badges' => $newBadges,
                'completed_missions' => $completedMissions,
                'is_draft' => false,
            ];
        });
    }

    /**
     * Streak logic supporting consecutive days, Streak Freeze (1x/week), and multi-7 day XP bonuses.
     */
    protected function updateUserStreak(User $user, Carbon $journalDate): array
    {
        $freezeUsed = false;
        $bonusXp = 0;

        $lastDate = $user->last_journal_date ? Carbon::parse($user->last_journal_date)->startOfDay() : null;
        $currentDate = $journalDate->copy()->startOfDay();

        if ($lastDate === null) {
            // First time ever journaling
            $user->current_streak = 1;
        } elseif ($lastDate->eq($currentDate)) {
            // Updating today's existing journal — preserve streak
        } elseif ($lastDate->eq($currentDate->copy()->subDay())) {
            // Consecutive day (yesterday was filled)
            $user->current_streak += 1;
        } elseif ($lastDate->lt($currentDate->copy()->subDay())) {
            // Missed at least 1 day. Check if gap is exactly 2 days and freeze is available!
            $daysDiff = $lastDate->diffInDays($currentDate);

            // Check if streak freeze was already used this week (Monday-Sunday)
            $freezeUsedThisWeek = false;
            if ($user->streak_freeze_used_at) {
                $weekStart = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
                $freezeUsedThisWeek = Carbon::parse($user->streak_freeze_used_at)->gte($weekStart);
            }

            if ($daysDiff == 2 && ! $freezeUsedThisWeek && ($user->streak_freezes > 0 || $user->streak_freeze_used_at === null)) {
                // Streak Freeze activated!
                $user->streak_freeze_used_at = $currentDate;
                $user->streak_freezes = 0;
                $user->current_streak += 1;
                $freezeUsed = true;
            } else {
                // Streak broken
                $user->current_streak = 1;
            }
        } else {
            // Backfilling a previous day
            if ($user->current_streak === 0) {
                $user->current_streak = 1;
            }
        }

        // Multiples of 7 streak bonus (+5 XP default per PRD, configurable by admin)
        $streakBonusConfig = (int) Setting::get('xp_streak_bonus', 5);
        if ($user->current_streak > 0 && $user->current_streak % 7 === 0) {
            $bonusXp = $streakBonusConfig;
        }

        $user->longest_streak = max($user->longest_streak, $user->current_streak);

        if ($lastDate === null || $currentDate->gte($lastDate)) {
            $user->last_journal_date = $currentDate;
        }

        $user->save();

        return [
            'freeze_used' => $freezeUsed,
            'bonus_xp' => $bonusXp,
        ];
    }

    /**
     * Check badge conditions and award any newly achieved badges.
     */
    public function checkAndAwardBadges(User $user, Journal $journal): array
    {
        $newlyAwarded = [];
        $existingBadgeIds = $user->badges()->pluck('badges.id')->toArray();

        $allBadges = Badge::where('is_active', true)->get();

        foreach ($allBadges as $badge) {
            if (in_array($badge->id, $existingBadgeIds)) {
                continue; // Already earned
            }

            $eligible = false;

            switch ($badge->slug) {
                case 'perfect-day':
                    if ($journal->is_perfect_day) {
                        $eligible = true;
                    }
                    break;

                case 'streak-7':
                    if ($user->current_streak >= 7) {
                        $eligible = true;
                    }
                    break;

                case 'early-bird':
                    // Check if student has >= 5 journals with Bangun Pagi completed
                    $earlyCount = JournalEntry::whereHas('journal', fn ($q) => $q->where('user_id', $user->id))
                        ->whereHas('habit', fn ($q) => $q->where('slug', 'bangun-pagi'))
                        ->where('completed', true)
                        ->count();
                    if ($earlyCount >= 5) {
                        $eligible = true;
                    }
                    break;

                case 'athlete':
                    // Check if student has >= 5 journals with Olahraga completed
                    $sportCount = JournalEntry::whereHas('journal', fn ($q) => $q->where('user_id', $user->id))
                        ->whereHas('habit', fn ($q) => $q->where('slug', 'berolahraga'))
                        ->where('completed', true)
                        ->count();
                    if ($sportCount >= 5) {
                        $eligible = true;
                    }
                    break;

                case 'xp-300':
                    if ($user->xp >= 300) {
                        $eligible = true;
                    }
                    break;

                default:
                    if ($badge->requirement_type === 'streak' && $user->current_streak >= $badge->requirement_value) {
                        $eligible = true;
                    } elseif ($badge->requirement_type === 'xp' && $user->xp >= $badge->requirement_value) {
                        $eligible = true;
                    }
                    break;
            }

            if ($eligible) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);
                $newlyAwarded[] = $badge;
            }
        }

        return $newlyAwarded;
    }

    /**
     * Update progress of weekly missions and auto-complete them when targets are met.
     */
    public function updateWeeklyMissions(User $user, Carbon $referenceDate): array
    {
        $weekStart = $referenceDate->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $weekEnd = $referenceDate->copy()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');

        $activeMissions = Mission::where('is_active', true)->get();
        $completedThisTurn = [];

        foreach ($activeMissions as $mission) {
            $userMission = UserMission::where('user_id', $user->id)
                ->where('mission_id', $mission->id)
                ->whereDate('week_start', $weekStart)
                ->first();

            if (! $userMission) {
                $userMission = UserMission::create([
                    'user_id' => $user->id,
                    'mission_id' => $mission->id,
                    'week_start' => $weekStart,
                    'current_count' => 0,
                    'is_completed' => false,
                ]);
            }

            if ($userMission->is_completed) {
                continue; // already finished this week
            }

            // Count occurrences this week
            $count = 0;
            if ($mission->habit_slug) {
                $count = JournalEntry::whereHas('journal', function ($q) use ($user, $weekStart, $weekEnd) {
                    $q->where('user_id', $user->id)
                        ->whereBetween('date', [$weekStart, $weekEnd]);
                })
                    ->whereHas('habit', fn ($q) => $q->where('slug', $mission->habit_slug))
                    ->where('completed', true)
                    ->count();
            } else {
                // General journal count this week
                $count = Journal::where('user_id', $user->id)
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->where('habits_completed', '>=', 4)
                    ->count();
            }

            $userMission->current_count = $count;

            if ($count >= $mission->target_count) {
                $userMission->is_completed = true;
                $userMission->completed_at = now();
                $userMission->save();

                // Award Mission XP Reward
                $user->increment('xp', $mission->xp_reward);
                XpLog::create([
                    'user_id' => $user->id,
                    'amount' => $mission->xp_reward,
                    'source' => 'mission',
                    'description' => "Menyelesaikan Misi Mingguan: {$mission->title}",
                    'date' => $referenceDate,
                ]);

                $completedThisTurn[] = $mission;
            } else {
                $userMission->save();
            }
        }

        return $completedThisTurn;
    }

    /**
     * Retrieve weekly missions and student progress for the current week.
     */
    public function getStudentWeeklyMissions(User $user): array
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');

        $missions = Mission::where('is_active', true)->get();
        $results = [];

        foreach ($missions as $m) {
            $um = UserMission::where('user_id', $user->id)
                ->where('mission_id', $m->id)
                ->whereDate('week_start', $weekStart)
                ->first();

            if (! $um) {
                $um = UserMission::create([
                    'user_id' => $user->id,
                    'mission_id' => $m->id,
                    'week_start' => $weekStart,
                    'current_count' => 0,
                    'is_completed' => false,
                ]);
            }

            $results[] = [
                'mission' => $m,
                'user_mission' => $um,
                'progress' => $um->progress_percent,
                'is_completed' => $um->is_completed,
            ];
        }

        return $results;
    }

    /**
     * Evaluate and update user level based on total XP threshold (100 XP per level).
     */
    public function checkAndAdvanceLevel(User $user): int
    {
        $newLevel = (int) floor($user->fresh()->xp / 100) + 1;
        $finalLevel = max(1, $newLevel);
        $user->update(['level' => $finalLevel]);

        return $finalLevel;
    }

    /**
     * Helper to evaluate weekly missions from a journal submission.
     */
    public function evaluateWeeklyMissions(User $user, Journal $journal): array
    {
        return $this->updateWeeklyMissions($user, Carbon::parse($journal->date));
    }
}

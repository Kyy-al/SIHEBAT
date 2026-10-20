<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiswaDashboardController extends Controller
{
    /**
     * Student home dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $habits = Habit::where('is_active', true)->orderBy('order')->get();

        // Check if today's journal exists
        $todayJournal = Journal::where('user_id', $user->id)
            ->where('date', $today)
            ->with('entries')
            ->first();

        // Weekly dots (last 7 days)
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $journal = Journal::where('user_id', $user->id)
                ->where('date', $date)
                ->first();
            $weekDays[] = [
                'date' => $date,
                'day_name' => $date->locale('id')->isoFormat('ddd'),
                'is_today' => $date->isToday(),
                'is_future' => $date->isFuture(),
                'completed' => $journal ? $journal->habits_completed : 0,
                'is_perfect' => $journal ? $journal->is_perfect_day : false,
            ];
        }

        // Classroom leaderboard (top 5)
        $classroom = $user->currentClassroom();
        $leaderboard = [];
        if ($classroom) {
            $leaderboard = $classroom->students()
                ->orderByDesc('xp')
                ->limit(5)
                ->get(['users.id', 'users.name', 'users.xp', 'users.current_streak', 'users.avatar']);
        }

        return view('siswa.dashboard', compact(
            'user',
            'habits',
            'todayJournal',
            'weekDays',
            'leaderboard',
            'today'
        ));
    }

    /**
     * Show journal entry form for today.
     */
    public function journalForm()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $habits = Habit::where('is_active', true)->orderBy('order')->get();

        // Load existing entries
        $journal = Journal::where('user_id', $user->id)
            ->where('date', $today)
            ->with('entries')
            ->first();

        $existingEntries = [];
        if ($journal) {
            foreach ($journal->entries as $entry) {
                $existingEntries[$entry->habit_id] = [
                    'completed' => $entry->completed,
                    'value' => $entry->value,
                ];
            }
        }

        return view('siswa.journal', compact('user', 'habits', 'journal', 'existingEntries', 'today'));
    }

    /**
     * Save journal entries.
     */
    public function saveJournal(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $habits = Habit::where('is_active', true)->orderBy('order')->get();

        return DB::transaction(function () use ($request, $user, $today, $habits) {
            // Create or update journal
            $journal = Journal::updateOrCreate(
                ['user_id' => $user->id, 'date' => $today],
                ['mood' => $request->input('mood'), 'notes' => $request->input('notes')]
            );

            $habitsCompleted = 0;
            $totalXp = 0;

            foreach ($habits as $habit) {
                $key = 'habit_' . $habit->id;
                $completed = false;
                $value = null;

                switch ($habit->slug) {
                    case 'bangun-pagi':
                        $time = $request->input($key . '_time');
                        if ($time) {
                            $completed = true;
                            $value = ['time' => $time];
                        }
                        break;

                    case 'beribadah':
                        $done = $request->boolean($key . '_done');
                        $types = $request->input($key . '_types', []);
                        if ($done) {
                            $completed = true;
                            $value = ['done' => true, 'types' => $types];
                        }
                        break;

                    case 'berolahraga':
                        $type = $request->input($key . '_type');
                        $duration = $request->input($key . '_duration');
                        if ($type && $duration) {
                            $completed = true;
                            $value = ['type' => $type, 'duration' => (int) $duration];
                        }
                        break;

                    case 'makan-sehat':
                        $items = $request->input($key . '_items', []);
                        if (count($items) >= 3) {
                            $completed = true;
                            $value = ['items' => $items];
                        }
                        break;

                    case 'gemar-belajar':
                        $duration = $request->input($key . '_duration');
                        $topic = $request->input($key . '_topic');
                        if ($duration && (int) $duration > 0) {
                            $completed = true;
                            $value = ['duration' => (int) $duration, 'topic' => $topic];
                        }
                        break;

                    case 'bermasyarakat':
                        $activities = $request->input($key . '_activities', []);
                        if (count($activities) > 0) {
                            $completed = true;
                            $value = ['activities' => $activities];
                        }
                        break;

                    case 'tidur-cepat':
                        $time = $request->input($key . '_time');
                        if ($time) {
                            $completed = true;
                            $value = ['time' => $time];
                        }
                        break;
                }

                $xpEarned = $completed ? $habit->xp_reward : 0;
                if ($completed) {
                    $habitsCompleted++;
                    $totalXp += $xpEarned;
                }

                JournalEntry::updateOrCreate(
                    ['journal_id' => $journal->id, 'habit_id' => $habit->id],
                    ['completed' => $completed, 'value' => $value, 'xp_earned' => $xpEarned]
                );
            }

            // Perfect day bonus
            $isPerfectDay = $habitsCompleted === 7;
            if ($isPerfectDay) {
                $totalXp += 30; // bonus XP
            }

            $journal->update([
                'habits_completed' => $habitsCompleted,
                'is_perfect_day' => $isPerfectDay,
                'xp_earned' => $totalXp,
            ]);

            // Update user streak & XP
            $this->updateStreak($user, $today);
            $user->increment('xp', $totalXp);

            // Recalculate level
            $newLevel = (int) floor($user->fresh()->xp / 100) + 1;
            $user->update(['level' => max(1, $newLevel)]);

            return redirect()->route('siswa.journal.success')
                ->with('journal_result', [
                    'habits_completed' => $habitsCompleted,
                    'xp_earned' => $totalXp,
                    'is_perfect_day' => $isPerfectDay,
                    'streak' => $user->fresh()->current_streak,
                ]);
        });
    }

    /**
     * Show success screen after journal submission.
     */
    public function journalSuccess()
    {
        $result = session('journal_result');
        if (! $result) {
            return redirect()->route('siswa.dashboard');
        }

        return view('siswa.journal-success', [
            'user' => Auth::user(),
            'result' => $result,
        ]);
    }

    /**
     * Leaderboard page.
     */
    public function leaderboard()
    {
        $user = Auth::user();
        $classroom = $user->currentClassroom();
        $students = collect();

        if ($classroom) {
            $students = $classroom->students()
                ->orderByDesc('xp')
                ->get(['users.id', 'users.name', 'users.xp', 'users.current_streak', 'users.avatar', 'users.level']);
        }

        $userRank = $students->search(fn ($s) => $s->id === $user->id) + 1;

        return view('siswa.leaderboard', compact('user', 'students', 'userRank'));
    }

    /**
     * Student profile page.
     */
    public function profile()
    {
        $user = Auth::user();
        $badges = $user->badges()->get();
        $totalJournals = Journal::where('user_id', $user->id)->count();
        $perfectDays = Journal::where('user_id', $user->id)->where('is_perfect_day', true)->count();

        return view('siswa.profile', compact('user', 'badges', 'totalJournals', 'perfectDays'));
    }

    /**
     * Update streak logic.
     */
    protected function updateStreak(User $user, Carbon $today): void
    {
        $yesterday = $today->copy()->subDay();

        if ($user->last_journal_date === null || $user->last_journal_date->lt($yesterday)) {
            // Streak reset (unless freeze available)
            if ($user->last_journal_date && $user->last_journal_date->eq($yesterday)) {
                // Consecutive day — continue streak
                $user->current_streak += 1;
            } else {
                // Gap detected
                $user->current_streak = 1;
            }
        } elseif ($user->last_journal_date->eq($yesterday)) {
            $user->current_streak += 1;
        } elseif ($user->last_journal_date->eq($today)) {
            // Already journaled today — no change
            return;
        } else {
            $user->current_streak = 1;
        }

        $user->longest_streak = max($user->longest_streak, $user->current_streak);
        $user->last_journal_date = $today;
        $user->save();
    }
}

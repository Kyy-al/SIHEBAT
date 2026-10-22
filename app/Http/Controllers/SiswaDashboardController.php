<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use App\Models\XpLog;
use App\Services\GamificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiswaDashboardController extends Controller
{
    /**
     * Student home dashboard.
     */
    public function index(GamificationService $gamificationService)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $habits = Habit::where('is_active', true)->orderBy('order')->get();

        // Check if today's journal exists
        $todayJournal = Journal::where('user_id', $user->id)
            ->whereDate('date', $today->format('Y-m-d'))
            ->with('entries')
            ->first();

        // Check if yesterday's journal exists
        $yesterday = $today->copy()->subDay();
        $yesterdayJournal = Journal::where('user_id', $user->id)
            ->whereDate('date', $yesterday->format('Y-m-d'))
            ->first();

        // Weekly dots (Monday through Sunday)
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $journal = Journal::where('user_id', $user->id)
                ->where('date', $date->format('Y-m-d'))
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

        // Weekly missions progress
        $weeklyMissions = $gamificationService->getStudentWeeklyMissions($user);

        return view('siswa.dashboard', compact(
            'user',
            'habits',
            'todayJournal',
            'yesterdayJournal',
            'weekDays',
            'leaderboard',
            'weeklyMissions',
            'today',
            'yesterday'
        ));
    }

    /**
     * Show journal entry form with support for Today and H-1 backfilling.
     */
    public function journalForm(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();

        // Target date parameter (default today, allowable: today or yesterday)
        $dateStr = $request->query('date', $today->format('Y-m-d'));
        $targetDate = Carbon::parse($dateStr)->startOfDay();

        // FR-JRN-03: Isi mundur maksimal H-1; tidak bisa mengisi tanggal mendatang
        if ($targetDate->gt($today)) {
            return redirect()->route('siswa.journal', ['date' => $today->format('Y-m-d')])
                ->with('error', 'Tidak dapat mengisi jurnal untuk tanggal masa mendatang.');
        }

        if ($targetDate->lt($yesterday)) {
            return redirect()->route('siswa.journal', ['date' => $today->format('Y-m-d')])
                ->with('error', 'Batas pengisian mundur maksimal adalah kemarin (H-1).');
        }

        $habits = Habit::where('is_active', true)->orderBy('order')->get();

        // Load existing entries for target date
        $journal = Journal::where('user_id', $user->id)
            ->where('date', $targetDate->format('Y-m-d'))
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

        return view('siswa.journal', [
            'user' => $user,
            'habits' => $habits,
            'journal' => $journal,
            'existingEntries' => $existingEntries,
            'targetDate' => $targetDate,
            'isToday' => $targetDate->isToday(),
            'isYesterday' => $targetDate->eq($yesterday),
            'today' => $today,
            'yesterday' => $yesterday,
        ]);
    }

    /**
     * Save journal entries with Gamification Service.
     */
    public function saveJournal(Request $request, GamificationService $gamificationService)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();

        $dateInput = $request->input('date') ?? $request->input('journal_date') ?? $today->format('Y-m-d');
        $journalDate = Carbon::parse($dateInput)->startOfDay();

        // Validation rule: only today or yesterday
        if ($journalDate->gt($today) || $journalDate->lt($yesterday)) {
            return back()
                ->withErrors([
                    'date' => 'Tanggal jurnal tidak valid. Pengisian hanya diperbolehkan untuk Hari Ini atau Kemarin (H-1).',
                    'journal_date' => 'Tanggal jurnal tidak valid. Pengisian hanya diperbolehkan untuk Hari Ini atau Kemarin (H-1).',
                ])
                ->with('error', 'Tanggal jurnal tidak valid. Pengisian hanya diperbolehkan untuk Hari Ini atau Kemarin (H-1).');
        }

        // Server-side validation for habit inputs
        $habits = Habit::where('is_active', true)->get();
        $submittedHabitsData = [];
        $habitsInputArray = $request->input('habits', []);

        foreach ($habits as $habit) {
            $prefix = 'habit_'.$habit->id;
            $hData = $habitsInputArray[$habit->id] ?? [];

            switch ($habit->slug) {
                case 'bangun-pagi':
                    $time = $request->input($prefix.'_time', $hData['time'] ?? null);
                    if ($time !== null && ! preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $time)) {
                        return back()->withErrors(['time' => 'Format jam bangun pagi harus berupa JJ:MM (contoh 04:30).']);
                    }
                    $submittedHabitsData[$habit->id] = [
                        'time' => $time,
                        'is_late' => $request->boolean($prefix.'_is_late', ! empty($hData['is_late'])),
                    ];
                    break;

                case 'beribadah':
                    $submittedHabitsData[$habit->id] = [
                        'done' => $request->boolean($prefix.'_done', ! empty($hData['done'])),
                        'faith' => $request->input($prefix.'_faith', $hData['faith'] ?? null),
                        'types' => $request->input($prefix.'_types', $hData['types'] ?? []),
                        'custom' => $request->input($prefix.'_custom', $hData['custom'] ?? null),
                    ];
                    break;

                case 'berolahraga':
                    $dur = $request->input($prefix.'_duration', $hData['duration'] ?? null);
                    if ($dur !== null && ($dur < 0 || $dur > 360)) {
                        return back()->withErrors(['duration' => 'Durasi olahraga tidak valid (harus antara 0 sampai 360 menit).']);
                    }
                    $submittedHabitsData[$habit->id] = [
                        'type' => $request->input($prefix.'_type', $hData['type'] ?? null),
                        'duration' => $dur,
                    ];
                    break;

                case 'makan-sehat':
                    $submittedHabitsData[$habit->id] = [
                        'items' => $request->input($prefix.'_items', $hData['items'] ?? []),
                    ];
                    break;

                case 'gemar-belajar':
                    $dur = $request->input($prefix.'_duration', $hData['duration'] ?? null);
                    if ($dur !== null && ($dur < 0 || $dur > 360)) {
                        return back()->withErrors(['duration' => 'Durasi belajar tidak valid (harus antara 0 sampai 360 menit).']);
                    }
                    $submittedHabitsData[$habit->id] = [
                        'duration' => $dur,
                        'topic' => $request->input($prefix.'_topic', $hData['topic'] ?? ''),
                    ];
                    break;

                case 'bermasyarakat':
                    $submittedHabitsData[$habit->id] = [
                        'activities' => $request->input($prefix.'_activities', $hData['activities'] ?? []),
                        'other_activity' => $request->input($prefix.'_other_activity', $hData['other_activity'] ?? null),
                    ];
                    break;

                case 'tidur-cepat':
                    $time = $request->input($prefix.'_time', $hData['time'] ?? null);
                    if ($time !== null && ! preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $time)) {
                        return back()->withErrors(['time' => 'Format jam tidur cepat harus berupa JJ:MM (contoh 21:00).']);
                    }
                    $submittedHabitsData[$habit->id] = [
                        'time' => $time,
                    ];
                    break;
            }
        }

        $mood = $request->input('mood');
        if ($mood !== null && ! in_array($mood, ['sangat_senang', 'senang', 'biasa', 'sedih', 'sangat_sedih'])) {
            return back()->withErrors(['mood' => 'Pilihan perasaan mood tidak valid.']);
        }

        $notes = $request->input('notes');
        if ($notes !== null && mb_strlen($notes) > 1000) {
            return back()->withErrors(['notes' => 'Catatan refleksi maksimal 1000 karakter.']);
        }

        $isDraft = $request->boolean('is_draft') || $request->input('action') === 'draft';

        // Process through central Gamification engine
        $result = $gamificationService->processJournal(
            $user,
            $journalDate,
            $submittedHabitsData,
            $mood,
            $notes,
            $isDraft
        );

        if ($isDraft) {
            return redirect()->route('siswa.journal', ['date' => $journalDate->format('Y-m-d')])
                ->with('success', 'Draf jurnal berhasil disimpan.');
        }

        return redirect()->route('siswa.journal.success')
            ->with('journal_result', [
                'habits_completed' => $result['habits_completed'],
                'xp_earned' => $result['xp_earned'],
                'is_perfect_day' => $result['is_perfect_day'],
                'streak' => $result['streak'],
                'freeze_used' => $result['freeze_used'],
                'streak_bonus_xp' => $result['streak_bonus_xp'],
                'new_badges' => array_map(fn ($b) => ['name' => $b->name, 'icon' => $b->icon, 'description' => $b->description], $result['new_badges']),
                'completed_missions' => array_map(fn ($m) => ['title' => $m->title, 'xp' => $m->xp_reward], $result['completed_missions']),
            ]);
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
     * Leaderboard with Individual per-class rank AND Inter-classroom rank (% based).
     */
    public function leaderboard(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'kelas'); // 'kelas' or 'antar_kelas'
        $period = $request->query('period', 'mingguan'); // 'mingguan', 'bulanan', 'semester'

        // Determine date range for period filter
        $now = Carbon::now();
        $startDate = match ($period) {
            'bulanan' => $now->copy()->startOfMonth(),
            'semester' => $now->copy()->subMonths(6)->startOfDay(),
            default => $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
        };
        $endDate = $now->copy()->endOfDay();

        // 1. Individual Students in current user's classroom (isolated per period via xp_logs, tie-breaking by XP, streak, name, id)
        $classroom = $user->currentClassroom();
        $students = collect();
        if ($classroom) {
            $studentIds = $classroom->students()->pluck('users.id');

            // Sum XP from xp_logs within the specific period
            $periodXpMap = XpLog::whereIn('user_id', $studentIds)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->groupBy('user_id')
                ->selectRaw('user_id, SUM(amount) as total_period_xp')
                ->pluck('total_period_xp', 'user_id');

            $students = $classroom->students()
                ->get(['users.id', 'users.name', 'users.xp as all_time_xp', 'users.current_streak', 'users.avatar', 'users.level'])
                ->map(function ($s) use ($periodXpMap) {
                    $s->xp = (int) ($periodXpMap[$s->id] ?? 0);

                    return $s;
                })
                ->sort(function ($a, $b) {
                    if ($a->xp !== $b->xp) {
                        return $b->xp <=> $a->xp;
                    }
                    if ($a->current_streak !== $b->current_streak) {
                        return $b->current_streak <=> $a->current_streak;
                    }
                    $nameComp = strcmp($a->name, $b->name);
                    if ($nameComp !== 0) {
                        return $nameComp;
                    }

                    return $a->id <=> $b->id;
                })
                ->values();
        }

        $userRank = $students->search(fn ($s) => $s->id === $user->id) !== false
            ? $students->search(fn ($s) => $s->id === $user->id) + 1
            : 0;

        // Privacy Mode (FR-LDB-05): Top 10 displayed; student outside top 10 only sees own rank
        $topStudents = $students->take(10);
        $myStudentOutsideTop = ($userRank > 10) ? $students->firstWhere('id', $user->id) : null;

        // 2. Inter-classroom Rankings (FR-LDB-02: Berdasarkan % partisipasi dan capaian yang adil)
        $academicYear = AcademicYear::active() ?? AcademicYear::latest()->first();
        $classrooms = $academicYear
            ? Classroom::where('academic_year_id', $academicYear->id)->with(['students', 'homeroomTeacher'])->get()
            : Classroom::with(['students', 'homeroomTeacher'])->get();

        $interClassRankings = [];
        $expectedDays = max(1, $now->diffInDays($startDate) + 1);

        foreach ($classrooms as $cls) {
            $totalStudents = $cls->students->count();
            if ($totalStudents === 0) {
                continue;
            }

            $studentIds = $cls->students->pluck('id');

            // Count journals submitted by students of this class in the period
            $totalJournals = Journal::whereIn('user_id', $studentIds)
                ->where('date', '>=', $startDate->format('Y-m-d'))
                ->count();

            // Total habits completed in the period
            $habitsCompleted = DB::table('journal_entries')
                ->join('journals', 'journal_entries.journal_id', '=', 'journals.id')
                ->whereIn('journals.user_id', $studentIds)
                ->where('journals.date', '>=', $startDate->format('Y-m-d'))
                ->where('journal_entries.completed', true)
                ->count();

            $maxPossibleJournals = $totalStudents * $expectedDays;
            $participationRate = min(100, round(($totalJournals / $maxPossibleJournals) * 100, 1));

            $maxPossibleHabits = $maxPossibleJournals * 7;
            $habitCompletionRate = min(100, round(($habitsCompleted / $maxPossibleHabits) * 100, 1));

            // Weighted composite score (40% participation, 60% habit consistency)
            $compositeScore = round(($participationRate * 0.4) + ($habitCompletionRate * 0.6), 1);

            $interClassRankings[] = [
                'classroom' => $cls,
                'total_students' => $totalStudents,
                'participation_rate' => $participationRate,
                'habit_rate' => $habitCompletionRate,
                'composite_score' => $compositeScore,
            ];
        }

        // Sort classes by composite score descending
        usort($interClassRankings, fn ($a, $b) => $b['composite_score'] <=> $a['composite_score']);

        return view('siswa.leaderboard', compact(
            'user',
            'classroom',
            'students',
            'userRank',
            'tab',
            'period',
            'interClassRankings',
            'topStudents',
            'myStudentOutsideTop'
        ));
    }

    /**
     * Rapor Saya page: Personal Rapor Pendidikan view for student with monthly calendar heatmap.
     */
    public function rapor(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $month = (int) $request->query('month', $now->month);
        $year = (int) $request->query('year', $now->year);

        $selectedMonth = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $selectedMonth->daysInMonth;

        // Calendar Heatmap data for this month
        $calendarDays = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($year, $month, $d)->startOfDay();
            $journal = Journal::where('user_id', $user->id)
                ->where('date', $date->format('Y-m-d'))
                ->first();

            $calendarDays[] = [
                'day' => $d,
                'date' => $date,
                'day_name' => $date->locale('id')->isoFormat('dd'),
                'is_today' => $date->isToday(),
                'is_future' => $date->isFuture(),
                'completed' => $journal ? $journal->habits_completed : 0,
                'is_perfect' => $journal ? $journal->is_perfect_day : false,
                'xp' => $journal ? $journal->xp_earned : 0,
            ];
        }

        // 7 Habits completion rates for this student
        $habits = Habit::where('is_active', true)->orderBy('order')->get();
        $monthStart = $selectedMonth->copy()->startOfMonth()->format('Y-m-d');
        $monthEnd = $selectedMonth->copy()->endOfMonth()->format('Y-m-d');

        $habitBreakdown = [];
        $totalJournalsThisMonth = Journal::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->count();

        foreach ($habits as $habit) {
            $doneCount = JournalEntry::whereHas('journal', function ($q) use ($user, $monthStart, $monthEnd) {
                $q->where('user_id', $user->id)
                    ->whereBetween('date', [$monthStart, $monthEnd]);
            })
                ->where('habit_id', $habit->id)
                ->where('completed', true)
                ->count();

            $rate = $totalJournalsThisMonth > 0 ? round(($doneCount / $totalJournalsThisMonth) * 100) : 0;
            $habitBreakdown[] = [
                'habit' => $habit,
                'done_count' => $doneCount,
                'rate' => $rate,
                'status' => $rate >= 75 ? 'Baik' : ($rate >= 50 ? 'Cukup' : 'Perlu Perhatian'),
            ];
        }

        // Recent XP logs
        $xpLogs = $user->xpLogs()->orderByDesc('created_at')->take(10)->get();

        return view('siswa.rapor', compact(
            'user',
            'selectedMonth',
            'calendarDays',
            'habitBreakdown',
            'totalJournalsThisMonth',
            'xpLogs'
        ));
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
     * Update student profile photo (avatar).
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'avatar.required' => 'Silakan pilih berkas foto terlebih dahulu.',
            'avatar.image' => 'Berkas harus berupa gambar.',
            'avatar.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'avatar.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $user = Auth::user();

        // Delete old avatar from public disk if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar in avatars folder on public disk
        $path = $request->file('avatar')->store('avatars', 'public');

        // Update user record: store path and clear moderation warning
        $user->update([
            'avatar' => $path,
            'avatar_warning' => null,
            'avatar_moderated_at' => null,
        ]);

        return redirect()->route('siswa.profile')->with('success', 'Foto profil berhasil diperbarui! Foto kamu kini tampil di papan peringkat.');
    }

    /**
     * Delete student profile photo (avatar).
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update([
            'avatar' => null,
        ]);

        return redirect()->route('siswa.profile')->with('success', 'Foto profil berhasil dihapus. Inisial nama akan digunakan kembali.');
    }
}

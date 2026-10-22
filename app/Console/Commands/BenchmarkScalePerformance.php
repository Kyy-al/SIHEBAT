<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\User;
use App\Models\XpLog;
use App\Services\GamificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BenchmarkScalePerformance extends Command
{
    protected $signature = 'benchmark:scale {--students=1000} {--days=90} {--cleanup}';

    protected $description = 'Seed 1,000 students x 90 days and benchmark dashboard, leaderboard, and rapor queries';

    public function handle(GamificationService $gamificationService): int
    {
        $numStudents = (int) $this->option('students');
        $numDays = (int) $this->option('days');
        $doCleanup = (bool) $this->option('cleanup');

        $this->info("=== SIHEBAT SCALE BENCHMARK: {$numStudents} Siswa x {$numDays} Hari ===");
        $this->info('Database: '.config('database.connections.mysql.database').' on '.config('database.connections.mysql.host'));

        // 1. Ensure basic reference records exist (academic year, classrooms, habits)
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027', 'semester' => 'Ganjil'],
            ['start_date' => now()->startOfYear(), 'end_date' => now()->endOfYear(), 'is_active' => true]
        );

        $habits = Habit::where('is_active', true)->orderBy('order')->get();
        if ($habits->isEmpty()) {
            $this->error('No habits found. Please run php artisan db:seed first.');

            return 1;
        }

        // Create 25 benchmark classrooms (40 students each for 1000 students)
        $classrooms = [];
        $grades = ['X', 'XI', 'XII'];
        for ($c = 1; $c <= 25; $c++) {
            $grade = $grades[($c - 1) % 3];
            $classrooms[] = Classroom::firstOrCreate(
                ['name' => "Kelas Bench-{$c}", 'academic_year_id' => $academicYear->id],
                ['grade' => $grade]
            );
        }

        // 2. Bulk Seed Students
        $this->info("1. Seeding {$numStudents} students...");
        $seedStart = microtime(true);

        $passwordHash = Hash::make('password123');
        $studentBatch = [];
        $existingBenchCount = User::where('email', 'like', 'bench_student_%@sihebat.test')->count();
        $startIdx = $existingBenchCount + 1;
        $targetNew = max(0, $numStudents - $existingBenchCount);

        for ($i = 0; $i < $targetNew; $i++) {
            $idx = $startIdx + $i;
            $studentBatch[] = [
                'name' => "Siswa Benchmark {$idx}",
                'gender' => $idx % 2 === 0 ? 'L' : 'P',
                'nis' => sprintf('88%06d', $idx),
                'email' => "bench_student_{$idx}@sihebat.test",
                'role' => 'siswa',
                'password' => $passwordHash,
                'initial_password' => null,
                'must_change_password' => false,
                'xp' => 0,
                'level' => 1,
                'current_streak' => 0,
                'longest_streak' => 0,
                'streak_freezes' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($studentBatch) >= 500) {
                User::insert($studentBatch);
                $studentBatch = [];
            }
        }
        if (! empty($studentBatch)) {
            User::insert($studentBatch);
        }

        $allBenchStudents = User::where('email', 'like', 'bench_student_%@sihebat.test')
            ->limit($numStudents)
            ->get();

        // Attach students to classrooms (round-robin)
        $classroomAttach = [];
        $nowStr = now()->toDateTimeString();
        foreach ($allBenchStudents as $idx => $st) {
            $cid = $classrooms[$idx % count($classrooms)]->id;
            $classroomAttach[] = [
                'classroom_id' => $cid,
                'user_id' => $st->id,
                'created_at' => $nowStr,
                'updated_at' => $nowStr,
            ];
        }
        // Insert classroom attachments (ignore duplicates)
        foreach (array_chunk($classroomAttach, 500) as $chunk) {
            DB::table('classroom_student')->insertOrIgnore($chunk);
        }

        $this->info('   Done seeding students in '.round(microtime(true) - $seedStart, 2).'s.');

        // 3. Bulk Seed Journals x 90 Days
        $this->info("2. Seeding journals and habit entries for past {$numDays} days (realistic participation ~80%)...");
        $journalStart = microtime(true);

        $dates = [];
        $today = Carbon::today();
        for ($d = $numDays - 1; $d >= 0; $d--) {
            $dates[] = $today->copy()->subDays($d);
        }

        $habitIds = $habits->pluck('id')->toArray();
        $totalJournalsInserted = 0;
        $totalEntriesInserted = 0;
        $totalXpLogsInserted = 0;

        // Process students in chunks of 100 to manage memory & bulk inserts
        $studentChunks = $allBenchStudents->chunk(100);
        $chunkIndex = 0;

        foreach ($studentChunks as $chunk) {
            $chunkIndex++;
            $journalsToInsert = [];
            $xpLogsToInsert = [];

            foreach ($chunk as $student) {
                $studentConsistency = 0.70 + (($student->id % 25) / 100.0); // 70% to 94%
                $streakCounter = 0;

                foreach ($dates as $date) {
                    $dateStr = $date->format('Y-m-d');
                    $hash = crc32($student->id.'_'.$dateStr);
                    $prob = ($hash % 100) / 100.0;

                    if ($prob <= $studentConsistency) {
                        $isPerfect = (($hash % 10) < 6); // 60% perfect day
                        $habitsCompleted = $isPerfect ? 7 : 4 + ($hash % 3);
                        $dayXp = ($habitsCompleted * 10) + ($isPerfect ? 30 : 0);

                        $streakCounter++;
                        if ($streakCounter % 7 === 0) {
                            $dayXp += 5; // Streak bonus
                        }

                        $journalsToInsert[] = [
                            'user_id' => $student->id,
                            'date' => $dateStr,
                            'habits_completed' => $habitsCompleted,
                            'is_perfect_day' => $isPerfect,
                            'xp_earned' => $dayXp,
                            'mood' => 'senang',
                            'notes' => 'Catatan jurnal kebiasaan harian',
                            'is_draft' => false,
                            'created_at' => $dateStr.' 20:00:00',
                            'updated_at' => $dateStr.' 20:00:00',
                        ];

                        $xpLogsToInsert[] = [
                            'user_id' => $student->id,
                            'amount' => $dayXp,
                            'source' => $isPerfect ? 'perfect_day' : 'habit',
                            'description' => "Capaian {$habitsCompleted} dari 7 kebiasaan",
                            'date' => $dateStr,
                            'created_at' => $dateStr.' 20:00:00',
                            'updated_at' => $dateStr.' 20:00:00',
                        ];
                    } else {
                        $streakCounter = 0;
                    }
                }
            }

            // Bulk insert journals
            foreach (array_chunk($journalsToInsert, 1000) as $jChunk) {
                DB::table('journals')->insertOrIgnore($jChunk);
                $totalJournalsInserted += count($jChunk);
            }

            // Bulk insert XP logs
            foreach (array_chunk($xpLogsToInsert, 1000) as $xChunk) {
                DB::table('xp_logs')->insertOrIgnore($xChunk);
                $totalXpLogsInserted += count($xChunk);
            }

            $this->output->write('.');
        }
        $this->newLine();
        $this->info('   Done seeding journals & logs in '.round(microtime(true) - $journalStart, 2).'s.');

        // 4. Seed Habit Entries for past 14 days of high-fidelity analysis
        $this->info('3. Seeding detailed journal_entries for past 14 days...');
        $entriesStart = microtime(true);
        $recentJournals = DB::table('journals')
            ->whereIn('user_id', $allBenchStudents->pluck('id'))
            ->where('date', '>=', $today->copy()->subDays(14)->toDateString())
            ->select('id', 'habits_completed')
            ->get();

        $entriesBatch = [];
        foreach ($recentJournals as $rj) {
            foreach ($habitIds as $hIdx => $hId) {
                $completed = ($hIdx < $rj->habits_completed);
                $entriesBatch[] = [
                    'journal_id' => $rj->id,
                    'habit_id' => $hId,
                    'completed' => $completed,
                    'value' => json_encode(['completed' => $completed]),
                    'xp_earned' => $completed ? 10 : 0,
                    'created_at' => $nowStr,
                    'updated_at' => $nowStr,
                ];

                if (count($entriesBatch) >= 2000) {
                    DB::table('journal_entries')->insertOrIgnore($entriesBatch);
                    $totalEntriesInserted += count($entriesBatch);
                    $entriesBatch = [];
                }
            }
        }
        if (! empty($entriesBatch)) {
            DB::table('journal_entries')->insertOrIgnore($entriesBatch);
            $totalEntriesInserted += count($entriesBatch);
        }
        $this->info('   Done seeding detailed entries in '.round(microtime(true) - $entriesStart, 2).'s.');

        // 5. Query Count Totals
        $actualStudents = User::where('role', 'siswa')->count();
        $actualJournals = DB::table('journals')->count();
        $actualEntries = DB::table('journal_entries')->count();
        $actualXpLogs = DB::table('xp_logs')->count();

        $this->info('--------------------------------------------------');
        $this->info('DATA VOLUME VERIFIED IN DATABASE:');
        $this->info('  Total Siswa          : '.number_format($actualStudents));
        $this->info('  Total Jurnal         : '.number_format($actualJournals));
        $this->info('  Total Journal Entries: '.number_format($actualEntries));
        $this->info('  Total XP Logs        : '.number_format($actualXpLogs));
        $this->info('--------------------------------------------------');

        // 6. BENCHMARK QUERIES
        $this->info('4. BENCHMARKING PERFORMANCE UNDER SCALE...');

        // A. Admin Dashboard Metrics Query (Counts & Aggregates)
        $t0 = microtime(true);
        $countStudents = User::where('role', 'siswa')->count();
        $todayStr = Carbon::today()->toDateString();
        $journalsToday = Journal::where('date', $todayStr)->count();
        $activeStreakCount = User::where('role', 'siswa')->where('current_streak', '>', 0)->count();
        $tAdminMetrics = round((microtime(true) - $t0) * 1000, 2);
        $this->info("  [1] Admin Dashboard Overview Counts  : {$tAdminMetrics} ms");

        // B. Rapor Pendidikan Aggregation Query (7 Habits x Past 30 Days)
        $t1 = microtime(true);
        $startDate = Carbon::today()->subDays(30)->toDateString();
        $endDate = Carbon::today()->toDateString();

        $habitAggregates = DB::table('journal_entries')
            ->join('journals', 'journal_entries.journal_id', '=', 'journals.id')
            ->join('habits', 'journal_entries.habit_id', '=', 'habits.id')
            ->whereBetween('journals.date', [$startDate, $endDate])
            ->select(
                'habits.id',
                'habits.name',
                DB::raw('COUNT(journal_entries.id) as total_entries'),
                DB::raw('SUM(CASE WHEN journal_entries.completed = 1 THEN 1 ELSE 0 END) as completed_entries'),
                DB::raw('ROUND(AVG(CASE WHEN journal_entries.completed = 1 THEN 1.0 ELSE 0.0 END) * 100, 2) as completion_pct')
            )
            ->groupBy('habits.id', 'habits.name')
            ->get();
        $tRaporAggregation = round((microtime(true) - $t1) * 1000, 2);
        $this->info("  [2] Rapor Pendidikan 7 Habits Aggreg : {$tRaporAggregation} ms (Processed {$habitAggregates->count()} habits across 30 days)");

        // C. Weekly Leaderboard Query (Grouped by xp_logs past 7 days)
        $t2 = microtime(true);
        $weekStart = Carbon::today()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd = Carbon::today()->endOfWeek(Carbon::SUNDAY)->toDateString();
        $weeklyLeaderboard = User::where('role', 'siswa')
            ->select('users.id', 'users.name', 'users.current_streak')
            ->selectSub(
                XpLog::selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('xp_logs.user_id', 'users.id')
                    ->whereBetween('date', [$weekStart, $weekEnd]),
                'period_xp'
            )
            ->orderByDesc('period_xp')
            ->orderByDesc('current_streak')
            ->orderBy('name')
            ->limit(50)
            ->get();
        $tWeeklyLeaderboard = round((microtime(true) - $t2) * 1000, 2);
        $this->info("  [3] Weekly Leaderboard Query (Top 50): {$tWeeklyLeaderboard} ms");

        // D. Monthly Leaderboard Query (Grouped by xp_logs past 30 days)
        $t3 = microtime(true);
        $monthStart = Carbon::today()->subDays(30)->toDateString();
        $monthlyLeaderboard = User::where('role', 'siswa')
            ->select('users.id', 'users.name', 'users.current_streak')
            ->selectSub(
                XpLog::selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('xp_logs.user_id', 'users.id')
                    ->whereBetween('date', [$monthStart, $endDate]),
                'period_xp'
            )
            ->orderByDesc('period_xp')
            ->orderByDesc('current_streak')
            ->orderBy('name')
            ->limit(50)
            ->get();
        $tMonthlyLeaderboard = round((microtime(true) - $t3) * 1000, 2);
        $this->info("  [4] Monthly Leaderboard Query (Top 50): {$tMonthlyLeaderboard} ms");

        // E. Semester Leaderboard Query (Grouped by xp_logs past 90 days)
        $t4 = microtime(true);
        $semesterStart = Carbon::today()->subDays(90)->toDateString();
        $semesterLeaderboard = User::where('role', 'siswa')
            ->select('users.id', 'users.name', 'users.current_streak')
            ->selectSub(
                XpLog::selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('xp_logs.user_id', 'users.id')
                    ->whereBetween('date', [$semesterStart, $endDate]),
                'period_xp'
            )
            ->orderByDesc('period_xp')
            ->orderByDesc('current_streak')
            ->orderBy('name')
            ->limit(50)
            ->get();
        $tSemesterLeaderboard = round((microtime(true) - $t4) * 1000, 2);
        $this->info("  [5] Semester Leaderboard (Top 50)    : {$tSemesterLeaderboard} ms");

        // F. Interclass Comparative Ranking (25 Classrooms by Participation Rate)
        $t5 = microtime(true);
        $classroomRankings = Classroom::where('academic_year_id', $academicYear->id)
            ->with('students')
            ->withCount('students')
            ->get()
            ->map(function ($cls) use ($startDate, $endDate) {
                $studentIds = $cls->students->pluck('id');
                $totalPossible = max(1, $studentIds->count() * 30);
                $filledCount = Journal::whereIn('user_id', $studentIds)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->count();
                $participationRate = round(($filledCount / $totalPossible) * 100, 2);

                return [
                    'classroom' => $cls->name,
                    'students' => $studentIds->count(),
                    'participation_rate' => $participationRate,
                ];
            })
            ->sortByDesc('participation_rate')
            ->values();
        $tInterclassRanking = round((microtime(true) - $t5) * 1000, 2);
        $this->info("  [6] Interclass Ranking (25 classes)  : {$tInterclassRanking} ms");

        $this->info('==================================================');
        $this->info('BENCHMARK SUMMARY:');
        $this->info("  Siswa                   : {$actualStudents}");
        $this->info("  Jurnal                  : {$actualJournals}");
        $this->info("  Entries                 : {$actualEntries}");
        $this->info("  XP Logs                 : {$actualXpLogs}");
        $this->info("  Admin Metrics Query     : {$tAdminMetrics} ms");
        $this->info("  Rapor Aggregation       : {$tRaporAggregation} ms");
        $this->info("  Weekly Leaderboard      : {$tWeeklyLeaderboard} ms");
        $this->info("  Monthly Leaderboard     : {$tMonthlyLeaderboard} ms");
        $this->info("  Semester Leaderboard    : {$tSemesterLeaderboard} ms");
        $this->info("  Interclass Comparison   : {$tInterclassRanking} ms");
        $this->info('==================================================');

        // Optional Cleanup if requested
        if ($doCleanup) {
            $this->info('Cleaning up benchmark data...');
            DB::table('xp_logs')->whereIn('user_id', $allBenchStudents->pluck('id'))->delete();
            DB::table('journals')->whereIn('user_id', $allBenchStudents->pluck('id'))->delete();
            DB::table('classroom_student')->whereIn('user_id', $allBenchStudents->pluck('id'))->delete();
            User::whereIn('id', $allBenchStudents->pluck('id'))->delete();
            $this->info('Cleanup complete.');
        }

        return 0;
    }
}

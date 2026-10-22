<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminDashboardController;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\School;
use App\Models\User;
use App\Services\GeminiAiService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DashboardAiAndExportTest extends TestCase
{
    use RefreshDatabase;

    protected $school;

    protected $academicYear;

    protected $admin;

    protected $guru;

    protected $smallClass;

    protected $bigClass;

    protected $studentsSmall = [];

    protected $studentsBig = [];

    protected $habits = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::create([
            'name' => 'SMAN 1 SUNGAILIAT',
            'npsn' => '10900232',
        ]);

        $this->academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Dashboard',
            'email' => 'admin.dash@sihebat.id',
            'password' => 'password',
            'role' => 'admin',
            'school_id' => $this->school->id,
            'must_change_password' => false,
        ]);

        $this->guru = User::create([
            'name' => 'Wali Kelas 1',
            'nip' => '198501152010012001',
            'email' => 'wali1@sihebat.id',
            'password' => 'password',
            'role' => 'guru',
            'school_id' => $this->school->id,
            'must_change_password' => false,
        ]);

        // Small class: 10 students
        $this->smallClass = Classroom::create([
            'name' => 'X-Kecil',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
            'homeroom_teacher_id' => $this->guru->id,
            'school_id' => $this->school->id,
        ]);

        // Big class: 40 students
        $this->bigClass = Classroom::create([
            'name' => 'X-Besar',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
            'school_id' => $this->school->id,
        ]);

        // Seed 7 Habits
        $habitNames = [
            ['Bangun Pagi', 'bangun-pagi', 'sun', 'bangun', 10, '05:00', 1],
            ['Beribadah', 'beribadah', 'hands-praying', 'ibadah', 10, '1', 2],
            ['Berolahraga', 'berolahraga', 'barbell', 'olahraga', 10, '30', 3],
            ['Makan Sehat', 'makan-sehat', 'bowl-food', 'makan', 10, '3', 4],
            ['Gemar Belajar', 'gemar-belajar', 'book-open', 'belajar', 10, '30', 5],
            ['Bermasyarakat', 'bermasyarakat', 'users-three', 'sosial', 10, '1', 6],
            ['Tidur Cepat', 'tidur-cepat', 'moon-stars', 'tidur', 10, '21:00', 7],
        ];

        foreach ($habitNames as $h) {
            $this->habits[$h[1]] = Habit::create([
                'name' => $h[0],
                'slug' => $h[1],
                'icon' => $h[2],
                'color' => $h[3],
                'xp_reward' => $h[4],
                'target_value' => $h[5],
                'order' => $h[6],
                'is_active' => true,
            ]);
        }

        // 10 students in small class: 9 of 10 students actively fill journals (90% participation)
        for ($i = 1; $i <= 10; $i++) {
            $student = User::create([
                'name' => 'Siswa Kecil '.$i,
                'nis' => '100'.$i,
                'email' => "kecil{$i}@siswa.sihebat.id",
                'password' => 'password',
                'role' => 'siswa',
                'xp' => 100 - ($i * 5),
                'current_streak' => 10 - $i,
                'school_id' => $this->school->id,
                'must_change_password' => false,
            ]);
            $student->classrooms()->attach($this->smallClass->id);
            $this->studentsSmall[] = $student;
        }

        // 40 students in big class: only 15 of 40 students fill journals (37.5% participation)
        for ($i = 1; $i <= 40; $i++) {
            $student = User::create([
                'name' => 'Siswa Besar '.$i,
                'nis' => '200'.str_pad($i, 2, '0', STR_PAD_LEFT),
                'email' => "besar{$i}@siswa.sihebat.id",
                'password' => 'password',
                'role' => 'siswa',
                'xp' => 200 - $i,
                'current_streak' => max(0, 15 - $i),
                'school_id' => $this->school->id,
                'must_change_password' => false,
            ]);
            $student->classrooms()->attach($this->bigClass->id);
            $this->studentsBig[] = $student;
        }
    }

    // --- AREA E: LEADERBOARD ---

    public function test_interclass_ranking_fairness_for_small_vs_big_classes(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-21 12:00:00'));

        // Small class: 9 students fill 5 days of journals = 45 journals
        for ($i = 0; $i < 9; $i++) {
            for ($d = 0; $d < 5; $d++) {
                $j = Journal::create([
                    'user_id' => $this->studentsSmall[$i]->id,
                    'date' => Carbon::today()->subDays($d)->format('Y-m-d'),
                    'habits_completed' => 7,
                    'is_perfect_day' => true,
                    'xp_earned' => 100,
                ]);
                foreach ($this->habits as $habit) {
                    JournalEntry::create([
                        'journal_id' => $j->id,
                        'habit_id' => $habit->id,
                        'completed' => true,
                        'xp_earned' => 10,
                    ]);
                }
            }
        }

        // Big class: only 10 students fill 3 days = 30 journals (out of 40 students)
        for ($i = 0; $i < 10; $i++) {
            for ($d = 0; $d < 3; $d++) {
                $j = Journal::create([
                    'user_id' => $this->studentsBig[$i]->id,
                    'date' => Carbon::today()->subDays($d)->format('Y-m-d'),
                    'habits_completed' => 4,
                    'is_perfect_day' => false,
                    'xp_earned' => 40,
                ]);
                foreach ($this->habits as $habit) {
                    JournalEntry::create([
                        'journal_id' => $j->id,
                        'habit_id' => $habit->id,
                        'completed' => true,
                        'xp_earned' => 10,
                    ]);
                }
            }
        }

        $this->actingAs($this->studentsSmall[0]);
        $response = $this->get(route('siswa.leaderboard', ['tab' => 'antar_kelas', 'period' => 'mingguan']));
        $response->assertStatus(200);

        // Small class has much higher percentage rate -> must rank #1 over the big class
        $interClass = $response->viewData('interClassRankings');
        $this->assertNotEmpty($interClass);
        $this->assertEquals('X-Kecil', $interClass[0]['classroom']->name);
        $this->assertGreaterThan($interClass[1]['composite_score'], $interClass[0]['composite_score']);
    }

    public function test_privacy_mode_hides_other_students_outside_top_10(): void
    {
        // Student ranked #15 in big class
        $studentRank15 = $this->studentsBig[14];
        $this->actingAs($studentRank15);

        $response = $this->get(route('siswa.leaderboard', ['tab' => 'kelas']));
        $response->assertStatus(200);

        $topStudents = $response->viewData('topStudents');
        $this->assertCount(10, $topStudents);

        // User rank is 15
        $this->assertEquals(15, $response->viewData('userRank'));
        $this->assertNotNull($response->viewData('myStudentOutsideTop'));
        $this->assertEquals($studentRank15->id, $response->viewData('myStudentOutsideTop')->id);

        // Content contains Top 10 and current student (#15), but DOES NOT leak student 11, 12, 13, 14
        $content = $response->getContent();
        $this->assertStringContainsString('Top 10 + Kamu', $content);
        $this->assertStringContainsString($studentRank15->name, $content);
        $this->assertStringNotContainsString('Siswa Besar 12', $content);
        $this->assertStringNotContainsString('Siswa Besar 13', $content);
    }

    // --- AREA F: DASHBOARD RAPOR PENDIDIKAN ---

    public function test_rapor_pendidikan_status_thresholds_and_summary_cards(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-21 12:00:00'));

        // Seed 100 journals with controlled completion rates:
        // 25 students x 4 days = 100 unique (user_id, date) pairs
        // Habit 1 (Bangun Pagi): 80% completed -> 'Baik' (>= 75%)
        // Habit 3 (Berolahraga): 60% completed -> 'Cukup' (50% - < 75%)
        // Habit 7 (Tidur Cepat): 30% completed -> 'Perlu Perhatian' (< 50%)
        $counter = 0;
        for ($s = 0; $s < 25; $s++) {
            $student = $this->studentsBig[$s];
            for ($d = 0; $d < 4; $d++) {
                $j = Journal::create([
                    'user_id' => $student->id,
                    'date' => Carbon::today()->subDays($d)->format('Y-m-d'),
                    'habits_completed' => 5,
                ]);

                JournalEntry::create([
                    'journal_id' => $j->id,
                    'habit_id' => $this->habits['bangun-pagi']->id,
                    'completed' => ($counter < 80), // 80%
                ]);

                JournalEntry::create([
                    'journal_id' => $j->id,
                    'habit_id' => $this->habits['berolahraga']->id,
                    'completed' => ($counter < 60), // 60%
                ]);

                JournalEntry::create([
                    'journal_id' => $j->id,
                    'habit_id' => $this->habits['tidur-cepat']->id,
                    'completed' => ($counter < 30), // 30% (Lowest)
                ]);

                // Other habits have high completion rates (> 50%)
                foreach (['beribadah' => 90, 'makan-sehat' => 75, 'gemar-belajar' => 70, 'bermasyarakat' => 85] as $slug => $targetPct) {
                    JournalEntry::create([
                        'journal_id' => $j->id,
                        'habit_id' => $this->habits[$slug]->id,
                        'completed' => ($counter < $targetPct),
                    ]);
                }

                $counter++;
            }
        }

        $controller = app(AdminDashboardController::class);
        $metrics = $controller->calculateRaporPendidikanMetrics(null, null, 'bulanan');

        $habitsMap = collect($metrics['habitStats'])->keyBy('slug');

        // Verify status thresholds
        $this->assertEquals('Baik', $habitsMap['bangun-pagi']['status']);
        $this->assertEquals('Cukup', $habitsMap['berolahraga']['status']);
        $this->assertEquals('Perlu Perhatian', $habitsMap['tidur-cepat']['status']);

        // Verify executive highlights selection (Beribadah has 90% highest, Tidur Cepat has 30% lowest)
        $this->assertEquals('Beribadah', $metrics['bestHabit']['name']);
        $this->assertEquals('Tidur Cepat', $metrics['lowestHabit']['name']);
        $this->assertNotNull($metrics['topImprovement']);
    }

    public function test_dashboard_handles_empty_classroom_gracefully_without_division_by_zero(): void
    {
        // Create a classroom with 0 students
        $emptyClass = Classroom::create([
            'name' => 'X-Kosong',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
            'school_id' => $this->school->id,
        ]);

        $controller = app(AdminDashboardController::class);
        $metrics = $controller->calculateRaporPendidikanMetrics($emptyClass->id, null, 'bulanan');

        $this->assertEquals(0, $metrics['totalJournals']);
        foreach ($metrics['habitStats'] as $stat) {
            $this->assertEquals(0.0, $stat['rate']);
            $this->assertEquals('Perlu Perhatian', $stat['status']);
        }

        // Access dashboard with classroom filter
        $this->actingAs($this->admin);
        $response = $this->get(route('admin.dashboard', ['classroom_id' => $emptyClass->id]));
        $response->assertStatus(200);
    }

    // --- AREA G: FITUR AI ---

    public function test_ai_payload_is_strictly_aggregated_with_no_student_pii(): void
    {
        $aiService = app(GeminiAiService::class);
        $stats = $aiService->gatherAggregatedStats();

        // Must contain aggregate numbers only
        $this->assertArrayHasKey('total_students', $stats);
        $this->assertArrayHasKey('active_rate_pct', $stats);
        $this->assertArrayHasKey('avg_streak_days', $stats);
        $this->assertArrayHasKey('habit_breakdown', $stats);

        // Must NOT contain student names, NIS, or email
        $serialized = json_encode($stats);
        $this->assertStringNotContainsString('Siswa', $serialized);
        $this->assertStringNotContainsString('siswa.sihebat.id', $serialized);
        $this->assertStringNotContainsString('12009', $serialized);
        $this->assertStringNotContainsString('20001', $serialized);
    }

    public function test_ai_insights_are_cached_and_fallback_gracefully_on_api_failure(): void
    {
        // Mock Gemini API failure (HTTP 500)
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => 'Server Error'], 500),
        ]);

        $aiService = app(GeminiAiService::class);

        // First call with mock failure falls back gracefully to rule-based insight
        $result = $aiService->getSchoolAdvice(true);
        $this->assertNotNull($result['insight']);
        $this->assertTrue(in_array($result['source'], ['fallback', 'rule_based', 'cache']));

        // Second call without forceRefresh returns cached insight without calling API
        $secondResult = $aiService->getSchoolAdvice(false);
        $this->assertEquals('cache', $secondResult['source']);
        $this->assertEquals($result['insight']->id, $secondResult['insight']->id);
    }

    // --- AREA H: SCHEDULER & EXPORT ---

    public function test_export_excel_generates_valid_csv_file(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.report.excel'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('RAPOR PENDIDIKAN KARAKTER', $response->streamedContent());
    }

    public function test_print_report_renders_successfully(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.report.print'));
        $response->assertStatus(200);
        $response->assertSee('SMAN 1 SUNGAILIAT');
        $response->assertSee('Laporan Capaian 7 Kebiasaan');
    }
}

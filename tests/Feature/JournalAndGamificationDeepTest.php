<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Badge;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\School;
use App\Models\User;
use App\Services\GamificationService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalAndGamificationDeepTest extends TestCase
{
    use RefreshDatabase;

    protected $school;

    protected $academicYear;

    protected $student;

    protected $classroom;

    protected $habits;

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

        $this->classroom = Classroom::create([
            'name' => 'X-1',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
            'school_id' => $this->school->id,
        ]);

        $this->student = User::create([
            'name' => 'Siswa Tester',
            'nis' => '12009',
            'email' => 'tester@siswa.sihebat.id',
            'password' => 'password',
            'role' => 'siswa',
            'school_id' => $this->school->id,
            'must_change_password' => false,
            'streak_freezes' => 1,
        ]);
        $this->student->classrooms()->attach($this->classroom->id);

        $habitsData = [
            ['name' => 'Bangun Pagi', 'slug' => 'bangun-pagi', 'icon' => 'sun', 'color' => 'bangun', 'xp_reward' => 10, 'target_value' => '05:00', 'order' => 1],
            ['name' => 'Beribadah', 'slug' => 'beribadah', 'icon' => 'hands-praying', 'color' => 'ibadah', 'xp_reward' => 10, 'target_value' => '1', 'order' => 2],
            ['name' => 'Berolahraga', 'slug' => 'berolahraga', 'icon' => 'barbell', 'color' => 'olahraga', 'xp_reward' => 10, 'target_value' => '30', 'order' => 3],
            ['name' => 'Makan Sehat', 'slug' => 'makan-sehat', 'icon' => 'bowl-food', 'color' => 'makan', 'xp_reward' => 10, 'target_value' => '3', 'order' => 4],
            ['name' => 'Gemar Belajar', 'slug' => 'gemar-belajar', 'icon' => 'book-open', 'color' => 'belajar', 'xp_reward' => 10, 'target_value' => '30', 'order' => 5],
            ['name' => 'Bermasyarakat', 'slug' => 'bermasyarakat', 'icon' => 'users-three', 'color' => 'sosial', 'xp_reward' => 10, 'target_value' => '1', 'order' => 6],
            ['name' => 'Tidur Cepat', 'slug' => 'tidur-cepat', 'icon' => 'moon-stars', 'color' => 'tidur', 'xp_reward' => 10, 'target_value' => '21:00', 'order' => 7],
        ];

        $this->habits = [];
        foreach ($habitsData as $hd) {
            $this->habits[$hd['slug']] = Habit::create($hd);
        }

        Badge::create([
            'name' => 'Hari Sempurna',
            'slug' => 'perfect-day',
            'description' => 'Selesaikan 7 kebiasaan',
            'icon' => 'sparkle',
            'color' => 'sun',
            'requirement_type' => 'perfect_day',
            'requirement_value' => 1,
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_database_level_unique_constraint_prevents_duplicate_journal_per_day(): void
    {
        Journal::create([
            'user_id' => $this->student->id,
            'date' => '2026-10-21',
            'habits_completed' => 5,
        ]);

        $this->expectException(QueryException::class);

        // Attempt second insert with same user_id and date
        Journal::create([
            'user_id' => $this->student->id,
            'date' => '2026-10-21',
            'habits_completed' => 7,
        ]);
    }

    public function test_only_today_and_yesterday_can_be_filled(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-21 10:00:00'));
        $this->actingAs($this->student);

        $hId = $this->habits['bangun-pagi']->id;

        // 1. Fill Today (2026-10-21) -> Allowed
        $response = $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            "habit_{$hId}_time" => '04:30',
        ]);
        $response->assertRedirect(route('siswa.journal.success'));

        // 2. Fill Yesterday (2026-10-20) -> Allowed
        $response = $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-20',
            "habit_{$hId}_time" => '04:45',
        ]);
        $response->assertRedirect(route('siswa.journal.success'));

        // 3. Fill H-2 (2026-10-19) -> Rejected
        $response = $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-19',
            "habit_{$hId}_time" => '04:30',
        ]);
        $response->assertSessionHasErrors(['date']);

        // 4. Fill Future date (2026-10-22) -> Rejected
        $response = $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-22',
            "habit_{$hId}_time" => '04:30',
        ]);
        $response->assertSessionHasErrors(['date']);
    }

    public function test_day_transition_at_2359_and_0001_wib(): void
    {
        $this->actingAs($this->student);
        $hId = $this->habits['bangun-pagi']->id;

        // At 23:59:50 WIB on Oct 21
        Carbon::setTestNow(Carbon::parse('2026-10-21 23:59:50', 'Asia/Jakarta'));

        // Can fill Oct 21 (Today)
        $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            "habit_{$hId}_time" => '04:30',
        ])->assertRedirect(route('siswa.journal.success'));

        // Advance by 25 seconds to 00:00:15 WIB on Oct 22
        Carbon::setTestNow(Carbon::parse('2026-10-22 00:00:15', 'Asia/Jakarta'));

        // Oct 21 is now H-1 -> still allowed!
        $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            "habit_{$hId}_time" => '04:35',
        ])->assertRedirect(route('siswa.journal.success'));

        // Oct 20 is now H-2 -> rejected!
        $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-20',
            "habit_{$hId}_time" => '04:30',
        ])->assertSessionHasErrors(['date']);
    }

    public function test_habit_input_validation_rules(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-21 12:00:00'));
        $this->actingAs($this->student);

        $wakeId = $this->habits['bangun-pagi']->id;
        $sportId = $this->habits['berolahraga']->id;
        $sleepId = $this->habits['tidur-cepat']->id;

        // 1. Invalid time format
        $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            "habit_{$wakeId}_time" => '25:99',
        ])->assertSessionHasErrors(['time']);

        // 2. Negative duration
        $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            "habit_{$sportId}_duration" => -15,
        ])->assertSessionHasErrors(['duration']);

        // 3. Absurd duration (1000 minutes)
        $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            "habit_{$sportId}_duration" => 1000,
        ])->assertSessionHasErrors(['duration']);

        // 4. Invalid mood
        $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            'mood' => 'super_angry_unknown',
        ])->assertSessionHasErrors(['mood']);
    }

    public function test_editing_journal_on_same_day_does_not_double_count_xp(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-21 14:00:00'));
        $this->actingAs($this->student);

        $sportId = $this->habits['berolahraga']->id;
        $studyId = $this->habits['gemar-belajar']->id;

        // First submission: 2 habits completed = +20 XP
        $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            "habit_{$sportId}_type" => 'Futsal',
            "habit_{$sportId}_duration" => 35,
            "habit_{$studyId}_duration" => 35,
            "habit_{$studyId}_topic" => 'Biologi',
        ]);

        $this->student->refresh();
        $this->assertEquals(20, $this->student->xp);
        $this->assertEquals(1, $this->student->current_streak);

        // Edit same day: add 1 more habit (Bangun Pagi) -> now 3 habits = +30 XP total
        $wakeId = $this->habits['bangun-pagi']->id;
        $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            "habit_{$wakeId}_time" => '04:30',
            "habit_{$sportId}_type" => 'Futsal',
            "habit_{$sportId}_duration" => 35,
            "habit_{$studyId}_duration" => 35,
            "habit_{$studyId}_topic" => 'Biologi',
        ]);

        $this->student->refresh();
        // Total user XP should be exactly 30, NOT 20 + 30 = 50!
        $this->assertEquals(30, $this->student->xp);
        // Streak remains 1
        $this->assertEquals(1, $this->student->current_streak);
    }

    public function test_habit_threshold_configuration_by_admin_affects_completion(): void
    {
        $service = app(GamificationService::class);
        $sportHabit = $this->habits['berolahraga'];

        // Default threshold is 30 mins
        $this->assertEquals('30', $sportHabit->target_value);

        // 25 mins is below threshold -> not completed
        $result = $service->evaluateHabit($sportHabit, ['type' => 'Lari', 'duration' => 25]);
        $this->assertFalse($result['completed']);

        // 35 mins is above threshold -> completed
        $result = $service->evaluateHabit($sportHabit, ['type' => 'Lari', 'duration' => 35]);
        $this->assertTrue($result['completed']);

        // Admin changes threshold to 40 mins
        $sportHabit->update(['target_value' => '40']);
        $sportHabit->refresh();

        // Now 35 mins is below new threshold (40) -> not completed!
        $result = $service->evaluateHabit($sportHabit, ['type' => 'Lari', 'duration' => 35]);
        $this->assertFalse($result['completed']);

        // 45 mins is completed
        $result = $service->evaluateHabit($sportHabit, ['type' => 'Lari', 'duration' => 45]);
        $this->assertTrue($result['completed']);
    }

    public function test_draft_saving_and_final_submission_work_separately(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-21 15:00:00'));
        $this->actingAs($this->student);

        $sportId = $this->habits['berolahraga']->id;

        // 1. Save as Draft
        $response = $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            'action' => 'draft',
            "habit_{$sportId}_type" => 'Renang',
            "habit_{$sportId}_duration" => 40,
        ]);

        $response->assertRedirect(route('siswa.journal', ['date' => '2026-10-21']));

        $journal = Journal::where('user_id', $this->student->id)->where('date', '2026-10-21')->first();
        $this->assertNotNull($journal);
        $this->assertTrue((bool) $journal->is_draft);

        // User XP should remain 0 when saving draft
        $this->student->refresh();
        $this->assertEquals(0, $this->student->xp);
        $this->assertEquals(0, $this->student->current_streak);

        // 2. Final Submission
        $response = $this->post(route('siswa.journal.save'), [
            'date' => '2026-10-21',
            "habit_{$sportId}_type" => 'Renang',
            "habit_{$sportId}_duration" => 40,
        ]);

        $response->assertRedirect(route('siswa.journal.success'));

        $journal->refresh();
        $this->assertFalse((bool) $journal->is_draft);
        $this->student->refresh();
        $this->assertEquals(10, $this->student->xp);
        $this->assertEquals(1, $this->student->current_streak);
    }

    public function test_streak_progression_freeze_activation_and_reset(): void
    {
        $service = app(GamificationService::class);

        // Day 1: Monday Oct 19
        $day1 = Carbon::parse('2026-10-19 10:00:00');
        $service->processJournal($this->student, $day1, [
            $this->habits['beribadah']->id => ['done' => true],
        ]);
        $this->student->refresh();
        $this->assertEquals(1, $this->student->current_streak);

        // Day 2: Tuesday Oct 20
        $day2 = Carbon::parse('2026-10-20 10:00:00');
        $service->processJournal($this->student, $day2, [
            $this->habits['beribadah']->id => ['done' => true],
        ]);
        $this->student->refresh();
        $this->assertEquals(2, $this->student->current_streak);

        // Day 3 (Wednesday Oct 21) was missed!
        // Day 4 (Thursday Oct 22): Streak Freeze activates!
        $day4 = Carbon::parse('2026-10-22 10:00:00');
        $result = $service->processJournal($this->student, $day4, [
            $this->habits['beribadah']->id => ['done' => true],
        ]);

        $this->assertTrue($result['freeze_used']);
        $this->student->refresh();
        $this->assertEquals(3, $this->student->current_streak);
        $this->assertEquals(0, $this->student->streak_freezes);

        // Day 5 (Friday Oct 23) was missed!
        // Day 6 (Saturday Oct 24): Streak freeze already used in same week -> streak breaks and resets to 1!
        $day6 = Carbon::parse('2026-10-24 10:00:00');
        $result = $service->processJournal($this->student, $day6, [
            $this->habits['beribadah']->id => ['done' => true],
        ]);

        $this->assertFalse($result['freeze_used']);
        $this->student->refresh();
        $this->assertEquals(1, $this->student->current_streak);
    }
}

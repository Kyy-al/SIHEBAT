<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\User;
use App\Models\XpLog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificationAndReportingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        User::where('role', 'siswa')->update(['must_change_password' => false]);
    }

    public function test_streak_freeze_protects_streak_once_a_week(): void
    {
        $student = User::where('role', 'siswa')->first();
        $student->update([
            'current_streak' => 10,
            'longest_streak' => 10,
            'last_journal_date' => Carbon::today()->subDays(2), // Missed 1 day (yesterday)
            'streak_freeze_used_at' => null,
        ]);

        $habits = Habit::where('is_active', true)->get();
        $payload = [
            'journal_date' => Carbon::today()->format('Y-m-d'),
            'mood' => 'senang',
        ];

        foreach ($habits as $habit) {
            $prefix = 'habit_'.$habit->id;
            switch ($habit->slug) {
                case 'bangun-pagi':
                    $payload[$prefix.'_time'] = '05:00';
                    break;
                case 'beribadah':
                    $payload[$prefix.'_checklist'] = ['subuh'];
                    break;
                case 'berolahraga':
                    $payload[$prefix.'_type'] = 'Lari';
                    $payload[$prefix.'_duration'] = '30';
                    break;
                case 'makan-sehat':
                    $payload[$prefix.'_menus'] = ['sarapan_sehat'];
                    break;
                case 'gemar-belajar':
                    $payload[$prefix.'_duration'] = '45';
                    $payload[$prefix.'_topic'] = 'Matematika';
                    break;
                case 'bermasyarakat':
                    $payload[$prefix.'_activities'] = ['gotong_royong'];
                    break;
                case 'tidur-cepat':
                    $payload[$prefix.'_time'] = '21:30';
                    break;
            }
        }

        $response = $this->actingAs($student)->post('/siswa/jurnal', $payload);
        $response->assertRedirect('/siswa/jurnal/sukses');

        $student->refresh();
        // Streak freeze protected the streak: was 10, protected and incremented to 11
        $this->assertEquals(11, $student->current_streak);
        $this->assertNotNull($student->streak_freeze_used_at);
        $this->assertEquals(Carbon::today()->format('Y-m-d'), $student->streak_freeze_used_at->format('Y-m-d'));
    }

    public function test_streak_resets_if_streak_freeze_was_already_used_in_the_same_week(): void
    {
        $student = User::where('role', 'siswa')->first();
        $student->update([
            'current_streak' => 15,
            'last_journal_date' => Carbon::today()->subDays(2), // Missed 1 day
            'streak_freeze_used_at' => Carbon::today()->startOfWeek(Carbon::MONDAY), // Already used this week
        ]);

        $habits = Habit::where('is_active', true)->get();
        $payload = [
            'journal_date' => Carbon::today()->format('Y-m-d'),
        ];
        foreach ($habits as $habit) {
            $payload['habit_'.$habit->id.'_time'] = '05:00';
            $payload['habit_'.$habit->id.'_checklist'] = ['subuh'];
            $payload['habit_'.$habit->id.'_type'] = 'Lari';
            $payload['habit_'.$habit->id.'_duration'] = '30';
            $payload['habit_'.$habit->id.'_menus'] = ['sarapan_sehat'];
            $payload['habit_'.$habit->id.'_topic'] = 'Fisika';
            $payload['habit_'.$habit->id.'_activities'] = ['gotong_royong'];
        }

        $this->actingAs($student)->post('/siswa/jurnal', $payload);

        $student->refresh();
        // Streak reset to 1 because freeze was already exhausted this week
        $this->assertEquals(1, $student->current_streak);
    }

    public function test_multi_7_streak_bonus_awards_extra_xp(): void
    {
        $student = User::where('role', 'siswa')->first();
        $student->update([
            'current_streak' => 6, // Reaching 7 today
            'last_journal_date' => Carbon::yesterday(),
            'xp' => 100,
        ]);

        $habits = Habit::where('is_active', true)->get();
        $payload = ['journal_date' => Carbon::today()->format('Y-m-d')];
        foreach ($habits as $habit) {
            $payload['habit_'.$habit->id.'_time'] = '05:00';
            $payload['habit_'.$habit->id.'_checklist'] = ['subuh'];
            $payload['habit_'.$habit->id.'_type'] = 'Lari';
            $payload['habit_'.$habit->id.'_duration'] = '30';
            $payload['habit_'.$habit->id.'_menus'] = ['sarapan_sehat'];
            $payload['habit_'.$habit->id.'_topic'] = 'Fisika';
            $payload['habit_'.$habit->id.'_activities'] = ['gotong_royong'];
        }

        $this->actingAs($student)->post('/siswa/jurnal', $payload);

        $student->refresh();
        $this->assertEquals(7, $student->current_streak);

        // Verify streak bonus XP log (+5 XP)
        $streakBonusLog = XpLog::where('user_id', $student->id)
            ->where('source', 'streak_bonus')
            ->first();

        $this->assertNotNull($streakBonusLog);
        $this->assertEquals(5, $streakBonusLog->amount);
    }

    public function test_student_can_fill_h_minus_1_backfill(): void
    {
        $student = User::where('role', 'siswa')->first();
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        // Delete any existing journal for yesterday
        Journal::where('user_id', $student->id)->where('date', $yesterday)->delete();

        $habits = Habit::where('is_active', true)->get();
        $payload = ['journal_date' => $yesterday];
        foreach ($habits as $habit) {
            $payload['habit_'.$habit->id.'_time'] = '05:00';
            $payload['habit_'.$habit->id.'_checklist'] = ['subuh'];
            $payload['habit_'.$habit->id.'_type'] = 'Lari';
            $payload['habit_'.$habit->id.'_duration'] = '30';
            $payload['habit_'.$habit->id.'_menus'] = ['sarapan_sehat'];
            $payload['habit_'.$habit->id.'_topic'] = 'Fisika';
            $payload['habit_'.$habit->id.'_activities'] = ['gotong_royong'];
        }

        $response = $this->actingAs($student)->post('/siswa/jurnal', $payload);
        $response->assertRedirect('/siswa/jurnal/sukses');

        $journal = Journal::where('user_id', $student->id)->whereDate('date', $yesterday)->first();
        $this->assertNotNull($journal);
    }

    public function test_student_cannot_fill_older_than_h_minus_1(): void
    {
        $student = User::where('role', 'siswa')->first();
        $twoDaysAgo = Carbon::today()->subDays(2)->format('Y-m-d');

        $payload = ['journal_date' => $twoDaysAgo];
        $response = $this->actingAs($student)->post('/siswa/jurnal', $payload);

        $response->assertSessionHasErrors(['journal_date']);
    }

    public function test_student_rapor_page_renders_heatmap_and_stats(): void
    {
        $student = User::where('role', 'siswa')->first();

        $response = $this->actingAs($student)->get('/siswa/rapor');
        $response->assertStatus(200);
        $response->assertSee('Rapor Kebiasaan');
        $response->assertSee('Kalender Partisipasi');
    }

    public function test_admin_can_view_official_print_report(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/laporan/cetak');
        $response->assertStatus(200);
        $response->assertSee('SMAN 1 SUNGAILIAT');
        $response->assertSee('Laporan Capaian 7 Kebiasaan Anak Indonesia Hebat');
        $response->assertSee('Pemerintah Provinsi Kepulauan Bangka Belitung');
    }

    public function test_admin_can_view_akar_masalah_page(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/akar-masalah?habit=tidur-cepat');
        $response->assertStatus(200);
        $response->assertSee('Analisis Akar Masalah');
        $response->assertSee('Tidur Cepat');
        $response->assertSee('Capaian Per Kelas');
    }

    public function test_admin_can_ask_tanya_data_ai(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->postJson('/admin/tanya-ai', [
            'question' => 'Kebiasaan apa yang paling rendah minggu ini?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'question',
            'answer',
            'source',
        ]);
        $this->assertEquals('success', $response->json('status'));
        $this->assertNotEmpty($response->json('answer'));
    }

    public function test_guru_can_view_classroom_print_report(): void
    {
        $guru = User::where('role', 'guru')->first();
        $classroom = Classroom::where('homeroom_teacher_id', $guru->id)->first();

        if (! $classroom) {
            $classroom = Classroom::first();
            $classroom->update(['homeroom_teacher_id' => $guru->id]);
        }

        $response = $this->actingAs($guru)->get('/guru/laporan/cetak/'.$classroom->id);
        $response->assertStatus(200);
        $response->assertSee('Laporan Capaian Karakter Siswa');
        $response->assertSee($classroom->name);
    }

    public function test_guru_can_ask_tanya_data_ai(): void
    {
        $guru = User::where('role', 'guru')->first();

        $response = $this->actingAs($guru)->postJson('/guru/tanya-ai', [
            'question' => 'Berapa persen siswa yang aktif mengisi jurnal hari ini?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'question',
            'answer',
            'source',
        ]);
        $this->assertEquals('success', $response->json('status'));
        $this->assertNotEmpty($response->json('answer'));
    }

    public function test_streak_mascot_evolution_and_achievement_mascot_rendering(): void
    {
        $student = User::where('role', 'siswa')->first();

        // 1. Test Streak 0 (Padam)
        $student->update(['current_streak' => 0]);
        $response0 = $this->actingAs($student)->get(route('siswa.dashboard'));
        $response0->assertStatus(200);
        $response0->assertSee('Api Padam');
        $response0->assertSee('Peta Evolusi Maskot Streak');
        $response0->assertSee('PENGINGAT JURNAL HARI INI');

        // 2. Test Streak 3 (Membara - Duolingo milestone)
        $student->update(['current_streak' => 3]);
        $response3 = $this->actingAs($student)->get(route('siswa.dashboard'));
        $response3->assertStatus(200);
        $response3->assertSee('Api Membara');

        // 3. Test Streak 7 (Ksatria Api)
        $student->update(['current_streak' => 7]);
        $response7 = $this->actingAs($student)->get(route('siswa.dashboard'));
        $response7->assertStatus(200);
        $response7->assertSee('Ksatria Api');

        // 4. Test Profile shows mascot achievements
        $profileResponse = $this->actingAs($student)->get(route('siswa.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Koleksi Lencana Penghargaan');
        $profileResponse->assertSee('achvCloud_'); // Unique defs ID in mascot-achievement component
    }

    public function test_journal_reminder_notification_displays_when_unfilled_and_hides_when_completed(): void
    {
        $student = User::where('role', 'siswa')->first();
        $today = Carbon::today()->format('Y-m-d');

        // Ensure no journal for today
        Journal::where('user_id', $student->id)->where('date', $today)->delete();

        // Unfilled: Reminder is visible
        $responseUnfilled = $this->actingAs($student)->get(route('siswa.dashboard'));
        $responseUnfilled->assertStatus(200);
        $responseUnfilled->assertSee('PENGINGAT JURNAL HARI INI');
        $responseUnfilled->assertSee('Jangan Lupa Isi Jurnal 7 Kebiasaan Hebatmu Hari Ini!');
        $responseUnfilled->assertSee('Isi Jurnal Sekarang');

        // Complete journal for today (7/7 perfect day)
        $journal = Journal::create([
            'user_id' => $student->id,
            'date' => $today,
            'mood' => 'senang',
            'habits_completed' => 7,
            'is_perfect_day' => true,
            'xp_earned' => 80,
        ]);

        // Completed: Reminder banner is hidden and celebration headline shown
        $responseCompleted = $this->actingAs($student)->get(route('siswa.dashboard'));
        $responseCompleted->assertStatus(200);
        $responseCompleted->assertDontSee('PENGINGAT JURNAL HARI INI');
        $responseCompleted->assertSee('Hebat, Jurnalmu Sudah Tercatat!');
    }
}

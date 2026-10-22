<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SiHebatPhase1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SiHebat');
        $response->assertSee('NIS / NIP');
    }

    public function test_student_can_login_with_nis(): void
    {
        $response = $this->post('/login', [
            'identifier' => '20261001',
            'password' => 'password',
        ]);

        $response->assertRedirect('/siswa/beranda');
        $this->assertAuthenticated();
    }

    public function test_student_must_change_password_on_first_login(): void
    {
        // Dewi Lestari has must_change_password = true
        $dewi = User::where('nis', '20261005')->first();

        $response = $this->actingAs($dewi)->get('/siswa/beranda');

        $response->assertRedirect('/ganti-sandi');

        // Now submit new password
        $changeResponse = $this->actingAs($dewi)->post('/ganti-sandi', [
            'password' => 'passwordBaru123',
            'password_confirmation' => 'passwordBaru123',
        ]);

        $changeResponse->assertRedirect('/siswa/beranda');
        $this->assertFalse($dewi->fresh()->must_change_password);
    }

    public function test_teacher_can_login_with_nip_and_access_guru_dashboard(): void
    {
        $response = $this->post('/login', [
            'identifier' => '198501152010012001',
            'password' => 'password',
        ]);

        $response->assertRedirect('/guru/dashboard');
        $this->assertAuthenticated();

        $teacherResponse = $this->get('/guru/dashboard');
        $teacherResponse->assertStatus(200);
        $teacherResponse->assertSee('Wali Kelas');
    }

    public function test_admin_can_login_and_access_admin_dashboard(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'admin@sihebat.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();

        $adminResponse = $this->get('/admin/dashboard');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Panel Admin');
    }

    public function test_student_cannot_access_admin_or_guru_routes(): void
    {
        $student = User::where('nis', '20261001')->first();

        // Must return 403 Forbidden without redirect leakage
        $this->actingAs($student)->get('/admin/dashboard')
            ->assertStatus(403);

        $this->actingAs($student)->get('/guru/dashboard')
            ->assertStatus(403);
    }

    public function test_student_can_fill_journal_and_earn_xp_and_streak(): void
    {
        $student = User::where('nis', '20261001')->first();
        $initialXp = $student->xp;
        $initialStreak = $student->current_streak;

        $habits = Habit::all();
        $payload = [
            'mood' => 'sangat_senang',
            'notes' => 'Hari ini saya sangat bersemangat menyelesaikan semua kebiasaan!',
        ];

        foreach ($habits as $h) {
            $prefix = 'habit_'.$h->id;
            switch ($h->slug) {
                case 'bangun-pagi':
                    $payload[$prefix.'_time'] = '04:45';
                    break;
                case 'beribadah':
                    $payload[$prefix.'_done'] = '1';
                    $payload[$prefix.'_types'] = ['Subuh', 'Dzuhur', 'Ashar'];
                    break;
                case 'berolahraga':
                    $payload[$prefix.'_type'] = 'Lari Pagi';
                    $payload[$prefix.'_duration'] = '35';
                    break;
                case 'makan-sehat':
                    $payload[$prefix.'_items'] = ['sarapan', 'siang', 'sayur_buah'];
                    break;
                case 'gemar-belajar':
                    $payload[$prefix.'_duration'] = '45';
                    $payload[$prefix.'_topic'] = 'Fisika Kinematika';
                    break;
                case 'bermasyarakat':
                    $payload[$prefix.'_activities'] = ['gotong_royong'];
                    break;
                case 'tidur-cepat':
                    $payload[$prefix.'_time'] = '21:00';
                    break;
            }
        }

        $response = $this->actingAs($student)->post('/siswa/jurnal', $payload);

        $response->assertRedirect('/siswa/jurnal/sukses');

        $journal = Journal::where('user_id', $student->id)->whereDate('date', Carbon::today())->first();
        $this->assertNotNull($journal);
        $this->assertEquals(7, $journal->habits_completed);
        $this->assertTrue($journal->is_perfect_day);

        // 7 * 10 XP + 30 Perfect day bonus = 100 XP
        $this->assertEquals(100, $journal->xp_earned);
        // Student XP gains journal XP (+100) + completed weekly mission reward (+40) = +140 XP
        $this->assertEquals($initialXp + 140, $student->fresh()->xp);

        // Consecutive day streak (yesterday was logged in seeder)
        $this->assertEquals($initialStreak + 1, $student->fresh()->current_streak);
    }

    public function test_admin_can_create_student_with_random_initial_password(): void
    {
        $admin = User::where('role', 'admin')->first();
        $classX1 = Classroom::where('name', 'X-1')->first();

        $response = $this->actingAs($admin)->post('/admin/siswa', [
            'name' => 'Fajar Nugraha',
            'nis' => '20261099',
            'classroom_id' => $classX1->id,
        ]);

        $response->assertSessionHas('success');

        $newStudent = User::where('nis', '20261099')->first();
        $this->assertNotNull($newStudent);
        $this->assertEquals('Fajar Nugraha', $newStudent->name);
        $this->assertTrue((bool) $newStudent->must_change_password);
        $this->assertNotEmpty($newStudent->initial_password);
        $this->assertTrue(Hash::check($newStudent->initial_password, $newStudent->password));
        $this->assertFalse(Hash::check('20261099', $newStudent->password), 'Password awal tidak boleh sama dengan NIS');
    }
}

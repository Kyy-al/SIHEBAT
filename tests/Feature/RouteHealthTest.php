<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Badge;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteHealthTest extends TestCase
{
    use RefreshDatabase;

    protected $school;

    protected $academicYear;

    protected $admin;

    protected $guru;

    protected $siswa;

    protected $classroom;

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
            'name' => 'Admin Test',
            'email' => 'admin.test@sihebat.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'school_id' => $this->school->id,
            'must_change_password' => false,
        ]);

        $this->guru = User::create([
            'name' => 'Guru Test',
            'nip' => '198501152010012001',
            'email' => 'guru.test@sihebat.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'school_id' => $this->school->id,
            'must_change_password' => false,
        ]);

        $this->classroom = Classroom::create([
            'name' => 'X-1',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
            'homeroom_teacher_id' => $this->guru->id,
            'school_id' => $this->school->id,
        ]);

        $this->siswa = User::create([
            'name' => 'Siswa Test',
            'nis' => '12009',
            'nisn' => '0118102423',
            'email' => 'siswa.test@sihebat.id',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'school_id' => $this->school->id,
            'must_change_password' => false,
        ]);
        $this->siswa->classrooms()->attach($this->classroom->id);

        Habit::create([
            'name' => 'Bangun Pagi',
            'slug' => 'bangun-pagi',
            'icon' => 'sun',
            'color' => 'bangun',
            'xp_reward' => 10,
            'order' => 1,
            'is_active' => true,
        ]);

        Badge::create([
            'name' => 'Penjaga Api',
            'slug' => 'streak-7',
            'description' => 'Streak 7 hari',
            'icon' => 'fire',
            'color' => 'coral',
            'requirement_type' => 'streak',
            'requirement_value' => 7,
            'is_active' => true,
        ]);
    }

    public function test_guest_can_access_login_and_redirected_from_protected_routes(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);

        $response = $this->get(route('siswa.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('guru.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_can_access_all_siswa_get_routes_without_error(): void
    {
        $this->actingAs($this->siswa);

        $routes = [
            route('siswa.dashboard'),
            route('siswa.journal'),
            route('siswa.leaderboard'),
            route('siswa.rapor'),
            route('siswa.profile'),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }

        // Journal success without session redirects to dashboard
        $this->get(route('siswa.journal.success'))->assertRedirect(route('siswa.dashboard'));

        // Journal success with session renders 200
        $this->withSession(['journal_result' => ['xp_earned' => 100, 'current_streak' => 5, 'is_perfect_day' => true]])
            ->get(route('siswa.journal.success'))
            ->assertStatus(200);
    }

    public function test_guru_can_access_all_guru_get_routes_without_error(): void
    {
        $this->actingAs($this->guru);

        $response = $this->get(route('guru.dashboard'));
        $response->assertStatus(200);

        $response = $this->get(route('guru.classroom', $this->classroom));
        $response->assertStatus(200);

        $response = $this->get(route('guru.report.print', $this->classroom));
        $response->assertStatus(200);
    }

    public function test_admin_can_access_all_admin_get_routes_without_error(): void
    {
        $this->actingAs($this->admin);

        $routes = [
            route('admin.dashboard'),
            route('admin.leaderboard'),
            route('admin.akar-masalah'),
            route('admin.report.print'),
            route('admin.report.excel'),
            route('admin.students'),
            route('admin.students.template'),
            route('admin.teachers'),
            route('admin.classrooms'),
            route('admin.academic-years'),
            route('admin.achievements'),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }
    }
}

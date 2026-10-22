<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class AuthAndAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $school;

    protected $academicYear;

    protected $admin;

    protected $guru1;

    protected $guru2;

    protected $classroom1;

    protected $classroom2;

    protected $student1;

    protected $student2;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('12009|127.0.0.1');

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
            'name' => 'Admin SiHebat',
            'email' => 'admin@sihebat.id',
            'password' => 'password',
            'role' => 'admin',
            'school_id' => $this->school->id,
            'must_change_password' => false,
        ]);

        $this->guru1 = User::create([
            'name' => 'Bu Ani Suryani',
            'nip' => '198501152010012001',
            'email' => 'bu.ani@sihebat.id',
            'password' => 'password',
            'role' => 'guru',
            'school_id' => $this->school->id,
            'must_change_password' => false,
        ]);

        $this->guru2 = User::create([
            'name' => 'Pak Budi Santoso',
            'nip' => '198203202008011003',
            'email' => 'pak.budi@sihebat.id',
            'password' => 'password',
            'role' => 'guru',
            'school_id' => $this->school->id,
            'must_change_password' => false,
        ]);

        $this->classroom1 = Classroom::create([
            'name' => 'X-1',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
            'homeroom_teacher_id' => $this->guru1->id,
            'school_id' => $this->school->id,
        ]);

        $this->classroom2 = Classroom::create([
            'name' => 'X-2',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
            'homeroom_teacher_id' => $this->guru2->id,
            'school_id' => $this->school->id,
        ]);

        $this->student1 = User::create([
            'name' => 'Ahmad Reza',
            'nis' => '12009',
            'nisn' => '0118102423',
            'email' => 'reza@siswa.sihebat.id',
            'password' => 'password',
            'role' => 'siswa',
            'school_id' => $this->school->id,
            'must_change_password' => false,
        ]);
        $this->student1->classrooms()->attach($this->classroom1->id);

        $this->student2 = User::create([
            'name' => 'Dewi Lestari',
            'nis' => '20261005',
            'nisn' => '0118102428',
            'email' => 'dewi@siswa.sihebat.id',
            'password' => '20261005',
            'role' => 'siswa',
            'school_id' => $this->school->id,
            'must_change_password' => true,
        ]);
        $this->student2->classrooms()->attach($this->classroom2->id);

        Habit::create([
            'name' => 'Bangun Pagi',
            'slug' => 'bangun-pagi',
            'icon' => 'sun',
            'color' => 'bangun',
            'xp_reward' => 10,
            'order' => 1,
            'is_active' => true,
        ]);
    }

    // --- AREA A: AUTENTIKASI ---

    public function test_student_login_with_correct_and_incorrect_credentials(): void
    {
        // 1. Correct credentials -> redirects to student dashboard
        $response = $this->post(route('login.submit'), [
            'identifier' => '12009',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('siswa.dashboard'));
        $this->assertAuthenticatedAs($this->student1);

        $this->post(route('logout'));
        $this->assertGuest();

        // 2. Incorrect password -> error without leaking which is wrong
        $response = $this->from(route('login'))->post(route('login.submit'), [
            'identifier' => '12009',
            'password' => 'salah_password',
        ]);
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['identifier']);
        $this->assertStringContainsString('belum cocok', session('errors')->first('identifier'));

        // 3. Unregistered NIS -> same friendly error (no account enumeration)
        $response = $this->from(route('login'))->post(route('login.submit'), [
            'identifier' => '9999999',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['identifier']);
        $this->assertStringContainsString('belum cocok', session('errors')->first('identifier'));
    }

    public function test_student_must_change_password_on_first_login_and_is_blocked_from_other_pages(): void
    {
        $this->actingAs($this->student2);

        // Attempting to access dashboard, journal, leaderboard, profile -> all redirected to /ganti-sandi
        $this->get(route('siswa.dashboard'))->assertRedirect(route('password.change'));
        $this->get(route('siswa.journal'))->assertRedirect(route('password.change'));
        $this->get(route('siswa.leaderboard'))->assertRedirect(route('password.change'));
        $this->get(route('siswa.profile'))->assertRedirect(route('password.change'));

        // Can access /ganti-sandi
        $this->get(route('password.change'))->assertStatus(200);

        // Can access logout
        $this->post(route('logout'))->assertRedirect(route('login'));
    }

    public function test_change_password_validation_rules(): void
    {
        $this->actingAs($this->student2);

        // 1. Cannot be equal to NIS
        $response = $this->from(route('password.change'))->post(route('password.update'), [
            'password' => '20261005',
            'password_confirmation' => '20261005',
        ]);
        $response->assertRedirect(route('password.change'));
        $response->assertSessionHasErrors(['password']);

        // 2. Cannot be equal to old password
        $response = $this->from(route('password.change'))->post(route('password.update'), [
            'password' => '20261005',
            'password_confirmation' => '20261005',
        ]);
        $response->assertSessionHasErrors(['password']);

        // 3. Must be at least 8 chars and confirmed
        $response = $this->from(route('password.change'))->post(route('password.update'), [
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
        $response->assertSessionHasErrors(['password']);

        $response = $this->from(route('password.change'))->post(route('password.update'), [
            'password' => 'ValidPassword123',
            'password_confirmation' => 'Mismatch123',
        ]);
        $response->assertSessionHasErrors(['password']);

        // 4. Valid new password
        $response = $this->post(route('password.update'), [
            'password' => 'SiswaHebat2026!',
            'password_confirmation' => 'SiswaHebat2026!',
        ]);
        $response->assertRedirect(route('siswa.dashboard'));

        $this->student2->refresh();
        $this->assertFalse((bool) $this->student2->must_change_password);
        $this->assertTrue(Hash::check('SiswaHebat2026!', $this->student2->password));

        // Now dashboard is unblocked
        $this->get(route('siswa.dashboard'))->assertStatus(200);
    }

    public function test_login_rate_limiting(): void
    {
        $ip = '127.0.0.1';
        $throttleKey = 'spamtest|'.$ip;
        RateLimiter::clear($throttleKey);

        // 5 consecutive failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.submit'), [
                'identifier' => 'spamtest',
                'password' => 'wrongpass',
            ]);
        }

        // 6th attempt on same NIS + IP should be locked out
        $response = $this->post(route('login.submit'), [
            'identifier' => 'spamtest',
            'password' => 'wrongpass',
        ]);
        $response->assertSessionHasErrors(['identifier']);
        $errors1 = session('errors');
        $msg1 = $errors1 instanceof ViewErrorBag ? $errors1->first('identifier') : (is_array($errors1) ? ($errors1['identifier'][0] ?? $errors1['identifier'] ?? '') : '');
        $this->assertStringContainsString('dikunci sementara', (string) $msg1);

        // Different NIS on the same IP should NOT be locked out
        $otherResponse = $this->post(route('login.submit'), [
            'identifier' => 'differentnis',
            'password' => 'wrongpass',
        ]);
        $errors2 = session('errors');
        $msg2 = $errors2 instanceof ViewErrorBag ? $errors2->first('identifier') : (is_array($errors2) ? ($errors2['identifier'][0] ?? $errors2['identifier'] ?? '') : '');
        $this->assertStringNotContainsString('dikunci sementara', (string) $msg2);
    }

    public function test_logout_invalidates_session(): void
    {
        $this->actingAs($this->student1);
        $this->assertAuthenticated();

        $response = $this->post(route('logout'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();

        // Old session cannot access protected page
        $this->get(route('siswa.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_can_reset_student_password(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('admin.students.update', $this->student1), [
            'name' => 'Ahmad Reza Updated',
            'gender' => 'L',
            'nis' => '12009',
            'nisn' => '0118102423',
            'reset_password' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->student1->refresh();
        $this->assertTrue((bool) $this->student1->must_change_password);
        $this->assertNotEmpty($this->student1->initial_password);
        $this->assertTrue(Hash::check($this->student1->initial_password, $this->student1->password));
        $this->assertFalse(Hash::check('12009', $this->student1->password), 'Password reset tidak boleh kembali ke NIS');
    }

    public function test_admin_can_export_student_credentials(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.students.export-credentials'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_student_import_csv_valid_and_invalid(): void
    {
        $this->actingAs($this->admin);

        // 1. Unsupported file format (.pdf)
        $invalidFile = UploadedFile::fake()->create('students.pdf', 100, 'application/pdf');
        $response = $this->post(route('admin.students.import'), [
            'excel_file' => $invalidFile,
        ]);
        $response->assertSessionHas('error');

        // 2. Valid CSV import with duplicate NIS (should update existing)
        $csvContent = "No,Nama Siswa,JK,NIS,NISN,Kelas\n1,Ahmad Reza Import,L,12009,0118102423,X-1\n2,Siswa Baru,P,20269999,0118999999,X-1\n";
        $csvFile = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

        $response = $this->post(route('admin.students.import'), [
            'excel_file' => $csvFile,
        ]);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['nis' => '20269999', 'name' => 'Siswa Baru']);
    }

    // --- AREA B: ROLE DAN HAK AKSES ---

    public function test_student_cannot_access_guru_or_admin_routes_returns_403(): void
    {
        $this->actingAs($this->student1);

        $this->get(route('admin.dashboard'))->assertStatus(403);
        $this->get(route('admin.students'))->assertStatus(403);
        $this->get(route('guru.dashboard'))->assertStatus(403);
        $this->get(route('guru.classroom', $this->classroom1))->assertStatus(403);
    }

    public function test_guru_cannot_access_admin_or_siswa_routes_returns_403(): void
    {
        $this->actingAs($this->guru1);

        $this->get(route('admin.dashboard'))->assertStatus(403);
        $this->get(route('admin.teachers'))->assertStatus(403);
        $this->get(route('siswa.dashboard'))->assertStatus(403);
        $this->get(route('siswa.journal'))->assertStatus(403);
    }

    public function test_admin_cannot_access_siswa_routes_returns_403(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('siswa.dashboard'))->assertStatus(403);
        $this->get(route('siswa.journal'))->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_to_login_on_all_protected_routes(): void
    {
        $this->get(route('siswa.dashboard'))->assertRedirect(route('login'));
        $this->get(route('guru.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_teacher_idor_protection_cannot_access_other_classroom(): void
    {
        // Guru 1 owns classroom 1, NOT classroom 2
        $this->actingAs($this->guru1);

        // Accessing own classroom -> 200
        $this->get(route('guru.classroom', $this->classroom1))->assertStatus(200);
        $this->get(route('guru.report.print', $this->classroom1))->assertStatus(200);

        // Accessing other classroom via URL/ID -> 403 Forbidden
        $this->get(route('guru.classroom', $this->classroom2))->assertStatus(403);
        $this->get(route('guru.report.print', $this->classroom2))->assertStatus(403);
    }

    public function test_student_journal_is_strictly_scoped_to_authenticated_user(): void
    {
        $this->actingAs($this->student1);

        $habit = Habit::first();

        // Submit journal with malicious attempt to specify user_id = student2->id
        $this->post(route('siswa.journal.save'), [
            'date' => now()->format('Y-m-d'),
            'user_id' => $this->student2->id,
            'habits' => [
                $habit->id => [
                    'completed' => '1',
                    'time' => '04:30',
                ],
            ],
        ]);

        // Verify journal was created for student1, NEVER student2
        $this->assertDatabaseHas('journals', [
            'user_id' => $this->student1->id,
            'date' => now()->format('Y-m-d'),
        ]);

        $this->assertDatabaseMissing('journals', [
            'user_id' => $this->student2->id,
            'date' => now()->format('Y-m-d'),
        ]);
    }
}

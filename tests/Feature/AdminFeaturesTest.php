<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Badge;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected AcademicYear $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->academicYear = AcademicYear::updateOrCreate(
            ['name' => '2026/2027', 'semester' => 'Ganjil'],
            [
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
                'is_active' => true,
            ]
        );

        $this->admin = User::updateOrCreate(
            ['email' => 'admin@sihebat.id'],
            [
                'name' => 'Admin SiHebat',
                'role' => 'admin',
                'password' => bcrypt('password'),
                'must_change_password' => false,
            ]
        );

        User::create([
            'name' => 'Siswa Juara Hebat',
            'role' => 'siswa',
            'nis' => '10001',
            'xp' => 1500,
            'current_streak' => 7,
            'longest_streak' => 7,
            'password' => bcrypt('password'),
        ]);
    }

    public function test_admin_dashboard_renders_successfully_with_charts_and_rapor_pendidikan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Kondisi Satuan Pendidikan Anda Tahun 2026');
        $response->assertSee('Bagaimana situasi yang dihadapi oleh satuan pendidikan saya saat ini?');
        $response->assertSee('Kemampuan Literasi');
        $response->assertSee('Pelajari Akar Masalah');
        $response->assertSee('chartKeseluruhan');
        $response->assertSee('chartGender');
        $response->assertSee('chartPerKelas');
        $response->assertSee('Podium Kehormatan 7 Kebiasaan');
        $response->assertSee('Emas');
        $response->assertSee('Perak');
        $response->assertSee('Perunggu');
    }

    public function test_admin_can_download_excel_student_template_matching_image_4(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.students.template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="format_import_siswa_sihebat.csv"');

        $content = $response->getContent();
        $this->assertStringContainsString('Nama Siswa', $content);
        $this->assertStringContainsString('NISN', $content);
        $this->assertStringContainsString('Ahmad Reza Maulana', $content);
        $this->assertStringContainsString('0118102423', $content);
        $this->assertStringContainsString('X.1', $content);
    }

    public function test_admin_can_import_students_using_csv_format(): void
    {
        $csvContent = "No.,Nama Siswa,JK,NIS,NISN,Kelas\n1,Reza Maulana Baru,L,99001,0199001001,X.1\n2,Zahra Maharani,P,99002,0199001002,X.2";

        $file = UploadedFile::fake()->createWithContent('import_siswa.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.students.import'), [
            'excel_file' => $file,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Reza Maulana Baru',
            'gender' => 'L',
            'nis' => '99001',
            'nisn' => '0199001001',
            'role' => 'siswa',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Zahra Maharani',
            'gender' => 'P',
            'nis' => '99002',
            'nisn' => '0199001002',
            'role' => 'siswa',
        ]);
    }

    public function test_admin_can_view_leaderboard_and_streak(): void
    {
        // Seed 3 students for podium
        User::create([
            'name' => 'Siswa Juara Satu',
            'role' => 'siswa',
            'nis' => '88001',
            'xp' => 3000,
            'current_streak' => 15,
            'longest_streak' => 15,
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.leaderboard'));

        $response->assertStatus(200);
        $response->assertSee('Papan Peringkat & Streak Siswa');
        $response->assertSee('Podium Kehormatan 7 Kebiasaan');
        $response->assertSee('Siswa Juara Satu');
        $response->assertSee('Streak Aktif');
        $response->assertSee('Rekor Terpanjang');
    }

    public function test_admin_can_refresh_ai_gemini_advice(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.ai.advice'));

        $response->assertSessionHas('success');
    }

    public function test_admin_can_update_classroom_and_assign_homeroom_teacher(): void
    {
        $teacher = User::create([
            'name' => 'Wali Kelas Unggulan',
            'role' => 'guru',
            'nip' => '198501012010011005',
            'gol_ruang' => 'IV/a',
            'jabatan' => 'Guru Ahli Madya',
            'email' => 'wali@sihebat.id',
            'password' => bcrypt('password'),
        ]);

        $classroom = Classroom::create([
            'name' => 'X-1',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.classrooms.update', $classroom), [
            'name' => 'X-1 Unggulan',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
            'homeroom_teacher_id' => $teacher->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('classrooms', [
            'id' => $classroom->id,
            'name' => 'X-1 Unggulan',
            'homeroom_teacher_id' => $teacher->id,
        ]);
    }

    public function test_admin_can_export_report_to_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.report.excel'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'attachment; filename="Rapor_Pendidikan_Karakter_SMAN1_SUNGAILIAT_'));
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }

    public function test_admin_can_delete_classroom(): void
    {
        $classroom = Classroom::create([
            'name' => 'X-99',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.classrooms.destroy', $classroom));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('classrooms', ['id' => $classroom->id]);
    }

    public function test_admin_can_update_and_delete_student(): void
    {
        $student = User::create([
            'name' => 'Siswa Tes Awal',
            'role' => 'siswa',
            'nis' => '77001',
            'nisn' => '0012345678',
            'password' => bcrypt('password'),
        ]);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.students.update', $student), [
            'name' => 'Siswa Tes Diperbarui',
            'gender' => 'P',
            'nis' => '77001',
            'nisn' => '0012345679',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => 'Siswa Tes Diperbarui',
            'gender' => 'P',
        ]);

        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.students.destroy', $student));
        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $student->id]);
    }

    public function test_admin_can_update_and_delete_teacher(): void
    {
        $classroom = Classroom::create([
            'name' => 'X.TestGuru',
            'grade' => 'X',
            'academic_year_id' => $this->academicYear->id,
        ]);

        // 1. Create teacher via storeTeacher (without NIP and with classroom assignment)
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.teachers.store'), [
            'name' => 'Guru Honorer Hebat',
            'nip' => '',
            'email' => 'honorer@sihebat.id',
            'gol_ruang' => 'IX',
            'jabatan' => 'Guru Pertama',
            'classroom_id' => $classroom->id,
        ]);

        $storeResponse->assertRedirect();
        $storeResponse->assertSessionHas('success');

        $teacher = User::where('email', 'honorer@sihebat.id')->first();
        $this->assertNotNull($teacher);
        $this->assertEquals('Guru Honorer Hebat', $teacher->name);
        $this->assertNull($teacher->nip);
        $this->assertEquals('IX', $teacher->gol_ruang);
        $this->assertEquals('guru', $teacher->role);

        // Verify classroom has this teacher assigned as homeroom
        $classroom->refresh();
        $this->assertEquals($teacher->id, $classroom->homeroom_teacher_id);

        // 2. View teachers page with search and filter
        $pageResponse = $this->actingAs($this->admin)->get(route('admin.teachers', [
            'search' => 'Honorer',
            'wali' => 'is_wali',
        ]));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Guru Honorer Hebat');
        $pageResponse->assertSee('X.TestGuru');

        // 3. Update teacher (add NIP and reset password)
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.teachers.update', $teacher), [
            'name' => 'Guru Baru Diperbarui, S.Pd.',
            'nip' => '199001012015011001',
            'email' => 'honorer.updated@sihebat.id',
            'gol_ruang' => 'IV/b',
            'jabatan' => 'Guru Ahli Utama',
            'classroom_id' => '', // unassign classroom
            'reset_password' => 1,
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $teacher->id,
            'name' => 'Guru Baru Diperbarui, S.Pd.',
            'nip' => '199001012015011001',
            'gol_ruang' => 'IV/b',
        ]);

        $classroom->refresh();
        $this->assertNull($classroom->homeroom_teacher_id);

        // 4. Delete teacher
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.teachers.destroy', $teacher));
        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $teacher->id]);
    }

    public function test_admin_can_request_habit_specific_ai_analysis(): void
    {
        Habit::firstOrCreate(
            ['slug' => 'bangun-pagi'],
            [
                'name' => 'Bangun Pagi',
                'icon' => 'sun',
                'color' => 'bangun',
                'order' => 1,
                'xp_reward' => 10,
                'is_active' => true,
            ]
        );

        $response = $this->actingAs($this->admin)->post(route('admin.akar-masalah.ai'), [
            'habit_slug' => 'bangun-pagi',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'habit',
            'analysis' => [
                'triggers',
                'critical_pattern',
                'recommendations',
            ],
        ]);
    }

    public function test_admin_can_crud_achievements(): void
    {
        // 1. View achievements list
        $indexResponse = $this->actingAs($this->admin)->get(route('admin.achievements'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Manajemen Achievement');

        // 2. Create achievement
        $createResponse = $this->actingAs($this->admin)->post(route('admin.achievements.store'), [
            'name' => 'Ksatria Fajar Baru',
            'slug' => 'ksatria-fajar-baru',
            'description' => 'Bangun sebelum pukul 05:00 selama 10 hari berturut-turut.',
            'requirement_type' => 'streak',
            'requirement_value' => 10,
            'icon' => 'sun',
            'color' => 'sun',
            'is_active' => true,
        ]);

        $createResponse->assertRedirect();
        $createResponse->assertSessionHas('success');
        $this->assertDatabaseHas('badges', [
            'slug' => 'ksatria-fajar-baru',
            'name' => 'Ksatria Fajar Baru',
            'requirement_value' => 10,
        ]);

        $badge = Badge::where('slug', 'ksatria-fajar-baru')->firstOrFail();

        // 3. Update achievement
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.achievements.update', $badge), [
            'name' => 'Ksatria Fajar Legenda',
            'slug' => 'ksatria-fajar-legenda',
            'description' => 'Deskripsi diperbarui.',
            'requirement_type' => 'streak',
            'requirement_value' => 14,
            'icon' => 'fire',
            'color' => 'coral',
            'is_active' => true,
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('badges', [
            'id' => $badge->id,
            'name' => 'Ksatria Fajar Legenda',
            'slug' => 'ksatria-fajar-legenda',
            'requirement_value' => 14,
        ]);

        // 4. Delete achievement
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.achievements.destroy', $badge));
        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('badges', ['id' => $badge->id]);
    }

    public function test_student_can_upload_and_delete_avatar_and_admin_can_moderate_photo(): void
    {
        Storage::fake('public');

        $student = User::create([
            'name' => 'Budi Santoso',
            'role' => 'siswa',
            'nis' => '10099',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'xp' => 500,
            'current_streak' => 3,
        ]);

        // 1. Student uploads a photo
        $avatarFile = UploadedFile::fake()->image('my_photo.png', 400, 400);

        $uploadResponse = $this->actingAs($student)->post(route('siswa.profile.avatar'), [
            'avatar' => $avatarFile,
        ]);

        $uploadResponse->assertRedirect(route('siswa.profile'));
        $uploadResponse->assertSessionHas('success');

        $student->refresh();
        $this->assertNotNull($student->avatar);
        Storage::disk('public')->assertExists($student->avatar);

        // 2. Profile renders avatar
        $profileResponse = $this->actingAs($student)->get(route('siswa.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee($student->avatar_url);

        // 3. Admin moderates inappropriate student photo
        $moderateResponse = $this->actingAs($this->admin)->post(
            route('admin.students.moderate-avatar', $student),
            ['warning_message' => 'Foto profil tidak menggunakan seragam sekolah yang sopan.']
        );

        $moderateResponse->assertRedirect();
        $moderateResponse->assertSessionHas('success');

        $oldAvatarPath = $student->avatar;
        $student->refresh();

        // Photo should be deleted and warning attached
        $this->assertNull($student->avatar);
        $this->assertEquals('Foto profil tidak menggunakan seragam sekolah yang sopan.', $student->avatar_warning);
        $this->assertNotNull($student->avatar_moderated_at);
        Storage::disk('public')->assertMissing($oldAvatarPath);

        // 4. Student sees warning banner on dashboard & profile
        $dashboardResponse = $this->actingAs($student)->get(route('siswa.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Peringatan Foto Profil dari Pihak Sekolah');
        $dashboardResponse->assertSee('Foto profil tidak menggunakan seragam sekolah yang sopan.');

        $profileWithWarning = $this->actingAs($student)->get(route('siswa.profile'));
        $profileWithWarning->assertStatus(200);
        $profileWithWarning->assertSee('Pemberitahuan Pihak Sekolah: Foto Profil Dimoderasi');
        $profileWithWarning->assertSee('Foto profil tidak menggunakan seragam sekolah yang sopan.');

        // 5. Student uploads a compliant photo -> warning is cleared!
        $newCompliantPhoto = UploadedFile::fake()->image('seragam_sekolah.jpg', 400, 400);

        $reuploadResponse = $this->actingAs($student)->post(route('siswa.profile.avatar'), [
            'avatar' => $newCompliantPhoto,
        ]);

        $reuploadResponse->assertRedirect(route('siswa.profile'));
        $student->refresh();
        $this->assertNotNull($student->avatar);
        $this->assertNull($student->avatar_warning);
        $this->assertNull($student->avatar_moderated_at);
        Storage::disk('public')->assertExists($student->avatar);

        // 6. Student deletes their own avatar
        $deleteResponse = $this->actingAs($student)->delete(route('siswa.profile.avatar.delete'));
        $deleteResponse->assertRedirect(route('siswa.profile'));
        $student->refresh();
        $this->assertNull($student->avatar);
    }

    public function test_admin_can_download_teacher_template_xlsx_and_csv(): void
    {
        // 1. Download CSV template
        $csvResponse = $this->actingAs($this->admin)->get(route('admin.teachers.template', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('Content-Disposition', 'attachment; filename="format_import_guru_dapodik.csv"');
        $csvContent = $csvResponse->getContent();
        $this->assertStringContainsString('Nama Rombel', $csvContent);
        $this->assertStringContainsString('Tingkat Kelas', $csvContent);
        $this->assertStringContainsString('Wali Kelas', $csvContent);
        $this->assertStringContainsString('MUHAMMAD RAMADHANI ZUHRI', $csvContent);
        $this->assertStringContainsString('Lesmiarti', $csvContent);

        // 2. Download XLSX template
        $xlsxResponse = $this->actingAs($this->admin)->get(route('admin.teachers.template', ['format' => 'xlsx']));
        $xlsxResponse->assertStatus(200);
        $xlsxResponse->assertHeader('Content-Disposition', 'attachment; filename=format_import_guru_dapodik.xlsx');
    }

    public function test_admin_can_import_teachers_and_rombel_matching_dapodik_format(): void
    {
        $csvContent = "No,Nama Rombel,Tingkat Kelas,L,P,Total,Wali Kelas\n1,X.1,10,15,21,36,MUHAMMAD RAMADHANI ZUHRI\n2,X.2,10,14,22,36,Lesmiarti";

        $file = UploadedFile::fake()->createWithContent('dapodik_rombel.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.teachers.import'), [
            'excel_file' => $file,
        ]);

        $response->assertSessionHas('success');

        // Verify teachers created with role guru
        $zuhri = User::where('role', 'guru')->where('name', 'MUHAMMAD RAMADHANI ZUHRI')->first();
        $this->assertNotNull($zuhri);
        $this->assertNotNull($zuhri->initial_password);
        $this->assertTrue($zuhri->must_change_password);

        $lesmiarti = User::where('role', 'guru')->where('name', 'Lesmiarti')->first();
        $this->assertNotNull($lesmiarti);
        $this->assertNotNull($lesmiarti->initial_password);

        // Verify classrooms created/updated and linked to homeroom teachers
        $rombel1 = Classroom::where('name', 'X.1')->orWhere('name', 'X-1')->first();
        $this->assertNotNull($rombel1);
        $this->assertEquals($zuhri->id, $rombel1->homeroom_teacher_id);
        $this->assertEquals('X', $rombel1->grade);

        $rombel2 = Classroom::where('name', 'X.2')->orWhere('name', 'X-2')->first();
        $this->assertNotNull($rombel2);
        $this->assertEquals($lesmiarti->id, $rombel2->homeroom_teacher_id);
        $this->assertEquals('X', $rombel2->grade);
    }

    public function test_admin_can_export_teacher_credentials(): void
    {
        // Ensure at least one teacher exists
        User::create([
            'name' => 'Pak Test Guru',
            'role' => 'guru',
            'nip' => '198701012012011001',
            'email' => 'pak.test@guru.sihebat.id',
            'password' => bcrypt('password'),
            'initial_password' => 'GuruABC12',
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.teachers.export-credentials'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('daftar_akun_guru', $response->headers->get('content-disposition'));
    }
}

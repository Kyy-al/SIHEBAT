<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test 1: XSS payload in journal notes and habit topic is safely escaped.
     */
    public function test_xss_payload_in_journal_notes_and_topic_is_properly_escaped(): void
    {
        $student = User::where('role', 'siswa')->first();
        $student->update(['must_change_password' => false]);
        $xssPayload = "<script>alert('XSS_ATTACK_VECTOR')</script><b onmouseover=alert(1)>bold</b>";

        $habits = Habit::all();
        $payload = [
            'journal_date' => Carbon::today()->format('Y-m-d'),
            'mood' => 'senang',
            'notes' => $xssPayload,
        ];

        foreach ($habits as $h) {
            $prefix = 'habit_'.$h->id;
            switch ($h->slug) {
                case 'bangun-pagi':
                    $payload[$prefix.'_time'] = '05:00';
                    break;
                case 'beribadah':
                    $payload[$prefix.'_done'] = '1';
                    break;
                case 'gemar-belajar':
                    $payload[$prefix.'_duration'] = '45';
                    $payload[$prefix.'_topic'] = $xssPayload;
                    break;
                case 'tidur-cepat':
                    $payload[$prefix.'_time'] = '21:00';
                    break;
            }
        }

        $res = $this->actingAs($student)->post('/siswa/jurnal', $payload);
        $res->assertSessionHasNoErrors();

        // 1. Check Student journal view does NOT render raw unescaped script tag
        $viewRes = $this->actingAs($student)->get('/siswa/jurnal?date='.Carbon::today()->format('Y-m-d'));
        $viewRes->assertStatus(200);
        $viewRes->assertDontSee("<script>alert('XSS_ATTACK_VECTOR')</script>", false);
        $viewRes->assertSee(e($xssPayload), false);

        // 2. Check Teacher classroom detail view escapes it
        $classroom = $student->currentClassroom() ?? Classroom::first();
        $teacher = $classroom->homeroomTeacher ?? User::where('role', 'guru')->first();
        $teacherRes = $this->actingAs($teacher)->get('/guru/kelas/'.$classroom->id);
        $teacherRes->assertStatus(200);
        $teacherRes->assertDontSee("<script>alert('XSS_ATTACK_VECTOR')</script>", false);
    }

    /**
     * Test 2: Mass assignment attempt to escalate privilege to admin or hijack XP/streak.
     */
    public function test_mass_assignment_cannot_elevate_role_or_manipulate_xp(): void
    {
        $student = User::where('role', 'siswa')->first();
        $student->update(['must_change_password' => false]);
        $initialXp = $student->xp;
        $initialStreak = $student->current_streak;

        // Attempt privilege escalation and XP spoofing via journal submission
        $payload = [
            'journal_date' => Carbon::today()->format('Y-m-d'),
            'role' => 'admin',
            'xp' => 999999,
            'current_streak' => 999,
            'is_perfect_day' => true,
            'mood' => 'biasa',
            'notes' => 'Tampering attempt',
        ];

        $this->actingAs($student)->post('/siswa/jurnal', $payload);

        $student->refresh();
        $this->assertEquals('siswa', $student->role);
        $this->assertNotEquals(999999, $student->xp);
        $this->assertNotEquals(999, $student->current_streak);

        // Attempt role modification via admin student update without proper role field
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->put('/admin/students/'.$student->id, [
            'name' => 'Renamed Student',
            'nis' => $student->nis,
            'role' => 'admin', // Malicious attempt to change role
        ]);

        $student->refresh();
        $this->assertEquals('siswa', $student->role);
    }

    /**
     * Test 3: CSV Formula Injection (CWE-1236) protection.
     */
    public function test_csv_export_escapes_formula_injection_characters(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Create or update a top student with a formula injection name
        $student = User::where('role', 'siswa')->first();
        $student->update(['must_change_password' => false]);
        $student->update([
            'name' => '=cmd|\' /C calc\'!A0',
            'xp' => 15000,
        ]);

        $res = $this->actingAs($admin)->get(route('admin.report.excel'));
        $res->assertStatus(200);

        $content = $res->streamedContent();

        // Formula character '=' should be sanitized with leading single quote '\''
        $this->assertStringNotContainsString(',=cmd|', $content);
        $this->assertStringContainsString("'=cmd|", $content);
    }

    /**
     * Test 4: Security headers are present on HTTP responses.
     */
    public function test_security_headers_are_attached_to_responses(): void
    {
        $res = $this->get('/login');
        $res->assertStatus(200);

        $res->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $res->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertFalse($res->headers->has('X-XSS-Protection'), 'Deprecated X-XSS-Protection header should be removed.');
        $res->assertHeader('Content-Security-Policy');
        $res->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $res->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    /**
     * Test 5: Malicious and oversized file uploads are rejected.
     */
    public function test_avatar_upload_restricts_mime_type_and_file_size(): void
    {
        Storage::fake('public');
        $student = User::where('role', 'siswa')->first();
        $student->update(['must_change_password' => false]);

        // 1. Attempt uploading executable PHP script masquerading as image
        $maliciousFile = UploadedFile::fake()->create('exploit.php', 10, 'application/x-php');

        $resPhp = $this->actingAs($student)->post('/siswa/profil/avatar', [
            'avatar' => $maliciousFile,
        ]);
        $resPhp->assertSessionHasErrors(['avatar']);

        // 2. Attempt uploading HTML file
        $htmlFile = UploadedFile::fake()->create('attack.html', 10, 'text/html');
        $resHtml = $this->actingAs($student)->post('/siswa/profil/avatar', [
            'avatar' => $htmlFile,
        ]);
        $resHtml->assertSessionHasErrors(['avatar']);

        // 3. Attempt uploading oversized file (>2MB = 2048KB)
        $largeFile = UploadedFile::fake()->image('huge_photo.jpg')->size(2500);
        $resLarge = $this->actingAs($student)->post('/siswa/profil/avatar', [
            'avatar' => $largeFile,
        ]);
        $resLarge->assertSessionHasErrors(['avatar']);

        // 4. Legitimate JPG image (500KB) is accepted
        $validFile = UploadedFile::fake()->image('profile.jpg')->size(500);
        $resValid = $this->actingAs($student)->post('/siswa/profil/avatar', [
            'avatar' => $validFile,
        ]);
        $resValid->assertSessionHasNoErrors();
        $resValid->assertRedirect('/siswa/profil');

        $student->refresh();
        $this->assertNotNull($student->avatar);
        Storage::disk('public')->assertExists($student->avatar);
    }
}

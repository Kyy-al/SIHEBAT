<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BenchmarkLoadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => json_encode([
                                    'executive_summary' => 'Performa kebiasaan siswa sangat baik.',
                                    'strengths' => ['Bangun pagi konsisten'],
                                    'weaknesses' => ['Tidur cepat perlu perhatian'],
                                    'action_plan' => ['Edukasi tidur tepat waktu'],
                                ])],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);
    }

    /**
     * Test benchmark: Query response time under scaled data volume.
     */
    public function test_dashboard_query_performance_under_scale(): void
    {
        $admin = User::where('role', 'admin')->first();
        $habits = Habit::where('is_active', true)->get();
        $classroom = Classroom::first();

        // 1. Bulk insert additional mock students to simulate large school
        $bulkStudents = [];
        $startNis = 20265000;
        for ($i = 0; $i < 200; $i++) {
            $bulkStudents[] = [
                'name' => 'Scale Student '.$i,
                'gender' => $i % 2 === 0 ? 'L' : 'P',
                'nis' => (string) ($startNis + $i),
                'email' => 'scale'.$i.'@sihebat.test',
                'role' => 'siswa',
                'password' => 'secret',
                'xp' => rand(100, 5000),
                'current_streak' => rand(1, 45),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        User::insert($bulkStudents);

        $newStudentIds = User::where('nis', '>=', (string) $startNis)->pluck('id')->toArray();

        // 2. Bulk insert mock journals for past 30 days
        $bulkJournals = [];
        $dates = [];
        for ($d = 1; $d <= 30; $d++) {
            $dates[] = Carbon::today()->subDays($d)->format('Y-m-d');
        }

        // Generate 3,000 journals for the new scale students
        foreach ($newStudentIds as $sId) {
            foreach ($dates as $dt) {
                $bulkJournals[] = [
                    'user_id' => $sId,
                    'date' => $dt,
                    'habits_completed' => 5,
                    'is_perfect_day' => false,
                    'xp_earned' => 50,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        // Chunk inserts to comply with SQLite maximum parameter bounds
        foreach (array_chunk($bulkJournals, 100) as $chunk) {
            Journal::insert($chunk);
        }

        // Measure Admin Dashboard response time
        $startTime = microtime(true);

        $res = $this->actingAs($admin)->get('/admin/dashboard');

        $elapsedSeconds = microtime(true) - $startTime;

        $res->assertStatus(200);

        // Performance requirement: Must respond under 2.0 seconds
        $this->assertLessThan(2.0, $elapsedSeconds, "Dashboard response time ({$elapsedSeconds}s) exceeded 2.0s limit.");
    }
}

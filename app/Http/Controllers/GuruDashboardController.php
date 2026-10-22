<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\User;
use App\Services\GeminiAiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class GuruDashboardController extends Controller
{
    /**
     * Teacher classroom dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $classrooms = $user->homeroomClassrooms()
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->with('academicYear')
            ->get();

        // Default to first classroom
        $classroom = $classrooms->first();
        $stats = $this->getClassroomStats($classroom);

        return view('guru.dashboard', compact('user', 'classrooms', 'classroom', 'stats'));
    }

    /**
     * Classroom detail.
     */
    public function classroomDetail(Classroom $classroom)
    {
        $user = Auth::user();

        // Ensure teacher owns this classroom
        if ($classroom->homeroom_teacher_id !== $user->id) {
            abort(403);
        }

        $classroom->load('homeroomTeacher');

        $students = $classroom->students()
            ->orderBy('name')
            ->get();

        $habits = Habit::where('is_active', true)->orderBy('order')->get();
        $stats = $this->getClassroomStats($classroom);

        return view('guru.classroom-detail', compact('user', 'classroom', 'students', 'habits', 'stats'));
    }

    /**
     * Tanya Data AI: Natural language question answering for Teachers.
     */
    public function askDataAi(Request $request, GeminiAiService $aiService)
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $rateKey = 'tanya_ai_quota:'.$user->id;
        if (RateLimiter::tooManyAttempts($rateKey, 20)) {
            $seconds = RateLimiter::availableIn($rateKey);

            return response()->json([
                'status' => 'error',
                'message' => "Batas pemakaian Tanya Data AI tercapai. Silakan coba lagi dalam {$seconds} detik.",
            ], 429);
        }
        RateLimiter::hit($rateKey, 3600);

        $classroom = Auth::user()->homeroomClassrooms()->first();
        $result = $aiService->answerDataQuestion($request->input('question'), $classroom?->id);

        if ($result['source'] === 'authorization_filter') {
            return response()->json([
                'status' => 'forbidden',
                'message' => $result['answer'],
                'question' => $request->input('question'),
                'answer' => $result['answer'],
                'source' => $result['source'],
                'details' => $result['details'] ?? null,
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'question' => $request->input('question'),
            'answer' => $result['answer'],
            'source' => $result['source'],
            'details' => $result['details'] ?? null,
        ]);
    }

    /**
     * Printable Classroom Official Rapor Pendidikan Report.
     */
    public function printReport(Classroom $classroom)
    {
        $user = Auth::user();

        // Ensure teacher owns this classroom
        if ($classroom->homeroom_teacher_id !== $user->id) {
            abort(403);
        }

        $classroom->load('homeroomTeacher');

        $students = $classroom->students()->orderBy('name')->get();
        $habits = Habit::where('is_active', true)->orderBy('order')->get();
        $stats = $this->getClassroomStats($classroom);

        $schoolProfile = [
            'name' => 'SMAN 1 SUNGAILIAT',
            'npsn' => '10900232',
            'address' => 'JL. PEMUDA SUNGAILIAT, Kelurahan Bukit Betung, Kec. Sungailiat',
            'city' => 'Kab. Bangka, Prov. Kepulauan Bangka Belitung',
            'accreditation' => 'A',
            'year' => date('Y'),
        ];

        return view('guru.report-print', compact(
            'classroom',
            'students',
            'habits',
            'stats',
            'schoolProfile'
        ));
    }

    /**
     * Download Classroom Official Rapor Pendidikan Report as Native PDF (Barryvdh/DomPDF).
     */
    public function downloadReportPdf(Classroom $classroom)
    {
        $user = Auth::user();

        if ($classroom->homeroom_teacher_id !== $user->id) {
            abort(403);
        }

        $classroom->load('homeroomTeacher');
        $students = $classroom->students()->orderBy('name')->get();
        $habits = Habit::where('is_active', true)->orderBy('order')->get();
        $stats = $this->getClassroomStats($classroom);

        $schoolProfile = [
            'name' => 'SMAN 1 SUNGAILIAT',
            'npsn' => '10900232',
            'address' => 'JL. PEMUDA SUNGAILIAT, Kelurahan Bukit Betung, Kec. Sungailiat',
            'city' => 'Kab. Bangka, Prov. Kepulauan Bangka Belitung',
            'accreditation' => 'A',
            'year' => date('Y'),
        ];

        $pdf = Pdf::loadView('guru.report-print', compact(
            'classroom',
            'students',
            'habits',
            'stats',
            'schoolProfile'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("rapor_kelas_{$classroom->name}.pdf");
    }

    /**
     * Get stats for a classroom.
     */
    protected function getClassroomStats(?Classroom $classroom): array
    {
        if (! $classroom) {
            return [
                'total_students' => 0,
                'today_filled' => 0,
                'avg_completion' => 0,
                'habits' => [],
                'needs_attention' => [],
            ];
        }

        $studentIds = $classroom->students()->pluck('users.id');
        $today = Carbon::today();
        $habits = Habit::where('is_active', true)->orderBy('order')->get();

        // Today's journal count
        $todayFilled = Journal::whereIn('user_id', $studentIds)
            ->where('date', $today)
            ->count();

        // Average completion rate this week
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        $avgCompletion = Journal::whereIn('user_id', $studentIds)
            ->whereBetween('date', [$weekStart, $today])
            ->avg('habits_completed') ?? 0;

        // Per-habit completion rate (this week)
        $habitStats = [];
        foreach ($habits as $habit) {
            $completedCount = DB::table('journal_entries')
                ->join('journals', 'journals.id', '=', 'journal_entries.journal_id')
                ->whereIn('journals.user_id', $studentIds)
                ->where('journal_entries.habit_id', $habit->id)
                ->where('journal_entries.completed', true)
                ->whereBetween('journals.date', [$weekStart, $today])
                ->count();

            $totalPossible = $studentIds->count() * $today->diffInDays($weekStart) + $studentIds->count();
            $rate = $totalPossible > 0 ? round(($completedCount / $totalPossible) * 100) : 0;

            $habitStats[] = [
                'habit' => $habit,
                'rate' => $rate,
                'status' => $rate >= 75 ? 'Baik' : ($rate >= 50 ? 'Cukup' : 'Perlu Perhatian'),
            ];
        }

        // Students needing attention (low streak or no journal today)
        $needsAttention = User::whereIn('id', $studentIds)
            ->where(function ($q) use ($today) {
                $q->where('current_streak', 0)
                    ->orWhereNull('last_journal_date')
                    ->orWhere('last_journal_date', '<', $today->copy()->subDays(2));
            })
            ->orderBy('current_streak')
            ->limit(5)
            ->get();

        return [
            'total_students' => $studentIds->count(),
            'today_filled' => $todayFilled,
            'avg_completion' => round($avgCompletion, 1),
            'habits' => $habitStats,
            'needs_attention' => $needsAttention,
        ];
    }
}

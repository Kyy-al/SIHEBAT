<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $students = $classroom->students()
            ->orderBy('name')
            ->get();

        $habits = Habit::where('is_active', true)->orderBy('order')->get();
        $stats = $this->getClassroomStats($classroom);

        return view('guru.classroom-detail', compact('user', 'classroom', 'students', 'habits', 'stats'));
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

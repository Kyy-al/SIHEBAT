<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    /**
     * Admin dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $academicYear = AcademicYear::active();

        $totalStudents = User::where('role', 'siswa')->count();
        $totalTeachers = User::where('role', 'guru')->count();
        $totalClassrooms = $academicYear
            ? Classroom::where('academic_year_id', $academicYear->id)->count()
            : 0;

        return view('admin.dashboard', compact(
            'user', 'academicYear', 'totalStudents', 'totalTeachers', 'totalClassrooms'
        ));
    }

    // ---- Academic Years ----

    public function academicYears()
    {
        $years = AcademicYear::orderByDesc('start_date')->get();
        return view('admin.academic-years', compact('years'));
    }

    public function storeAcademicYear(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($request->boolean('is_active')) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($request->all());

        return back()->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function toggleAcademicYear(AcademicYear $academicYear)
    {
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        return back()->with('success', 'Tahun ajaran aktif berhasil diubah.');
    }

    // ---- Classrooms ----

    public function classrooms()
    {
        $academicYear = AcademicYear::active();
        $classrooms = $academicYear
            ? Classroom::where('academic_year_id', $academicYear->id)
                ->with(['homeroomTeacher', 'academicYear'])
                ->withCount('students')
                ->orderBy('name')
                ->get()
            : collect();

        $teachers = User::where('role', 'guru')->orderBy('name')->get();

        return view('admin.classrooms', compact('classrooms', 'teachers', 'academicYear'));
    }

    public function storeClassroom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'grade' => 'required|in:X,XI,XII',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        $academicYear = AcademicYear::active();
        if (! $academicYear) {
            return back()->with('error', 'Belum ada tahun ajaran aktif.');
        }

        Classroom::create([
            'name' => $request->input('name'),
            'grade' => $request->input('grade'),
            'academic_year_id' => $academicYear->id,
            'homeroom_teacher_id' => $request->input('homeroom_teacher_id'),
        ]);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    // ---- Users (Students & Teachers) ----

    public function students()
    {
        $academicYear = AcademicYear::active();
        $students = User::where('role', 'siswa')
            ->with('classrooms')
            ->orderBy('name')
            ->paginate(25);

        $classrooms = $academicYear
            ? Classroom::where('academic_year_id', $academicYear->id)->orderBy('name')->get()
            : collect();

        return view('admin.students', compact('students', 'classrooms'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'nis' => 'required|string|max:20|unique:users,nis',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ]);

        $student = User::create([
            'name' => $request->input('name'),
            'nis' => $request->input('nis'),
            'role' => 'siswa',
            'password' => Hash::make($request->input('nis')), // Default password = NIS
            'must_change_password' => true,
        ]);

        if ($request->input('classroom_id')) {
            $student->classrooms()->attach($request->input('classroom_id'));
        }

        return back()->with('success', "Siswa {$student->name} berhasil ditambahkan. Kata sandi default: NIS.");
    }

    public function teachers()
    {
        $teachers = User::where('role', 'guru')
            ->with('homeroomClassrooms')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.teachers', compact('teachers'));
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'nip' => 'required|string|max:30|unique:users,nip',
            'email' => 'nullable|email|unique:users,email',
        ]);

        User::create([
            'name' => $request->input('name'),
            'nip' => $request->input('nip'),
            'email' => $request->input('email'),
            'role' => 'guru',
            'password' => Hash::make($request->input('nip')), // Default password = NIP
            'must_change_password' => true,
        ]);

        return back()->with('success', 'Guru berhasil ditambahkan. Kata sandi default: NIP.');
    }
}

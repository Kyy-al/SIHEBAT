<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\SiswaDashboardController;
use Illuminate\Support\Facades\Route;

// ---- Guest routes ----
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// ---- Authenticated routes ----
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Change password (first login)
    Route::get('/ganti-sandi', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/ganti-sandi', [AuthController::class, 'changePassword'])->name('password.update');

    // ---- Siswa ----
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/beranda', [SiswaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/jurnal', [SiswaDashboardController::class, 'journalForm'])->name('journal');
        Route::post('/jurnal', [SiswaDashboardController::class, 'saveJournal'])->name('journal.save');
        Route::get('/jurnal/sukses', [SiswaDashboardController::class, 'journalSuccess'])->name('journal.success');
        Route::get('/rapor', [SiswaDashboardController::class, 'rapor'])->name('rapor');
        Route::get('/peringkat', [SiswaDashboardController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/profil', [SiswaDashboardController::class, 'profile'])->name('profile');
        Route::post('/profil/avatar', [SiswaDashboardController::class, 'updateAvatar'])->name('profile.avatar');
        Route::delete('/profil/avatar', [SiswaDashboardController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    });

    // ---- Guru ----
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
        Route::get('/kelas/{classroom}', [GuruDashboardController::class, 'classroomDetail'])->name('classroom');
        Route::post('/tanya-ai', [GuruDashboardController::class, 'askDataAi'])->name('ai.ask')->middleware('throttle:15,1');
        Route::get('/laporan/cetak/{classroom}', [GuruDashboardController::class, 'printReport'])->name('report.print');
        Route::get('/laporan/pdf/{classroom}', [GuruDashboardController::class, 'downloadReportPdf'])->name('report.pdf');
    });

    // ---- Admin ----
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/tanya-ai', [AdminDashboardController::class, 'askDataAi'])->name('ai.ask')->middleware('throttle:15,1');
        Route::get('/laporan/cetak', [AdminDashboardController::class, 'printReport'])->name('report.print');
        Route::get('/laporan/pdf', [AdminDashboardController::class, 'downloadReportPdf'])->name('report.pdf');
        Route::get('/laporan/excel', [AdminDashboardController::class, 'exportReportExcel'])->name('report.excel');
        Route::get('/laporan/xlsx', [AdminDashboardController::class, 'exportReportExcel'])->name('report.xlsx');
        Route::get('/akar-masalah', [AdminDashboardController::class, 'akarMasalahDetail'])->name('akar-masalah');
        Route::post('/akar-masalah/ai-analisis', [AdminDashboardController::class, 'aiHabitAnalysis'])->name('akar-masalah.ai');

        // Master Data
        Route::get('/tahun-ajaran', [AdminDashboardController::class, 'academicYears'])->name('academic-years');
        Route::post('/tahun-ajaran', [AdminDashboardController::class, 'storeAcademicYear'])->name('academic-years.store');
        Route::patch('/tahun-ajaran/{academicYear}/aktifkan', [AdminDashboardController::class, 'toggleAcademicYear'])->name('academic-years.toggle');

        Route::put('/kebiasaan/{habit}', [AdminDashboardController::class, 'updateHabit'])->name('habits.update');

        Route::get('/kelas', [AdminDashboardController::class, 'classrooms'])->name('classrooms');
        Route::post('/kelas', [AdminDashboardController::class, 'storeClassroom'])->name('classrooms.store');
        Route::put('/kelas/{classroom}', [AdminDashboardController::class, 'updateClassroom'])->name('classrooms.update');
        Route::delete('/kelas/{classroom}', [AdminDashboardController::class, 'destroyClassroom'])->name('classrooms.destroy');

        Route::get('/siswa', [AdminDashboardController::class, 'students'])->name('students');
        Route::post('/siswa', [AdminDashboardController::class, 'storeStudent'])->name('students.store');
        Route::put('/siswa/{user}', [AdminDashboardController::class, 'updateStudent'])->name('students.update');
        Route::delete('/siswa/{user}', [AdminDashboardController::class, 'destroyStudent'])->name('students.destroy');
        Route::post('/siswa/{user}/moderasi-foto', [AdminDashboardController::class, 'moderateStudentAvatar'])->name('students.moderate-avatar');
        Route::post('/siswa/import', [AdminDashboardController::class, 'importStudents'])->name('students.import');
        Route::get('/siswa/template', [AdminDashboardController::class, 'downloadStudentTemplate'])->name('students.template');
        Route::get('/siswa/ekspor-akun', [AdminDashboardController::class, 'exportStudentCredentials'])->name('students.export-credentials');

        Route::get('/leaderboard', [AdminDashboardController::class, 'leaderboard'])->name('leaderboard');
        Route::post('/ai-advice', [AdminDashboardController::class, 'askGemini'])->name('ai.advice')->middleware('throttle:15,1');

        Route::get('/guru', [AdminDashboardController::class, 'teachers'])->name('teachers');
        Route::post('/guru', [AdminDashboardController::class, 'storeTeacher'])->name('teachers.store');
        Route::put('/guru/{user}', [AdminDashboardController::class, 'updateTeacher'])->name('teachers.update');
        Route::delete('/guru/{user}', [AdminDashboardController::class, 'destroyTeacher'])->name('teachers.destroy');
        Route::post('/guru/import', [AdminDashboardController::class, 'importTeachers'])->name('teachers.import');
        Route::get('/guru/template', [AdminDashboardController::class, 'downloadTeacherTemplate'])->name('teachers.template');
        Route::get('/guru/ekspor-akun', [AdminDashboardController::class, 'exportTeacherCredentials'])->name('teachers.export-credentials');

        // Achievements / Badges CRUD
        Route::get('/achievements', [AdminDashboardController::class, 'achievements'])->name('achievements');
        Route::post('/achievements', [AdminDashboardController::class, 'storeAchievement'])->name('achievements.store');
        Route::put('/achievements/{badge}', [AdminDashboardController::class, 'updateAchievement'])->name('achievements.update');
        Route::delete('/achievements/{badge}', [AdminDashboardController::class, 'destroyAchievement'])->name('achievements.destroy');

        // XP Rules Configuration
        Route::get('/pengaturan/xp', [AdminDashboardController::class, 'xpSettings'])->name('settings.xp');
        Route::post('/pengaturan/xp', [AdminDashboardController::class, 'updateXpSettings'])->name('settings.xp.update');
    });
});

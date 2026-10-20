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
        Route::get('/peringkat', [SiswaDashboardController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/profil', [SiswaDashboardController::class, 'profile'])->name('profile');
    });

    // ---- Guru ----
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
        Route::get('/kelas/{classroom}', [GuruDashboardController::class, 'classroomDetail'])->name('classroom');
    });

    // ---- Admin ----
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Master Data
        Route::get('/tahun-ajaran', [AdminDashboardController::class, 'academicYears'])->name('academic-years');
        Route::post('/tahun-ajaran', [AdminDashboardController::class, 'storeAcademicYear'])->name('academic-years.store');
        Route::patch('/tahun-ajaran/{academicYear}/aktifkan', [AdminDashboardController::class, 'toggleAcademicYear'])->name('academic-years.toggle');

        Route::get('/kelas', [AdminDashboardController::class, 'classrooms'])->name('classrooms');
        Route::post('/kelas', [AdminDashboardController::class, 'storeClassroom'])->name('classrooms.store');

        Route::get('/siswa', [AdminDashboardController::class, 'students'])->name('students');
        Route::post('/siswa', [AdminDashboardController::class, 'storeStudent'])->name('students.store');

        Route::get('/guru', [AdminDashboardController::class, 'teachers'])->name('teachers');
        Route::post('/guru', [AdminDashboardController::class, 'storeTeacher'])->name('teachers.store');
    });
});

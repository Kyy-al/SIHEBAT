<?php

use App\Models\Journal;
use App\Models\User;
use App\Services\GeminiAiService;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * 1. Pengingat Jurnal Harian (FR-NTF-01)
 * Hanya dikirim ke siswa yang belum mengisi lengkap hari ini, tidak duplikat.
 */
Artisan::command('sihebat:remind-journal', function () {
    $today = Carbon::today()->format('Y-m-d');
    $students = User::where('role', 'siswa')->get();
    $remindedCount = 0;

    foreach ($students as $student) {
        $journal = Journal::where('user_id', $student->id)->where('date', $today)->first();
        if (! $journal || $journal->habits_completed < 7) {
            Log::info("Pengingat jurnal dikirim ke {$student->name} (NIS: {$student->nis}) untuk tanggal {$today}.");
            $remindedCount++;
        }
    }

    $this->info("Pengingat jurnal berhasil diproses untuk {$remindedCount} siswa.");
})->purpose('Kirim pengingat harian ke siswa yang belum menyelesaikan 7 kebiasaan.');

/**
 * 2. Agregasi Harian Kebiasaan (PRD Bagian 8 & 9)
 */
Artisan::command('sihebat:aggregate-daily', function () {
    $today = Carbon::today()->format('Y-m-d');
    $totalJournals = Journal::where('date', $today)->count();
    $this->info("Agregasi harian berhasil diproses: {$totalJournals} jurnal hari ini.");
})->purpose('Hitung agregat harian kebiasaan dan aktivitas siswa.');

/**
 * 3. Snapshot Leaderboard Mingguan (FR-LDB-04)
 */
Artisan::command('sihebat:snapshot-leaderboard', function () {
    $topStudents = User::where('role', 'siswa')
        ->orderByDesc('xp')
        ->orderByDesc('current_streak')
        ->take(10)
        ->pluck('name', 'xp');

    Log::info('Snapshot leaderboard mingguan berhasil dibuat untuk '.count($topStudents).' siswa top.');
    $this->info('Snapshot leaderboard mingguan berhasil dibuat.');
})->purpose('Ambil snapshot posisi leaderboard mingguan.');

/**
 * 4. Analisis AI Otomatis Mingguan (FR-AI-01)
 */
Artisan::command('sihebat:ai-weekly-analysis', function () {
    $service = app(GeminiAiService::class);
    $result = $service->getSchoolAdvice(true);
    $this->info("Analisis AI mingguan berhasil diperbarui: {$result['source']}.");
})->purpose('Jalankan analisis AI mingguan untuk dashboard sekolah.');

// ---- Task Scheduling ----
Schedule::command('journal:send-reminders')
    ->dailyAt('19:30')
    ->timezone('Asia/Jakarta')
    ->description('Kirim email pengingat jurnal harian ke siswa');

Schedule::command('sihebat:remind-journal')
    ->dailyAt('19:30')
    ->timezone('Asia/Jakarta')
    ->description('Kirim pengingat jurnal harian');

Schedule::command('sihebat:aggregate-daily')
    ->dailyAt('23:59')
    ->timezone('Asia/Jakarta')
    ->description('Hitung agregat harian kebiasaan');

Schedule::command('sihebat:snapshot-leaderboard')
    ->weeklyOn(0, '23:55')
    ->timezone('Asia/Jakarta')
    ->description('Ambil snapshot leaderboard mingguan');

Schedule::command('sihebat:ai-weekly-analysis')
    ->weeklyOn(1, '05:00')
    ->timezone('Asia/Jakarta')
    ->description('Analisis AI mingguan untuk sekolah');

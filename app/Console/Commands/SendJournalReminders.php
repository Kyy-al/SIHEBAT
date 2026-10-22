<?php

namespace App\Console\Commands;

use App\Mail\JournalReminderMail;
use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendJournalReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'journal:send-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Kirim email pengingat pengisian jurnal harian kepada siswa yang belum mengisi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today()->format('Y-m-d');

        // Find students who already submitted a final journal today
        $filledStudentIds = Journal::where('date', $today)
            ->where('is_draft', false)
            ->pluck('user_id');

        // Find students who haven't filled today and have a valid email
        $unfilledStudents = User::where('role', 'siswa')
            ->whereNotIn('id', $filledStudentIds)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        $count = 0;
        foreach ($unfilledStudents as $student) {
            try {
                Mail::to($student->email)->send(new JournalReminderMail($student));
                $count++;
            } catch (\Exception $e) {
                Log::warning("Gagal mengirim email pengingat ke {$student->email}: ".$e->getMessage());
            }
        }

        $this->info("Berhasil mengirim {$count} email pengingat jurnal hari ini ({$today}).");

        return Command::SUCCESS;
    }
}

<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JournalReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $student) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Jangan Lupa Isi Jurnal 7 Kebiasaan Hari Ini - SiHebat SMAN 1 Sungailiat',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.journal-reminder',
        );
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MatchSchedule extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $mailData)
    {
        $this->mailData = $mailData;
        $this->afterCommit(); // Send after database transaction is committed
    }

    public function build()
    {
        return $this->subject($this->mailData['subject'])
                    ->view('emails.matches.match_schedule')
                    ->with($this->mailData);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

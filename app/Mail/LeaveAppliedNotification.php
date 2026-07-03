<?php

namespace App\Mail;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveAppliedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $approverName;
    public $requestedDays;

    /**
     * Create a new message instance.
     */
    public function __construct(Leave $leave, string $approverName, int $requestedDays)
    {
        $this->leave = $leave;
        $this->approverName = $approverName;
        $this->requestedDays = $requestedDays;
    }

    /**
     * Get the message envelope.
     */
    public function jsonEnvelope(): Envelope
    {
        return new Envelope(
            subject: 'New Leave Request: ' . $this->leave->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.leave_applied',
        );
    }
}
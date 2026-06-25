<?php

namespace App\Mail;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $complaint;
    public $isAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Complaint $complaint, bool $isAdmin = false)
    {
        $this->user = $user;
        $this->complaint = $complaint;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isAdmin 
            ? "[New Complaint] #{$this->complaint->id}: {$this->complaint->title}"
            : "Complaint Submitted: #{$this->complaint->id} - {$this->complaint->title}";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.complaint-created',
        );
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

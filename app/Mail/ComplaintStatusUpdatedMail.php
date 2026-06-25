<?php

namespace App\Mail;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $complaint;
    public $status;
    public $comment;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Complaint $complaint, string $status, ?string $comment = null)
    {
        $this->user = $user;
        $this->complaint = $complaint;
        $this->status = $status;
        $this->comment = $comment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Complaint Update: #{$this->complaint->id} Status Changed to {$this->status}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.status-updated',
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

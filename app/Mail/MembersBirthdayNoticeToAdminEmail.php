<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MembersBirthdayNoticeToAdminEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $birthdayMembers;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct($birthdayMembers, string $message)
    {
        $this->birthdayMembers = $birthdayMembers;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Members Birthday Notice - '.now()->format('F j, Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.members-birthday-admin-notice-mail',
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

<?php

namespace App\Mail;

use App\Models\MessageBroadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessageBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    protected MessageBroadcast $messageBroadcast;

    /**
     * Create a new message instance.
     */
    public function __construct(MessageBroadcast $messageBroadcast)
    {
        $this->messageBroadcast = $messageBroadcast;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->messageBroadcast->title ?: 'Message Broadcast Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.message-broadcast-mail',
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

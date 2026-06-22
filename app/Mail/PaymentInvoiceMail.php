<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentInvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $student;
    public $isRemainder;

    /**
     * Create a new message instance.
     */
    public function __construct(User $student, $isRemainder = false)
    {
        $this->student = $student;
        $this->isRemainder = $isRemainder;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Dynamically change the subject line based on the payment type
        $subject = $this->isRemainder 
            ? 'Receipt: Final Admission Balance Paid - Wearefirst' 
            : 'Welcome to Wearefirst! Your Registration Invoice';

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
            view: 'emails.students.invoice',
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
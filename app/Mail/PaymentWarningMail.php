<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentWarningMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $balanceDue;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->balanceDue = 15000.00 - $user->admission_fee_paid;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required: Admission Fee Balance Due',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.students.warning',
        );
    }
}
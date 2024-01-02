<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AmountEscalationNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenantName;
    public $escalationRate;
    public $newRentAmount;
    public $unitName;
    public $propertyName;
    public $oldRentAmount;
    public $escalationStartDate;
    public $escalationEndDate;
    public $companyName;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData)
    {
        //
        $tenantName = $emailData['tenantName'];
        $escalationRate = $emailData['escalationRate'];
        $newRentAmount = $emailData['newRentAmount'];
        $unitName = $emailData['unitName'];
        $propertyName = $emailData['propertyName'];
        $oldRentAmount = $emailData['oldRentAmount'];
        $escalationStartDate = $emailData['escalationStartDate'];
        $escalationEndDate = $emailData['escalationEndDate'];
        $companyName = 'Hamud Realtors';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notice of Rent Increase',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.amount-escalation-notification-email',
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

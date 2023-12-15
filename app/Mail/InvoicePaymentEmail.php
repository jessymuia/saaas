<?php

namespace App\Mail;

use App\Models\InvoicePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoicePaymentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoicePayment;
    public $companyName = "Hamuud Realtors";

    /**
     * Create a new message instance.
     */
    public function __construct($invoicePaymentID)
    {
        //
        $this->invoicePayment = InvoicePayment::find($invoicePaymentID);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Receipt #' . $this->invoicePayment->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.invoice-payment',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(storage_path('app/' . $this->invoicePayment->document_path)),
        ];
    }
}

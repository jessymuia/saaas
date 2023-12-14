<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct($invoiceID)
    {
        //
        $this->invoice = Invoice::find($invoiceID);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice #' . $this->invoice->id . ' - ' . $this->invoice->tenancyAgreement->tenant->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.invoice',
            with: [
                'invoiceData' => [
                    'id' => $this->invoice->id,
                    'invoice_date' => $this->invoice->issue_date,
                    'invoice_due_date' => $this->invoice->invoice_due_date,
                    'invoice_for_month' => $this->invoice->invoice_for_month,
                ],
                'companyName' => 'Hamuud Realtors',
                'tenantName' => $this->invoice->tenancyAgreement->tenant->name,
            ]
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
            Attachment::fromPath(storage_path('app/' . $this->invoice->document_url)),
        ];
    }
}

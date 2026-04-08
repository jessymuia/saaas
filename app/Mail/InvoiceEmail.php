<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\ManualInvoices;
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
    public $invoiceType;

    /**
     * Create a new message instance.
     */
    public function __construct($invoiceID)
    {
        //
        $this->invoice = Invoice::find($invoiceID) ?? ManualInvoices::find($invoiceID);
        $this->invoiceType = ManualInvoices::find($invoiceID) ? 'manual' : 'system';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $addressedTo = $this->invoiceType === 'manual'
            ? $this->invoice->property_owner_id != null
                ? $this->invoice->propertyOwner->name
                : $this->invoice->client->name
            : $this->invoice->tenancyAgreement->tenant->name;
        return new Envelope(
            subject: 'Invoice #' . $this->invoice->id . ' - ' . $addressedTo,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $addressedTo = $this->invoiceType === 'manual'
            ? $this->invoice->property_owner_id != null
                ? $this->invoice->propertyOwner->name
                : $this->invoice->client->name
            : $this->invoice->tenancyAgreement->tenant->name;
        return new Content(
            view: 'mail.invoice',
            with: [
                'invoiceData' => [
                    'id' => $this->invoice->id,
                    'invoice_date' => $this->invoice->issue_date,
                    'invoice_due_date' => $this->invoice->invoice_due_date,
                    'invoice_for_month' => $this->invoice->invoice_for_month,
                ],
                'companyName' => 'Keyquest Realtors Limited',
                'tenantName' => $addressedTo,
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

<?php

namespace App\Mail;

use App\Models\CreditNote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CreditNoteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $creditNote;
    public $companyName = 'Hamuud Realtors';

    /**
     * Create a new message instance.
     */
    public function __construct($creditNoteID)
    {
        //
        $this->creditNote = CreditNote::find($creditNoteID);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Credit Note for Invoice #' . $this->creditNote->invoice->id . ' - ' . $this->creditNote->invoice->tenancyAgreement->tenant->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.credit-note',
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
            Attachment::fromPath(storage_path('app/'.$this->creditNote->document_url))
        ];
    }
}

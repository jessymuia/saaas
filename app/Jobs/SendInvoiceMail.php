<?php

namespace App\Jobs;

use App\Mail\InvoiceEmail;
use App\Models\EmailAttachments;
use App\Models\SentEmails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//    protected $email;
    protected $invoice;

    /**
     * Create a new job instance.
     */
    public function __construct($invoice)
    {
        //
//        $this->email = $email;
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        try {
            $email = new InvoiceEmail($this->invoice->id);

//             $email->to($this->invoice->tenancyAgreement->tenant->email);
//             $email->to("dundafuta@gmail.com");
//            Mail::to("dundafuta@gmail.com")
            Mail::to($this->invoice->tenancyAgreement->tenant->email)
                ->send($email);

            DB::transaction(function () use ($email) {
                $this->invoice->issue_date = now();
                $this->invoice->save();

                // insert record in sent emails
                $sentEmails = new SentEmails();

                $sentEmails->recipient_email = $this->invoice->tenancyAgreement->tenant->email;
                $sentEmails->subject = 'Invoice Email';
                $sentEmails->reference_id = $this->invoice->id;
                $sentEmails->body = $email->render();
                $sentEmails->delivery_status = 'SENT';

                $sentEmails->save();

                // get the file from storage and retrieve its details
                $emailAttachments = new EmailAttachments();
                $emailAttachments->sent_email_id = $sentEmails->id;
                $emailAttachments->file_name = File::name(storage_path('app/' . $this->invoice->document_url));
                $emailAttachments->file_size = File::size(storage_path('app/' . $this->invoice->document_url));
                $emailAttachments->mime_type = File::mimeType(storage_path('app/' . $this->invoice->document_url));
                $emailAttachments->full_file_path = storage_path('app/' . $this->invoice->document_url);

                $emailAttachments->save();
            });
//            return true;
        }catch (\Exception $e){
            Log::error("---------------------------------------------------------------------");
            Log::error("Failed sending email");
            Log::error('InvoiceEmail: ' . $e->getMessage());
            Log::error($e->getLine() . " ". $e->getFile());
            Log::error($e->getTraceAsString());
            Log::error("---------------------------------------------------------------------");
//            Notification::make('invoiceEmail')
//                ->title('Failed sending invoice')
//                ->danger()
//                ->send();

            // insert record in sent emails
            $sentEmails = new SentEmails();

            $sentEmails->recipient_email = $this->invoice->tenancyAgreement->tenant->email;
            $sentEmails->subject = 'Invoice Email';
            $sentEmails->reference_id = $this->invoice->id;
            $sentEmails->body = $email->render();
            $sentEmails->delivery_status = 'FAILED';
            $sentEmails->failure_reason = $e->getMessage();

            $sentEmails->save();

//            return false;
        }
    }
}

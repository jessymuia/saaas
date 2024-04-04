<?php

namespace App\Models;

use App\Mail\CreditNoteEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CreditNote extends DefaultAppModel
{
    protected $fillable = [
        'name',
        'invoice_id',
        'issue_date',
        'reason_for_issuance',
        'amount_credited',
        'notes',
        'document_url',
        'is_confirmed',
        'is_document_generated',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function generateCreditNoteDocument()
    {
        try {
            $vatTotal = 0;


            $creditNoteItems = '
                    <tr style="height: 30px;">
                        <td class="s8b" dir="ltr" colspan="3" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$this->name.'</td>
                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">1</td>
                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$this->amount_credited.'</td>
                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$this->amount_credited.'</td>
                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">None</td>
                    </tr>';

            $tenantName = $this->invoice->tenancyAgreement->tenant->name;
            $propertyName = $this->invoice->tenancyAgreement->unit->property->name;
            $unitName = $this->invoice->tenancyAgreement->unit->name;

            $detailsArray = [
                'customerName' => $unitName.
                    ' '.$tenantName.' '.$propertyName,
                'taxDate'=> Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
                    ->format('M j, Y'),
                'logoUrl'=>'file://'.getcwd().'/images/hamud_top_doc_logo.png',
                'creditNoteItemsHTML' => $creditNoteItems,
                'creditNoteNumber' => $this->id,
                'invoiceNumber' => $this->invoice->id,
                'creditNoteItemsTotal'=> number_format($this->amount_credited,2),
                'vatTotal' => number_format($vatTotal,2),
                'creditNoteTotal' => number_format($this->amount_credited + $vatTotal,2),
            ];

            $content = File::get(resource_path('documents/templates/credit-note-output-document.html'));

            foreach ($detailsArray as $key => $value) {
                $content = str_replace("@#$key", $value, $content);
            }

            $pdfName = Carbon::createFromFormat('Y-m-d H:i:s',$this->created_at)->format('F,Y').
                '_for_' .
                str_replace(" ","_",$tenantName) . '_' .
                str_replace(" ","_",$propertyName) .
                '_for_unit_' .
                str_replace(" ","_",$unitName) .
                '_invoice'.
                '_'.$this->id;

            $pdfPath = Storage::path('credit-notes') . '/' . $pdfName . '.pdf';

            $snappy = App::make('snappy.pdf');
            $snappy->setOption('enable-local-file-access', true);
            $snappy->setOption('disable-smart-shrinking', false);
            $snappy->setOption('margin-bottom', '1in');
            $snappy->setOption('margin-left', '1in');
            $snappy->setOption('margin-right', '1in');
            $snappy->setOption('margin-top', '1in');
//            $snappy->generateFromHtml($content, Storage::url($pdfPath));
//            Pdf::generateFromHtml($content, Storage::url($pdfPath));
            $snappy->generateFromHtml($content, $pdfPath);

            Log::error("Ndio hii: " . $pdfPath);

            Log::info('--------------------------------------------------------------------------');

            // check if file exists
            if (file_exists($pdfPath)) {
                $savedPath = explode('/', $pdfPath);
                // retrieve the string after the string 'app'
                foreach ($savedPath as $key => $value) {
                    if ($value == 'app') {
                        $savedPath = implode("/", array_slice($savedPath, $key + 1));
                        break;
                    }
                }

                $this->is_document_generated = 1;
                $this->document_url = $savedPath;
                $this->updated_by = auth()->user()->id;

                $this->save();

                return true;
            } else {
                return false;
            }
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return false;
        }
    }

    public function sendCreditNoteEmail()
    {
        try {
            $email = new CreditNoteEmail($this->id);

            // record the email sent
            DB::transaction(function () use ($email) {
                $this->issue_date = now();
                $this->save();

                // insert record in sent emails
                $sentEmails = new SentEmails();

                $sentEmails->recipient_email = $this->invoice->tenancyAgreement->tenant->email;
                $sentEmails->subject = 'Credit Note Email';
                $sentEmails->reference_id = $this->id;
                $sentEmails->body = $email->render();
                $sentEmails->delivery_status = 'SENT';

                $sentEmails->save();

                // get the file from storage and retrieve its details
                $emailAttachments = new EmailAttachments();
                $emailAttachments->sent_email_id = $sentEmails->id;
                $emailAttachments->file_name = File::name(storage_path('app/' . $this->document_url));
                $emailAttachments->file_size = File::size(storage_path('app/' . $this->document_url));
                $emailAttachments->mime_type = File::mimeType(storage_path('app/' . $this->document_url));
                $emailAttachments->full_file_path = Storage::path(storage_path('app/' . $this->document_url));

                $emailAttachments->save();

                 Mail::to($this->invoice->tenancyAgreement->tenant->email)
//                Mail::to('dundafuta@gmail.com')
                    ->send($email);
            });

            return true;
        }catch (\Exception $exception){
            Log::error("-------------------------------------------");
            Log::error("Error sending email: " . $exception->getMessage());
            Log::error($exception->getTraceAsString());
            Log::error("-------------------------------------------");

            // insert record in sent emails
            $sentEmails = new SentEmails();

            $sentEmails->recipient_email = $this->invoice->tenancyAgreement->tenant->email;
            $sentEmails->subject = 'Credit Note Email';
            $sentEmails->reference_id = $this->invoice->id;
            $sentEmails->body = $email->render();
            $sentEmails->delivery_status = 'FAILED';
            $sentEmails->failure_reason = $exception->getMessage();

            $sentEmails->save();

            return false;
        }
    }
}

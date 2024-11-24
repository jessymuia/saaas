<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use App\Jobs\SendInvoiceMail;
use App\Mail\InvoiceEmail;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Knp\Snappy\Pdf;
use App\Models\CompanyDetails;

class Invoice extends DefaultAppModel
{
    protected $fillable = [
        'tenancy_agreement_id',
        'comments',
        'invoice_status',
        'issue_date',
        'invoice_for_month',
        'invoice_due_date',
        'is_confirmed',
        'is_generated',
        'document_url',
        'status',
        'archive',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at'
    ];

    protected $appends = ['amount','unpaid_amount'];

    public function tenancyAgreement(){
        return $this->belongsTo(TenancyAgreement::class);
    }

    public function tenancyBills(){
        return $this->hasMany(TenancyBill::class,'invoice_id','id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->created_by = auth()->id();
            $model->saveQuietly();
        });

        static::updated(function ($model) {
            $model->updated_by = auth()->id();
            $model->saveQuietly();
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->id();
            $model->deleted_at = now();
            $model->save();
        });
    }

    public function scopeAccessibleByUser(Builder $query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        return $query->whereHas('tenancyAgreement.unit.property', function (Builder $query) use ($user) {
            $query->whereHas('users', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('property_management_users.status', true);
            });
        });
    }

    public function getAmountAttribute(){
        // sum amount of tenancy bills (includes the rent bill amount)
        $invoiceSum = 0;
        $invoiceSum += $this->tenancyBills()->sum('total_amount');
//        $this->tenancyAgreement()->each(function($item) use (&$invoiceSum){
//            $invoiceSum += $item->amount;
//        });

        return $invoiceSum;
    }

    public function getUnpaidAmountAttribute(){
        // sum amount of tenancy bills (includes the rent bill amount)
        $invoiceSum = 0;
        $invoiceSum += $this->tenancyBills()->sum('total_amount');
//        $this->tenancyAgreement()->each(function($item) use (&$invoiceSum){
//            $invoiceSum += $item->amount;
//        });

        return $invoiceSum - $this->invoicePayments()->sum('amount') - $this->creditNote()->sum('amount_credited');
    }

    public function creditNote(){
        return $this->hasMany(CreditNote::class,'invoice_id','id');
    }

    public function invoicePayments(){
        return $this->hasMany(InvoicePayment::class,'invoice_id','id');
    }

    public function totalDue()
    {
        // get the sum of all credit notes
        $creditNoteSum = $this->creditNote()->sum('amount_credited');
        // get the total due
        return $this->amount - $creditNoteSum - $this->invoicePayments()->sum('amount');
    }

    public function invoiceIsSent()
    {
        // check if the invoice has an issue date, document url and whether the invoice was sent successfully
        $isSuccessfullySent = SentEmails::query()
            ->where('reference_id', $this->id)
            ->where('delivery_status', '=','SENT')
            ->where('subject', 'Invoice Email')
            ->exists();

        return $this->issue_date && $this->document_url && $isSuccessfullySent;
    }

    public function generateDocument(Invoice $sI,$isRegenerate = false){
        // get all tenancy bills
        $tenancyBills = $this->tenancyBills()->get(['name','amount','vat','total_amount']);

        Log::error("Count of tenancy bills: ".count($tenancyBills));

        try {
            $invoiceItems = '';
            $billsSum = 0;
            $vatTotal = 0;

            // invoice current
            $invoice = Invoice::find($this->id);

            // check if any of below variables are empty
                TenancyAgreement::find($invoice->tenancy_agreement_id)->tenant->first()->name ??  throw new \Exception('Tenancy Agreement is missing');
                TenancyAgreement::find($invoice->tenancy_agreement_id)->property->first()->name ??  throw new \Exception('Property is missing');
                TenancyAgreement::find($invoice->tenancy_agreement_id)->unit->first()->name ??  throw new \Exception('Property Unit is missing');

            $tenantName = $invoice->tenancyAgreement
                ->join('tenants','tenancy_agreements.tenant_id','=','tenants.id')
                ->where('tenancy_agreements.id','=',$invoice->tenancy_agreement_id)
                ->first()->name;

            // check if the property has payment details
            PropertyPaymentDetails::query()
                ->where('property_id', $sI->tenancyAgreement->property->id)
                ->first() ??  throw new \Exception('Property Payment Details is missing for property id: '
                .$sI->tenancyAgreement->property->id .' - '. $sI->tenancyAgreement->property->name);

            $propertyName = $sI->tenancyAgreement->property->name;
            $propertyId = $sI->tenancyAgreement->property->id;

            $unitName = $sI->tenancyAgreement->unit->name;

            foreach ($tenancyBills as $tenancyBill) {
                $billsSum += $tenancyBill->amount;
                $vatTotal += $tenancyBill->vat;
                $invoiceItems .= '
                    <tr style="height: 30px;">
                        <td class="s_cell_with_right_left_border" colspan="3">'.$tenancyBill->name.'</td>
                        <td class="s_cell_with_right_left_border" colspan="1">1</td>
                        <td class="s_cell_with_right_left_border" colspan="1">'.$tenancyBill->amount.'</td>
                        <td class="s_cell_with_right_left_border" colspan="1">'.$tenancyBill->vat.'</td>
                        <td class="s_cell_with_right_left_border" colspan="1">'.$tenancyBill->total_amount.'</td>
                    </tr>';
            }

            // count the tenancy bills, check if the count is past 19
            $remainingRows = 14 - $tenancyBills->count();

            for ($i = 0; $i < $remainingRows; $i++) {
                $invoiceItems .= '
                    <tr style="height: 30px;">
                        <td class="s_cell_with_right_left_border" colspan="3"></td>
                        <td class="s_cell_with_right_left_border" colspan="1"></td>
                        <td class="s_cell_with_right_left_border" colspan="1"></td>
                        <td class="s_cell_with_right_left_border" colspan="1"></td>
                        <td class="s_cell_with_right_left_border" colspan="1"></td>
                    </tr>';
            }

            // add the bottom row of the invoice
            $invoiceItems .= '
                <tr style="height: 30px;">
                    <td class="s_bottom_cell" colspan="3"></td>
                    <td class="s_bottom_cell" colspan="1"></td>
                    <td class="s_bottom_cell" colspan="1"></td>
                    <td class="s_bottom_cell" colspan="1"></td>
                    <td class="s_bottom_cell" colspan="1"></td>
                </tr>';


            //get the details of latest company; update to logged in company
            $company = CompanyDetails::latest()->first();

            $detailsArray = [
                'companyName' => $company->name,
                'companyAddress' => $company->address,
                'companyEmail' => $company->email,
                'companyPhoneNumber' => $company->phone_number,
                'companyLocation' => $company->location,
                'customerName' => 'HSE#'.$unitName.' '.$tenantName,
                'propertyName' => $propertyName,
//                'invoiceDate'=> Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
//                    ->format('M j, Y'),
                'invoiceDate'=> Carbon::createFromFormat('Y-m-d', $this->invoice_for_month)
                    ->format('M j, Y'),
                // 'logoUrl'=>'file://'.getcwd().'/images/hamud_top_doc_logo.png',
                'logoUrl'=> 'file://'.storage_path('/app/public/'.$company->logo),
                'invoiceItemsHTML' => $invoiceItems,
                'invoiceNumber' => $this->id,
                'payBillAccountNumber'=> $tenantName.'/'.$unitName,
                'billsTotal'=> number_format($billsSum,2),
                'vatTotal' => number_format($vatTotal,2),
                'invoiceTotal' => number_format($invoice->amount,2),
                'bankAccountName' => strtoupper(PropertyPaymentDetails::query()
                    ->where('property_id', $propertyId)
                    ->first()->account_name),
                'bankAccountNumber' => strtoupper(PropertyPaymentDetails::query()
                    ->where('property_id', $propertyId)
                    ->first()->account_number),
                'bankName' => strtoupper(PropertyPaymentDetails::query()
                    ->where('property_id', $propertyId)
                    ->first()->bank_name),
                'mpesaPaybillNumber' => strtoupper(PropertyPaymentDetails::query()
                    ->where('property_id', $propertyId)
                    ->first()->mpesa_paybill_number),
            ];

            $content = File::get(resource_path('documents/templates/invoice-output-document.html'));

            foreach ($detailsArray as $key => $value) {
                $content = str_replace("@#$key", $value, $content);
            }




            Log::info('Tenant name: '. $tenantName);
            Log::info('Tenancy agreement id: '. TenancyAgreement::find($invoice->tenancy_agreement_id)->id);
            Log::info('Tenant name: '. TenancyAgreement::find($invoice->tenancy_agreement_id)->first()->tenant_id);

            $pdfName = Carbon::createFromFormat('Y-m-d H:i:s',$this->created_at)->format('F, Y').
                '_for_' .
                $tenantName . '_' .
                $propertyName .
                '_for_unit_' .
                $unitName .
                '_invoice'.
                '_'.$this->id;

            // remove any spaces in the pdf name, and any slashes and special characters
            $pdfName = preg_replace('/[\s\/!@#]+/', '_', $pdfName);

            // get the path but without the clatter file system
            $pdfPath = Storage::path('invoices') . '/' . $pdfName . '.pdf';

            // delete the file if it exists already
            if ($isRegenerate){
                Log::info('Deleting file: '.$pdfPath);
                // check if file exists then unlink
                if (file_exists($pdfPath)){
                    unlink($pdfPath);
                }
            }

//            Storage::url($pdfPath);

//            Storage::put($pdfPath, $content);
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

//            \Barryvdh\DomPDF\PDF::loadView('documents.templates.invoice-output-document', $detailsArray)->save($pdfPath);
//            \Barryvdh\DomPDF\Facade\Pdf::loadView('documents.templates.invoice-output-document', $detailsArray)->save($pdfPath);
//            $pdf = SnappyPdf::loadView('documents.templates.invoice-output-document', $detailsArray)->output();
//            Log::error("Ndio hii: " . $pdf);
//            $pdf = SnappyPdf::loadView('documents.templates.invoice-output-document', $detailsArray);
//            $pdf = SnappyPdf::loadView('documents.templates.invoice-output-document', $detailsArray)->save($pdfPath);
//            $pdf = SnappyPdf::loadHTML($content)->output();
            Log::error("Ndio hii: " . $pdfPath);
//            Storage::put($pdfPath, $pdf->output());

//            Log::info(Storage::url($pdfPath));

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
                $this->is_generated = 1;
                $this->document_url = $savedPath;
                $this->updated_by = auth()->user()->id;

                $this->save();

                return true;
            } else {
                return false;
            }
        }catch (\Exception $exception){
            // print the stack trace and the error message
            Log::error("Failed to generate document for invoice: {$this->id}");
            Log::error($exception->getLine());
            Log::error($exception->getTraceAsString());
            Log::error($exception->getMessage());
            return false;
        }
    }

    public function sendInvoiceMail()
    {
        SendInvoiceMail::dispatch($this);
//        try {
//            $email = new InvoiceEmail($this->id);
//
//            // $email->to($this->tenancyAgreement->tenant->email);
//            Mail::to('dundafuta@gmail.com')
//                ->send($email);
//
//            DB::transaction(function () use ($email) {
//                $this->issue_date = now();
//                $this->save();
//
//                // insert record in sent emails
//                $sentEmails = new SentEmails();
//
//                $sentEmails->recipient_email = $this->tenancyAgreement->tenant->email;
//                $sentEmails->subject = 'Invoice Email';
//                $sentEmails->body = $email->render();
//                $sentEmails->delivery_status = 'SENT';
//
//                $sentEmails->save();
//
//                // get the file from storage and retrieve its details
//                $emailAttachments = new EmailAttachments();
//                $emailAttachments->sent_email_id = $sentEmails->id;
//                $emailAttachments->file_name = File::name(storage_path('app/' . $this->document_url));
//                $emailAttachments->file_size = File::size(storage_path('app/' . $this->document_url));
//                $emailAttachments->mime_type = File::mimeType(storage_path('app/' . $this->document_url));
//                $emailAttachments->full_file_path = storage_path('app/' . $this->document_url);
//
//                $emailAttachments->save();
//            });
//            return true;
//        }catch (\Exception $e){
//            Log::error("---------------------------------------------------------------------");
//            Log::error("Failed sending email");
//            Log::error('InvoiceEmail: ' . $e->getMessage());
//            Log::error($e->getLine() . " ". $e->getFile());
//            Log::error($e->getTraceAsString());
//            Log::error("---------------------------------------------------------------------");
////            Notification::make('invoiceEmail')
////                ->title('Failed sending invoice')
////                ->danger()
////                ->send();
//
//            // insert record in sent emails
//            $sentEmails = new SentEmails();
//
//            $sentEmails->recipient_email = $this->tenancyAgreement->tenant->email;
//            $sentEmails->subject = 'Invoice Email';
//            $sentEmails->body = $email->render();
//            $sentEmails->delivery_status = 'FAILED';
//            $sentEmails->failure_reason = $e->getMessage();
//
//            $sentEmails->save();
//
//            return false;
//        }
    }
}

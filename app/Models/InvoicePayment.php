<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InvoicePayment extends DefaultAppModel
{
    protected $fillable = [
        'invoice_id',
        'tenant_id',
        'payment_type_id',
        'received_by',
        'payment_date',
        'amount',
        'paid_by',
        'payment_reference',
        'description',
        'document_generated_at',
        'document_sent_at',
        'document_generated_by',
        'is_confirmed',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    // foreign keys
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(RefPaymentType::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function documentGeneratedBy()
    {
        return $this->belongsTo(User::class, 'document_generated_by');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function generateInvoicePaymentReceipt()
    {
        try {
            // invoice current
//            $invoice = Invoice::find($this->id);

            // check if any of below variables are empty
            TenancyAgreement::find($this->invoice->tenancy_agreement_id)->tenant->first()->name ??  throw new \Exception('Tenancy Agreement is missing');
            TenancyAgreement::find($this->invoice->tenancy_agreement_id)->property->first()->name ??  throw new \Exception('Property is missing');
            TenancyAgreement::find($this->invoice->tenancy_agreement_id)->unit->first()->name ??  throw new \Exception('Property Unit is missing');

            $tenantName = $this->invoice->tenancyAgreement->tenant->name;

            $propertyName = $this->invoice->tenancyAgreement->property->name;

            $unitName = $this->invoice->tenancyAgreement->unit->name;

            $receiptItems = '
                    <tr style="height: 30px;">
                        <td class="s8b" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">1</td>
                        <td class="s8" dir="ltr" colspan="4" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;"> Invoice Payment for #'. $this->invoice->id .'</td>
                        <td class="s8" dir="ltr" colspan="2" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$this->amount.'</td>
                    </tr>';

            $detailsArray = [
                'customerName' => $unitName.' '.$tenantName,
                'propertyName' => $propertyName,
                'receiptDate' => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
                    ->format('M j, Y'),
                'logoUrl'=>'file://'.getcwd().'/images/hamud_top_doc_logo.png',
                'receiptItemsHTML' => $receiptItems,
                'receiptNumber' => $this->id,
                'totalAmountPaid' => number_format($this->amount,2),
                'paymentType' => $this->paymentType->type,
                'paidBy' => $this->paid_by,
            ];

            $content = File::get(resource_path('documents/templates/invoice-payment-receipt-output-document.html'));

            foreach ($detailsArray as $key => $value) {
                $content = str_replace("@#$key", $value, $content);
            }

            Log::error("Created at: ".$this->created_at);

            $pdfName = "invoice_payment_receipt".
                '_for_' .
                $tenantName . '_' .
                $propertyName .
                '_for_unit_' .
                $unitName .
                '_'. $this->id. '_'.
                Carbon::createFromFormat('Y-m-d H:i:s',$this->created_at)->format('F, Y');

            // get the path but without the clatter file system
            $pdfPath = Storage::path('invoice_payments') . '/' . $pdfName . '.pdf';

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

                $this->document_path = $savedPath;
                $this->document_generated_at = now();
                $this->document_generated_by = auth()->user()->id;
                $this->updated_by = auth()->user()->id;

                $this->save();

                return true;
            } else {
                return false;
            }
        }catch (\Exception $exception){
            Log::error($exception->getTraceAsString());
            Log::error($exception->getMessage());
            return false;
        }
    }
}

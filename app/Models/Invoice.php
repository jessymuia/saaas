<?php

namespace App\Models;

use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Knp\Snappy\Pdf;

class Invoice extends DefaultAppModel
{
    protected $fillable = [
        'tenancy_agreement_id',
        'comments',
        'invoice_status',
        'issue_date',
        'is_generated',
        'document_url',
        'status',
        'archive',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function tenancyAgreement(){
        return $this->belongsTo(TenancyAgreement::class);
    }

    public function tenancyBills(){
        return $this->hasMany(TenancyBill::class,'invoice_id','id');
    }

    public function generateDocument(Invoice $sI){
        // get all tenancy bills
        $tenancyBills = $this->tenancyBills()->get(['name','amount']);

        Log::error("Count of tenancy bills: ".count($tenancyBills));

        try {
            $invoiceItems = '';
            $billsSum = 0;
            $vatTotal = 0;

            foreach ($tenancyBills as $tenancyBill) {
                $billsSum += $tenancyBill->amount;
                $invoiceItems .= '
                    <tr style="height: 30px;">
                        <td class="s8b" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">1</td>
                        <td class="s8" dir="ltr" colspan="3" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$tenancyBill->name.'</td>
                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$tenancyBill->amount.'</td>
                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$tenancyBill->amount.'</td>
                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">None</td>
                    </tr>';
            }

            $detailsArray = [
                'invoiceDate'=> Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
                    ->format('M j, Y'),
                'logoUrl'=>'file://'.getcwd().'/images/hamud_top_doc_logo.png',
                'invoiceItemsHTML' => $invoiceItems,
                'invoiceNumber' => $this->id,
                'billsTotal'=> number_format($billsSum,2),
                'vatTotal' => number_format($vatTotal,2),
                'invoiceTotal' => number_format($billsSum + $vatTotal,2),
            ];

            $detailsArrayForView = [
                'invoiceDate'=> Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
                    ->format('M j, Y'),
                'logoUrl'=>'file://'.getcwd().'/images/hamud_top_doc_logo.png',
                'invoiceItemsHTML' => $invoiceItems,
                'invoiceNumber' => $this->id,
                'billsTotal'=> number_format($billsSum,2),
                'vatTotal' => number_format($vatTotal,2),
                'invoiceTotal' => number_format($billsSum + $vatTotal,2),
            ];

            $content = file_get_contents(public_path('documents/templates/invoice-output-document.html'));

//            Log::error("File content: ".$content);

            foreach ($detailsArray as $key => $value) {
                $content = str_replace("@#$key", $value, $content);
            }

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

            $propertyName = $sI->tenancyAgreement->property->name;

            $unitName = $sI->tenancyAgreement->unit->name;


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

            $pdfPath = 'invoices/' . $pdfName . '.pdf';
//            Storage::url($pdfPath);

//            Storage::put($pdfPath, $content);
            $snappy = App::make('snappy.pdf');
            $snappy->setOption('enable-local-file-access', true);
            $snappy->setOption('disable-smart-shrinking', false);
            $snappy->setOption('margin-bottom', '0in');
            $snappy->setOption('margin-left', '0in');
            $snappy->setOption('margin-right', '0in');
            $snappy->setOption('margin-top', '0in');
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
                $this->is_generated = 1;
                $this->document_url = $pdfPath;
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
}

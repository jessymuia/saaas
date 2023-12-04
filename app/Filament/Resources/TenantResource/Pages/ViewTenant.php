<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\InvoiceResource\RelationManagers\CreditNoteRelationManager;
use App\Filament\Resources\TenantResource;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\TenancyAgreement;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            //
            TenantResource\Widgets\TenantPaymentsDueWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            //
        ];
    }

    private function generateStatementOfAccount($tenantID)
    {
        // get all invoices and convert to array
        $invoices = Invoice::query()
            ->whereHas('tenancyAgreement', function ($query) use ($tenantID) {
                $query->where('tenant_id', '=', $tenantID);
            })
            ->orderBy('created_at', 'desc')
            ->select(['id', 'created_at as transaction_date'])
            ->selectRaw('concat("INV #", id,". Due on ") as transaction, concat("invoice") as transaction_type')
//            ->with('creditNote', function ($query){
//                $query->select('id');
//            })
            ->get(['amount'])
            ->toArray();
//        dd($invoices);
        // get all credit notes and convert to array
        $creditNotes = CreditNote::query()
            ->orderBy('created_at', 'desc')
            ->whereHas('invoice', function ($query) use ($tenantID) {
                $query->whereHas('tenancyAgreement', function ($query) use ($tenantID) {
                    $query->where('tenant_id', '=', $tenantID);
                });
            })
            ->select(['id', 'created_at as transaction_date','amount_credited as amount'])
            ->selectRaw('concat("CRN #", id,". ", name,". Issued on ") as transaction, concat("credit_note") as transaction_type')
            ->get()
            ->toArray();
//        dd($creditNotes);
        // get all invoice payments
        $invoicePayments = InvoicePayment::query()
            ->orderBy('payment_date', 'desc')
            ->whereHas('invoice', function ($query) use ($tenantID) {
                $query->whereHas('tenancyAgreement', function ($query) use ($tenantID) {
                    $query->where('tenant_id', '=', $tenantID);
                });
            })
            ->select(['id', 'payment_date as transaction_date','amount'])
            ->selectRaw('concat("PMT #", id,". Paid on ") as transaction, concat("payment") as transaction_type')
            ->get()
            ->toArray();
//        dd($invoicePayments);

        // merge the three arrays
        $transactions = array_merge($invoices, $creditNotes, $invoicePayments);
        // sort the array by transaction date
        usort($transactions, function ($a, $b) {
            return $a['transaction_date'] <=> $b['transaction_date'];
        });

        // obtain the total due
        $totalDue = 0;
        foreach ($transactions as $transaction) {
            if ($transaction['transaction_type'] == 'invoice') {
                $totalDue += $transaction['amount'];
            } elseif ($transaction['transaction_type'] == 'credit_note') {
                $totalDue -= $transaction['amount'];
            } elseif ($transaction['transaction_type'] == 'payment') {
                $totalDue -= $transaction['amount'];
            }
        }

        $tenant = TenancyAgreement::query()
            ->where('tenant_id','=',$tenantID)
            ->first();

//        dd($totalDue);
        try {
            $statementOfAccountItems = '';

            $propertyName = $sI->tenancyAgreement->property->name;

            $unitName = $sI->tenancyAgreement->unit->name;

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
                'customerName' => $unitName.' '.$tenantName,
                'invoiceDate'=> Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
                    ->format('M j, Y'),
                'logoUrl'=>'file://'.getcwd().'/images/hamud_top_doc_logo.png',
                'invoiceItemsHTML' => $invoiceItems,
                'invoiceNumber' => $this->id,
                'payBillAccountNumber'=> $tenantName.'/'.$unitName,
                'billsTotal'=> number_format($billsSum,2),
                'vatTotal' => number_format($vatTotal,2),
                'invoiceTotal' => number_format($billsSum + $vatTotal,2),
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

            // get the path but without the clatter file system
            $pdfPath = Storage::path('invoices') . '/' . $pdfName . '.pdf';

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
            Log::error($exception->getMessage());
            return false;
        }
    }
}

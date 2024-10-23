<?php

namespace App\Models;

use App\Models\CompanyDetails;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PropertyOwners extends DefaultAppModel
{
    protected $fillable = [
        'property_id',
        'name',
        'email',
        'phone_number',
        'address',
        'balance_carried_forward',
        'has_invoice_for_balance_carried_forward',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at'
    ];

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

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function createInvoiceForBalanceCarriedForward()
    {
        // check whether the balance carried forward is greater than zero
        if ($this->balance_carried_forward <= 0){
            return [
                "status" => -1,
                "message" => "Balance carried forward is zero or less than zero. No invoice will be created."
            ];
        }
        // check if the invoice for balance carried forward exists
        if($this->has_invoice_for_balance_carried_forward){
            return [
                "status" => -1,
                "message" => "Invoice for balance carried forward already exists."
            ];
        }

        try {
            // create invoice for balance carried forward
            DB::transaction(function (){
                // create the manual invoice
                $manualInvoice = ManualInvoices::create([
                    'comments' => "Invoice for Balance Carried Forward",
                    'property_owner_id' => $this->id,
                    'invoice_status' => 'unpaid',
                    'invoice_for_month' => now()->format('Y-m-d'),
                    'invoice_due_date' => Carbon::parse(now())->addDays(5)->format('Y-m-d'), // 5 days from now
                    'created_by' => auth()->user()->id,
                ]);

                // create the manual invoice item
                ManualInvoiceItem::create([
                    'manual_invoice_id' => $manualInvoice->id,
                    'name' => "Balance Carried Forward Invoice",
                    'bill_date' => now(),
                    'due_date' => Carbon::parse(now())->addDays(5)->format('Y-m-d'), // 5 days from now
                    'amount' => $this->balance_carried_forward,
                    'vat' => 0.0,
                    'total_amount' => $this->balance_carried_forward,
                    'billing_type_id' => RefBillingType::query()->select('id')->first()->id,
                    'category' => 'balance_carried_forward',
                    'created_by' => auth()->user()->id,
                ]);

                // update the tenancy agreement to reflect that the invoice for balance carried forward has been created
                $this->has_invoice_for_balance_carried_forward = true;
                $this->save();
            });
            return [
                "status" => 1,
                "message" => "Invoice for balance carried forward created successfully."
            ];
        } catch (\Exception $e) {
            Log::error("Error creating invoice for balance carried forward: ".$e->getMessage());
            return [
                "status" => -1,
                "message" => "Error creating invoice for balance carried forward."
            ];
        }
    }

    public function generateStatementOfAccount()
    {
        // get all invoices and convert to array
//        $invoices = Invoice::query()
//            ->where('tenancy_agreement_id', '=', $tenancyAgreement->id)
//            ->orderBy('created_at', 'desc')
//            ->select(['id', 'invoice_for_month as transaction_date','invoice_due_date'])
//            ->selectRaw("concat('INV #', id,'. Due on ', TO_CHAR(invoice_for_month,'Mon DD, YYYY')) as transaction, concat('invoice') as transaction_type")
////            ->with('creditNote', function ($query){
////                $query->select('id');
////            })
//            ->get(['amount','unpaid_amount'])
//            ->toArray();

        $invoices = ManualInvoices::query()
            ->where('property_owner_id','=',$this->id)
            ->orderBy('created_at', 'desc')
            ->select(['id', 'invoice_for_month as transaction_date','invoice_due_date'])
            ->selectRaw("concat('INV #', id,'. Due on ', TO_CHAR(invoice_for_month,'Mon DD, YYYY')) as transaction, concat('invoice') as transaction_type")
            ->get(['amount','unpaid_amount'])
            ->toArray();

//        dd($invoices);

        // get all credit notes and convert to array
//        $creditNotes = CreditNote::query()
//            ->orderBy('created_at', 'desc')
//            ->whereHas('invoice', function ($query) use ($tenancyAgreement) {
//                $query->where('tenancy_agreement_id', '=', $tenancyAgreement->id);
//            })
//            ->select(['id', 'created_at as transaction_date','amount_credited as amount'])
//            ->selectRaw("concat('CRN #', id,'. ', name,'. Issued on ') as transaction, concat('credit_note') as transaction_type")
//            ->get()
//            ->toArray();

        // get all invoice payments
        $invoicePayments = InvoicePayment::query()
            ->orderBy('payment_date', 'desc')
            ->where('property_owner_id','=',$this->id)
            ->select(['id', 'payment_date as transaction_date','amount'])
            ->selectRaw("concat('PMT #', id,'. Paid on ') as transaction, concat('payment') as transaction_type")
            ->get()
            ->toArray();

        // merge the three arrays
        $transactions = array_merge($invoices, $creditNotes ?? [], $invoicePayments ?? []);
        // sort the array by transaction date
        usort($transactions, function ($a, $b) {
            return $a['transaction_date'] <=> $b['transaction_date'];
        });

        // obtain the total due
        $amountDue = 0;
        foreach ($transactions as $transaction) {
            if ($transaction['transaction_type'] == 'invoice') {
                $amountDue += $transaction['amount'];
            } elseif ($transaction['transaction_type'] == 'credit_note') {
                $amountDue -= $transaction['amount'];
            } elseif ($transaction['transaction_type'] == 'payment') {
                $amountDue -= $transaction['amount'];
            }
        }

//        $tenant = $tenancyAgreement->tenant()->first();
        Log::error("Error: " . $this->created_at);

//        dd($totalDue);
        try {
            $statementOfAccountItems = '
                <tr style="height: 30px;">
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="2" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::createFromFormat('Y-m-d H:m:s',$this->created_at)->format('M j, Y').'</td>
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="3" style="text-align: left; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000;  color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">Balance Forward</td>
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;"></td>
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.number_format(0,2).'</td>
                </tr>';

//            $propertyName = $tenancyAgreement->property->name;
            $propertyName = $this->property->name;

//            $unitName = $tenancyAgreement->unit->name;
            $unitName = "";
            $balanceCarriedForward = 0;
            $runningBalance = $balanceCarriedForward;
            $runningAmountDue = 0;

//            foreach ($transactions as $transaction) {
//                $signOfTransaction = $transaction['transaction_type'] == 'invoice' ? '+' : '-';
//                $runningBalance += $transaction['transaction_type'] == 'invoice' ? $amountDue - $transaction['amount'] : $amountDue + $transaction['amount'];
//                $statementOfAccountItems .= '
//                    <tr style="height: 30px;">
//                        <td class="s8b" dir="ltr" colspan="1" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::createFromFormat('Y-m-d H:i:s',$transaction['transaction_date'])->format('F j, Y').'</td>
//                        <td class="s8" dir="ltr" colspan="4" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$transaction['transaction'].'</td>
//                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: right; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$signOfTransaction.number_format($transaction['amount'],2).'</td>
//                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: right; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.number_format($runningBalance,2).'</td>
//                    </tr>';
//            }
            foreach ($transactions as $transaction) {
                $signOfTransaction = $transaction['transaction_type'] == 'invoice' ? '' : '-';
                $runningBalance += $transaction['transaction_type'] == 'invoice' ? $runningAmountDue + $transaction['amount'] : $runningAmountDue - $transaction['amount'];
                $statementOfAccountItems .= '
                    <tr style="height: 30px;">
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="2" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::parse($transaction['transaction_date'])->format('M j, Y').'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="3" style="text-align: left; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000;  color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$transaction['transaction'].'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$signOfTransaction.number_format($transaction['amount'],2).'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.number_format($runningBalance,2).'</td>
                    </tr>';
            }

            // check if the statement of account items can fit in one page,
            // if they are more than one page, then add a page break
            // if less than one page, padd the table with empty rows
            $statementOfAccountItems .= str_repeat(
                '
                        <tr style="height: 30px;">
                            <td class="s_cell_with_right_left_border" colspan="2"></td>
                            <td class="s_cell_with_right_left_border" colspan="3"></td>
                            <td class="s_cell_with_right_left_border" colspan="1"></td>
                            <td class="s_cell_with_right_left_border" colspan="1"></td>
                        </tr>',
                19 - count($transactions)
            );

            $current = 0;
            $oneToThirtyPastDue = 0;
            $thirtyOneToSixtyPastDue = 0;
            $sixtyOneToNinetyPastDue = 0;
            $overNinetyPastDue = 0;
            // iterate over the invoices obtaining the current, 1-30, 31-60, 61-90, over 90
            foreach ($invoices as $invoice) {
                $invoiceDueDate = Carbon::createFromFormat('Y-m-d',$invoice['invoice_due_date']);
                $daysDifference = $invoiceDueDate->diffInDays(Carbon::now());
                if ($daysDifference > 90){
                    $overNinetyPastDue += $invoice['amount'];
                } elseif ($daysDifference > 60){
                    $sixtyOneToNinetyPastDue += $invoice['amount'];
                } elseif ($daysDifference > 30){
                    $thirtyOneToSixtyPastDue += $invoice['amount'];
                } elseif ($daysDifference > 0 ){
                    $oneToThirtyPastDue += $invoice['amount'];
                } elseif ($daysDifference == 0){
                    $current += $invoice['amount'];
                }
            }
            //get the latest company; switch to company of logged in user
            $company = CompanyDetails::latest()->first();

            $detailsArray = [
//                'customerName' => $unitName.' '.$tenancyAgreement->tenant->name,
                'companyName' => $company->name,
                'companyAddress' => $company->address,
                'companyEmail' => $company->email,
                'companyPhoneNumber' => $company->phone_number,
                'companyLocation' => $company->location,
                'customerName' => $unitName.' '.$this->name,
                'propertyName' => $propertyName,
                'dateGenerated'=> Carbon::now()->format('M j, Y'),
                'logoUrl'=> 'file://'.storage_path('/app/public/'.$company->logo),
                // 'logoUrl'=>'file://'.getcwd().'/images/hamud_top_doc_logo.png',
                'amountDue' => number_format($amountDue,2),
                'amountEnc' => number_format(0,2),
                'statementOfAccountItemsHTML' => $statementOfAccountItems,
                'current'=> number_format($current,2),
                'oneToThirtyPastDue'=> number_format($oneToThirtyPastDue,2),
                'thirtyOneToSixtyPastDue'=> number_format($thirtyOneToSixtyPastDue,2),
                'sixtyOneToNinetyPastDue'=> number_format($sixtyOneToNinetyPastDue,2),
                'overNinetyPastDue'=> number_format($overNinetyPastDue,2),
            ];

            $content = File::get(resource_path('documents/templates/statement-of-account-output-document.html'));

            foreach ($detailsArray as $key => $value) {
                $content = str_replace("@#$key", $value, $content);
            }

            $pdfName = "Statement of Account".
                '_for_' .
                $this->name . '_' .
                $propertyName .
                '_for_unit_' .
                $unitName .
                '_'.$this->id;

            // get the path but without the clatter file system
            $pdfPath = Storage::path('statements_of_account') . '/' . $pdfName . '.pdf';

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

            // trigger download of the file then delete it
            return response()->download($pdfPath)->deleteFileAfterSend(true);

            // check if file exists
//            if (file_exists($pdfPath)) {
//                $savedPath = explode('/', $pdfPath);
//                // retrieve the string after the string 'app'
//                foreach ($savedPath as $key => $value) {
//                    if ($value == 'app') {
//                        $savedPath = implode("/", array_slice($savedPath, $key + 1));
//                        break;
//                    }
//                }
////                $this->is_generated = 1;
////                $this->document_url = $savedPath;
////                $this->updated_by = auth()->user()->id;
////
////                $this->save();
//
//                return true;
//            } else {
//                return false;
//            }
        }catch (\Exception $exception){
            Log::error($exception->getTraceAsString());
            Log::error($exception->getMessage());
            return false;
        }
    }

    public function generateStatementOfAccountVersionTwo()
    {
        // get all invoices and convert to array
//        $invoices = Invoice::query()
//            ->where('tenancy_agreement_id', '=', $tenancyAgreement->id)
//            ->orderBy('created_at', 'desc')
//            ->select(['id', 'invoice_for_month as transaction_date','invoice_due_date'])
//            ->selectRaw("concat('INV #', id,'. Due on ', TO_CHAR(invoice_for_month,'Mon DD, YYYY')) as transaction, concat('invoice') as transaction_type")
////            ->with('creditNote', function ($query){
////                $query->select('id');
////            })
//            ->get(['amount','unpaid_amount'])
//            ->toArray();

        $invoices = ManualInvoices::query()
            ->where('property_owner_id', '=', $this->id)
            ->orderBy('created_at', 'desc')
            ->select(['id as invoice_id', 'invoice_for_month as transaction_date','invoice_due_date'])
            ->selectRaw("concat('INV #', id,'. Due on ', TO_CHAR(invoice_for_month,'Mon DD, YYYY')) as transaction, concat('invoice') as transaction_type")
            ->where('is_confirmed','=', true)
            ->get(['amount','unpaid_amount'])
            ->toArray();

        foreach ($invoices as $key => $invoice){
            // check for null results
            if(Invoice::query()->find($invoice['invoice_id']) == null)
                Log::error("This one is an error, invoice id: ".$invoice['invoice_id']);
            $invoice['unpaid_amount'] = ManualInvoices::query()->find($invoice['invoice_id'])->unpaid_amount;
            $invoice['amount'] = ManualInvoices::query()->find($invoice['invoice_id'])->amount;
            $invoices[$key] = $invoice;
        }

        usort($invoices, function ($a, $b) {
            return $a['transaction_date'] <=> $b['transaction_date'];
        });

//        dd($invoices);

        // get all credit notes and convert to array
//        $creditNotes = CreditNote::query()
//            ->orderBy('created_at', 'desc')
//            ->whereHas('invoice', function ($query) use ($tenancyAgreement) {
//                $query->where('tenancy_agreement_id', '=', $tenancyAgreement->id);
//            })
//            ->select(['id', 'created_at as transaction_date','amount_credited as amount'])
//            ->selectRaw("concat('CRN #', id,'. ', name,'. Issued on ') as transaction, concat('credit_note') as transaction_type")
//            ->get()
//            ->toArray();

        // get all invoice payments
        $invoicePayments = InvoicePayment::query()
            ->orderBy('payment_date', 'desc')
            ->where('property_owner_id','=',$this->id)
            ->select(['id', 'invoice_id', 'payment_date as transaction_date','amount'])
            ->selectRaw("concat('PMT #', id,'. Paid on ') as transaction, concat('payment') as transaction_type")
            ->where('is_confirmed', '=', true)
            ->get()
            ->toArray();

        // merge the three arrays
        $transactions = array_merge($invoices, $creditNotes ?? [], $invoicePayments ?? []);
        // sort the array by transaction date
        usort($transactions, function ($a, $b) {
            return $a['transaction_date'] <=> $b['transaction_date'];
        });

        // obtain the total due
        $amountDue = 0;
        foreach ($transactions as $transaction) {
            if ($transaction['transaction_type'] == 'invoice') {
                $amountDue += $transaction['amount'];
            } elseif ($transaction['transaction_type'] == 'credit_note') {
                $amountDue -= $transaction['amount'];
            } elseif ($transaction['transaction_type'] == 'payment') {
                $amountDue -= $transaction['amount'];
            }
        }

//        $tenant = $tenancyAgreement->tenant()->first();
        Log::error("Error: " . $this->created_at);

//        dd($totalDue);
        try {
            $statementOfAccountItems = '
                <tr style="height: 30px;">
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="2" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::createFromFormat('Y-m-d H:m:s',$this->created_at)->format('M j, Y').'</td>
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="3" style="text-align: left; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000;  color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">Balance Forward</td>
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;"></td>
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.number_format(0,2).'</td>
                </tr>';

//            $propertyName = $tenancyAgreement->property->name;
            $propertyName = $this->property->name;

//            $unitName = $tenancyAgreement->unit->name;
            $unitName = "";
            $balanceCarriedForward = 0;
            $runningBalance = $balanceCarriedForward;
            $runningAmountDue = 0;

//            foreach ($transactions as $transaction) {
//                $signOfTransaction = $transaction['transaction_type'] == 'invoice' ? '+' : '-';
//                $runningBalance += $transaction['transaction_type'] == 'invoice' ? $amountDue - $transaction['amount'] : $amountDue + $transaction['amount'];
//                $statementOfAccountItems .= '
//                    <tr style="height: 30px;">
//                        <td class="s8b" dir="ltr" colspan="1" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::createFromFormat('Y-m-d H:i:s',$transaction['transaction_date'])->format('F j, Y').'</td>
//                        <td class="s8" dir="ltr" colspan="4" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$transaction['transaction'].'</td>
//                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: right; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$signOfTransaction.number_format($transaction['amount'],2).'</td>
//                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: right; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.number_format($runningBalance,2).'</td>
//                    </tr>';
//            }
            foreach ($transactions as $transaction) {
                $signOfTransaction = $transaction['transaction_type'] == 'invoice' ? '' : '-';
//                $runningBalance += $transaction['transaction_type'] == 'invoice' ? $runningAmountDue + $transaction['amount'] : $runningAmountDue - $transaction['amount'];
                $runningBalance = array_key_exists('unpaid_amount',$transaction) ?  number_format($transaction['unpaid_amount'],2) : "";
                $statementOfAccountItems .= '
                    <tr style="height: 30px;">
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="2" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::parse($transaction['transaction_date'])->format('M j, Y').'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="3" style="text-align: left; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000;  color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$transaction['transaction'].'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$signOfTransaction.number_format($transaction['amount'],2).'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.$runningBalance.'</td>
                    </tr>';
            }

            // check if the statement of account items can fit in one page,
            // if they are more than one page, then add a page break
            // if less than one page, padd the table with empty rows
            $statementOfAccountItems .= str_repeat(
                '
                        <tr style="height: 30px;">
                            <td class="s_cell_with_right_left_border" colspan="2"></td>
                            <td class="s_cell_with_right_left_border" colspan="3"></td>
                            <td class="s_cell_with_right_left_border" colspan="1"></td>
                            <td class="s_cell_with_right_left_border" colspan="1"></td>
                        </tr>',
                19 - count($transactions)
            );

            $current = 0;
            $oneToThirtyPastDue = 0;
            $thirtyOneToSixtyPastDue = 0;
            $sixtyOneToNinetyPastDue = 0;
            $overNinetyPastDue = 0;
            // iterate over the invoices obtaining the current, 1-30, 31-60, 61-90, over 90
            foreach ($invoices as $invoice) {
                $invoiceDueDate = Carbon::createFromFormat('Y-m-d',$invoice['invoice_due_date']);
                $daysDifference = $invoiceDueDate->diffInDays(Carbon::now());
                if ($daysDifference > 90){
                    $overNinetyPastDue += $invoice['unpaid_amount'];
                } elseif ($daysDifference > 60){
                    $sixtyOneToNinetyPastDue += $invoice['unpaid_amount'];
                } elseif ($daysDifference > 30){
                    $thirtyOneToSixtyPastDue += $invoice['unpaid_amount'];
                } elseif ($daysDifference > 0 ){
                    $oneToThirtyPastDue += $invoice['unpaid_amount'];
                } elseif ($daysDifference == 0){
                    $current += $invoice['unpaid_amount'];
                }
            }

            $company = CompanyDetails::latest()->first();

            $detailsArray = [
//                'customerName' => $unitName.' '.$tenancyAgreement->tenant->name,
                'companyName' => $company->name,
                'companyAddress' => $company->address,
                'companyEmail' => $company->email,
                'companyPhoneNumber' => $company->phone_number,
                'companyLocation' => $company->location,
                'customerName' => $unitName.' '.$this->name,
                'propertyName' => $propertyName,
                'dateGenerated'=> Carbon::now()->format('M j, Y'),
                'logoUrl'=>'file://'.storage_path('/app/public/'.$company->logo),
                // 'logoUrl'=>'file://'.getcwd().'/images/hamud_top_doc_logo.png',
                'amountDue' => number_format($amountDue,2),
                'amountEnc' => number_format(0,2),
                'statementOfAccountItemsHTML' => $statementOfAccountItems,
                'current'=> number_format($current,2),
                'oneToThirtyPastDue'=> number_format($oneToThirtyPastDue,2),
                'thirtyOneToSixtyPastDue'=> number_format($thirtyOneToSixtyPastDue,2),
                'sixtyOneToNinetyPastDue'=> number_format($sixtyOneToNinetyPastDue,2),
                'overNinetyPastDue'=> number_format($overNinetyPastDue,2),
            ];

            $content = File::get(resource_path('documents/templates/statement-of-account-output-document.html'));

            foreach ($detailsArray as $key => $value) {
                $content = str_replace("@#$key", $value, $content);
            }

            $pdfName = "Statement of Account".
                '_for_' .
                $this->name . '_' .
                $propertyName .
                '_for_unit_' .
                $unitName .
                '_'.$this->id;

            // get the path but without the clatter file system
            $pdfPath = Storage::path('statements_of_account') . '/' . $pdfName . '.pdf';

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

            // trigger download of the file then delete it
            return response()->download($pdfPath)->deleteFileAfterSend(true);

            // check if file exists
//            if (file_exists($pdfPath)) {
//                $savedPath = explode('/', $pdfPath);
//                // retrieve the string after the string 'app'
//                foreach ($savedPath as $key => $value) {
//                    if ($value == 'app') {
//                        $savedPath = implode("/", array_slice($savedPath, $key + 1));
//                        break;
//                    }
//                }
////                $this->is_generated = 1;
////                $this->document_url = $savedPath;
////                $this->updated_by = auth()->user()->id;
////
////                $this->save();
//
//                return true;
//            } else {
//                return false;
//            }
        }catch (\Exception $exception){
            Log::error($exception->getTraceAsString());
            Log::error($exception->getMessage());
            return false;
        }
    }
}

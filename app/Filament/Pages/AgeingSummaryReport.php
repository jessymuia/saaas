<?php

namespace App\Filament\Pages;

use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\ManualInvoices;
use App\Models\Property;
use App\Utils\AppPermissions;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use UnitEnum;
use BackedEnum;

class AgeingSummaryReport extends Page implements HasForms
{
    use InteractsWithForms;

    public $property;
    public $zero_to_thirty_days = false;
    public $thirty_one_to_sixty_days = false;
    public $sixty_one_to_ninety_days = false;
    public $over_ninety_days = false;

    public $statementPath;

    protected static UnitEnum|string|null $navigationGroup = 'Tenancy Management';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.ageing-summary-report';

    public ?array $generateReportForm = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can(AppPermissions::READ_INVOICE_PAYMENTS_PERMISSION);
    }

    public function mount()
    {
        abort_unless(auth()->user()->can(AppPermissions::READ_INVOICE_PAYMENTS_PERMISSION), 403);
    }

    public function render(): View
    {
        $this->form->fill();
        return parent::render();
    }

    public function form(Form $form): Form
    {
        $form->schema([
            Select::make('property')
                ->label('Property')
                // BelongsToTenant scope is active — only current tenant's properties
                ->options(Property::query()->select(['id', 'name'])->get()->pluck('name', 'id')),
            Section::make('ranges-section')
                ->heading('Ranges *')
                ->description('Tick the periods to include in the report')
                ->schema([
                    Checkbox::make('zero_to_thirty_days')->label('0-30 days'),
                    Checkbox::make('thirty_one_to_sixty_days')->label('31-60 days'),
                    Checkbox::make('sixty_one_to_ninety_days')->label('61-90 days'),
                    Checkbox::make('over_ninety_days')->label('Over 90 days'),
                ]),
        ])->statePath('generateReportForm');

        return $form;
    }

    public function submitGenerateReportForm()
    {
        $submittedFormData = $this->form->getState();
        $this->customValidate($submittedFormData);
        return response()->download($this->statementPath);
    }

    public function customValidate($submittedFormData)
    {
        $rules = [
            'property'                  => ['nullable', 'numeric', 'exists:properties,id'],
            'zero_to_thirty_days'       => ['nullable', 'boolean'],
            'thirty_one_to_sixty_days'  => ['nullable', 'boolean'],
            'sixty_one_to_ninety_days'  => ['nullable', 'boolean'],
            'over_ninety_days'          => ['nullable', 'boolean'],
        ];

        $messages = [
            'property.numeric'                => 'Please select a valid property',
            'property.exists'                 => 'Please select a valid property',
            'zero_to_thirty_days.boolean'     => 'Invalid value for 0-30 days',
            'thirty_one_to_sixty_days.boolean'=> 'Invalid value for 31-60 days',
            'sixty_one_to_ninety_days.boolean'=> 'Invalid value for 61-90 days',
            'over_ninety_days.boolean'        => 'Invalid value for over 90 days',
        ];

        $validator = Validator::make($submittedFormData, $rules, $messages);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->displayErrorNotification($error);
            }
            return false;
        }

        $atLeastOneRangeSelected =
            $submittedFormData['zero_to_thirty_days']      ||
            $submittedFormData['thirty_one_to_sixty_days'] ||
            $submittedFormData['sixty_one_to_ninety_days'] ||
            $submittedFormData['over_ninety_days'];

        $this->property               = $submittedFormData['property'] == 'null' ? null : $submittedFormData['property'];
        $this->zero_to_thirty_days    = $atLeastOneRangeSelected ? $submittedFormData['zero_to_thirty_days']      : true;
        $this->thirty_one_to_sixty_days = $atLeastOneRangeSelected ? $submittedFormData['thirty_one_to_sixty_days'] : true;
        $this->sixty_one_to_ninety_days = $atLeastOneRangeSelected ? $submittedFormData['sixty_one_to_ninety_days'] : true;
        $this->over_ninety_days       = $atLeastOneRangeSelected ? $submittedFormData['over_ninety_days']         : true;

        $outputText = '';

        if ($this->property) {
            $propertyTotalDue = $propertyOneToThirtyPastDue = $propertyThirtyOneToSixtyPastDue
                = $propertySixtyOneToNinetyPastDue = $propertyOverNinetyPastDue = 0;

            $property = Property::find($this->property);
            $outputText .= $this->generatePropertyHeader($property->name);

            // Phase 12: tenancyAgreements() is scoped via BelongsToTenant on related models
            foreach ($property->tenancyAgreements()->get() as $tenancyAgreement) {
                $row = $this->generateSingleStatementOfAccount($tenancyAgreement);
                $outputText .= $row['rowContentHtml'];
                $propertyTotalDue               += $row['pendingPayments']['total_due'] ?? 0;
                $propertyOneToThirtyPastDue     += $row['pendingPayments']['zero_to_thirty_days'] ?? 0;
                $propertyThirtyOneToSixtyPastDue+= $row['pendingPayments']['thirty_one_to_sixty_days'] ?? 0;
                $propertySixtyOneToNinetyPastDue+= $row['pendingPayments']['sixty_one_to_ninety_days'] ?? 0;
                $propertyOverNinetyPastDue      += $row['pendingPayments']['over_ninety_days'] ?? 0;
            }

            $outputText .= $this->generatePropertyFooter(
                $property->name,
                $propertyTotalDue,
                $propertyOneToThirtyPastDue,
                $propertyThirtyOneToSixtyPastDue,
                $propertySixtyOneToNinetyPastDue,
                $propertyOverNinetyPastDue
            );
        } else {
            $allPropertiesTotalDue = $allPropertyOneToThirtyPastDueTotal
                = $allPropertyThirtyOneToSixtyPastDueTotal
                = $allPropertySixtyOneToNinetyPastDueTotal
                = $allPropertyOverNinetyPastDueTotal = 0;

            // BelongsToTenant scope ensures only current tenant's properties are returned
            foreach (Property::all() as $property) {
                $propertyTotalDue = $propertyOneToThirtyPastDue = $propertyThirtyOneToSixtyPastDue
                    = $propertySixtyOneToNinetyPastDue = $propertyOverNinetyPastDue = 0;

                $outputText .= $this->generatePropertyHeader($property->name);

                foreach ($property->tenancyAgreements()->get() as $tenancyAgreement) {
                    $row = $this->generateSingleStatementOfAccount($tenancyAgreement);
                    $outputText .= $row['rowContentHtml'];
                    $propertyTotalDue               += $row['pendingPayments']['total_due'];
                    $propertyOneToThirtyPastDue     += $row['pendingPayments']['zero_to_thirty_days'] ?? 0;
                    $propertyThirtyOneToSixtyPastDue+= $row['pendingPayments']['thirty_one_to_sixty_days'] ?? 0;
                    $propertySixtyOneToNinetyPastDue+= $row['pendingPayments']['sixty_one_to_ninety_days'] ?? 0;
                    $propertyOverNinetyPastDue      += $row['pendingPayments']['over_ninety_days'] ?? 0;
                }

                $outputText .= $this->generatePropertyFooter(
                    $property->name,
                    $propertyTotalDue,
                    $propertyOneToThirtyPastDue,
                    $propertyThirtyOneToSixtyPastDue,
                    $propertySixtyOneToNinetyPastDue,
                    $propertyOverNinetyPastDue
                );

                $allPropertiesTotalDue                  += $propertyTotalDue;
                $allPropertyOneToThirtyPastDueTotal     += $propertyOneToThirtyPastDue;
                $allPropertyThirtyOneToSixtyPastDueTotal+= $propertyThirtyOneToSixtyPastDue;
                $allPropertySixtyOneToNinetyPastDueTotal+= $propertySixtyOneToNinetyPastDue;
                $allPropertyOverNinetyPastDueTotal      += $propertyOverNinetyPastDue;
            }

            $outputText .= $this->generatePropertyFooter(
                'All Properties',
                $allPropertiesTotalDue,
                $allPropertyOneToThirtyPastDueTotal,
                $allPropertyThirtyOneToSixtyPastDueTotal,
                $allPropertySixtyOneToNinetyPastDueTotal,
                $allPropertyOverNinetyPastDueTotal
            );
        }

        $detailsArray = [
            'tableHeaderHTML'        => $this->generateTableHeader(),
            'dateGenerated'          => Carbon::now()->format('M j, Y'),
            'logoUrl'                => 'file://' . getcwd() . '/images/hamud_top_doc_logo.png',
            'amountDue'              => number_format(0, 2),
            'amountEnc'              => number_format(0, 2),
            'reportContentItemsHTML' => $outputText,
        ];

        $content = File::get(resource_path('documents/templates/ageing-summary-report-output-document.html'));

        foreach ($detailsArray as $key => $value) {
            $content = str_replace("@#$key", $value, $content);
        }

        $pdfName = str_replace(' ', '', 'Ageing Summary Report' . '_as_of_' . now()->format('F j, Y h:i:s'));
        $pdfPath = Storage::path('ageing_summary_reports') . '/' . $pdfName . '.pdf';

        $snappy = App::make('snappy.pdf');
        $snappy->setOption('enable-local-file-access', true);
        $snappy->setOption('disable-smart-shrinking', false);
        $snappy->setOption('margin-bottom', '1in');
        $snappy->setOption('margin-left', '1in');
        $snappy->setOption('margin-right', '1in');
        $snappy->setOption('margin-top', '1in');
        $snappy->setOption('orientation', 'landscape');
        $snappy->generateFromHtml($content, $pdfPath);

        $this->statementPath = $pdfPath;
    }

    public function displayErrorNotification($message = 'Report generation failed'): void
    {
        Notification::make()->title($message)->danger()->send();
    }

    private function generatePropertyHeader($propertyName): string
    {
        return '<tr style="height: 30px;">
                    <td class="cell_property_header" colspan="15">' . ucwords($propertyName) . '</td>
                </tr>';
    }

    private function generatePropertyFooter(
        $propertyName, $totalDue, $oneToThirtyPastDue,
        $thirtyOneToSixtyPastDue, $sixtyOneToNinetyPastDue, $overNinetyPastDue
    ): string {
        $html = '<tr style="height: 30px;">
                    <td class="cell_property_header" colspan="5"> Total ' . $propertyName . '</td>';

        if ($this->zero_to_thirty_days)
            $html .= '<td class="cell_property_footer" colspan="1">' . number_format($oneToThirtyPastDue, 2) . '</td>';
        if ($this->thirty_one_to_sixty_days)
            $html .= '<td class="cell_property_footer" colspan="2">' . number_format($thirtyOneToSixtyPastDue, 2) . '</td>';
        if ($this->sixty_one_to_ninety_days)
            $html .= '<td class="cell_property_footer" colspan="3">' . number_format($sixtyOneToNinetyPastDue, 2) . '</td>';
        if ($this->over_ninety_days)
            $html .= '<td class="cell_property_footer" colspan="2">' . number_format($overNinetyPastDue, 2) . '</td>';

        $html .= '<td class="cell_property_footer" colspan="2">' . number_format($totalDue, 2) . '</td></tr>';

        return $html;
    }

    private function generateTableHeader(): string
    {
        $html = '<tr style="height: 30px;">
                    <td colspan="1"></td>
                    <td colspan="4"></td>';

        if ($this->zero_to_thirty_days)
            $html .= '<td class="cell_row_header" colspan="1">0-30 days</td>';
        if ($this->thirty_one_to_sixty_days)
            $html .= '<td class="cell_row_header" colspan="2">31-60 days</td>';
        if ($this->sixty_one_to_ninety_days)
            $html .= '<td class="cell_row_header" colspan="3">61-90 days</td>';
        if ($this->over_ninety_days)
            $html .= '<td class="cell_row_header" colspan="2">Over 90 days</td>';

        $html .= '<td class="cell_row_header" colspan="2">Total Due</td></tr>';

        return $html;
    }

    private function generateSingleStatementOfAccount($tenancyAgreement): array
    {
        // Phase 12: Invoice query — BelongsToTenant scope provides saas_client_id filter.
        // Additional explicit saas_client_id guard added for Citus safety.
        $saasClientId = tenant()?->id;

        $invoices = Invoice::query()
            ->where('saas_client_id', $saasClientId)          // Phase 12: explicit guard
            ->where('tenancy_agreement_id', $tenancyAgreement->id)
            ->orderBy('created_at', 'desc')
            ->select(['id', 'invoice_for_month as transaction_date', 'invoice_due_date'])
            ->selectRaw("concat('INV #', id, '. Due on ', TO_CHAR(invoice_for_month,'Mon DD, YYYY')) as transaction, 'invoice' as transaction_type")
            ->get(['amount', 'unpaid_amount'])
            ->toArray();

        // Phase 12: ManualInvoices — was using tenant_id (the property tenant/lessee),
        // which is unrelated to saas_client_id. BelongsToTenant scope handles isolation.
        // Added explicit saas_client_id guard for Citus co-location safety.
        $manualInvoices = ManualInvoices::query()
            ->where('saas_client_id', $saasClientId)          // Phase 12: explicit guard
            ->where('tenant_id', $tenancyAgreement->tenant_id)
            ->orderBy('created_at', 'desc')
            ->select(['id', 'invoice_for_month as transaction_date', 'invoice_due_date'])
            ->selectRaw("concat('INV #', id, '. Due on ', TO_CHAR(invoice_for_month,'Mon DD, YYYY')) as transaction, 'invoice' as transaction_type")
            ->get(['amount', 'unpaid_amount'])
            ->toArray();

        $invoices = array_merge($invoices, $manualInvoices);
        usort($invoices, fn($a, $b) => $a['transaction_date'] <=> $b['transaction_date']);

        // Phase 12: CreditNote — scoped via whereHas which traverses BelongsToTenant-scoped Invoice
        $creditNotes = CreditNote::query()
            ->where('saas_client_id', $saasClientId)          // Phase 12: explicit guard
            ->orderBy('created_at', 'desc')
            ->whereHas('invoice', function ($query) use ($tenancyAgreement) {
                $query->where('tenancy_agreement_id', $tenancyAgreement->id);
            })
            ->select(['id', 'created_at as transaction_date', 'amount_credited as amount'])
            ->selectRaw("concat('CRN #', id, '. ', name, '. Issued on ') as transaction, 'credit_note' as transaction_type")
            ->get()
            ->toArray();

        // Phase 12: InvoicePayment — was only filtering by tenant_id (lessee), no saas_client_id.
        // Added explicit saas_client_id guard to prevent cross-tenant data leakage.
        $invoicePayments = InvoicePayment::query()
            ->where('saas_client_id', $saasClientId)          // Phase 12: explicit guard
            ->where('tenant_id', $tenancyAgreement->tenant_id)
            ->orderBy('payment_date', 'desc')
            ->select(['id', 'payment_date as transaction_date', 'amount'])
            ->selectRaw("concat('PMT #', id, '. Paid on ') as transaction, 'payment' as transaction_type")
            ->get()
            ->toArray();

        $transactions = array_merge($invoices, $creditNotes, $invoicePayments);
        usort($transactions, fn($a, $b) => $a['transaction_date'] <=> $b['transaction_date']);

        $current = $oneToThirtyPastDue = $thirtyOneToSixtyPastDue
            = $sixtyOneToNinetyPastDue = $overNinetyPastDue = $amountDue = 0;

        foreach ($transactions as $transaction) {
            if ($transaction['transaction_type'] === 'invoice') {
                $amountDue += $transaction['amount'];
                $invoiceDueDate = Carbon::createFromFormat('Y-m-d', $transaction['invoice_due_date']);
                $daysDifference = $invoiceDueDate->diffInDays(Carbon::now());

                if ($daysDifference > 90)      $overNinetyPastDue      += $transaction['unpaid_amount'];
                elseif ($daysDifference > 60)  $sixtyOneToNinetyPastDue+= $transaction['unpaid_amount'];
                elseif ($daysDifference > 30)  $thirtyOneToSixtyPastDue+= $transaction['unpaid_amount'];
                elseif ($daysDifference > 0)   $oneToThirtyPastDue     += $transaction['unpaid_amount'];
                else                           $current                += $transaction['unpaid_amount'];

            } elseif ($transaction['transaction_type'] === 'credit_note') {
                $amountDue -= $transaction['amount'];
            } elseif ($transaction['transaction_type'] === 'payment') {
                $amountDue -= $transaction['amount'];
            }
        }

        $totalDue       = 0;
        $pendingPayments = [];

        $rowContentHtml = '<tr style="height: 30px;">
            <td class="details_header_cell_for_statement_summary" colspan="1">' . $tenancyAgreement->unit->name . '</td>
            <td class="details_header_cell_for_statement_summary" colspan="4">' . ($tenancyAgreement?->tenant?->name ?? 'Name not defined') . '</td>';

        if ($this->zero_to_thirty_days) {
            $rowContentHtml .= '<td class="table_cell_for_statement_summary" colspan="1">' . number_format($oneToThirtyPastDue, 2) . '</td>';
            $totalDue += $oneToThirtyPastDue;
            $pendingPayments['zero_to_thirty_days'] = $oneToThirtyPastDue;
        }
        if ($this->thirty_one_to_sixty_days) {
            $rowContentHtml .= '<td class="table_cell_for_statement_summary" colspan="2">' . number_format($thirtyOneToSixtyPastDue, 2) . '</td>';
            $totalDue += $thirtyOneToSixtyPastDue;
            $pendingPayments['thirty_one_to_sixty_days'] = $thirtyOneToSixtyPastDue;
        }
        if ($this->sixty_one_to_ninety_days) {
            $rowContentHtml .= '<td class="table_cell_for_statement_summary" colspan="3">' . number_format($sixtyOneToNinetyPastDue, 2) . '</td>';
            $totalDue += $sixtyOneToNinetyPastDue;
            $pendingPayments['sixty_one_to_ninety_days'] = $sixtyOneToNinetyPastDue;
        }
        if ($this->over_ninety_days) {
            $rowContentHtml .= '<td class="table_cell_for_statement_summary" colspan="2">' . number_format($overNinetyPastDue, 2) . '</td>';
            $totalDue += $overNinetyPastDue;
            $pendingPayments['over_ninety_days'] = $overNinetyPastDue;
        }

        $rowContentHtml .= '<td class="table_cell_for_statement_summary" colspan="2">' . number_format($totalDue, 2) . '</td></tr>';
        $pendingPayments['total_due'] = $totalDue;

        return [
            'rowContentHtml'  => $rowContentHtml,
            'pendingPayments' => $pendingPayments,
        ];
    }
}
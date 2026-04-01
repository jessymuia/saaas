<?php

namespace App\Filament\Pages\App;

use App\Models\RefBillingType;
use App\Models\Unit;
use App\Utils\AppPermissions;
use App\Utils\AppUtils;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use UnitEnum;
use BackedEnum;

class TenancyScheduleReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static UnitEnum|string|null $navigationGroup = 'Tenancy Management';

    protected string $view = 'filament.pages.tenancy-schedule-report';

    public ?array $generateReportForm = [];

    public $statementPath;

    public $unit_id;
    public $amount;
    public $duration_in_months;
    public $billing_type_id;
    public $deposit_amount;
    public $start_date;
    public $end_date;

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
            Select::make('unit_id')
                ->label('Unit')
                ->options(Unit::all()->pluck('name', 'id'))
                ->required(),
            TextInput::make('duration_in_months')
                ->label('Duration (in months)')
                ->numeric()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    Log::info("Out here");
                    $start_date = $get('start_date');
                    $duration_in_months = $get('duration_in_months');
                    if ($start_date && $duration_in_months) {
                        $end_date = Carbon::parse($start_date)
                            ->addMonths($duration_in_months)
                            ->format('Y-m-d');
                        $set('end_date', $end_date);
                    }
                })
                ->required(),
            Select::make('billing_type_id')
                ->label('Billing Type')
                ->options(RefBillingType::all()->pluck('type', 'id'))
                ->required(),
            TextInput::make('amount')
                ->label("Rent/Lease Amount")
                ->required()
                ->minValue(0),
            TextInput::make('deposit_amount')
                ->label("Deposit Amount")
                ->required()
                ->minValue(0),
            DatePicker::make('start_date')
                ->required()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $start_date = $get('start_date');
                    $duration_in_months = $get('duration_in_months');
                    Log::info("Out here");
                    if ($start_date && $duration_in_months) {
                        $end_date = Carbon::parse($start_date)
                            ->addMonths($duration_in_months)
                            ->format('Y-m-d');
                        Log::info("ENd date " . $end_date);
                        $set('end_date', $end_date);
                    }
                })
                ->afterOrEqual(today()),
        ]);

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
            'unit_id'            => ['required'],
            'amount'             => ['required'],
            'duration_in_months' => ['required'],
            'billing_type_id'    => ['required'],
            'deposit_amount'     => ['required'],
            'start_date'         => ['required'],
        ];

        $messages = [
            'unit_id.required'            => 'Unit is required',
            'amount.required'             => 'Amount is required',
            'duration_in_months.required' => 'Duration is required',
            'billing_type_id.required'    => 'Billing Type is required',
            'deposit_amount.required'     => 'Deposit Amount is required',
            'start_date.required'         => 'Start Date is required',
        ];

        $validator = Validator::make($submittedFormData, $rules, $messages);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->displayErrorNotification($error);
            }
            return false;
        }

        $this->generateDocument($validator->validated());
    }

    public function generateDocument($scheduleData)
    {
        try {
            $scheduleItems  = '';
            $vatTotal       = 0;
            $scheduleSubTotal = 0;
            $rentAmountTotal  = 0;

            for (
                $j = Carbon::parse($scheduleData['start_date']);
                $j < Carbon::parse($scheduleData['start_date'])->addMonths($scheduleData['duration_in_months']);
                $j->addMonth()
            ) {
                $invoiceDueDate = $j->addDays(5)->format('Y-m-d');
                $vatAmount      = $scheduleData['amount'] * 0.16;

                $scheduleSubTotal  += $scheduleData['amount'] + $vatAmount;
                $vatTotal          += $vatAmount;
                $rentAmountTotal   += $scheduleData['amount'];

                $scheduleItems .= '
                    <tr style="height: 30px;">
                        <td class="s_cell_with_right_left_border" colspan="3">' . $invoiceDueDate . '</td>
                        <td class="s_cell_with_right_left_border" colspan="1">' . $scheduleData['amount'] . '</td>
                        <td class="s_cell_with_right_left_border" colspan="1">' . $vatAmount . '</td>
                        <td class="s_cell_with_right_left_border" colspan="2">' . ($scheduleData['amount'] + $vatAmount) . '</td>
                    </tr>';
            }

            $scheduleItems .= '
                <tr style="height: 30px;">
                    <td class="s_bottom_cell" colspan="3"></td>
                    <td class="s_bottom_cell" colspan="1"></td>
                    <td class="s_bottom_cell" colspan="1"></td>
                    <td class="s_bottom_cell" colspan="2"></td>
                </tr>';

            $detailsArray = [
                'logoUrl'          => 'file://' . getcwd() . '/images/hamud_top_doc_logo.png',
                'unitAndProperty'  => Unit::find($scheduleData['unit_id'])->name . ' - ' . Unit::find($scheduleData['unit_id'])->property->name,
                'tenancyDuration'  => $scheduleData['duration_in_months'] . ' months',
                'billingType'      => RefBillingType::find($scheduleData['billing_type_id'])->type,
                'rentLeaseAmount'  => number_format($scheduleData['amount'], 2),
                'depositAmount'    => number_format($scheduleData['deposit_amount'], 2),
                'startDate'        => Carbon::parse($scheduleData['start_date'])->format('M j, Y'),
                'endDate'          => Carbon::parse(Carbon::parse($scheduleData['start_date'])->addMonths($scheduleData['duration_in_months']))->format('M j, Y'),
                'rentLeaseSubtotal'=> number_format($rentAmountTotal, 2),
                'vatTotal'         => number_format($vatTotal, 2),
                'scheduleItems'    => $scheduleItems,
                'scheduleSubTotal' => number_format($scheduleSubTotal, 2),
            ];

            $content = File::get(resource_path('documents/templates/tenancy-schedule-report-output-document.html'));

            foreach ($detailsArray as $key => $value) {
                $content = str_replace("@#$key", $value, $content);
            }

            $pdfName = Carbon::createFromFormat('Y-m-d H:i:s', now())->format('F, Y') .
                'schedule_report_for_' .
                Unit::find($scheduleData['unit_id'])->name . ' - ' . Unit::find($scheduleData['unit_id'])->property->name;

            $pdfPath = Storage::path('tenancy_schedule_report') . '/' . $pdfName . '.pdf';

            $snappy = App::make('snappy.pdf');
            $snappy->setOption('enable-local-file-access', true);
            $snappy->setOption('disable-smart-shrinking', false);
            $snappy->setOption('margin-bottom', '1in');
            $snappy->setOption('margin-left', '1in');
            $snappy->setOption('margin-right', '1in');
            $snappy->setOption('margin-top', '1in');
            $snappy->generateFromHtml($content, $pdfPath);

            Log::error("Ndio hii: " . $pdfPath);
            Log::info('--------------------------------------------------------------------------');

            $this->statementPath = $pdfPath;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    public function displayErrorNotification($message = 'Report generation failed'): void
    {
        \Filament\Notifications\Notification::make()->title($message)->danger()->send();
    }
}

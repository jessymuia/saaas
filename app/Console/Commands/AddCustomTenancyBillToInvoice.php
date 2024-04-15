<?php

namespace App\Console\Commands;

use App\Models\TenancyBill;
use Illuminate\Console\Command;

class AddCustomTenancyBillToInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-custom-tenancy-bill-to-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add custom tenancy bill to invoice';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $invoiceId = 6569;
        $tenancyBill = TenancyBill::create([
            'invoice_id' => $invoiceId,
            'tenancy_agreement_id' => 261,
            'bill_date' => '2024-03-25',
            'due_date' => '2024-04-05',
            'amount' => 998.71,
            'vat' => 159.80,
            'total_amount' => 1158.51,
            'billing_type_id' => 1,
            'utility_id' => 2,
            'name' => 'March Electricity bill B',
            'bill_description' => 'March Electricity bill B',
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 1,
            'archive' => 0
        ]);

        if ($tenancyBill) {
            $this->info('Custom tenancy bill added successfully');
        } else {
            $this->error('Failed to add custom tenancy bill');
        }
    }
}

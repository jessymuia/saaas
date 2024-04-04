<?php

namespace App\Console\Commands;

use App\Jobs\SendInvoiceMail;
use App\Models\Invoice;
use Illuminate\Console\Command;

class TestMailSending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-mail-sending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test mail sending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Testing mail sending');

        $invoice = Invoice::find(1);

        SendInvoiceMail::dispatch($invoice);
    }
}

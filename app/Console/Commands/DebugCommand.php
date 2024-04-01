<?php

namespace App\Console\Commands;

use App\Models\TenancyAgreement;
use Illuminate\Console\Command;

class DebugCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to debug application logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $tenancyAgreement = 36;
        $unitOccupationMonthlyRecords = TenancyAgreement::query()
            ->where('id', $tenancyAgreement)
            ->with('monthlyOccupationRecords')
            ->get();

        $this->info("The count: ". $unitOccupationMonthlyRecords->count());
        foreach ($unitOccupationMonthlyRecords as $unitOccupationMonthlyRecord) {
            $this->info($unitOccupationMonthlyRecord);
        }

        $this->info("......................................................");

        // get unit name for this tenancy agreement
        $this->info("Unit name: ". $unitOccupationMonthlyRecords->first()->unit->name);
    }
}

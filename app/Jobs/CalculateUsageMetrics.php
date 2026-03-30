<?php

namespace App\Jobs;

use App\Models\SaasClient;
use App\Models\UsageMetric;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateUsageMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        SaasClient::all()->each(function (SaasClient $client) {
            try {
                DB::statement("SET app.current_tenant_id = '{$client->id}'");

                UsageMetric::updateOrCreate(
                    ['saas_client_id' => $client->id],
                    [
                        'properties_count'  => DB::table('properties')->where('saas_client_id', $client->id)->count(),
                        'units_count'       => DB::table('units')->where('saas_client_id', $client->id)->count(),
                        'users_count'       => DB::table('users')->where('saas_client_id', $client->id)->count(),
                        'tenants_count'     => DB::table('tenants')->where('saas_client_id', $client->id)->count(),
                        'invoices_count'    => DB::table('invoices')->where('saas_client_id', $client->id)->count(),
                        'last_calculated_at'=> now(),
                    ]
                );

                Log::info("Usage metrics calculated for {$client->id}");
            } catch (\Exception $e) {
                Log::error("Failed to calculate metrics for {$client->id}: {$e->getMessage()}");
            }
        });
    }
}
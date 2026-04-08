<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SaasClientSeeder extends Seeder
{
    /**
     * Seed 8 tenant SaaS clients, each with:
     *  - a saas_clients record
     *  - a domains record
     *  - an active subscription
     *  - an admin user (@kakaye.co.ke)
     */
    public function run(): void
    {
        $starterPlanId      = DB::table('plans')->where('slug', 'starter')->value('id');
        $professionalPlanId = DB::table('plans')->where('slug', 'professional')->value('id');
        $enterprisePlanId   = DB::table('plans')->where('slug', 'enterprise')->value('id');
        $trialPlanId        = DB::table('plans')->where('slug', 'trial')->value('id');

        $clients = [
            [
                'name'         => 'Karibu Homes',
                'slug'         => 'karibu-homes',
                'email'        => 'karibu.homes@kakaye.co.ke',
                'contact_name' => 'James Kariuki',
                'phone'        => '+254711001001',
                'status'       => 'active',
                'plan_id'      => $professionalPlanId,
                'billing_cycle'=> 'monthly',
            ],
            [
                'name'         => 'Nairobi Realty',
                'slug'         => 'nairobi-realty',
                'email'        => 'nairobi.realty@kakaye.co.ke',
                'contact_name' => 'Grace Wanjiku',
                'phone'        => '+254722002002',
                'status'       => 'active',
                'plan_id'      => $starterPlanId,
                'billing_cycle'=> 'monthly',
            ],
            [
                'name'         => 'Savannah Properties',
                'slug'         => 'savannah-properties',
                'email'        => 'savannah.props@kakaye.co.ke',
                'contact_name' => 'David Omondi',
                'phone'        => '+254733003003',
                'status'       => 'active',
                'plan_id'      => $professionalPlanId,
                'billing_cycle'=> 'yearly',
            ],
            [
                'name'         => 'Kilimanjaro Rentals',
                'slug'         => 'kilimanjaro-rentals',
                'email'        => 'kilimanjaro.rentals@kakaye.co.ke',
                'contact_name' => 'Aisha Mwangi',
                'phone'        => '+254744004004',
                'status'       => 'active',
                'plan_id'      => $enterprisePlanId,
                'billing_cycle'=> 'yearly',
            ],
            [
                'name'         => 'Uhuru Property Group',
                'slug'         => 'uhuru-property',
                'email'        => 'uhuru.property@kakaye.co.ke',
                'contact_name' => 'Peter Njoroge',
                'phone'        => '+254755005005',
                'status'       => 'active',
                'plan_id'      => $starterPlanId,
                'billing_cycle'=> 'monthly',
            ],
            [
                'name'         => 'Mombasa Realtors',
                'slug'         => 'mombasa-realtors',
                'email'        => 'mombasa.realtors@kakaye.co.ke',
                'contact_name' => 'Fatuma Hassan',
                'phone'        => '+254766006006',
                'status'       => 'active',
                'plan_id'      => $professionalPlanId,
                'billing_cycle'=> 'monthly',
            ],
            [
                'name'         => 'Lakeview Properties',
                'slug'         => 'lakeview-properties',
                'email'        => 'lakeview.props@kakaye.co.ke',
                'contact_name' => 'Samuel Otieno',
                'phone'        => '+254777007007',
                'status'       => 'active',
                'plan_id'      => $starterPlanId,
                'billing_cycle'=> 'yearly',
            ],
            [
                'name'         => 'Highland Estates',
                'slug'         => 'highland-estates',
                'email'        => 'highland.estates@kakaye.co.ke',
                'contact_name' => 'Mary Chebet',
                'phone'        => '+254788008008',
                'status'       => 'trial',
                'plan_id'      => $trialPlanId,
                'billing_cycle'=> 'monthly',
            ],
        ];

        foreach ($clients as $clientData) {
            $slug = $clientData['slug'];

            if (DB::table('saas_clients')->where('slug', $slug)->exists()) {
                $this->command->warn("  Skipping [{$slug}] — already exists.");
                continue;
            }

            $clientId = (string) Str::uuid();

            // 1. Create the saas_client
            DB::table('saas_clients')->insert([
                'id'           => $clientId,
                'name'         => $clientData['name'],
                'slug'         => $slug,
                'email'        => $clientData['email'],
                'contact_name' => $clientData['contact_name'],
                'phone'        => $clientData['phone'],
                'status'       => $clientData['status'],
                'plan_id'      => $clientData['plan_id'],
                'is_suspended' => false,
                'data'         => json_encode(['onboarded' => true]),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // 2. Create a domain record
            DB::table('domains')->insert([
                'id'             => (string) Str::uuid(),
                'saas_client_id' => $clientId,
                'domain'         => $slug . '.propman.kakaye.co.ke',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // 3. Create an active subscription
            $isYearly = $clientData['billing_cycle'] === 'yearly';
            DB::table('subscriptions')->insert([
                'id'             => (string) Str::uuid(),
                'saas_client_id' => $clientId,
                'plan_id'        => $clientData['plan_id'],
                'status'         => $clientData['status'] === 'trial' ? 'trialing' : 'active',
                'billing_cycle'  => $clientData['billing_cycle'],
                'starts_at'      => now()->startOfMonth(),
                'ends_at'        => $isYearly ? now()->addYear() : now()->addMonth(),
                'trial_ends_at'  => $clientData['status'] === 'trial' ? now()->addDays(30) : null,
                'cancelled_at'   => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            $this->command->info("  Created client [{$slug}]");
        }

        $this->command->info('SaasClients seeded: ' . count($clients) . ' clients.');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'           => 'Trial',
                'slug'           => 'trial',
                'description'    => 'Free trial plan — explore the platform for 30 days with limited capacity.',
                'price_monthly'  => 0.00,
                'price_yearly'   => 0.00,
                'max_properties' => 2,
                'max_units'      => 20,
                'max_users'      => 2,
                'is_active'      => true,
                'limits'         => json_encode([
                    'reports'     => false,
                    'mpesa'       => false,
                    'api_access'  => false,
                ]),
            ],
            [
                'name'           => 'Starter',
                'slug'           => 'starter',
                'description'    => 'Perfect for small landlords managing a handful of properties.',
                'price_monthly'  => 2500.00,
                'price_yearly'   => 25000.00,
                'max_properties' => 10,
                'max_units'      => 100,
                'max_users'      => 5,
                'is_active'      => true,
                'limits'         => json_encode([
                    'reports'     => true,
                    'mpesa'       => true,
                    'api_access'  => false,
                ]),
            ],
            [
                'name'           => 'Professional',
                'slug'           => 'professional',
                'description'    => 'For growing agencies handling multiple properties and a full team.',
                'price_monthly'  => 7500.00,
                'price_yearly'   => 75000.00,
                'max_properties' => 50,
                'max_units'      => 500,
                'max_users'      => 20,
                'is_active'      => true,
                'limits'         => json_encode([
                    'reports'     => true,
                    'mpesa'       => true,
                    'api_access'  => true,
                ]),
            ],
            [
                'name'           => 'Enterprise',
                'slug'           => 'enterprise',
                'description'    => 'Unlimited scale for large real estate firms and property management companies.',
                'price_monthly'  => 20000.00,
                'price_yearly'   => 200000.00,
                'max_properties' => -1,
                'max_units'      => -1,
                'max_users'      => -1,
                'is_active'      => true,
                'limits'         => json_encode([
                    'reports'     => true,
                    'mpesa'       => true,
                    'api_access'  => true,
                    'white_label' => true,
                    'dedicated_support' => true,
                ]),
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->upsert(
                array_merge($plan, [
                    'id'         => (string) Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
                ['slug'],
                ['name', 'description', 'price_monthly', 'price_yearly', 'max_properties', 'max_units', 'max_users', 'is_active', 'limits', 'updated_at']
            );
        }

        $this->command->info('Plans seeded: ' . count($plans));
    }
}

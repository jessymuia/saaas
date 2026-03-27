<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Plan;
use App\Models\SaasClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MakeAdminUser extends Command
{
    protected $signature   = 'make:admin-user';
    protected $description = 'Create a Filament admin user under the system tenant';

    public function handle(): void
    {
        // 1. Ensure a Plan exists (Adjusted for your actual schema)
        // We'll use firstOrCreate to avoid "price" or other missing columns
        $plan = Plan::first();
        
        if (!$plan) {
            $this->warn('No plans found. Creating a basic system plan...');
            $plan = new Plan();
            $plan->name = 'System Plan';
            $plan->slug = 'system-plan';
            // Add other required fields here if you have them, e.g.:
            // $plan->amount = 0; 
            $plan->save();
        }

        // 2. Ensure System Tenant (ID 1) exists
        $systemTenant = SaasClient::find(1);

        if (!$systemTenant) {
            $this->info('Creating System tenant...');
            $systemTenant = new SaasClient();
            $systemTenant->id = 1;
            $systemTenant->name = 'System';
            $systemTenant->slug = 'system';
            $systemTenant->plan_id = $plan->id;
            $systemTenant->status = 'active'; // Or whatever your status column expects
            $systemTenant->save();
            $this->info('System tenant created.');
        }

        // 3. Collect User Data
        $name     = $this->ask('Name', 'Admin');
        $email    = $this->ask('Email address', 'admin@gmail.com');
        $password = $this->secret('Password');

        if (!$password) {
            $this->error('Password is required.');
            return;
        }

        // 4. Create the User
        // We use the model directly to ensure 'saas_client_id' is passed to Citus
        $user = User::withoutGlobalScopes()->updateOrCreate(
            ['email' => $email],
            [
                'name'           => $name,
                'password'       => Hash::make($password),
                'saas_client_id' => 1,
                'phone_number'   => '000000000', 
            ]
        );

        $this->info("Admin [{$email}] created/updated successfully.");
    }
}
<?php

namespace Database\Seeders;

use App\Models\AppRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SaasClientUsersSeeder extends Seeder
{
    /**
     * Seed one admin user per SaaS client.
     * All users receive password: "password" and @kakaye.co.ke email addresses.
     *
     * A second accountant user is also created for each client.
     */
    public function run(): void
    {
        $adminRole      = AppRole::findOrCreate('admin');
        $accountantRole = AppRole::findOrCreate('accountant');

        $clientUsers = [
            'karibu-homes' => [
                [
                    'name'         => 'James Kariuki',
                    'email'        => 'james.kariuki@kakaye.co.ke',
                    'phone_number' => '+254711001001',
                    'role'         => 'admin',
                ],
                [
                    'name'         => 'Alice Mutua',
                    'email'        => 'alice.mutua@kakaye.co.ke',
                    'phone_number' => '+254711001002',
                    'role'         => 'accountant',
                ],
            ],
            'nairobi-realty' => [
                [
                    'name'         => 'Grace Wanjiku',
                    'email'        => 'grace.wanjiku@kakaye.co.ke',
                    'phone_number' => '+254722002001',
                    'role'         => 'admin',
                ],
                [
                    'name'         => 'Brian Kamau',
                    'email'        => 'brian.kamau@kakaye.co.ke',
                    'phone_number' => '+254722002002',
                    'role'         => 'accountant',
                ],
            ],
            'savannah-properties' => [
                [
                    'name'         => 'David Omondi',
                    'email'        => 'david.omondi@kakaye.co.ke',
                    'phone_number' => '+254733003001',
                    'role'         => 'admin',
                ],
                [
                    'name'         => 'Lucy Atieno',
                    'email'        => 'lucy.atieno@kakaye.co.ke',
                    'phone_number' => '+254733003002',
                    'role'         => 'caretaker',
                ],
            ],
            'kilimanjaro-rentals' => [
                [
                    'name'         => 'Aisha Mwangi',
                    'email'        => 'aisha.mwangi@kakaye.co.ke',
                    'phone_number' => '+254744004001',
                    'role'         => 'admin',
                ],
                [
                    'name'         => 'Hassan Abdalla',
                    'email'        => 'hassan.abdalla@kakaye.co.ke',
                    'phone_number' => '+254744004002',
                    'role'         => 'accountant',
                ],
            ],
            'uhuru-property' => [
                [
                    'name'         => 'Peter Njoroge',
                    'email'        => 'peter.njoroge@kakaye.co.ke',
                    'phone_number' => '+254755005001',
                    'role'         => 'admin',
                ],
                [
                    'name'         => 'Ruth Kamande',
                    'email'        => 'ruth.kamande@kakaye.co.ke',
                    'phone_number' => '+254755005002',
                    'role'         => 'accountant',
                ],
            ],
            'mombasa-realtors' => [
                [
                    'name'         => 'Fatuma Hassan',
                    'email'        => 'fatuma.hassan@kakaye.co.ke',
                    'phone_number' => '+254766006001',
                    'role'         => 'admin',
                ],
                [
                    'name'         => 'Omar Salim',
                    'email'        => 'omar.salim@kakaye.co.ke',
                    'phone_number' => '+254766006002',
                    'role'         => 'caretaker',
                ],
            ],
            'lakeview-properties' => [
                [
                    'name'         => 'Samuel Otieno',
                    'email'        => 'samuel.otieno@kakaye.co.ke',
                    'phone_number' => '+254777007001',
                    'role'         => 'admin',
                ],
                [
                    'name'         => 'Caroline Auma',
                    'email'        => 'caroline.auma@kakaye.co.ke',
                    'phone_number' => '+254777007002',
                    'role'         => 'accountant',
                ],
            ],
            'highland-estates' => [
                [
                    'name'         => 'Mary Chebet',
                    'email'        => 'mary.chebet@kakaye.co.ke',
                    'phone_number' => '+254788008001',
                    'role'         => 'admin',
                ],
            ],
        ];

        foreach ($clientUsers as $clientSlug => $users) {
            $clientId = DB::table('saas_clients')->where('slug', $clientSlug)->value('id');

            if (!$clientId) {
                $this->command->warn("  Skipping users for [{$clientSlug}] — client not found.");
                continue;
            }

            foreach ($users as $userData) {
                if (User::where('email', $userData['email'])->where('saas_client_id', $clientId)->exists()) {
                    $this->command->warn("  Skipping [{$userData['email']}] — already exists.");
                    continue;
                }

                $user = User::create([
                    'id'                => (string) Str::uuid(),
                    'saas_client_id'    => $clientId,
                    'name'              => $userData['name'],
                    'email'             => $userData['email'],
                    'phone_number'      => $userData['phone_number'],
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                    'status'            => true,
                    'archive'           => false,
                ]);

                $role = match ($userData['role']) {
                    'admin'      => $adminRole,
                    'accountant' => $accountantRole,
                    default      => AppRole::findOrCreate($userData['role']),
                };

                $user->assignRole($role);

                // Link in the saas_client_users pivot
                DB::table('saas_client_users')->insert([
                    'id'             => (string) Str::uuid(),
                    'saas_client_id' => $clientId,
                    'user_id'        => $user->id,
                    'role'           => $userData['role'],
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                $this->command->info("  Created [{$userData['role']}] {$userData['email']} → {$clientSlug}");
            }
        }

        $this->command->info('SaasClientUsers seeded.');
    }
}

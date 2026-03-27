<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch the UUID of the saas_client seeded in DatabaseSeeder
        $saasClientId = DB::table('saas_clients')->where('slug', 'test-client')->value('id');

        DB::table('users')->insert([
            'name'              => 'Test User',
            'email'             => 'lancerbrian001@gmail.com',
            'phone_number'      => '08123456789',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'saas_client_id'    => $saasClientId, // UUID string from saas_clients
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }
}
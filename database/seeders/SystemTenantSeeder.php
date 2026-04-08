<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemTenantSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('saas_clients')->insertOrIgnore([
            'id'         => 1,
            'name'       => 'System',
            'email'      => 'system@propman.internal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
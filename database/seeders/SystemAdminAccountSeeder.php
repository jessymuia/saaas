<?php

namespace Database\Seeders;

use App\Models\SystemAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemAdminAccountSeeder extends Seeder
{
    public function run(): void
    {
        SystemAdmin::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\PropertyServices;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PropertyServices::factory()
            ->count(100)
            ->create();
    }
}

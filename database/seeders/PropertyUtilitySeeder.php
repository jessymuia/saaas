<?php

namespace Database\Seeders;

use App\Models\PropertyUtility;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyUtilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        PropertyUtility::factory()
            ->count(100)
            ->create();
    }
}

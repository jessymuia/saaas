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
        $expectedCount = 20;
        // factory method with exception handling
        do{
            try {
                PropertyUtility::factory()->count($expectedCount)->create();
            } catch (\Exception $e) {
                // continue
            }
        } while (PropertyUtility::query()->count() < $expectedCount);
    }
}

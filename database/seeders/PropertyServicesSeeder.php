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
        $expectedCount = 20;
        do{
            try{
                PropertyServices::factory()
                    ->count($expectedCount)
                    ->create();
            }catch (\Exception $e){
                // continue
            }
        }while (PropertyServices::query()->count() < $expectedCount);
    }
}

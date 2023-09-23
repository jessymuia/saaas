<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // initial user
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'lancerbrian001@gmail.com',
            'phone_number' => '08123456789',
            'password' => bcrypt('password'),
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create default roles
        $roles = [
            'admin',
            'accountant',
            'caretaker',
        ];

        foreach ($roles as $role) {
            \App\Models\AppRole::findOrCreate($role);
        }
    }
}

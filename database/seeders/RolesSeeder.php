<?php

namespace Database\Seeders;

use App\Models\AppRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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

        // define permissions for admin to be all
        $adminRole = AppRole::findOrCreate('admin');

        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

        // assign the admin role to the initial user
        $user = \App\Models\User::query()
            ->where('email','lancerbrian001@gmail.com')
            ->first();

        $user->assignRole($adminRole);
    }
}

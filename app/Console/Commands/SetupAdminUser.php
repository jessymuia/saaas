<?php

namespace App\Console\Commands;

use App\Models\AppRole;
use App\Models\User;
use Illuminate\Console\Command;

class SetupAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup administrator user and define their permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

        // get default user
        $admin = User::findOrFail(1);

        $adminRole = AppRole::findOrCreate('admin');

        // assign admin user admin role
        $admin->assignRole($adminRole);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use App\Utils\AppPermissions;

class SetupCompanyDetailsPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-company-details-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load company details permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Define your permissions
        $permissions = [
            AppPermissions::CREATE_COMPANY_DETAILS_PERMISSION,
            AppPermissions::READ_COMPANY_DETAILS_PERMISSION,
            AppPermissions::UPDATE_COMPANY_DETAILS_PERMISSION,
            AppPermissions::DELETE_COMPANY_DETAILS_PERMISSION,
            AppPermissions::RESTORE_COMPANY_DETAILS_PERMISSION
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin = User::findOrFail(1);
        if ($admin) {
            $admin->givePermissionTo($permissions);
            $this->info('Company details permissions loaded & assigned to admin.');
        } else {
            $this->error('Admin user not found.');
        }
    }
}

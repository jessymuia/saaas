<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use App\Utils\AppPermissions;

class SetupPropertyManagementUsersPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-property-management-users-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load property management users permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Define your permissions
        $permissions = [
            AppPermissions::CREATE_PROPERTY_MANAGEMENT_USERS_PERMISSION,
            AppPermissions::READ_PROPERTY_MANAGEMENT_USERS_PERMISSION,
            AppPermissions::UPDATE_PROPERTY_MANAGEMENT_USERS_PERMISSION,
            AppPermissions::DELETE_PROPERTY_MANAGEMENT_USERS_PERMISSION,
            AppPermissions::RESTORE_PROPERTY_MANAGEMENT_USERS_PERMISSION
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin = User::findOrFail(1);
        if ($admin) {
            $admin->givePermissionTo($permissions);
            $this->info('Property management users permissions loaded & assigned to admin.');
        } else {
            $this->error('Admin user not found.');
        }
    }
}

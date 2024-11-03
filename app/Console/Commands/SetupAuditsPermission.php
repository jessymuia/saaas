<?php

namespace App\Console\Commands;

use App\Models\AppRole;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use App\Utils\AppPermissions;

class SetupAuditsPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-audits-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load audits permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Define your permissions
        $permissions = [
            AppPermissions::CREATE_AUDITS_PERMISSION,
            AppPermissions::READ_AUDITS_PERMISSION,
            AppPermissions::UPDATE_AUDITS_PERMISSION,
            AppPermissions::DELETE_AUDITS_PERMISSION,
            AppPermissions::RESTORE_AUDITS_PERMISSION
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }


        // find the admin role
        $adminRole = AppRole::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->info('Audits permissions loaded & assigned to admin role.');
        } else {
            $this->error('Admin role not found.');
        }
    }
}

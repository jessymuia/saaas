<?php

namespace App\Filament\Resources\Central\TenantUserResource\Pages;

use App\Filament\Resources\Central\TenantUserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateTenantUser extends CreateRecord
{
    protected static string $resource = TenantUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set RLS context to the selected tenant
        DB::statement("SET app.current_tenant_id = '{$data['saas_client_id']}'");
        return $data;
    }
}
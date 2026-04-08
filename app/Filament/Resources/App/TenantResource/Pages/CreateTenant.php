<?php

namespace App\Filament\Resources\App\TenantResource\Pages;

use App\Filament\Resources\App\TenantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;
}

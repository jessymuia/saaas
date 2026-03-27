<?php

namespace App\Filament\Resources\App\TenantResource\Pages;

use App\Filament\Resources\App\TenantResource;
use Filament\Resources\Pages\ListRecords;

class ListTenants extends ListRecords
{
    protected static string $resource = TenantResource::class;
}

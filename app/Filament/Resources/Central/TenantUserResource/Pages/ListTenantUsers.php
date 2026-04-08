<?php

namespace App\Filament\Resources\Central\TenantUserResource\Pages;

use App\Filament\Resources\Central\TenantUserResource;
use Filament\Resources\Pages\ListRecords;

class ListTenantUsers extends ListRecords
{
    protected static string $resource = TenantUserResource::class;
}
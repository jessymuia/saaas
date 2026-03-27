<?php

namespace App\Filament\Resources\App\CompanyDetailsResource\Pages;

use App\Filament\Resources\App\CompanyDetailsResource;
use Filament\Resources\Pages\ListRecords;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyDetailsResource::class;
}

<?php

namespace App\Filament\Resources\App\AuditResource\Pages;

use App\Filament\Resources\App\AuditResource;
use Filament\Resources\Pages\ListRecords;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;
}

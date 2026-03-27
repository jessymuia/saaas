<?php

namespace App\Filament\Resources\App\UserResource\Pages;

use App\Filament\Resources\App\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
}

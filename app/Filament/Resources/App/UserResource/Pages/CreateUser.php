<?php

namespace App\Filament\Resources\App\UserResource\Pages;

use App\Filament\Resources\App\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}

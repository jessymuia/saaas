<?php

namespace App\Filament\Resources\App\UserResource\Pages;

use App\Filament\Resources\App\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
}

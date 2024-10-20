<?php

namespace App\Filament\Resources\CompanyDetailsResource\Pages;

use App\Filament\Resources\CompanyDetailsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyDetailsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->id;

        $fullPath = Storage::disk('public')->url($data['logo']);
        $path = Storage::disk('public')->path($data['logo']);
        $filename = basename($path);
        $size = Storage::disk('public')->size($data['logo']);
        $mimeType = Storage::disk('public')->mimeType($data['logo']);
        $extension = File::extension($filename);

        $data['filename'] = $filename;
        $data['path'] = $path;
        $data['full_path'] = $fullPath;
        $data['size'] = $size;
        $data['mime_type'] = $mimeType;
        $data['extension'] = $extension;

        return $data;
    }
}

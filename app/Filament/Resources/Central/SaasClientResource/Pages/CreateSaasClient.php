<?php

namespace App\Filament\Resources\Central\SaasClientResource\Pages;

use App\Filament\Resources\Central\SaasClientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSaasClient extends CreateRecord
{
    protected static string $resource = SaasClientResource::class;

    protected ?string $tenantDomain = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Extract the domain so it doesn't get merged into the model attributes
        $this->tenantDomain = $data['domain'] ?? null;
        
        // 2. Remove it from the main payload
        unset($data['domain']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // 3. Create the domain record once the SaasClient exists
        if ($this->tenantDomain) {
            $this->record->domains()->create([
                'domain' => $this->tenantDomain,
            ]);
        }
    }
}
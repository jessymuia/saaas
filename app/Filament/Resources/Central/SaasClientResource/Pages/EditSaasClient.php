<?php

namespace App\Filament\Resources\Central\SaasClientResource\Pages;

use App\Filament\Resources\Central\SaasClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditSaasClient extends EditRecord
{
    protected static string $resource = SaasClientResource::class;

    protected ?string $tenantDomain = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    // Tenancy reads $record->data after delete and crashes if null.
                    // Force it to an empty array so the post-delete hooks don't blow up.
                    DB::table('saas_clients')
                        ->where('id', $this->record->id)
                        ->update(['data' => '{}']);
                })
                ->successRedirectUrl(fn () => $this->getResource()::getUrl('index')),
        ];
    }

    /**
     * Pre-fill the domain field from the first related domain record.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['domain'] = $this->record->domains()->value('domain');

        return $data;
    }

    /**
     * Extract domain before saving so it doesn't get written to saas_clients.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->tenantDomain = $data['domain'] ?? null;
        unset($data['domain']);

        return $data;
    }

    /**
     * Sync the domain record after the SaasClient is updated.
     */
    protected function afterSave(): void
    {
        if ($this->tenantDomain) {
            $existing = $this->record->domains()->first();

            if ($existing) {
                $existing->update(['domain' => $this->tenantDomain]);
            } else {
                $this->record->domains()->create(['domain' => $this->tenantDomain]);
            }
        }
    }
}
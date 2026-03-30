<?php

namespace App\Filament\Resources\Central\SaasClientResource\Pages;

use App\Filament\Resources\Central\SaasClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditSaasClient extends EditRecord
{
    protected static string $resource = SaasClientResource::class;

    protected ?string $tenantDomain = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function () {
                    $id = $this->record->id;

                    
                    DB::table('domains')->where('saas_client_id', $id)->delete();
                    DB::table('subscriptions')->where('saas_client_id', $id)->delete();
                    DB::table('usage_metrics')->where('saas_client_id', $id)->delete();

                    
                    DB::table('saas_clients')->where('id', $id)->update(['data' => '{}']);

                    
                    DB::table('saas_clients')->where('id', $id)->delete();

                   
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->requiresConfirmation()
                ->color('danger'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['domain'] = $this->record->domains()->value('domain');
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->tenantDomain = $data['domain'] ?? null;
        unset($data['domain']);
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->tenantDomain) {
            $existing = $this->record->domains()->first();

            if ($existing) {
                $existing->update(['domain' => explode('.', $this->tenantDomain)[0]]);
            } else {
                $this->record->domains()->create(['domain' => explode('.', $this->tenantDomain)[0]]);
            }
        }
    }
}
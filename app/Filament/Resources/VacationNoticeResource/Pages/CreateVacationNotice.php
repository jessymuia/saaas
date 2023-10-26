<?php

namespace App\Filament\Resources\VacationNoticeResource\Pages;

use App\Filament\Resources\VacationNoticeResource;
use App\Models\TenancyAgreement;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVacationNotice extends CreateRecord
{
    protected static string $resource = VacationNoticeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
//        dd($data);
        // get tenancy agreement id
        $data['tenancy_agreement_id'] = TenancyAgreement::query()
            ->where('unit_id', $data['unit_id'])
            ->where('tenant_id', $data['tenant_id'])
            ->where('status', '1')
            ->value('id');
        $data['created_by'] = auth()->user()->id;

        return $data;
    }
}

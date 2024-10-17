<x-filament-panels::page>
    <div>
        <form wire:submit="submitGenerateReportForm">
            {{ $this->form}}

            <x-filament::button
                @class([
                    'mt-8',
                ])
                wire:submit="submitGenerateReportForm"
                type="submit" color="primary">
                Generate Report
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>
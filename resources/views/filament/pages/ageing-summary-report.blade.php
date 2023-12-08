<x-filament-panels::page>
    <div>
        <form wire:submit="submitGenerateReportForm">
            {{ $this->form}}
            <button type="submit">Submit</button>
        </form>
    </div>
</x-filament-panels::page>

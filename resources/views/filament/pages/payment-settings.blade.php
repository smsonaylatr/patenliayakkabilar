<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top: 24px;">
            <x-filament::button type="submit">
                Ayarları Kaydet
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

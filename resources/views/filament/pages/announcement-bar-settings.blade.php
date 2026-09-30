<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-8 pt-8" style="padding-top: 3rem; margin-top: 2rem; display: block; clear: both;">
            <x-filament::button type="submit" size="lg" class="mt-8">
                Ayarları Kaydet
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

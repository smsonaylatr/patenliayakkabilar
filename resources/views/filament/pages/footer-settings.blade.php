<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-8 pt-8" style="padding-top: 2rem; margin-top: 1rem;">
            <x-filament::button type="submit" size="lg">
                Ayarları Kaydet
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

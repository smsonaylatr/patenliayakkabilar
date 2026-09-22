<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex gap-4">
            <x-filament::button type="submit">
                Kaydet
            </x-filament::button>

            <x-filament::button color="gray" wire:click="sendTestMail" type="button">
                Test Mail Gönder
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

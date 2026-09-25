<div>
    <x-filament::icon-button
        color="gray"
        :icon="\Filament\Support\Icons\Heroicon::OutlinedArrowPath"
        icon-size="lg"
        wire:click="reload"
        tooltip="Sistemi Yenile (Önbellek & Octane)"
        :label="'Sistemi Yenile'"
        class="fi-topbar-reload-btn"
    />
</div>

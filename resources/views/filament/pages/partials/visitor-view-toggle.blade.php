@php
    $viewMode = $viewMode ?? (request()->cookie('av_view_mode', session('av_view_mode', 'list')));
    if (! in_array($viewMode, ['list', 'grid'])) {
        $viewMode = 'list';
    }
@endphp

<div 
    x-data="{
        currentMode: @js($viewMode),
        setMode(mode) {
            this.currentMode = mode;
            try {
                localStorage.setItem('av_view_mode', mode);
            } catch(e) {}
            if (typeof $wire !== 'undefined' && typeof $wire.setViewMode === 'function') {
                $wire.setViewMode(mode);
            }
        }
    }"
    x-init="
        try {
            const saved = localStorage.getItem('av_view_mode');
            if (saved && (saved === 'list' || saved === 'grid') && saved !== currentMode) {
                setMode(saved);
            }
        } catch(e) {}
    "
    class="av-view-toggle-group"
    role="group"
    aria-label="Görünüm Modu"
>
    {{-- Liste Görünümü Butonu --}}
    <button 
        type="button" 
        @click="setMode('list')"
        :class="{ 'is-active': currentMode === 'list' }"
        class="av-view-toggle-btn {{ $viewMode === 'list' ? 'is-active' : '' }}"
        title="Liste Görünümü"
        aria-label="Liste Görünümü"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        <span class="av-view-toggle-label">Liste</span>
    </button>

    {{-- Izgara Görünümü Butonu --}}
    <button 
        type="button" 
        @click="setMode('grid')"
        :class="{ 'is-active': currentMode === 'grid' }"
        class="av-view-toggle-btn {{ $viewMode === 'grid' ? 'is-active' : '' }}"
        title="Izgara Görünümü"
        aria-label="Izgara Görünümü"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
        </svg>
        <span class="av-view-toggle-label">Izgara</span>
    </button>
</div>

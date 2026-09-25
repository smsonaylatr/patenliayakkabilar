<div class="flex items-center ms-2 me-1">
    <button
        type="button"
        wire:click="reload"
        wire:loading.attr="disabled"
        title="Sistemi Yenile (optimize:clear & octane:reload)"
        aria-label="Sistemi Yenile"
        class="group relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-gradient-to-r from-[#ff4e00] to-[#ff6a00] hover:from-[#e04500] hover:to-[#ff5500] shadow-sm shadow-orange-500/20 active:scale-95 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-[#ff4e00] focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:opacity-60 disabled:cursor-not-allowed"
    >
        <svg
            wire:loading.class="animate-spin"
            class="w-3.5 h-3.5 transition-transform duration-300 group-hover:rotate-180"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2.5"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
        </svg>
        <span class="inline">Yenile</span>
    </button>
</div>

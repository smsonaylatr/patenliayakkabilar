{{-- Floating Social Sidebar + İndirim Kuponu Widget --}}
@php
    $sidebarSettings = \App\Models\Setting::whereIn('key', [
        'footer_facebook',
        'footer_instagram',
    ])->pluck('value', 'key')->toArray();
@endphp

<div id="floating-sidebar"
     class="fixed right-3 top-1/2 -translate-y-1/2 z-50 hidden md:flex flex-col items-center gap-0"
     x-data="{ couponCopied: false }"
>
    {{-- Social Icons + İndirim Container --}}
    <div class="flex flex-col items-center bg-gray-100/90 backdrop-blur-sm rounded-full shadow-lg border border-gray-200/60 py-3 px-2 gap-1">

        {{-- Facebook --}}
        @if(!empty($sidebarSettings['footer_facebook']))
        <a href="{{ $sidebarSettings['footer_facebook'] }}"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="Facebook"
           class="group flex items-center justify-center w-10 h-10 rounded-full bg-brand-black text-white hover:bg-brand-orange hover:scale-110 transition-all duration-200"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
            </svg>
        </a>
        @endif

        {{-- Instagram --}}
        @if(!empty($sidebarSettings['footer_instagram']))
        <a href="{{ $sidebarSettings['footer_instagram'] }}"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="Instagram"
           class="group flex items-center justify-center w-10 h-10 rounded-full bg-brand-black text-white hover:bg-brand-orange hover:scale-110 transition-all duration-200"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
            </svg>
        </a>
        @endif

        {{-- Ayırıcı Çizgi --}}
        <div class="w-6 h-px bg-gray-300 my-1"></div>

        {{-- %5 İNDİRİM Butonu (Dikey Yazı) --}}
        <button
            @click="
                navigator.clipboard.writeText('SOSYAL5');
                couponCopied = true;
                $dispatch('show-notification', { message: 'SOSYAL5 kupon kodu kopyalandı! 🎉', type: 'success' });
                setTimeout(() => couponCopied = false, 3000);
            "
            class="group relative flex items-center justify-center cursor-pointer mt-1"
            title="Kupon kodunu kopyala: SOSYAL5"
        >
            <div class="bg-white border border-gray-200 rounded-full py-5 px-2.5 shadow-sm hover:shadow-md hover:border-brand-orange/50 transition-all duration-200">
                <span class="[writing-mode:vertical-lr] text-[11px] font-bold tracking-widest text-brand-black group-hover:text-brand-orange transition-colors duration-200"
                      x-text="couponCopied ? 'KOPYALANDİ!' : '%5 İNDİRİM'"
                >
                    %5 İNDİRİM
                </span>
            </div>
            {{-- Tooltip --}}
            <div class="absolute right-full mr-2 whitespace-nowrap bg-brand-black text-white text-xs font-medium py-1.5 px-3 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                Kodu kopyala: SOSYAL5
                <div class="absolute top-1/2 -translate-y-1/2 -right-1 w-2 h-2 bg-brand-black rotate-45"></div>
            </div>
        </button>
    </div>
</div>

{{-- Mobilde Sağ Alt Köşede Küçük Versiyon --}}
<div id="floating-sidebar-mobile"
     class="fixed right-3 bottom-20 z-50 flex md:hidden flex-col items-center gap-1.5"
     x-data="{ couponCopied: false, showSocial: false }"
>
    {{-- Açılır Sosyal İkonlar --}}
    <template x-if="showSocial">
        <div class="flex flex-col items-center gap-1.5 mb-1"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
        >
            @if(!empty($sidebarSettings['footer_facebook']))
            <a href="{{ $sidebarSettings['footer_facebook'] }}"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="Facebook"
               class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-black text-white shadow-lg"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                </svg>
            </a>
            @endif

            @if(!empty($sidebarSettings['footer_instagram']))
            <a href="{{ $sidebarSettings['footer_instagram'] }}"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="Instagram"
               class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-black text-white shadow-lg"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                </svg>
            </a>
            @endif
        </div>
    </template>

    {{-- Ana %5 İndirim Butonu (Toggle) --}}
    <button
        @click="
            if (!showSocial) {
                showSocial = true;
            } else {
                navigator.clipboard.writeText('SOSYAL5');
                couponCopied = true;
                $dispatch('show-notification', { message: 'SOSYAL5 kupon kodu kopyalandı! 🎉', type: 'success' });
                setTimeout(() => { couponCopied = false; showSocial = false; }, 2000);
            }
        "
        class="flex items-center justify-center w-12 h-12 rounded-full bg-brand-orange text-white shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200"
        :title="showSocial ? 'Kupon kodunu kopyala' : 'Sosyal medya & indirim'"
    >
        <span class="text-[10px] font-black leading-tight text-center" x-text="couponCopied ? '✓' : '%5'">%5</span>
    </button>
</div>

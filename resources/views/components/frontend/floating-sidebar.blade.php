{{-- Floating Social Sidebar + İndirim Kuponu Widget --}}
{{-- Halıköy projesindeki gibi sol tarafta minimal dikey çubuk --}}
@php
    $sidebarSettings = \App\Models\Setting::whereIn('key', [
        'footer_facebook',
        'footer_instagram',
    ])->pluck('value', 'key')->toArray();
@endphp

{{-- Desktop: Sol kenarda sabit dikey sidebar --}}
<div id="floating-sidebar" class="fixed left-0 top-1/2 -translate-y-1/2 z-50 hidden md:flex flex-col items-center">
    <div class="flex flex-col items-center">
        {{-- Facebook --}}
        @if(!empty($sidebarSettings['footer_facebook']))
        <a href="{{ $sidebarSettings['footer_facebook'] }}"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="Facebook"
           class="flex items-center justify-center w-9 h-9 text-black hover:text-gray-600 transition-colors"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 320 512" aria-hidden="true">
                <path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 68 2.5l5.3-98.9c-8.8-.7-69.6-6-107.8-6C131.7-7 80 45.8 80 161.1v40.4H0v97.8h80z"/>
            </svg>
        </a>
        @endif

        {{-- Instagram --}}
        @if(!empty($sidebarSettings['footer_instagram']))
        <a href="{{ $sidebarSettings['footer_instagram'] }}"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="Instagram"
           class="flex items-center justify-center w-9 h-9 text-black hover:text-gray-600 transition-colors"
        >
            <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true">
                <path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/>
            </svg>
        </a>
        @endif

        {{-- %5 İNDİRİM Dikey Yazı --}}
        <a href="{{ route('products.index') }}"
           class="block border border-gray-300 rounded-full px-1.5 py-3 mt-0.5 hover:border-gray-500 transition-colors group"
        >
            <span class="[writing-mode:vertical-lr] text-[10px] font-semibold tracking-[0.15em] text-black group-hover:text-gray-600 transition-colors whitespace-nowrap">%5 İNDİRİM</span>
        </a>
    </div>
</div>

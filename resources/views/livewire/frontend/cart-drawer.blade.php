<div 
    x-data="{ 
        open: false,
        activeTab: 'cart'
    }" 
    x-on:open-cart.window="open = true"
    x-on:toggle-cart.window="open = !open"
    @keydown.escape.window="open = false"
    x-cloak
    class="relative z-[9998]" 
    aria-labelledby="cart-drawer-title" 
    role="dialog" 
    aria-modal="true"
    x-show="open"
    style="display: none;"
>
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="transition-opacity duration-500 ease-out" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity duration-500 ease-in" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-black/60" 
         style="z-index: 9998;"
         @click="open = false"></div>

    <!-- Drawer Container -->
    <div class="fixed inset-x-0 bottom-0 md:bottom-0 md:top-0 pointer-events-none flex items-end md:items-stretch md:justify-end" style="top: 12%; z-index: 9998;">
        <div x-show="open" 
             x-transition:enter="transition-all duration-500 ease-[cubic-bezier(.32,.72,0,1)]" 
             x-transition:enter-start="translate-y-full md:translate-y-0 md:translate-x-full opacity-95" 
             x-transition:enter-end="translate-y-0 md:translate-x-0 opacity-100" 
             x-transition:leave="transition-all duration-300 ease-[cubic-bezier(.32,.72,0,1)]" 
             x-transition:leave-start="translate-y-0 md:translate-x-0 opacity-100" 
             x-transition:leave-end="translate-y-full md:translate-y-0 md:translate-x-full opacity-0" 
             class="pointer-events-auto w-full md:max-w-[380px] lg:max-w-[420px] h-full md:max-h-full shadow-2xl rounded-t-3xl md:rounded-none overflow-hidden"
             style="will-change: transform; transform: translateZ(0); backface-visibility: hidden;">

            <div class="flex flex-col h-full bg-white rounded-t-[24px] md:rounded-none md:rounded-l-2xl overflow-hidden shadow-[0_-8px_40px_rgba(0,0,0,0.08)] md:shadow-[-8px_0_40px_rgba(0,0,0,0.08)]">
                
                <!-- Drag Pill (Mobile) -->
                <div class="flex justify-center pt-[10px] pb-1 md:hidden shrink-0">
                    <div class="w-12 h-1 rounded-full bg-black/[0.06]"></div>
                </div>

                <!-- Header: Tabs -->
                <div class="shrink-0 flex items-center justify-between px-5 lg:px-12 pt-6 md:pt-8 pb-5 md:pb-6 border-b border-black/[0.06]">
                    <div class="flex items-center gap-10">
                        <!-- Sepet Tab -->
                        <button @click="activeTab = 'cart'" 
                                :class="activeTab === 'cart' ? 'opacity-100' : 'opacity-[0.2] hover:opacity-100'"
                                class="transition-opacity duration-300 relative">
                            <span class="text-2xl lg:text-[1.875rem] font-bold leading-none tracking-tight">Sepet</span>
                            @if($items->sum('quantity') > 0)
                                <span class="absolute font-medium text-xs lg:text-sm leading-none"
                                      style="top: -2px; left: calc(100% + 4px);">{{ $items->sum('quantity') }}</span>
                            @endif
                        </button>
                        <!-- İnceledikleriniz Tab -->
                        <button @click="activeTab = 'recent'" 
                                :class="activeTab === 'recent' ? 'opacity-100' : 'opacity-[0.2] hover:opacity-100'"
                                class="transition-opacity duration-300">
                            <span class="text-2xl lg:text-[1.875rem] font-bold leading-none tracking-tight">İnceledikleriniz</span>
                        </button>
                    </div>
                    <!-- Close Button (Desktop) -->
                    <button @click="open = false" 
                            class="hidden sm:flex items-center justify-center w-12 h-12 rounded-full border border-black/10 text-black/40 hover:text-black hover:border-black/40 transition-all shrink-0 ml-auto">
                        <svg class="w-[14px] h-[14px]" viewBox="0 0 20 20" stroke="currentColor" fill="none" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15L15 5M5 5L15 15"/></svg>
                    </button>
                </div>

                <!-- Content Panels -->
                <div class="flex-1 flex flex-col overflow-hidden min-h-0">

                    <!-- Cart Panel -->
                    <div x-show="activeTab === 'cart'" class="flex flex-col h-full">
                        
                        @if(count($items) > 0)
                            <!-- Scrollable: Products -->
                            <div class="flex-1 overflow-y-auto overscroll-contain px-5 lg:px-12 py-6 lg:py-8" style="scrollbar-width: thin;">
                                <ul class="flex flex-col">
                                    @foreach($items as $index => $item)
                                        <li class="flex gap-4 md:gap-6 {{ $index > 0 ? 'pt-4 md:pt-6 border-t border-black/[0.06]' : '' }} pb-4 md:pb-6">
                                            <!-- Product Image -->
                                            <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate 
                                               class="block shrink-0 grow-0 rounded-lg overflow-hidden" style="width:80px;height:80px;">
                                                <img src="{{ $item->product->images->first() ? $item->product->images->first()->image_url : asset('img/placeholder.svg') }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     width="80" height="80"
                                                     class="w-full h-full object-cover" 
                                                     loading="lazy">
                                            </a>

                                            <!-- Product Details -->
                                            <div class="flex-1 min-w-0 flex flex-col justify-center gap-1">
                                                <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate 
                                                   class="font-medium text-sm leading-tight line-clamp-2">{{ $item->product->name }}</a>
                                                @if($item->variant)
                                                    <span class="text-xs text-black/40">{{ $item->variant->size }}</span>
                                                @endif
                                                <span class="text-sm">{{ number_format($item->price, 2) }}TL</span>
                                            </div>

                                            <!-- Quantity & Remove -->
                                            <div class="shrink-0 flex flex-col items-end justify-between" style="min-height:80px;">
                                                <div class="flex items-center justify-center border border-black/10 rounded-lg relative" style="width:48px;height:36px;">
                                                    <span class="text-sm text-center">{{ $item->quantity }}</span>
                                                    <div class="absolute right-0 hidden lg:flex flex-col items-center justify-center h-full w-5">
                                                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" class="flex justify-center w-full items-center h-[18px] text-black/30 hover:text-black">
                                                            <svg class="w-2 h-2" viewBox="0 0 8 6" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M0.5 4.75L4 1.25L7.5 4.75"/></svg>
                                                        </button>
                                                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" class="flex justify-center w-full items-center h-[18px] text-black/30 hover:text-black">
                                                            <svg class="w-2 h-2" viewBox="0 0 8 6" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M0.5 1.25L4 4.75L7.5 1.25"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <button wire:click="removeItem({{ $item->id }})" class="text-xs text-black/40 hover:text-black transition-colors">Kaldır</button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                <!-- Beğenebilirsiniz (Recommendations) -->
                                @if($recommendations->count() > 0)
                                    <div class="mt-4 pt-6 border-t border-black/[0.06]">
                                        <div class="flex justify-between items-center mb-4">
                                            <p class="font-medium text-lg">Beğenebilirsiniz</p>
                                            <div class="flex gap-2">
                                                <button class="w-9 h-9 rounded-full border border-black/10 flex items-center justify-center text-black/30 hover:text-black hover:border-black/40 transition-all">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 6L8 12L14 18"/></svg>
                                                </button>
                                                <button class="w-9 h-9 rounded-full border border-black/10 flex items-center justify-center text-black/30 hover:text-black hover:border-black/40 transition-all">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6L16 12L10 18"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                        @foreach($recommendations->take(2) as $rec)
                                            <div class="flex items-center gap-4 {{ !$loop->last ? 'mb-4 pb-4 border-b border-black/[0.06]' : '' }}">
                                                <a href="{{ route('products.show', $rec->slug) }}" wire:navigate class="block shrink-0 rounded-lg overflow-hidden" style="width:72px;height:72px;">
                                                    <img src="{{ $rec->images->first() ? $rec->images->first()->image_url : asset('img/placeholder.svg') }}" alt="{{ $rec->name }}" width="72" height="72" class="w-full h-full object-cover" loading="lazy">
                                                </a>
                                                <div class="flex-1 min-w-0">
                                                    <a href="{{ route('products.show', $rec->slug) }}" wire:navigate class="font-medium text-sm leading-tight line-clamp-2 mb-1">{{ $rec->name }}</a>
                                                    <span class="text-sm font-semibold">{{ number_format($rec->discount_price ?? $rec->price, 2) }} ₺</span>
                                                </div>
                                                <a href="{{ route('products.show', $rec->slug) }}" wire:navigate class="shrink-0">
                                                    {{-- Mobile: siyah yuvarlak + --}}
                                                    <span class="md:hidden w-10 h-10 rounded-full bg-black flex items-center justify-center text-white">
                                                        <svg class="w-4 h-4" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 6H6M9.5 6H6M6 6V2.5M6 6V9.5"/></svg>
                                                    </span>
                                                    {{-- Desktop: pill buton --}}
                                                    <span class="hidden md:inline-flex items-center gap-1 px-3 py-2 text-xs font-medium border border-black rounded-full hover:bg-black hover:text-white transition-colors">
                                                        + Hızlı Ekleme
                                                    </span>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Footer -->
                            <div class="shrink-0">
                                <!-- Footer Icons: Sipariş Notu, Kargo, İndirim -->
                                <div class="flex border-t border-black/[0.06]">
                                    <button class="flex-1 flex items-center justify-center gap-[10px] py-3 md:py-4 text-black/60 hover:text-black transition-colors">
                                        <svg class="w-5 h-5 opacity-30" viewBox="0 0 25 24" stroke="currentColor" fill="none" stroke-width="1">
                                            <path class="fill-black/[0.03]" d="M12.9 2H12.1C8.73969 2 7.05953 2 5.77606 2.65396C4.64708 3.2292 3.7292 4.14708 3.15396 5.27606C2.5 6.55953 2.5 8.23969 2.5 11.6V12.4C2.5 15.7603 2.5 17.4405 3.15396 18.7239C3.7292 19.8529 4.64708 20.7708 5.77606 21.346C7.05953 22 8.73969 22 12.1 22H13.5C14.4319 22 14.8978 22 15.2654 21.8478C15.7554 21.6448 16.1448 21.2554 16.3478 20.7654C16.5 20.3978 16.5 19.9319 16.5 19C16.5 18.0681 16.5 17.6021 16.6522 17.2346C16.8552 16.7445 17.2446 16.3552 17.7346 16.1522C18.1022 16 18.5681 16 19.5 16C20.4319 16 20.8978 16 21.2654 15.8477C21.7554 15.6447 22.1448 15.2554 22.3478 14.7653C22.5 14.3978 22.5 13.9319 22.5 13V11.6C22.5 8.23969 22.5 6.55953 21.846 5.27606C21.2708 4.14708 20.3529 3.2292 19.2239 2.65396C17.9405 2 16.2603 2 12.9 2Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 21.5V19.2C16.5 18.0799 16.5 17.5198 16.718 17.092C16.9097 16.7157 17.2157 16.4097 17.592 16.218C18.0198 16 18.5799 16 19.7 16H22M7.5 7H15.5M7.5 11H13.5M7.5 15H9.5M15.6716 22H12.1C8.73969 22 7.05953 22 5.77606 21.346C4.64708 20.7708 3.7292 19.8529 3.15396 18.7239C2.5 17.4405 2.5 15.7603 2.5 12.4V11.6C2.5 8.23969 2.5 6.55953 3.15396 5.27606C3.7292 4.14708 4.64708 3.2292 5.77606 2.65396C7.05953 2 8.73969 2 12.1 2H12.9C16.2603 2 17.9405 2 19.2239 2.65396C20.3529 3.2292 21.2708 4.14708 21.846 5.27606C22.5 6.55953 22.5 8.23969 22.5 11.6V15.1716C22.5 15.5088 22.5 15.6774 22.4912 15.8399C22.4171 17.2049 21.8791 18.5036 20.9663 19.5212C20.8577 19.6423 20.7385 19.7615 20.5 20C20.2615 20.2385 20.1423 20.3577 20.0212 20.4663C19.0036 21.3791 17.7049 21.9171 16.3399 21.9912C16.1774 22 16.0088 22 15.6716 22Z"/>
                                        </svg>
                                        <span class="text-sm leading-tight">Sipariş notu</span>
                                    </button>
                                    <button class="flex-1 flex items-center justify-center gap-[10px] py-3 md:py-4 text-black/60 hover:text-black transition-colors border-x border-black/[0.06]">
                                        <svg class="w-5 h-5 opacity-30" viewBox="0 0 25 24" stroke="currentColor" fill="none" stroke-width="1">
                                            <path class="fill-black/[0.03]" d="M22.5 14.2825V7.28202C22.5 7.10718 22.4083 6.94515 22.2583 6.8552C22.0995 6.75991 21.9009 6.76067 21.7429 6.85718L15.0012 10.9731C14.0927 11.5278 13.6384 11.8052 13.1525 11.9135C12.7228 12.0092 12.2772 12.0092 11.8475 11.9135C11.3616 11.8052 10.9073 11.5278 9.99878 10.9731L3.25714 6.85718C3.09906 6.76067 2.90048 6.75991 2.74166 6.8552C2.59174 6.94515 2.5 7.10718 2.5 7.28202V14.2825C2.5 15.2735 2.5 15.769 2.64219 16.2143C2.76802 16.6083 2.97396 16.972 3.24708 17.2826C3.55572 17.6336 3.98062 17.8886 4.83042 18.3984L10.0304 21.5184C10.9283 22.0572 11.3773 22.3266 11.8565 22.4318C12.2805 22.5249 12.7195 22.5249 13.1435 22.4318C13.6227 22.3266 14.0717 22.0572 14.9696 21.5184L20.1696 18.3984C21.0194 17.8886 21.4443 17.6336 21.7529 17.2826C22.026 16.972 22.232 16.6083 22.3578 16.2143C22.5 15.769 22.5 15.2735 22.5 14.2825Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.99988 9.5L17 4M12.5 12.5L21.5 7M12.5 12.5L3.5 7M12.5 12.5V22.5M2.5 9.71771V14.2823C2.5 15.2733 2.5 15.7688 2.64219 16.2141C2.76802 16.6081 2.97396 16.9718 3.24708 17.2824C3.55572 17.6334 3.98062 17.8884 4.83042 18.3983L10.0304 21.5183C10.9283 22.057 11.3773 22.3264 11.8565 22.4316C12.2805 22.5247 12.7195 22.5247 13.1435 22.4316C13.6227 22.3264 14.0717 22.057 14.9696 21.5183L20.1696 18.3983C21.0194 17.8884 21.4443 17.6334 21.7529 17.2824C22.026 16.9718 22.232 16.6081 22.3578 16.2141C22.5 15.7688 22.5 15.2733 22.5 14.2823V9.71771C22.5 8.72669 22.5 8.23117 22.3578 7.78593C22.232 7.39192 22.026 7.02818 21.7529 6.71757C21.4443 6.36657 21.0194 6.11163 20.1696 5.60175L14.9696 2.48175C14.0717 1.94301 13.6227 1.67364 13.1435 1.56839C12.7195 1.4753 12.2805 1.4753 11.8565 1.56839C11.3773 1.67364 10.9283 1.94301 10.0304 2.48175L4.83042 5.60175C3.98062 6.11163 3.55572 6.36657 3.24708 6.71757C2.97396 7.02818 2.76802 7.39192 2.64219 7.78593C2.5 8.23117 2.5 8.72669 2.5 9.71771Z"/>
                                        </svg>
                                        <span class="text-sm leading-tight">Kargo</span>
                                    </button>
                                    <button class="flex-1 flex items-center justify-center gap-[10px] py-3 md:py-4 text-black/60 hover:text-black transition-colors">
                                        <svg class="w-5 h-5 opacity-30" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1">
                                            <path class="fill-black/[0.03]" d="M7.24 4.03H7.09C6.01 4.03 5.47 4.03 5.06 4.24C4.7 4.42 4.4 4.72 4.22 5.08C4.01 5.49 4.01 6.03 4.01 7.11V7.23C4.01 7.65 4.01 7.86 3.97 8.06C3.93 8.24 3.87 8.41 3.78 8.57C3.68 8.75 3.55 8.91 3.28 9.23L2.84 9.75C2.24 10.46 1.94 10.81 1.82 11.21C1.72 11.56 1.72 11.93 1.82 12.28C1.93 12.68 2.23 13.03 2.84 13.74L3.28 14.26C3.55 14.58 3.69 14.74 3.78 14.92C3.87 15.08 3.93 15.25 3.97 15.43C4.01 15.63 4.01 15.84 4.01 16.26V16.38C4.01 17.46 4.01 18 4.22 18.41C4.4 18.77 4.7 19.07 5.06 19.25C5.47 19.46 6.01 19.46 7.09 19.46H7.24C7.66 19.46 7.87 19.46 8.07 19.5C8.25 19.54 8.42 19.6 8.58 19.69C8.76 19.79 8.92 19.93 9.24 20.2L9.74 20.63C10.45 21.24 10.81 21.55 11.21 21.66C11.56 21.76 11.94 21.76 12.29 21.66C12.69 21.54 13.05 21.24 13.76 20.63L14.26 20.2C14.58 19.93 14.74 19.79 14.92 19.69C15.08 19.6 15.25 19.54 15.43 19.5C15.63 19.46 15.84 19.46 16.26 19.46H16.37C17.45 19.46 17.99 19.46 18.4 19.25C18.76 19.07 19.06 18.77 19.24 18.41C19.45 18 19.45 17.46 19.45 16.38V16.27C19.45 15.85 19.45 15.64 19.49 15.44C19.53 15.26 19.59 15.09 19.68 14.93C19.78 14.75 19.92 14.59 20.19 14.27L20.62 13.77C21.23 13.06 21.54 12.7 21.65 12.3C21.75 11.95 21.75 11.57 21.65 11.22C21.53 10.82 21.23 10.46 20.62 9.75L20.19 9.25C19.92 8.93 19.78 8.77 19.68 8.59C19.59 8.43 19.53 8.26 19.49 8.08C19.45 7.88 19.45 7.67 19.45 7.25V7.14C19.45 6.06 19.45 5.52 19.24 5.11C19.06 4.75 18.76 4.45 18.4 4.27C17.99 4.06 17.45 4.06 16.37 4.06H16.26C15.84 4.06 15.63 4.06 15.43 4.02C15.25 3.98 15.08 3.92 14.92 3.83C14.74 3.73 14.58 3.59 14.26 3.32L13.76 2.89C13.05 2.28 12.69 1.97 12.29 1.86C11.94 1.76 11.56 1.76 11.21 1.86C10.81 1.98 10.45 2.28 9.74 2.89L9.24 3.32C8.92 3.59 8.76 3.73 8.58 3.83C8.42 3.92 8.25 3.98 8.07 4.02C7.87 4.06 7.66 4.06 7.24 4.06V4.03Z"/>
                                            <path stroke-linecap="round" d="M8.38 15.36L11.75 11.74L15.12 8.12M14.92 14.59C14.92 14.81 14.74 14.99 14.52 14.99C14.3 14.99 14.12 14.81 14.12 14.59M14.92 14.59C14.92 14.37 14.74 14.19 14.52 14.19C14.3 14.19 14.12 14.37 14.12 14.59M14.92 14.59H14.12M9.42 9.01C9.42 9.23 9.24 9.41 9.02 9.41C8.8 9.41 8.62 9.23 8.62 9.01M9.42 9.01C9.42 8.79 9.24 8.61 9.02 8.61C8.8 8.61 8.62 8.79 8.62 9.01M9.42 9.01H8.62"/>
                                        </svg>
                                        <span class="text-sm leading-tight">İndirim</span>
                                    </button>
                                </div>

                                <!-- Summary & Buttons -->
                                <div class="bg-black/[0.025] px-5 lg:px-12 py-6 lg:py-8" style="padding-bottom: calc(1.5rem + 76px + env(safe-area-inset-bottom, 0px));">
                                    <!-- Subtotal Row -->
                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div class="text-sm leading-[1.375] text-black/60">
                                            Vergi dahildir ve gönderim bedeli ödeme sırasında hesaplanır
                                        </div>
                                        <div class="text-right place-self-end">
                                            <span class="text-sm block leading-[1.375]">ara toplam</span>
                                            <span class="text-lg md:text-2xl font-bold leading-none tracking-tight">{{ number_format($total, 2) }}TL</span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="grid gap-4">
                                        <a href="{{ route('checkout') }}" @click="open = false" wire:navigate
                                           class="flex items-center justify-center gap-2 w-full py-4 bg-black text-white text-sm font-medium rounded-full hover:bg-black/90 transition-colors">
                                            <svg class="w-4 h-4 hidden sm:block" viewBox="0 0 20 20" stroke="currentColor" fill="none" stroke-width="1"><path stroke-linecap="round" d="M5.833 6.667V5.833c0-1.086 0-1.628.139-2.069a2.667 2.667 0 011.959-1.959c.44-.138.983-.138 2.069-.138s1.628 0 2.069.138a2.667 2.667 0 011.959 1.959c.138.44.138.983.138 2.069v.834M10 11.667v1.666M8.333 18.333h3.334c1.707 0 2.561 0 3.242-.256a3.333 3.333 0 001.834-1.834c.257-.681.257-1.535.257-3.243 0-1.707 0-2.561-.257-3.242a3.333 3.333 0 00-1.834-1.834c-.681-.257-1.535-.257-3.242-.257H8.333c-1.707 0-2.561 0-3.242.257a3.333 3.333 0 00-1.834 1.834C3 12.105 3 12.959 3 14.667c0 1.707 0 2.561.257 3.242a3.333 3.333 0 001.834 1.834c.681.257 1.535.257 3.242.257z"/></svg>
                                            Ödeme ekranı
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Empty Cart -->
                            <div class="flex-1 overflow-y-auto px-5 lg:px-12">
                                <div class="flex flex-col items-center justify-center py-20 text-center max-w-[288px] mx-auto">
                                    <div class="w-24 h-24 bg-black/[0.025] rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-12 h-12 text-black/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                    <h3 class="text-lg font-medium mb-2">Sepetiniz Boş</h3>
                                    <p class="text-black/50 text-sm leading-tight mb-6">Alışverişe başlamak için ürünlerimize göz atın.</p>
                                    <button @click="open = false" class="text-black font-semibold border-b-2 border-black pb-1 hover:text-black/60 hover:border-black/60 transition-colors">
                                        Alışverişe Devam Et
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Recently Viewed Panel -->
                    <div x-show="activeTab === 'recent'" class="flex-1 overflow-y-auto px-5 lg:px-12 py-8">
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <svg class="w-12 h-12 text-black/10 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-black/40 text-sm">Henüz incelediğiniz ürün yok</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

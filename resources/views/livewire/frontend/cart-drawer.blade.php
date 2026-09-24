<div 
    x-data="{ 
        open: false,
        activeTab: 'cart',
        notePanel: false,
        shippingPanel: false,
        discountPanel: false
    }" 
    x-on:open-cart.window="open = true"
    x-on:toggle-cart.window="open = !open"
    @keydown.escape.window="if(notePanel) notePanel = false; else if(shippingPanel) shippingPanel = false; else if(discountPanel) discountPanel = false; else open = false"
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
    <div class="fixed inset-x-0 bottom-0 top-[12%] md:top-0 pointer-events-none flex items-end md:items-stretch md:justify-end" style="z-index: 9998;">
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
                            class="hidden sm:flex items-center justify-center w-9 h-9 rounded-full border border-black/10 text-black/40 hover:text-black hover:border-black/40 transition-all shrink-0 ml-auto">
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
                                        </div>
                                        <div class="flex flex-col">
                                            @foreach($recommendations as $rec)
                                                <div class="flex items-center gap-4 {{ !$loop->last ? 'mb-4 pb-4 border-b border-black/[0.06]' : '' }}">
                                                    <a href="{{ route('products.show', $rec->slug) }}" wire:navigate class="block shrink-0 rounded-lg overflow-hidden" style="width:72px;height:72px;">
                                                        <img src="{{ $rec->images->first() ? $rec->images->first()->image_url : asset('img/placeholder.svg') }}" alt="{{ $rec->name }}" width="72" height="72" class="w-full h-full object-cover" loading="lazy">
                                                    </a>
                                                    <div class="flex-1 min-w-0">
                                                        <a href="{{ route('products.show', $rec->slug) }}" wire:navigate class="font-medium text-sm leading-tight line-clamp-2 mb-1 block">{{ $rec->name }}</a>
                                                        <span class="text-sm font-semibold">{{ number_format($rec->discount_price ?? $rec->price, 2) }} ₺</span>
                                                    </div>
                                                    <a href="{{ route('products.show', $rec->slug) }}" wire:navigate class="shrink-0 w-10 h-10 rounded-full bg-black flex items-center justify-center text-white hover:bg-black/80 transition-colors">
                                                        <svg class="w-4 h-4" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 6H6M9.5 6H6M6 6V2.5M6 6V9.5"/></svg>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Footer -->
                            <div class="shrink-0">
                                <!-- Footer Icons: Sipariş Notu, Kargo, İndirim -->
                                <div class="flex border-t border-black/[0.06]" wire:ignore>
                                    <button @click="notePanel = true; shippingPanel = false; discountPanel = false" :class="notePanel ? 'text-black bg-black/[0.04]' : 'text-black/60 hover:text-black'" class="flex-1 flex items-center justify-center gap-[10px] py-3 md:py-4 transition-colors">
                                        <svg class="w-5 h-5" :class="notePanel ? 'opacity-80' : 'opacity-30'" viewBox="0 0 25 24" stroke="currentColor" fill="none" stroke-width="1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 21.5V19.2C16.5 18.0799 16.5 17.5198 16.718 17.092C16.9097 16.7157 17.2157 16.4097 17.592 16.218C18.0198 16 18.5799 16 19.7 16H22M7.5 7H15.5M7.5 11H13.5M7.5 15H9.5M15.6716 22H12.1C8.73969 22 7.05953 22 5.77606 21.346C4.64708 20.7708 3.7292 19.8529 3.15396 18.7239C2.5 17.4405 2.5 15.7603 2.5 12.4V11.6C2.5 8.23969 2.5 6.55953 3.15396 5.27606C3.7292 4.14708 4.64708 3.2292 5.77606 2.65396C7.05953 2 8.73969 2 12.1 2H12.9C16.2603 2 17.9405 2 19.2239 2.65396C20.3529 3.2292 21.2708 4.14708 21.846 5.27606C22.5 6.55953 22.5 8.23969 22.5 11.6V15.1716C22.5 15.5088 22.5 15.6774 22.4912 15.8399C22.4171 17.2049 21.8791 18.5036 20.9663 19.5212C20.8577 19.6423 20.7385 19.7615 20.5 20C20.2615 20.2385 20.1423 20.3577 20.0212 20.4663C19.0036 21.3791 17.7049 21.9171 16.3399 21.9912C16.1774 22 16.0088 22 15.6716 22Z"/>
                                        </svg>
                                        <span class="text-sm leading-tight">Sipariş notu</span>
                                    </button>
                                    <button @click="shippingPanel = true; notePanel = false; discountPanel = false" :class="shippingPanel ? 'text-black bg-black/[0.04]' : 'text-black/60 hover:text-black'" class="flex-1 flex items-center justify-center gap-[10px] py-3 md:py-4 transition-colors border-x border-black/[0.06]">
                                        <svg class="w-5 h-5" :class="shippingPanel ? 'opacity-80' : 'opacity-30'" viewBox="0 0 25 24" stroke="currentColor" fill="none" stroke-width="1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.99988 9.5L17 4M12.5 12.5L21.5 7M12.5 12.5L3.5 7M12.5 12.5V22.5M2.5 9.71771V14.2823C2.5 15.2733 2.5 15.7688 2.64219 16.2141C2.76802 16.6081 2.97396 16.9718 3.24708 17.2824C3.55572 17.6334 3.98062 17.8884 4.83042 18.3983L10.0304 21.5183C10.9283 22.057 11.3773 22.3264 11.8565 22.4316C12.2805 22.5247 12.7195 22.5247 13.1435 22.4316C13.6227 22.3264 14.0717 22.057 14.9696 21.5183L20.1696 18.3983C21.0194 17.8884 21.4443 17.6334 21.7529 17.2824C22.026 16.9718 22.232 16.6081 22.3578 16.2141C22.5 15.7688 22.5 15.2733 22.5 14.2823V9.71771C22.5 8.72669 22.5 8.23117 22.3578 7.78593C22.232 7.39192 22.026 7.02818 21.7529 6.71757C21.4443 6.36657 21.0194 6.11163 20.1696 5.60175L14.9696 2.48175C14.0717 1.94301 13.6227 1.67364 13.1435 1.56839C12.7195 1.4753 12.2805 1.4753 11.8565 1.56839C11.3773 1.67364 10.9283 1.94301 10.0304 2.48175L4.83042 5.60175C3.98062 6.11163 3.55572 6.36657 3.24708 6.71757C2.97396 7.02818 2.76802 7.39192 2.64219 7.78593C2.5 8.23117 2.5 8.72669 2.5 9.71771Z"/>
                                        </svg>
                                        <span class="text-sm leading-tight">Kargo</span>
                                    </button>
                                    <button @click="discountPanel = true; notePanel = false; shippingPanel = false" :class="discountPanel ? 'text-black bg-black/[0.04]' : 'text-black/60 hover:text-black'" class="flex-1 flex items-center justify-center gap-[10px] py-3 md:py-4 transition-colors">
                                        <svg class="w-5 h-5" :class="discountPanel ? 'opacity-80' : 'opacity-30'" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1">
                                            <path stroke-linecap="round" d="M8.38 15.36L11.75 11.74L15.12 8.12M14.92 14.59C14.92 14.81 14.74 14.99 14.52 14.99C14.3 14.99 14.12 14.81 14.12 14.59M14.92 14.59C14.92 14.37 14.74 14.19 14.52 14.19C14.3 14.19 14.12 14.37 14.12 14.59M14.92 14.59H14.12M9.42 9.01C9.42 9.23 9.24 9.41 9.02 9.41C8.8 9.41 8.62 9.23 8.62 9.01M9.42 9.01C9.42 8.79 9.24 8.61 9.02 8.61C8.8 8.61 8.62 8.79 8.62 9.01M9.42 9.01H8.62"/>
                                        </svg>
                                        <span class="text-sm leading-tight">İndirim</span>
                                    </button>
                                </div>

                                <!-- Summary & Buttons -->
                                <div class="px-5 lg:px-12 py-5 lg:py-6" style="padding-bottom: calc(1.5rem + 0px + env(safe-area-inset-bottom, 0px));">
                                    <!-- Subtotal Row -->
                                    <div class="grid grid-cols-2 gap-4 mb-5">
                                        <div class="text-sm leading-[1.375] text-black/60">
                                            Vergi dahildir ve gönderim bedeli ödeme sırasında hesaplanır
                                        </div>
                                        <div class="text-right place-self-end">
                                            <span class="text-sm block leading-[1.375]">ara toplam</span>
                                            <span class="text-lg md:text-2xl font-bold leading-none tracking-tight">{{ number_format($total, 2) }}TL</span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="grid gap-3">
                                        <a href="{{ route('checkout') }}" @click="open = false" wire:navigate
                                           class="flex items-center justify-center gap-2 w-full py-4 bg-black text-white text-sm font-medium rounded-full hover:bg-black/90 transition-colors">
                                            <svg class="w-4 h-4" viewBox="0 0 20 20" stroke="currentColor" fill="none" stroke-width="1"><path stroke-linecap="round" d="M5.833 6.667V5.833c0-1.086 0-1.628.139-2.069a2.667 2.667 0 011.959-1.959c.44-.138.983-.138 2.069-.138s1.628 0 2.069.138a2.667 2.667 0 011.959 1.959c.138.44.138.983.138 2.069v.834M10 11.667v1.666M8.333 18.333h3.334c1.707 0 2.561 0 3.242-.256a3.333 3.333 0 001.834-1.834c.257-.681.257-1.535.257-3.243 0-1.707 0-2.561-.257-3.242a3.333 3.333 0 00-1.834-1.834c-.681-.257-1.535-.257-3.242-.257H8.333c-1.707 0-2.561 0-3.242.257a3.333 3.333 0 00-1.834 1.834C3 12.105 3 12.959 3 14.667c0 1.707 0 2.561.257 3.242a3.333 3.333 0 001.834 1.834c.681.257 1.535.257 3.242.257z"/></svg>
                                            Siparişi Onayla
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

    <!-- Bottom Sheet: Sipariş Notu -->
    <div x-show="notePanel" style="display: none;" class="fixed inset-0 md:left-auto md:w-[380px] lg:w-[420px]" :style="{ zIndex: 10000 }" @keydown.escape.window.stop="notePanel = false">
        <div x-show="notePanel" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="notePanel = false" class="absolute inset-0 bg-black/40 md:bg-transparent"></div>
        <div x-show="notePanel" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="absolute bottom-0 inset-x-0 bg-white rounded-t-2xl md:rounded-t-none px-6 pt-6 pb-[calc(1.5rem+15px+env(safe-area-inset-bottom,0px))] md:pb-8 md:shadow-[-8px_0_40px_rgba(0,0,0,0.08)]">
            <div class="flex items-center justify-between mb-5">
                <h4 class="text-base font-semibold">Özel talimatlar sipariş edin</h4>
                <button @click="notePanel = false" class="w-9 h-9 flex items-center justify-center rounded-full border border-black/10 hover:border-black/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <textarea rows="3" placeholder="Sipariş notu" class="w-full border border-black/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black/30 resize-none mb-4"></textarea>
            <button @click="notePanel = false" class="px-6 py-3 bg-black text-white text-sm font-medium rounded-full hover:bg-black/90 transition-colors">Uygula</button>
        </div>
    </div>

    <!-- Bottom Sheet: Kargo Tahmini -->
    <div x-show="shippingPanel" style="display: none;" class="fixed inset-0 md:left-auto md:w-[380px] lg:w-[420px]" :style="{ zIndex: 10000 }" @keydown.escape.window.stop="shippingPanel = false"
         x-data="{ city: '', postalCode: '', calculated: false }">
        <div x-show="shippingPanel" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="shippingPanel = false" class="absolute inset-0 bg-black/40 md:bg-transparent"></div>
        <div x-show="shippingPanel" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="absolute bottom-0 inset-x-0 bg-white rounded-t-2xl md:rounded-t-none px-6 pt-6 pb-[calc(1.5rem+15px+env(safe-area-inset-bottom,0px))] md:pb-8 md:shadow-[-8px_0_40px_rgba(0,0,0,0.08)]">
            <div class="flex items-center justify-between mb-5">
                <h4 class="text-base font-semibold">Kargo tahmini</h4>
                <button @click="shippingPanel = false" class="w-9 h-9 flex items-center justify-center rounded-full border border-black/10 hover:border-black/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <!-- Şehir Seçici -->
            <div class="relative mb-4">
                <label class="text-xs text-black/40 mb-1 block">Ülke/bölge</label>
                <select x-model="city" class="w-full border border-black/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black/30 appearance-none bg-white">
                    <option value="">---</option>
                    <option value="adana">Adana</option>
                    <option value="adiyaman">Adıyaman</option>
                    <option value="afyon">Afyonkarahisar</option>
                    <option value="agri">Ağrı</option>
                    <option value="aksaray">Aksaray</option>
                    <option value="amasya">Amasya</option>
                    <option value="ankara">Ankara</option>
                    <option value="antalya">Antalya</option>
                    <option value="ardahan">Ardahan</option>
                    <option value="artvin">Artvin</option>
                    <option value="aydin">Aydın</option>
                    <option value="balikesir">Balıkesir</option>
                    <option value="bartin">Bartın</option>
                    <option value="batman">Batman</option>
                    <option value="bayburt">Bayburt</option>
                    <option value="bilecik">Bilecik</option>
                    <option value="bingol">Bingöl</option>
                    <option value="bitlis">Bitlis</option>
                    <option value="bolu">Bolu</option>
                    <option value="burdur">Burdur</option>
                    <option value="bursa">Bursa</option>
                    <option value="canakkale">Çanakkale</option>
                    <option value="cankiri">Çankırı</option>
                    <option value="corum">Çorum</option>
                    <option value="denizli">Denizli</option>
                    <option value="diyarbakir">Diyarbakır</option>
                    <option value="duzce">Düzce</option>
                    <option value="edirne">Edirne</option>
                    <option value="elazig">Elazığ</option>
                    <option value="erzincan">Erzincan</option>
                    <option value="erzurum">Erzurum</option>
                    <option value="eskisehir">Eskişehir</option>
                    <option value="gaziantep">Gaziantep</option>
                    <option value="giresun">Giresun</option>
                    <option value="gumushane">Gümüşhane</option>
                    <option value="hakkari">Hakkari</option>
                    <option value="hatay">Hatay</option>
                    <option value="igdir">Iğdır</option>
                    <option value="isparta">Isparta</option>
                    <option value="istanbul">İstanbul</option>
                    <option value="izmir">İzmir</option>
                    <option value="kahramanmaras">Kahramanmaraş</option>
                    <option value="karabuk">Karabük</option>
                    <option value="karaman">Karaman</option>
                    <option value="kars">Kars</option>
                    <option value="kastamonu">Kastamonu</option>
                    <option value="kayseri">Kayseri</option>
                    <option value="kilis">Kilis</option>
                    <option value="kirikkale">Kırıkkale</option>
                    <option value="kirklareli">Kırklareli</option>
                    <option value="kirsehir">Kırşehir</option>
                    <option value="kocaeli">Kocaeli</option>
                    <option value="konya">Konya</option>
                    <option value="kutahya">Kütahya</option>
                    <option value="malatya">Malatya</option>
                    <option value="manisa">Manisa</option>
                    <option value="mardin">Mardin</option>
                    <option value="mersin">Mersin</option>
                    <option value="mugla">Muğla</option>
                    <option value="mus">Muş</option>
                    <option value="nevsehir">Nevşehir</option>
                    <option value="nigde">Niğde</option>
                    <option value="ordu">Ordu</option>
                    <option value="osmaniye">Osmaniye</option>
                    <option value="rize">Rize</option>
                    <option value="sakarya">Sakarya</option>
                    <option value="samsun">Samsun</option>
                    <option value="sanliurfa">Şanlıurfa</option>
                    <option value="siirt">Siirt</option>
                    <option value="sinop">Sinop</option>
                    <option value="sirnak">Şırnak</option>
                    <option value="sivas">Sivas</option>
                    <option value="tekirdag">Tekirdağ</option>
                    <option value="tokat">Tokat</option>
                    <option value="trabzon">Trabzon</option>
                    <option value="tunceli">Tunceli</option>
                    <option value="usak">Uşak</option>
                    <option value="van">Van</option>
                    <option value="yalova">Yalova</option>
                    <option value="yozgat">Yozgat</option>
                    <option value="zonguldak">Zonguldak</option>
                </select>
                <svg class="w-4 h-4 absolute right-4 top-8 text-black/40 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </div>
            <!-- Posta Kodu -->
            <input type="text" x-model="postalCode" placeholder="Posta/Posta kodu" class="w-full border border-black/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black/30 mb-5">
            <!-- Hesapla -->
            <button @click="if(city) calculated = true" class="px-6 py-3 bg-black text-white text-sm font-medium rounded-full hover:bg-black/90 transition-colors mb-4">Hesapla</button>
            <!-- Sonuç -->
            <div x-show="calculated" x-transition.opacity class="flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-xl mt-1">
                <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm text-green-700 font-medium">Ücretsiz kargo · Tahmini 2-4 iş günü</span>
            </div>
        </div>
    </div>

    <!-- Bottom Sheet: İndirim Kodu -->
    <div x-show="discountPanel" style="display: none;" class="fixed inset-0 md:left-auto md:w-[380px] lg:w-[420px]" :style="{ zIndex: 10000 }" x-data="{ code: '' }" @keydown.escape.window.stop="discountPanel = false">
        <div x-show="discountPanel" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="discountPanel = false" class="absolute inset-0 bg-black/40 md:bg-transparent"></div>
        <div x-show="discountPanel" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="absolute bottom-0 inset-x-0 bg-white rounded-t-2xl md:rounded-t-none px-6 pt-6 pb-[calc(1.5rem+15px+env(safe-area-inset-bottom,0px))] md:pb-8 md:shadow-[-8px_0_40px_rgba(0,0,0,0.08)]">
            <div class="flex items-center justify-between mb-5">
                <h4 class="text-base font-semibold">İndirim</h4>
                <button @click="discountPanel = false" class="w-9 h-9 flex items-center justify-center rounded-full border border-black/10 hover:border-black/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <input type="text" x-model="code" placeholder="İndirim kodu" class="w-full border border-black/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black/30 mb-5">
            <button @click="if(code) $wire.applyCoupon(code)" class="px-6 py-3 bg-black text-white text-sm font-medium rounded-full hover:bg-black/90 transition-colors">Uygula</button>
        </div>
    </div>
</div>

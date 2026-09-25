<div 
    x-data="{ 
        open: false,
        activeTab: 'cart',
        notePanel: false,
        shippingPanel: false,
        discountPanel: false,
        recentProducts: [],
        loadRecent() { try { this.recentProducts = JSON.parse(localStorage.getItem('recently_viewed') || '[]'); } catch(e) { this.recentProducts = []; } },
        dragY: 0,
        dragging: false,
        closing: false,
        touchStartY: 0,
        startDrag(e) {
            if (window.innerWidth >= 768) return;
            this.touchStartY = e.touches[0].clientY;
            this.dragging = true;
            this.closing = false;
            this.dragY = 0;
        },
        onDrag(e) {
            if (!this.dragging) return;
            const diff = e.touches[0].clientY - this.touchStartY;
            this.dragY = Math.max(0, diff);
        },
        endDrag() {
            if (!this.dragging) return;
            this.dragging = false;
            if (this.dragY > 120) {
                this.closing = true;
                this.dragY = window.innerHeight;
                setTimeout(() => {
                    this.open = false;
                    setTimeout(() => {
                        this.dragY = 0;
                        this.closing = false;
                    }, 500);
                }, 400);
            } else {
                this.dragY = 0;
            }
        },
        closeDrawer() {
            if (window.innerWidth < 768) {
                this.closing = true;
                this.dragY = window.innerHeight;
                setTimeout(() => {
                    this.open = false;
                    setTimeout(() => {
                        this.dragY = 0;
                        this.closing = false;
                    }, 500);
                }, 400);
            } else {
                this.open = false;
            }
        }
    }" 
    x-on:open-cart.window="open = true"
    x-on:toggle-cart.window="open = !open"
    @keydown.escape.window="if(notePanel) notePanel = false; else if(shippingPanel) shippingPanel = false; else if(discountPanel) discountPanel = false; else closeDrawer()"
    x-cloak
    class="relative"
    style="z-index: 10000;"
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
         @click="closeDrawer()"></div>

    <!-- Drawer Container -->
    <div class="fixed inset-x-0 bottom-0 top-[12%] md:top-0 pointer-events-none flex items-end md:items-stretch md:justify-end" style="z-index: 10001;">
        <div x-show="open" 
             x-transition:enter="transition-all duration-500 ease-[cubic-bezier(.32,.72,0,1)]" 
             x-transition:enter-start="translate-y-full md:translate-y-0 md:translate-x-full opacity-95" 
             x-transition:enter-end="translate-y-0 md:translate-x-0 opacity-100" 
             x-transition:leave="transition-all duration-300 ease-[cubic-bezier(.32,.72,0,1)]" 
             x-transition:leave-start="translate-y-0 md:translate-x-0 opacity-100" 
             x-transition:leave-end="translate-y-full md:translate-y-0 md:translate-x-full opacity-0" 
             class="pointer-events-auto w-full h-full md:max-h-full shadow-2xl rounded-t-3xl md:rounded-none overflow-hidden cart-drawer-panel"
             style="will-change: transform; transform: translateZ(0); backface-visibility: hidden;">
             <style>@media(min-width:768px){.cart-drawer-panel{max-width:420px!important}}@media(min-width:1024px){.cart-drawer-panel{max-width:460px!important}}</style>

            <div class="flex flex-col h-full bg-white rounded-t-[24px] md:rounded-none md:rounded-l-2xl overflow-hidden shadow-[0_-8px_40px_rgba(0,0,0,0.08)] md:shadow-[-8px_0_40px_rgba(0,0,0,0.08)]"
                 :style="dragging ? 'transform: translateY(' + dragY + 'px); transition: none;' : (closing ? 'transform: translateY(' + dragY + 'px); transition: transform 0.4s ease-out;' : (dragY > 0 ? 'transform: translateY(' + dragY + 'px); transition: transform 0.3s ease;' : 'transition: transform 0.3s ease;'))"
                 @touchend="endDrag()" @touchcancel="endDrag()">
                
                <!-- Drag Pill (Mobile) -->
                <div class="flex justify-center pt-[10px] pb-1 md:hidden shrink-0 cursor-grab active:cursor-grabbing"
                     @touchstart="startDrag($event)" @touchmove.prevent="onDrag($event)"
                     style="touch-action: none;">
                    <div class="w-12 h-1 rounded-full bg-black/[0.06]"></div>
                </div>

                <!-- Header: Tabs -->
                <div class="shrink-0 flex items-center justify-between px-5 lg:px-12 pt-6 md:pt-8 pb-5 md:pb-6 border-b border-black/[0.06]"
                     @touchstart="startDrag($event)" @touchmove.prevent="onDrag($event)">
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
                        <button @click="activeTab = 'recent'; loadRecent()" 
                                :class="activeTab === 'recent' ? 'opacity-100' : 'opacity-[0.2] hover:opacity-100'"
                                class="transition-opacity duration-300">
                            <span class="text-2xl lg:text-[1.875rem] font-bold leading-none tracking-tight">İnceledikleriniz</span>
                        </button>
                    </div>
                    <!-- Close Button (Desktop) -->
                    <button @click="closeDrawer()" 
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
                                                    @php
                                                        $variantLabel = $item->variant->size;
                                                        if (!$variantLabel && $item->variant->color) {
                                                            $variantLabel = is_array($item->variant->color) ? implode(' / ', $item->variant->color) : $item->variant->color;
                                                        }
                                                    @endphp
                                                    @if($variantLabel)
                                                    <span class="text-xs text-black/40">{{ $variantLabel }}</span>
                                                    @endif
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
                                                <button wire:click="removeItem({{ $item->id }})" class="p-1.5 text-black/30 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all relative" style="right: 8px;" title="Kaldır" aria-label="Ürünü sepetten kaldır">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                <!-- Beğenebilirsiniz (Horizontal Carousel) -->
                                @if($recommendations->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-black/[0.06]"
                                         x-data="{
                                             scrollEl: null,
                                             init() { this.scrollEl = this.$refs.recScroll; }
                                         }">
                                        <div class="flex justify-between items-center mb-3">
                                            <p class="font-medium text-base">Beğenebilirsiniz</p>
                                            <div class="flex items-center gap-1.5">
                                                <button @click="scrollEl.scrollBy({left: -260, behavior: 'smooth'})" class="w-8 h-8 rounded-full border border-black/10 flex items-center justify-center text-black/40 hover:text-black hover:border-black/30 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                                                </button>
                                                <button @click="scrollEl.scrollBy({left: 260, behavior: 'smooth'})" class="w-8 h-8 rounded-full border border-black/10 flex items-center justify-center text-black/40 hover:text-black hover:border-black/30 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div x-ref="recScroll" class="flex gap-3 overflow-x-auto pb-1 -mx-5 px-5 lg:-mx-12 lg:px-12 snap-x snap-mandatory hide-scrollbar" style="-webkit-overflow-scrolling: touch;">
                                            @foreach($recommendations as $rec)
                                                <div class="snap-start shrink-0 flex items-center gap-3 rounded-xl border border-black/[0.06] p-2.5 hover:border-black/15 transition-colors" style="width: 260px;">
                                                    <a href="{{ route('products.show', $rec->slug) }}" wire:navigate class="block shrink-0 rounded-lg overflow-hidden" style="width:64px;height:64px;">
                                                        <img src="{{ $rec->images->first() ? $rec->images->first()->image_url : asset('img/placeholder.svg') }}" alt="{{ $rec->name }}" width="64" height="64" class="w-full h-full object-cover" loading="lazy">
                                                    </a>
                                                    <div class="flex-1 min-w-0">
                                                        <a href="{{ route('products.show', $rec->slug) }}" wire:navigate class="font-medium text-xs leading-tight line-clamp-2 mb-0.5 block">{{ $rec->name }}</a>
                                                        <span class="text-xs text-black/60 block mb-1.5">{{ number_format($rec->discount_price ?? $rec->price, 2) }}TL</span>
                                                        <button wire:click="quickAddToCart({{ $rec->id }})" 
                                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-black text-white text-[11px] font-medium rounded-full hover:bg-black/80 transition-colors">
                                                            <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 6H6M9.5 6H6M6 6V2.5M6 6V9.5"/></svg>
                                                            Hızlı Ekle
                                                        </button>
                                                    </div>
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
                                    <button @click="notePanel = true; shippingPanel = false; discountPanel = false" :class="notePanel ? 'text-black bg-black/[0.04]' : 'text-black/50 hover:text-black'" class="flex-1 flex items-center justify-center gap-2 py-3.5 md:py-4 transition-colors">
                                        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                            <polyline points="10 9 9 9 8 9"/>
                                        </svg>
                                        <span class="text-sm leading-tight">Sipariş notu</span>
                                    </button>
                                    <button @click="shippingPanel = true; notePanel = false; discountPanel = false" :class="shippingPanel ? 'text-black bg-black/[0.04]' : 'text-black/50 hover:text-black'" class="flex-1 flex items-center justify-center gap-2 py-3.5 md:py-4 transition-colors border-x border-black/[0.06]">
                                        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                                        </svg>
                                        <span class="text-sm leading-tight">Kargo</span>
                                    </button>
                                    <button @click="discountPanel = true; notePanel = false; shippingPanel = false" :class="discountPanel ? 'text-black bg-black/[0.04]' : 'text-black/50 hover:text-black'" class="flex-1 flex items-center justify-center gap-2 py-3.5 md:py-4 transition-colors">
                                        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="9" cy="9" r="1"/>
                                            <circle cx="15" cy="15" r="1"/>
                                            <line x1="8" y1="16" x2="16" y2="8"/>
                                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                        </svg>
                                        <span class="text-sm leading-tight">İndirim</span>
                                    </button>
                                </div>

                                <!-- Summary & Buttons -->
                                <div class="px-5 lg:px-12 py-4 lg:py-6" style="padding-bottom: calc(1.5rem + env(safe-area-inset-bottom, 0px));">
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
                                    <button @click="closeDrawer()" class="text-black font-semibold border-b-2 border-black pb-1 hover:text-black/60 hover:border-black/60 transition-colors">
                                        Alışverişe Devam Et
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Recently Viewed Panel -->
                    <div x-show="activeTab === 'recent'" class="flex-1 overflow-y-auto px-5 lg:px-12 py-6">

                        <!-- Boş Durum -->
                        <template x-if="recentProducts.length === 0">
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <svg class="w-12 h-12 text-black/10 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-black/40 text-sm">Henüz incelediğiniz ürün yok</p>
                            </div>
                        </template>

                        <!-- Ürün Listesi -->
                        <template x-if="recentProducts.length > 0">
                            <ul class="space-y-4">
                                <template x-for="(rp, idx) in recentProducts" :key="rp.id">
                                    <li class="flex items-center gap-3 group">
                                        <a :href="'/urun/' + rp.slug" class="shrink-0 w-14 h-14 rounded-xl overflow-hidden bg-gray-50 border border-black/[0.04]">
                                            <img :src="rp.image" :alt="rp.name" class="w-full h-full object-cover" loading="lazy">
                                        </a>
                                        <div class="flex-1 min-w-0">
                                            <a :href="'/urun/' + rp.slug" class="font-medium text-sm leading-tight line-clamp-2 hover:underline" x-text="rp.name"></a>
                                            <div class="text-sm mt-0.5" x-text="parseFloat(rp.price).toLocaleString('tr-TR', {minimumFractionDigits: 2}) + 'TL'"></div>
                                            <button @click="Livewire.find('{{ $_instance->getId() }}').call('quickAddToCart', rp.id)" class="inline-flex items-center gap-1 mt-1.5 px-3 py-1 bg-black text-white text-[11px] font-medium rounded-full hover:bg-black/80 transition-colors">
                                                <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 6H6M9.5 6H6M6 6V2.5M6 6V9.5"/></svg>
                                                Sepete Ekle
                                            </button>
                                        </div>
                                        <button @click="recentProducts.splice(idx, 1); localStorage.setItem('recently_viewed', JSON.stringify(recentProducts))" class="p-1.5 text-black/20 hover:text-red-400 transition-colors shrink-0 self-start" title="Kaldır">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <template x-teleport="body">
    <div x-show="notePanel" style="display: none; z-index: 10000;" class="fixed inset-0 cart-bottom-sheet" @keydown.escape.window.stop="notePanel = false">
        <style>@media(min-width:768px){.cart-bottom-sheet{left:auto!important;width:420px!important}}@media(min-width:1024px){.cart-bottom-sheet{width:460px!important}}</style>
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
    </template>

    <template x-teleport="body">
    <!-- Bottom Sheet: Kargo Tahmini -->
    <div x-show="shippingPanel" style="display: none; z-index: 10000;" class="fixed inset-0 cart-bottom-sheet" @keydown.escape.window.stop="shippingPanel = false"
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
                <span class="text-sm text-green-700 font-medium">Ücretsiz kargo · Tahmini {{ $deliveryEstimate }}</span>
            </div>
        </div>
    </div>
    </template>

    <template x-teleport="body">
    <!-- Bottom Sheet: İndirim Kodu -->
    <div x-show="discountPanel" style="display: none; z-index: 10000;" class="fixed inset-0 cart-bottom-sheet" x-data="{ code: '' }" @keydown.escape.window.stop="discountPanel = false">
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
    </template>
</div>

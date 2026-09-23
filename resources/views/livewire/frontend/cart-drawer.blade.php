<div 
    x-data="{ 
        open: false,
        activeTab: 'cart'
    }" 
    x-on:open-cart.window="open = true"
    x-on:toggle-cart.window="open = !open"
    @keydown.escape.window="open = false"
    x-cloak
    class="relative z-[65]" 
    aria-labelledby="cart-drawer-title" 
    role="dialog" 
    aria-modal="true"
    x-show="open"
    style="display: none;"
>
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-400" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-black/50 transition-opacity" 
         @click="open = false"></div>

    <!-- Bottom Sheet Container -->
    <div class="fixed inset-x-0 bottom-[76px] md:bottom-0 top-0 pointer-events-none flex items-end md:justify-end">
        <div x-show="open" 
             x-transition:enter="transform transition ease-out duration-500" 
             x-transition:enter-start="translate-y-full md:translate-y-0 md:translate-x-full" 
             x-transition:enter-end="translate-y-0 md:translate-x-0" 
             x-transition:leave="transform transition ease-in duration-400" 
             x-transition:leave-start="translate-y-0 md:translate-x-0" 
             x-transition:leave-end="translate-y-full md:translate-y-0 md:translate-x-full" 
             class="pointer-events-auto w-full md:max-w-md md:h-full"
             style="max-height: calc(100% - 60px);">

            <div class="flex flex-col h-full bg-white rounded-t-[20px] md:rounded-t-none md:rounded-l-[20px] overflow-hidden shadow-2xl">
                
                <!-- Drag Indicator (Mobil) -->
                <div class="flex justify-center pt-3 pb-1 md:hidden">
                    <div class="w-12 h-1 bg-gray-200 rounded-full"></div>
                </div>

                <!-- Header: Sekmeler -->
                <div class="flex items-center justify-between px-5 pt-4 pb-4 border-b border-gray-100/60">
                    <div class="flex items-center gap-8">
                        <!-- Sepet Sekmesi -->
                        <button @click="activeTab = 'cart'" 
                                :class="activeTab === 'cart' ? 'opacity-100' : 'opacity-20 hover:opacity-60'"
                                class="transition-opacity">
                            <span class="text-2xl font-bold text-gray-900 tracking-tight">Sepet</span>
                            @if($items->sum('quantity') > 0)
                                <sup class="text-xs font-medium text-gray-900 ml-0.5">{{ $items->sum('quantity') }}</sup>
                            @endif
                        </button>
                        <!-- İnceledikleriniz Sekmesi -->
                        <button @click="activeTab = 'recent'" 
                                :class="activeTab === 'recent' ? 'opacity-100' : 'opacity-20 hover:opacity-60'"
                                class="transition-opacity">
                            <span class="text-2xl font-bold text-gray-900 tracking-tight">İnceledikleriniz</span>
                        </button>
                    </div>
                    <!-- Kapatma Butonu (Desktop) -->
                    <button @click="open = false" class="hidden md:flex items-center justify-center w-9 h-9 rounded-full border border-gray-200 text-gray-400 hover:text-gray-900 hover:border-gray-400 transition-all">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15L15 5M5 5L15 15"/></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 flex flex-col overflow-hidden">

                    <!-- Sepet Paneli -->
                    <div x-show="activeTab === 'cart'" class="flex flex-col h-full">
                        
                        <!-- Scrollable: Ürünler -->
                        <div class="flex-1 overflow-y-auto px-5">
                            
                            @if(count($items) > 0)
                                <!-- Ürün Listesi -->
                                <ul class="divide-y divide-gray-100/80">
                                    @foreach($items as $item)
                                        <li class="flex items-start gap-4 py-5">
                                            <!-- Ürün Görseli -->
                                            <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate class="shrink-0 w-20 h-20 md:w-24 md:h-24 rounded-lg overflow-hidden bg-gray-50">
                                                <img src="{{ $item->product->images->first() ? $item->product->images->first()->image_url : asset('img/placeholder.svg') }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy">
                                            </a>

                                            <!-- Ürün Detayları -->
                                            <div class="flex-1 flex flex-col gap-1 min-w-0">
                                                <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate class="text-sm font-medium text-gray-900 leading-tight hover:underline line-clamp-2">{{ $item->product->name }}</a>
                                                @if($item->variant)
                                                    <span class="text-xs text-gray-400">{{ $item->variant->size }}</span>
                                                @endif
                                                <span class="text-sm text-gray-900 mt-1">{{ number_format($item->price * $item->quantity, 2) }}TL</span>
                                            </div>

                                            <!-- Miktar & Kaldır -->
                                            <div class="shrink-0 flex flex-col items-end justify-between h-full gap-3">
                                                <!-- Miktar Kutusu -->
                                                <div class="flex items-center border border-gray-200 rounded">
                                                    <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors lg:hidden">
                                                        <svg class="w-3 h-3" viewBox="0 0 8 6" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M0.5 1.25L4 4.75L7.5 1.25"/></svg>
                                                    </button>
                                                    <span class="w-8 h-8 flex items-center justify-center text-sm text-gray-900 border-x border-gray-200 lg:border-x-0">{{ $item->quantity }}</span>
                                                    @if($item->quantity >= ($item->maxStock ?? 999))
                                                        <span class="w-8 h-8 flex items-center justify-center text-gray-200 lg:hidden">
                                                            <svg class="w-3 h-3" viewBox="0 0 8 6" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M0.5 4.75L4 1.25L7.5 4.75"/></svg>
                                                        </span>
                                                    @else
                                                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors lg:hidden">
                                                            <svg class="w-3 h-3" viewBox="0 0 8 6" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M0.5 4.75L4 1.25L7.5 4.75"/></svg>
                                                        </button>
                                                    @endif
                                                </div>
                                                <!-- Kaldır -->
                                                <button wire:click="removeItem({{ $item->id }})" class="text-xs text-gray-400 hover:text-gray-900 transition-colors">Kaldır</button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <!-- Boş Sepet -->
                                <div class="flex flex-col items-center justify-center py-20 text-center">
                                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Sepetiniz Boş</h3>
                                    <p class="text-gray-500 max-w-[200px]">Alışverişe başlamak için ürünlerimize göz atın.</p>
                                    <button @click="open = false" class="mt-6 text-black font-semibold border-b-2 border-black pb-1 hover:text-gray-600 hover:border-gray-600 transition-colors">
                                        Alışverişe Devam Et
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        @if(count($items) > 0)
                            <div class="border-t border-gray-100/60">
                                
                                <!-- Footer İkonları: Sipariş Notu, Kargo, İndirim -->
                                <div class="flex border-b border-gray-100/60">
                                    <button class="flex-1 flex items-center justify-center gap-2 py-3 text-gray-600 hover:text-gray-900 transition-colors">
                                        <svg class="w-5 h-5 opacity-40" viewBox="0 0 25 24" fill="none" stroke="currentColor" stroke-width="1"><path class="fill-gray-100" d="M12.9 2H12.1C8.73969 2 7.05953 2 5.77606 2.65396C4.64708 3.2292 3.7292 4.14708 3.15396 5.27606C2.5 6.55953 2.5 8.23969 2.5 11.6V12.4C2.5 15.7603 2.5 17.4405 3.15396 18.7239C3.7292 19.8529 4.64708 20.7708 5.77606 21.346C7.05953 22 8.73969 22 12.1 22H13.5C14.4319 22 14.8978 22 15.2654 21.8478C15.7554 21.6448 16.1448 21.2554 16.3478 20.7654C16.5 20.3978 16.5 19.9319 16.5 19C16.5 18.0681 16.5 17.6021 16.6522 17.2346C16.8552 16.7445 17.2446 16.3552 17.7346 16.1522C18.1022 16 18.5681 16 19.5 16C20.4319 16 20.8978 16 21.2654 15.8477C21.7554 15.6447 22.1448 15.2554 22.3478 14.7653C22.5 14.3978 22.5 13.9319 22.5 13V11.6C22.5 8.23969 22.5 6.55953 21.846 5.27606C21.2708 4.14708 20.3529 3.2292 19.2239 2.65396C17.9405 2 16.2603 2 12.9 2Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 21.5V19.2C16.5 18.0799 16.5 17.5198 16.718 17.092C16.9097 16.7157 17.2157 16.4097 17.592 16.218C18.0198 16 18.5799 16 19.7 16H22M7.5 7H15.5M7.5 11H13.5M7.5 15H9.5M15.6716 22H12.1C8.73969 22 7.05953 22 5.77606 21.346C4.64708 20.7708 3.7292 19.8529 3.15396 18.7239C2.5 17.4405 2.5 15.7603 2.5 12.4V11.6C2.5 8.23969 2.5 6.55953 3.15396 5.27606C3.7292 4.14708 4.64708 3.2292 5.77606 2.65396C7.05953 2 8.73969 2 12.1 2H12.9C16.2603 2 17.9405 2 19.2239 2.65396C20.3529 3.2292 21.2708 4.14708 21.846 5.27606C22.5 6.55953 22.5 8.23969 22.5 11.6V15.1716C22.5 15.5088 22.5 15.6774 22.4912 15.8399C22.4171 17.2049 21.8791 18.5036 20.9663 19.5212C20.8577 19.6423 20.7385 19.7615 20.5 20C20.2615 20.2385 20.1423 20.3577 20.0212 20.4663C19.0036 21.3791 17.7049 21.9171 16.3399 21.9912C16.1774 22 16.0088 22 15.6716 22Z"/></svg>
                                        <span class="text-sm">Sipariş notu</span>
                                    </button>
                                    <button class="flex-1 flex items-center justify-center gap-2 py-3 text-gray-600 hover:text-gray-900 transition-colors border-x border-gray-100/60">
                                        <svg class="w-5 h-5 opacity-40" viewBox="0 0 25 24" fill="none" stroke="currentColor" stroke-width="1"><path class="fill-gray-100" d="M22.5 14.2825V7.28202C22.5 7.10718 22.4083 6.94515 22.2583 6.8552C22.0995 6.75991 21.9009 6.76067 21.7429 6.85718L15.0012 10.9731C14.0927 11.5278 13.6384 11.8052 13.1525 11.9135C12.7228 12.0092 12.2772 12.0092 11.8475 11.9135C11.3616 11.8052 10.9073 11.5278 9.99878 10.9731L3.25714 6.85718C3.09906 6.76067 2.90048 6.75991 2.74166 6.8552C2.59174 6.94515 2.5 7.10718 2.5 7.28202V14.2825C2.5 15.2735 2.5 15.769 2.64219 16.2143C2.76802 16.6083 2.97396 16.972 3.24708 17.2826C3.55572 17.6336 3.98062 17.8886 4.83042 18.3984L10.0304 21.5184C10.9283 22.0572 11.3773 22.3266 11.8565 22.4318C12.2805 22.5249 12.7195 22.5249 13.1435 22.4318C13.6227 22.3266 14.0717 22.0572 14.9696 21.5184L20.1696 18.3984C21.0194 17.8886 21.4443 17.6336 21.7529 17.2826C22.026 16.972 22.232 16.6083 22.3578 16.2143C22.5 15.769 22.5 15.2735 22.5 14.2825Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.99988 9.5L17 4M12.5 12.5L21.5 7M12.5 12.5L3.5 7M12.5 12.5V22.5M2.5 9.71771V14.2823C2.5 15.2733 2.5 15.7688 2.64219 16.2141C2.76802 16.6081 2.97396 16.9718 3.24708 17.2824C3.55572 17.6334 3.98062 17.8884 4.83042 18.3983L10.0304 21.5183C10.9283 22.057 11.3773 22.3264 11.8565 22.4316C12.2805 22.5247 12.7195 22.5247 13.1435 22.4316C13.6227 22.3264 14.0717 22.057 14.9696 21.5183L20.1696 18.3983C21.0194 17.8884 21.4443 17.6334 21.7529 17.2824C22.026 16.9718 22.232 16.6081 22.3578 16.2141C22.5 15.7688 22.5 15.2733 22.5 14.2823V9.71771C22.5 8.72669 22.5 8.23117 22.3578 7.78593C22.232 7.39192 22.026 7.02818 21.7529 6.71757C21.4443 6.36657 21.0194 6.11163 20.1696 5.60175L14.9696 2.48175C14.0717 1.94301 13.6227 1.67364 13.1435 1.56839C12.7195 1.4753 12.2805 1.4753 11.8565 1.56839C11.3773 1.67364 10.9283 1.94301 10.0304 2.48175L4.83042 5.60175C3.98062 6.11163 3.55572 6.36657 3.24708 6.71757C2.97396 7.02818 2.76802 7.39192 2.64219 7.78593C2.5 8.23117 2.5 8.72669 2.5 9.71771Z"/></svg>
                                        <span class="text-sm">Kargo</span>
                                    </button>
                                    <button class="flex-1 flex items-center justify-center gap-2 py-3 text-gray-600 hover:text-gray-900 transition-colors">
                                        <svg class="w-5 h-5 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path class="fill-gray-100" d="M7.24012 4.02997H7.09012C6.01012 4.02997 5.47012 4.02997 5.06012 4.23997C4.70012 4.41997 4.40012 4.71997 4.22012 5.07997C4.01012 5.48997 4.01012 6.02997 4.01012 7.10997V7.22997C4.01012 7.64997 4.01012 7.85997 3.97012 8.05997C3.93012 8.23997 3.87012 8.40997 3.78012 8.56997C3.68012 8.74997 3.55012 8.90997 3.28012 9.22997L2.84012 9.74997C2.24012 10.46 1.94012 10.81 1.82012 11.21C1.72012 11.56 1.72012 11.93 1.82012 12.28C1.93012 12.68 2.23012 13.03 2.84012 13.74L3.28012 14.26C3.55012 14.58 3.69012 14.74 3.78012 14.92C3.87012 15.08 3.93012 15.25 3.97012 15.43C4.01012 15.63 4.01012 15.84 4.01012 16.26V16.38C4.01012 17.46 4.01012 18 4.22012 18.41C4.40012 18.77 4.70012 19.07 5.06012 19.25C5.47012 19.46 6.01012 19.46 7.09012 19.46H7.24012C7.66012 19.46 7.87012 19.46 8.07012 19.5C8.25012 19.54 8.42012 19.6 8.58012 19.69C8.76012 19.79 8.92012 19.93 9.24012 20.2L9.74012 20.63C10.4501 21.24 10.8101 21.55 11.2101 21.66C11.5601 21.76 11.9401 21.76 12.2901 21.66C12.6901 21.54 13.0501 21.24 13.7601 20.63L14.2601 20.2C14.5801 19.93 14.7401 19.79 14.9201 19.69C15.0801 19.6 15.2501 19.54 15.4301 19.5C15.6301 19.46 15.8401 19.46 16.2601 19.46H16.3701C17.4501 19.46 17.9901 19.46 18.4001 19.25C18.7601 19.07 19.0601 18.77 19.2401 18.41C19.4501 18 19.4501 17.46 19.4501 16.38V16.27C19.4501 15.85 19.4501 15.64 19.4901 15.44C19.5301 15.26 19.5901 15.09 19.6801 14.93C19.7801 14.75 19.9201 14.59 20.1901 14.27L20.6201 13.77C21.2301 13.06 21.5401 12.7 21.6501 12.3C21.7501 11.95 21.7501 11.57 21.6501 11.22C21.5301 10.82 21.2301 10.46 20.6201 9.74997L20.1901 9.24997C19.9201 8.92997 19.7801 8.76997 19.6801 8.58997C19.5901 8.42997 19.5301 8.25997 19.4901 8.07997C19.4501 7.87997 19.4501 7.66997 19.4501 7.24997V7.13997C19.4501 6.05997 19.4501 5.51997 19.2401 5.10997C19.0601 4.74997 18.7601 4.44997 18.4001 4.26997C17.9901 4.05997 17.4501 4.05997 16.3701 4.05997H16.2601C15.8401 4.05997 15.6301 4.05997 15.4301 4.01997C15.2501 3.97997 15.0801 3.91997 14.9201 3.82997C14.7401 3.72997 14.5801 3.58997 14.2601 3.31997L13.7601 2.88997C13.0501 2.27997 12.6901 1.96997 12.2901 1.85997C11.9401 1.75997 11.5601 1.75997 11.2101 1.85997C10.8101 1.97997 10.4501 2.27997 9.74012 2.88997L9.24012 3.31997C8.92012 3.58997 8.76012 3.72997 8.58012 3.82997C8.42012 3.91997 8.25012 3.97997 8.07012 4.01997C7.87012 4.05997 7.66012 4.05997 7.24012 4.05997V4.02997Z"/><path stroke-linecap="round" d="M8.38012 15.36L11.7501 11.74L15.1201 8.11997M14.9201 14.59C14.9201 14.81 14.7401 14.99 14.5201 14.99C14.3001 14.99 14.1201 14.81 14.1201 14.59M14.9201 14.59C14.9201 14.37 14.7401 14.19 14.5201 14.19C14.3001 14.19 14.1201 14.37 14.1201 14.59M14.9201 14.59H14.1201M9.42012 9.00997C9.42012 9.22997 9.24012 9.40997 9.02012 9.40997C8.80012 9.40997 8.62012 9.22997 8.62012 9.00997M9.42012 9.00997C9.42012 8.78997 9.24012 8.60997 9.02012 8.60997C8.80012 8.60997 8.62012 8.78997 8.62012 9.00997M9.42012 9.00997H8.62012"/></svg>
                                        <span class="text-sm">İndirim</span>
                                    </button>
                                </div>

                                <!-- Ara Toplam & Butonlar -->
                                <div class="bg-gray-50/50 px-5 py-5">
                                    <!-- Ara Toplam -->
                                    <div class="flex items-end justify-between mb-5">
                                        <p class="text-sm text-gray-500 max-w-[180px] leading-snug">Vergi dahildir ve gönderim bedeli ödeme sırasında hesaplanır</p>
                                        <div class="text-right">
                                            <span class="text-sm text-gray-500 block">ara toplam</span>
                                            <span class="text-xl font-bold text-gray-900 tracking-tight">{{ number_format($total, 2) }}TL</span>
                                        </div>
                                    </div>

                                    <!-- Havale İndirimi Butonu -->
                                    <a href="{{ route('checkout') }}" @click="open = false" wire:navigate
                                       class="flex items-center justify-center w-full py-3.5 bg-black text-white text-sm font-medium rounded-full mb-3 hover:bg-gray-800 transition-colors">
                                        % 5 Havale indirimi
                                    </a>

                                    <!-- Ödeme Ekranı Butonu -->
                                    <a href="{{ route('checkout') }}" @click="open = false" wire:navigate
                                       class="flex items-center justify-center w-full py-3.5 bg-black text-white text-sm font-medium rounded-full hover:bg-gray-800 transition-colors">
                                        Ödeme ekranı
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- İnceledikleriniz Paneli -->
                    <div x-show="activeTab === 'recent'" class="flex-1 overflow-y-auto px-5 py-8">
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <svg class="w-12 h-12 text-gray-200 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-gray-400 text-sm">Henüz incelediğiniz ürün yok</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

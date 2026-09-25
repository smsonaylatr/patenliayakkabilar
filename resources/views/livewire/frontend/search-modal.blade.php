<div 
    x-data="{ 
        open: false,
        closeSearch() {
            this.open = false;
        }
    }" 
    @open-search.window="open = true; $nextTick(() => { setTimeout(() => $refs.searchInput && $refs.searchInput.focus(), 80); })"
    @keydown.escape.window="closeSearch()"
    x-init="$watch('open', value => {
        if (value) document.body.classList.add('overflow-hidden');
        else document.body.classList.remove('overflow-hidden');
    })"
    class="relative z-[10000]" 
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
    style="display: none;"
    x-show="open"
    x-cloak
>
    <!-- Backdrop (Hafif ve GPU dostu karartma) -->
    <div 
        x-show="open" 
        x-transition:enter="transition-opacity duration-300 ease-out" 
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100" 
        x-transition:leave="transition-opacity duration-200 ease-in" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0" 
        class="fixed inset-0 bg-black/50"
        @click="closeSearch()"
    ></div>

    <!-- Modal Container -->
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto pt-6 sm:pt-20 px-3 sm:px-4 flex items-start justify-center pointer-events-none">
        
        <!-- Spotlight / Modern Search Panel -->
        <div 
            x-show="open" 
            x-transition:enter="transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]" 
            x-transition:enter-start="opacity-0 -translate-y-4 scale-[0.96]" 
            x-transition:enter-end="opacity-100 translate-y-0 scale-100" 
            x-transition:leave="transition-all duration-200 ease-in" 
            x-transition:leave-start="opacity-100 translate-y-0 scale-100" 
            x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.98]" 
            class="pointer-events-auto relative w-full max-w-2xl bg-white rounded-2xl sm:rounded-3xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.25)] border border-gray-100/80 overflow-hidden"
            @click.away="closeSearch()"
            style="will-change: transform, opacity; transform: translateZ(0); backface-visibility: hidden;"
        >
            <div class="p-3 sm:p-4">
                <!-- Search Input Bar -->
                <div class="relative flex items-center">
                    <div class="pointer-events-none absolute left-4 flex items-center justify-center text-gray-400">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        x-ref="searchInput"
                        type="text" 
                        aria-label="Arama Kutusu"
                        class="h-13 sm:h-15 w-full rounded-xl sm:rounded-2xl border-0 bg-gray-50/90 pl-11 sm:pl-13 pr-12 text-gray-900 placeholder:text-gray-400 focus:bg-white focus:ring-2 focus:ring-black text-sm sm:text-base transition-all duration-200" 
                        placeholder="Ürün, kategori veya özellik arayın..."
                    >
                    
                    <!-- Loading Indicator -->
                    <div wire:loading wire:target="search" class="absolute right-12 sm:right-13">
                        <div class="h-4 w-4 sm:h-5 sm:w-5 animate-spin rounded-full border-2 border-gray-300 border-t-black"></div>
                    </div>
                    
                    <!-- Close Button -->
                    <button 
                        @click="closeSearch()" 
                        aria-label="Aramayı Kapat" 
                        class="absolute right-2 text-gray-400 hover:text-black hover:bg-gray-100 rounded-full w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center transition-all duration-200 active:scale-90"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Search Results -->
                @if(strlen($search) >= 2)
                    <div class="mt-4 max-h-[60vh] overflow-y-auto overscroll-contain px-1">
                        @if($results->count() > 0)
                            <div class="flex items-center justify-between mb-3 px-2">
                                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Ürünler ({{ $results->count() }})</h3>
                                <span class="text-[10px] text-gray-400 font-medium">Sonuçlar anlık listeleniyor</span>
                            </div>
                            <ul class="space-y-1.5">
                                @foreach($results as $product)
                                    <li>
                                        <a href="{{ route('products.show', $product->slug) }}" @click="closeSearch()" wire:navigate class="flex items-center gap-3.5 sm:gap-4 rounded-xl p-2 hover:bg-gray-50/90 active:bg-gray-100 transition-colors group">
                                            <div class="h-14 w-14 sm:h-16 sm:w-16 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100 border border-gray-100">
                                                <img src="{{ $product->images->first() ? $product->images->first()->image_url : asset('img/placeholder.svg') }}" alt="{{ $product->name }}" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-medium text-gray-900 truncate group-hover:text-black">{{ $product->name }}</h4>
                                                <div class="mt-1 flex items-center gap-2">
                                                    @php
                                                        $displayPrice = $product->price;
                                                        $displayDiscount = $product->discount_price;
                                                        if ($displayDiscount && $displayPrice && $displayDiscount > $displayPrice) {
                                                            $displayPrice = $product->discount_price;
                                                            $displayDiscount = $product->price;
                                                        }
                                                    @endphp
                                                    @if($displayDiscount)
                                                        <span class="text-sm font-bold text-brand-orange">{{ number_format($displayDiscount, 2) }} ₺</span>
                                                        <span class="text-xs text-gray-400 line-through">{{ number_format($displayPrice, 2) }} ₺</span>
                                                    @else
                                                        <span class="text-sm font-bold text-gray-900">{{ number_format($displayPrice, 2) }} ₺</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-gray-300 group-hover:text-black group-hover:translate-x-0.5 transition-all duration-200 pr-1">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="px-4 py-10 text-center">
                                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-700 font-medium">"<strong>{{ $search }}</strong>" ile ilgili ürün bulunamadı</p>
                                <p class="text-xs text-gray-400 mt-1">Farklı bir anahtar kelime veya model adı deneyebilirsiniz.</p>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Suggestions Area (Boş Arama Durumu) -->
                    <div class="mt-5 px-2 pb-2 text-left">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Popüler Aramalar</h3>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button wire:click="$set('search', 'Işıklı')" class="px-3.5 py-1.5 bg-gray-50 hover:bg-black hover:text-white text-gray-700 text-xs sm:text-sm rounded-full font-medium transition-all duration-200 border border-gray-100 active:scale-95">Işıklı</button>
                            <button wire:click="$set('search', 'Tekerlekli')" class="px-3.5 py-1.5 bg-gray-50 hover:bg-black hover:text-white text-gray-700 text-xs sm:text-sm rounded-full font-medium transition-all duration-200 border border-gray-100 active:scale-95">Tekerlekli</button>
                            <button wire:click="$set('search', 'Kız Çocuk')" class="px-3.5 py-1.5 bg-gray-50 hover:bg-black hover:text-white text-gray-700 text-xs sm:text-sm rounded-full font-medium transition-all duration-200 border border-gray-100 active:scale-95">Kız Çocuk</button>
                            <button wire:click="$set('search', 'Erkek Çocuk')" class="px-3.5 py-1.5 bg-gray-50 hover:bg-black hover:text-white text-gray-700 text-xs sm:text-sm rounded-full font-medium transition-all duration-200 border border-gray-100 active:scale-95">Erkek Çocuk</button>
                            <button wire:click="$set('search', '4 Tekerlekli')" class="px-3.5 py-1.5 bg-gray-50 hover:bg-black hover:text-white text-gray-700 text-xs sm:text-sm rounded-full font-medium transition-all duration-200 border border-gray-100 active:scale-95">4 Tekerlekli</button>
                        </div>
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                            <span>Aramayı kapatmak için <kbd class="px-1.5 py-0.5 bg-gray-100 rounded text-[10px] font-mono text-gray-600">ESC</kbd></span>
                            <span>Patenli Ayakkabılar&reg;</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

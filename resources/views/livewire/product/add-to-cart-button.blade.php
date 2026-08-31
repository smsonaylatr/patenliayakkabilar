<div class="w-full flex flex-col gap-6" x-data="{ qty: @entangle('quantity') }">
    @php
        $isOutOfStock = !$product->inStock();
    @endphp

    @if(!$isOutOfStock)
    <!-- Adet Seçici Kutusu (Shopier Style) -->
    <div class="flex items-center justify-between border border-gray-200 rounded-full h-14 px-5 bg-white">
        <span class="text-base font-medium text-gray-900">Adet</span>
        <div class="flex items-center gap-1">
            <button type="button" @click="qty > 1 ? qty-- : null" aria-label="Adedi Azalt" class="text-gray-500 bg-gray-50 hover:bg-gray-100 rounded-full focus:outline-none w-9 h-9 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-minus text-[10px]"></i>
            </button>
            <span class="text-base font-medium text-gray-900 w-10 text-center" x-text="qty"></span>
            <button type="button" @click="qty++" aria-label="Adedi Artır" class="text-gray-500 bg-gray-50 hover:bg-gray-100 rounded-full focus:outline-none w-9 h-9 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-plus text-[10px]"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Sepete Ekle / Tükendi Butonu -->
    <button 
        @if(!$isOutOfStock) wire:click="addToCart" @endif 
        wire:loading.attr="disabled" 
        type="button" 
        @if($isOutOfStock) disabled @endif
        class="group relative flex w-full h-14 items-center justify-center gap-3 overflow-hidden rounded-full {{ $isOutOfStock ? 'bg-gray-200 text-gray-500 cursor-not-allowed shadow-none' : 'bg-gray-900 text-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] hover:scale-[1.02] hover:bg-black hover:shadow-[0_8px_30px_rgb(0,0,0,0.2)]' }} px-4 sm:px-8 text-base font-bold transition-all duration-300 disabled:cursor-not-allowed disabled:opacity-70">
        
        @if(!$isOutOfStock)
        <!-- Shine effect on hover -->
        <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-100%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(100%)]">
            <div class="relative h-full w-8 bg-white/20"></div>
        </div>
        @endif

        <span class="flex items-center gap-2">
            @if($isOutOfStock)
                <i class="fa-solid fa-ban text-sm"></i>
                Tükendi
            @else
                <svg wire:loading.remove wire:target="addToCart" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                
                <svg wire:loading wire:target="addToCart" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                Sepete Ekle
            @endif
        </span>
    </button>

    @if($isOutOfStock)
    <!-- Gelince Haber Ver Butonu -->
    <button 
        @click="$dispatch('open-stock-modal', { productId: {{ $product->id }}, variantId: '{{ $variantId }}' })"
        type="button" 
        class="w-full h-14 flex items-center justify-center gap-2.5 rounded-full bg-amber-500 hover:bg-amber-600 text-white font-bold text-base shadow-[0_8px_25px_rgba(245,158,11,0.25)] transition-all duration-300 hover:scale-[1.02] cursor-pointer">
        <i class="fa-solid fa-bell"></i>
        <span>Gelince Haber Ver</span>
    </button>
    @endif
</div>

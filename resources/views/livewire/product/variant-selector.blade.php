<div class="mt-8" x-data="{ selectedId: @entangle('selectedVariantId').live }">
    @php
        $allOutOfStock = !$product->inStock() || $product->variants->every(fn($v) => !$product->status || $v->stock <= 0);
    @endphp

    <div class="relative" x-data="{ 
        open: false,
        variants: [
            @foreach($product->variants as $variant)
                { id: {{ $variant->id }}, size: '{{ addslashes($variant->size) }}', stock: {{ $product->status ? $variant->stock : 0 }} }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ],
        get selectedSize() {
            if (!this.selectedId) {
                return '{{ $allOutOfStock ? "Beden (Tüm Stoklar Tükenmiştir)" : "Beden" }}';
            }
            let v = this.variants.find(v => v.id == this.selectedId);
            return v ? v.size : 'Beden';
        }
    }" @click.away="open = false" @open-variant-selector.window="open = true; setTimeout(() => $el.scrollIntoView({behavior: 'smooth', block: 'center'}), 100)">
        
        <!-- Dropdown Butonu -->
        <button 
            type="button"
            @click="open = !open"
            class="flex items-center justify-between w-full h-14 rounded-full border border-gray-200 bg-white px-5 text-base font-medium focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 sm:text-sm transition-colors cursor-pointer"
            :class="open ? 'border-gray-900 ring-1 ring-gray-900' : ''"
        >
            <span x-text="selectedSize" class="{{ $allOutOfStock ? 'text-gray-400 font-medium' : 'text-gray-900' }}"></span>
            <svg class="w-5 h-5 text-gray-800 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown Menü İçeriği -->
        <ul 
            x-show="open" 
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="mt-2 w-full rounded-2xl bg-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 py-2 focus:outline-none max-h-72 overflow-y-auto z-30 relative"
            style="display: none;"
        >
            <template x-for="variant in variants" :key="variant.id">
                <li>
                    <button 
                        type="button"
                        @click="if(variant.stock > 0) { selectedId = variant.id; open = false; }"
                        class="w-full flex items-center justify-between px-5 py-3 text-base font-medium transition-colors border-b border-gray-50 last:border-b-0"
                        :class="{
                            'text-gray-900 hover:bg-gray-50 cursor-pointer': variant.stock > 0,
                            'text-gray-400 bg-gray-50/40 cursor-not-allowed': variant.stock <= 0,
                            'bg-gray-100 font-bold': selectedId == variant.id
                        }"
                        :disabled="variant.stock <= 0"
                    >
                        <span x-text="variant.size" :class="{ 'line-through text-gray-400': variant.stock <= 0 }"></span>
                        
                        <template x-if="variant.stock > 0">
                            <span 
                                class="text-xs font-semibold px-2.5 py-1 rounded-md border transition-colors"
                                :class="variant.stock <= 3 
                                    ? 'text-amber-700 bg-amber-50 border-amber-200/80' 
                                    : 'text-emerald-700 bg-emerald-50 border-emerald-100/60'"
                                x-text="variant.stock <= 3 ? ('Son ' + variant.stock + ' Adet') : (variant.stock + ' Adet Kaldı')"
                            ></span>
                        </template>
                        <template x-if="variant.stock <= 0">
                            <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200/80">Tükendi</span>
                        </template>
                    </button>
                </li>
            </template>
        </ul>
    </div>
</div>

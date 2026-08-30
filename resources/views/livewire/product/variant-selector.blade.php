<div class="mt-6" x-data="{ 
    selectedId: @entangle('selectedVariantId').live,
    hasError: false,
    variants: [
        @foreach($product->variants as $variant)
            { 
                id: {{ $variant->id }}, 
                size: '{{ addslashes($variant->size) }}', 
                stock: {{ $product->status ? $variant->stock : 0 }} 
            }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ],
    get selectedVariant() {
        return this.variants.find(v => v.id == this.selectedId) || null;
    },
    selectVariant(v) {
        if (v.stock > 0) {
            this.selectedId = v.id;
            this.hasError = false;
        } else {
            $dispatch('open-stock-modal', { productId: {{ $product->id }}, variantId: v.id });
        }
    }
}" 
@open-variant-selector.window="hasError = true; setTimeout(() => $el.scrollIntoView({behavior: 'smooth', block: 'center'}), 100); setTimeout(() => hasError = false, 3000)"
>
    {{-- Başlık ve Canlı Stok Durumu --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-gray-900">Beden Seçimi</span>
            <span class="text-xs text-gray-400 font-normal">(Ayakkabı Numarası)</span>
        </div>

        {{-- Dinamik Rozet --}}
        <div>
            <template x-if="selectedVariant && selectedVariant.stock > 3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Stokta Var</span>
                </span>
            </template>

            <template x-if="selectedVariant && selectedVariant.stock > 0 && selectedVariant.stock <= 3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs animate-pulse">
                    <i class="fa-solid fa-fire text-amber-500 text-[11px]"></i>
                    <span>Son <span x-text="selectedVariant.stock"></span> Adet!</span>
                </span>
            </template>

            <template x-if="!selectedVariant">
                <span class="text-xs font-medium text-gray-400 flex items-center gap-1.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Numaranızı seçin
                </span>
            </template>
        </div>
    </div>

    {{-- Beden Kutucukları Grid --}}
    <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-2 sm:gap-2.5"
         :class="{ 'ring-2 ring-red-400/80 ring-offset-2 rounded-2xl p-1 bg-red-50/30': hasError }">
        <template x-for="variant in variants" :key="variant.id">
            <button 
                type="button"
                @click="selectVariant(variant)"
                class="group relative flex flex-col items-center justify-center py-2.5 px-2 rounded-xl border transition-all duration-200 select-none text-center"
                :class="{
                    'bg-gray-900 text-white border-gray-900 shadow-md ring-2 ring-gray-900/20 scale-[1.02]': selectedId == variant.id,
                    'bg-white text-gray-900 border-gray-200 hover:border-gray-900 hover:shadow-xs cursor-pointer': variant.stock > 0 && selectedId != variant.id,
                    'bg-gray-50 text-gray-400 border-gray-200/60 cursor-pointer hover:border-amber-300 hover:bg-amber-50/30': variant.stock <= 0
                }"
            >
                {{-- Beden Numarası --}}
                <span class="text-base sm:text-lg font-bold tracking-tight leading-none" x-text="variant.size"></span>

                {{-- Durum Alt Yazısı --}}
                <template x-if="variant.stock > 3">
                    <span class="mt-1 text-[10px] font-semibold transition-colors leading-none"
                          :class="selectedId == variant.id ? 'text-emerald-300 font-bold' : 'text-emerald-600'">
                        Stokta
                    </span>
                </template>

                <template x-if="variant.stock > 0 && variant.stock <= 3">
                    <span class="mt-1 text-[10px] font-extrabold transition-colors leading-none"
                          :class="selectedId == variant.id ? 'text-amber-300' : 'text-amber-600'"
                          x-text="'Son ' + variant.stock">
                    </span>
                </template>

                <template x-if="variant.stock <= 0">
                    <span class="mt-1 text-[9px] font-medium text-gray-400 leading-none">
                        Tükendi
                    </span>
                </template>

                {{-- Tükendi için Çapraz Çizgi --}}
                <template x-if="variant.stock <= 0">
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center overflow-hidden rounded-xl">
                        <div class="w-[140%] h-[1px] bg-gray-300 -rotate-25"></div>
                    </div>
                </template>
            </button>
        </template>
    </div>

    {{-- Seçim / Hata Bilgilendirme Çubuğu --}}
    <div class="mt-3">
        <template x-if="hasError && !selectedVariant">
            <div class="flex items-center gap-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 px-3.5 py-2.5 rounded-xl shadow-2xs">
                <i class="fa-solid fa-circle-exclamation text-sm"></i>
                <span>Lütfen sepete eklemek için yukarıdan bir ayakkabı numarası seçin.</span>
            </div>
        </template>

        <template x-if="!hasError && selectedVariant">
            <div class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-100 text-xs">
                <div class="flex items-center gap-2 text-gray-800">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>Seçilen: <strong class="font-bold text-gray-900" x-text="selectedVariant.size + ' Numara'"></strong></span>
                </div>
                <div class="text-gray-500 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-truck-fast text-emerald-600"></i>
                    <span>Aynı gün kargoya hazır</span>
                </div>
            </div>
        </template>
    </div>
</div>

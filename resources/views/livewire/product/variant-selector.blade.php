<div class="mt-8" x-data="{ selectedId: @entangle('selectedVariantId').live }">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-900 tracking-wide">Beden Seçimi</h2>
        <a href="#" class="text-sm text-gray-500 hover:text-gray-900 underline decoration-gray-300 hover:decoration-gray-900 transition-colors">Beden Tablosu</a>
    </div>

    <div class="flex flex-wrap gap-3">
        @foreach($product->variants as $variant)
            <button 
                @click="selectedId = {{ $variant->id }}"
                type="button" 
                class="flex items-center justify-center border-2 py-2.5 px-6 rounded-full text-sm font-medium transition-all duration-150 {{ $variant->stock <= 0 ? 'opacity-40 cursor-not-allowed line-through' : '' }}"
                :class="selectedId == {{ $variant->id }} 
                    ? 'border-gray-900 bg-gray-900 text-white shadow-md transform scale-105' 
                    : 'border-gray-200 bg-white text-gray-600 hover:border-gray-400 hover:bg-gray-50 hover:text-gray-900 hover:shadow-sm'"
                {{ $variant->stock <= 0 ? 'disabled' : '' }}
            >
                {{ $variant->size }}
            </button>
        @endforeach
    </div>
</div>

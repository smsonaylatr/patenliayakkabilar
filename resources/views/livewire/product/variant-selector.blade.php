<div class="mt-8" x-data="{ selectedId: @entangle('selectedVariantId').live }">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-900 tracking-wide">Beden Seçimi</h2>
        <a href="#" class="text-sm text-gray-500 hover:text-gray-900 underline decoration-gray-300 hover:decoration-gray-900 transition-colors">Beden Tablosu</a>
    </div>

    <div class="relative mt-2">
        <select 
            x-model="selectedId" 
            class="block w-full appearance-none rounded-xl border border-gray-300 bg-white py-3.5 pl-4 pr-10 text-base font-medium text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 sm:text-sm transition-colors cursor-pointer"
        >
            @foreach($product->variants as $variant)
                <option value="{{ $variant->id }}" {{ $variant->stock <= 0 ? 'disabled' : '' }}>
                    {{ $variant->size }} Numarası {{ $variant->stock <= 0 ? '- Stokta Yok' : '' }}
                </option>
            @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
        </div>
    </div>
</div>

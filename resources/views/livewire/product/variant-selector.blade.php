<div class="mt-8" x-data="{ selectedId: @entangle('selectedVariantId').live }">
    <div class="relative">
        <select 
            x-model="selectedId" 
            class="block w-full h-14 rounded-full border border-gray-200 bg-white px-5 pr-10 text-base font-medium text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 sm:text-sm transition-colors cursor-pointer"
        >
            <option value="">Beden</option>
            @foreach($product->variants as $variant)
                <option value="{{ $variant->id }}" {{ $variant->stock <= 0 ? 'disabled' : '' }}>
                    {{ $variant->size }} {{ $variant->stock <= 0 ? '(Stokta Yok)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
</div>

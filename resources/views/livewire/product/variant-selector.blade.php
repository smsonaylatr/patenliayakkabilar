<div class="mt-8" x-data="{ selectedId: @entangle('selectedVariantId').live }">
    <div class="relative">
        <select 
            x-model="selectedId" 
            class="block w-full h-14 appearance-none rounded-full border border-gray-200 bg-white px-5 pr-10 text-base font-medium text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 sm:text-sm transition-colors cursor-pointer"
        >
            <option value="">Beden</option>
            @foreach($product->variants as $variant)
                <option value="{{ $variant->id }}" {{ $variant->stock <= 0 ? 'disabled' : '' }}>
                    {{ $variant->size }} {{ $variant->stock <= 0 ? '(Stokta Yok)' : '' }}
                </option>
            @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-5">
            <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
    </div>
</div>

@php
    $productInStock = (bool) $product->inStock();
    $hasVariants = $product->variants->count() > 0;
    $singleAutoVariant = ($product->requires_size === false && $product->variants->count() === 1) ? $product->variants->first() : null;
    $initialVariantId = $singleAutoVariant ? $singleAutoVariant->id : ($variantId ?: '');
    $initialVariantLabel = '';
    if ($singleAutoVariant) {
        $initialVariantLabel = $singleAutoVariant->size ?: (is_array($singleAutoVariant->color) ? implode(' / ', $singleAutoVariant->color) : ($singleAutoVariant->color ?? ''));
    }
    $productPrice = (float) ($product->discount_price ?? $product->price);
    $productImage = $product->images->first() ? $product->images->first()->image_url : asset('img/placeholder.svg');
    $baseGtagItem = \App\Services\GoogleTagService::formatProductItem($product);
@endphp

<div class="w-full flex flex-col gap-6"
     x-data="{
         qty: {{ (int) $quantity }},
         maxStock: {{ (int) $maxStock }},
         variantId: '{{ $initialVariantId }}',
         variantLabel: @js($initialVariantLabel),
         productInStock: {{ $productInStock ? 'true' : 'false' }},
         hasVariants: {{ $hasVariants ? 'true' : 'false' }},
         productData: {
             id: {{ $product->id }},
             name: @js($product->name),
             slug: @js($product->slug),
             url: @js(route('products.show', $product->slug)),
             image: @js($productImage),
             price: {{ $productPrice }},
             delivery_time: @js($product->delivery_time ?: '1-3 iş günü'),
             gtag_item: @js($baseGtagItem)
         },
         get isOutOfStock() {
             return !this.productInStock || (Boolean(this.variantId) && this.maxStock <= 0);
         },
         onVariantSelected(detail) {
             if (!detail || (detail.productId && detail.productId != this.productData.id)) return;
             this.variantId = detail.variantId ? String(detail.variantId) : '';
             this.variantLabel = detail.label || detail.size || detail.color || '';
             this.maxStock = Math.max(0, parseInt(detail.stock ?? 0, 10));
             if (this.qty > this.maxStock) {
                 this.qty = Math.max(1, this.maxStock);
             }
         },
         handleAddToCart() {
             if (this.isOutOfStock) {
                 window.dispatchEvent(new CustomEvent('show-notification', {
                     detail: { type: 'warning', message: 'Seçili ürün/beden stokta bulunmamaktadır.' }
                 }));
                 return;
             }

             if (this.hasVariants && !this.variantId) {
                 window.dispatchEvent(new CustomEvent('open-variant-selector'));
                 return;
             }

             const addQty = Math.max(1, Math.min(this.maxStock || 1, this.qty));

             window.dispatchEvent(new CustomEvent('cart:optimistic-add', {
                 detail: {
                     product_id: this.productData.id,
                     variant_id: this.variantId ? parseInt(this.variantId, 10) : null,
                     name: this.productData.name,
                     slug: this.productData.slug,
                     url: this.productData.url,
                     image: this.productData.image,
                     price: this.productData.price,
                     variant_label: this.variantLabel,
                     quantity: addQty,
                     max_stock: this.maxStock,
                     delivery_time: this.productData.delivery_time,
                     gtag_item: this.productData.gtag_item
                 }
             }));
         }
     }"
     @client-variant-selected.window="onVariantSelected($event.detail)">

    <!-- Adet Seçici Kutusu (Shopier Style) -->
    <div x-show="!isOutOfStock" class="flex items-center justify-between border border-gray-200 rounded-full h-12 sm:h-14 px-4 sm:px-5 bg-white">
        <span class="text-sm sm:text-base font-medium text-gray-900">Adet</span>
        <div class="flex items-center gap-1">
            <button type="button" @click="qty > 1 ? qty-- : null" aria-label="Adedi Azalt" class="text-gray-500 bg-gray-50 hover:bg-gray-100 rounded-full focus:outline-none w-11 h-11 sm:w-9 sm:h-9 flex items-center justify-center transition-colors cursor-pointer">
                <i class="fa-solid fa-minus text-[10px]"></i>
            </button>
            <span class="text-base font-medium text-gray-900 w-10 text-center" x-text="qty"></span>
            <button type="button" @click="qty < maxStock ? qty++ : null" :disabled="qty >= maxStock" aria-label="Adedi Artır" :class="qty >= maxStock ? 'text-gray-300 bg-gray-50 cursor-not-allowed' : 'text-gray-500 bg-gray-50 hover:bg-gray-100 cursor-pointer'" class="rounded-full focus:outline-none w-11 h-11 sm:w-9 sm:h-9 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-plus text-[10px]"></i>
            </button>
        </div>
    </div>

    <!-- Sepete Ekle / Tükendi Butonu -->
    <button 
        @click="handleAddToCart()"
        type="button" 
        :disabled="isOutOfStock"
        :class="isOutOfStock ? 'bg-gray-200 text-gray-500 cursor-not-allowed shadow-none' : 'bg-gray-900 text-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] hover:scale-[1.02] hover:bg-black hover:shadow-[0_8px_30px_rgb(0,0,0,0.2)] cursor-pointer'"
        class="group relative flex w-full h-12 sm:h-14 items-center justify-center gap-3 overflow-hidden rounded-full px-4 sm:px-8 text-sm sm:text-base font-bold transition-all duration-300 disabled:cursor-not-allowed disabled:opacity-70">
        
        <!-- Shine effect on hover -->
        <div x-show="!isOutOfStock" class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-100%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(100%)]">
            <div class="relative h-full w-8 bg-white/20"></div>
        </div>

        <span class="flex items-center gap-2">
            <template x-if="isOutOfStock">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-ban text-sm"></i>
                    <span x-text="(variantId && maxStock <= 0) ? 'Bu Beden Tükendi' : 'Tükendi'"></span>
                </span>
            </template>
            <template x-if="!isOutOfStock">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>Sepete Ekle</span>
                </span>
            </template>
        </span>
    </button>

    <!-- Gelince Haber Ver Butonu -->
    <button 
        x-show="isOutOfStock"
        x-cloak
        @click="$dispatch('open-stock-modal', { productId: productData.id, variantId: variantId })"
        type="button" 
        class="w-full h-12 sm:h-14 flex items-center justify-center gap-2.5 rounded-full bg-neutral-950 hover:bg-black text-white font-bold text-sm sm:text-base border border-neutral-800 shadow-[0_8px_25px_rgba(0,0,0,0.25)] transition-all duration-300 hover:scale-[1.02] cursor-pointer">
        <i class="fa-solid fa-bell"></i>
        <span x-text="(variantId && maxStock <= 0) ? 'Bu Beden İçin Gelince Haber Ver' : 'Gelince Haber Ver'"></span>
    </button>
</div>

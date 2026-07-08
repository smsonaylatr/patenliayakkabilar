{{-- Product Detail Page --}}
<x-layouts.app>
    <x-slot:title>{{ $product->meta_title ?? $product->name . ' - Patenli Ayakkabılar' }}</x-slot:title>
    <x-slot:description>{{ $product->meta_description ?? Str::limit($product->short_description ?? 'Çocuklar için güvenli ve eğlenceli patenli ayakkabılar.', 155) }}</x-slot:description>
    <x-slot:ogImage>{{ $product->images->first()?->image_url ?? asset('images/og-image.jpg') }}</x-slot:ogImage>
    <x-slot:schema>
        <script type="application/ld+json">
        {
          "@@context": "https://schema.org/",
          "@@type": "Product",
          "name": "{{ $product->name }}",
          "image": [
            "{{ $product->images->first()?->image_url ?? asset('images/og-image.jpg') }}"
           ],
          "description": "{{ Str::limit(strip_tags($product->short_description), 200) }}",
          "sku": "{{ $product->sku ?? $product->id }}",
          "offers": {
            "@@type": "Offer",
            "url": "{{ url()->current() }}",
            "priceCurrency": "TRY",
            "price": "{{ $product->discount_price ?? $product->price }}",
            "itemCondition": "https://schema.org/NewCondition",
            "availability": "https://schema.org/{{ $product->stock > 0 ? 'InStock' : 'OutOfStock' }}"
          }
        }
        </script>
    </x-slot:schema>

    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
                <!-- Image gallery -->
                <livewire:product.product-gallery :product="$product" />

                <!-- Product info -->
                <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                    <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 leading-tight">{{ $product->name }}</h1>
                    <div class="mt-4">
                        <h2 class="sr-only">Product information</h2>
                        <div class="flex items-center gap-4">
                            @if($product->discount_price)
                                <p class="text-3xl md:text-4xl font-bold text-red-600">{{ number_format($product->discount_price, 2) }} ₺</p>
                                <p class="text-xl text-gray-400 line-through decoration-gray-300">{{ number_format($product->price, 2) }} ₺</p>
                            @else
                                <p class="text-3xl md:text-4xl font-bold text-gray-900">{{ number_format($product->price, 2) }} ₺</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="sr-only">Description</h3>
                        <div class="text-lg text-gray-500 leading-relaxed font-light">
                            <p>{{ $product->short_description }}</p>
                        </div>
                    </div>

                    @if($product->variants->count() > 0)
                        <div class="mt-10">
                            <livewire:product.variant-selector :product="$product" />
                        </div>
                    @endif

                    <div class="mt-10">
                        <livewire:product.add-to-cart-button :product="$product" />
                    </div>
                    
                    <div class="mt-12 border-t border-gray-100 pt-8">
                        <h2 class="text-base font-semibold text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            Öne Çıkan Özellikler
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Ortopedik taban desteği ile gün boyu rahatlık</span>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Bas-çek gizli tekerlek mekanizması</span>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Pil değişimi gerektirmeyen LED ışıklı taban</span>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Hava alabilen file dokulu dış yüzey tasarımı</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

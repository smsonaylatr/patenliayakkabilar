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
            "price": "{{ ($product->discount_price && $product->price && $product->discount_price > $product->price) ? $product->price : ($product->discount_price ?? $product->price) }}",
            "itemCondition": "https://schema.org/NewCondition",
            "availability": "https://schema.org/{{ $product->stock > 0 ? 'InStock' : 'OutOfStock' }}"
          }
        }
        </script>
    </x-slot:schema>

    <style>
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.3s ease;
            opacity: 0;
        }
        .accordion-content.open {
            max-height: 1000px;
            opacity: 1;
        }
    </style>

    <div class="py-10 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 lg:items-start">
                <!-- Image gallery -->
                <livewire:product.product-gallery :product="$product" />

                <!-- Product info -->
                <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">{{ $product->name }}</h1>

                    {{-- Fiyat --}}
                    <div class="mt-4">
                        <h2 class="sr-only">Product information</h2>
                        <div class="flex items-center gap-3">
                            @php
                                $displayPrice = $product->price;
                                $displayDiscount = $product->discount_price;
                                if ($displayDiscount && $displayPrice && $displayDiscount > $displayPrice) {
                                    $displayPrice = $product->discount_price;
                                    $displayDiscount = $product->price;
                                }
                            @endphp

                            @if($displayDiscount)
                                <p class="text-3xl font-bold text-gray-900">{{ number_format($displayDiscount, 2) }} ₺</p>
                                <p class="text-lg text-gray-400 line-through">{{ number_format($displayPrice, 2) }} ₺</p>
                                @php $percent = round(($displayPrice - $displayDiscount) / $displayPrice * 100); @endphp
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-red-500 text-white">%{{ $percent }}</span>
                            @else
                                <p class="text-3xl font-bold text-gray-900">{{ number_format($displayPrice, 2) }} ₺</p>
                            @endif
                        </div>
                    </div>

                    {{-- Kısa açıklama --}}
                    @if($product->short_description)
                    <div class="mt-5">
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $product->short_description }}</p>
                    </div>
                    @endif

                    {{-- Varyant Seçici --}}
                    @if($product->variants->count() > 0)
                        <div class="mt-6">
                            <livewire:product.variant-selector :product="$product" />
                        </div>
                    @endif

                    {{-- Sepete Ekle --}}
                    <div class="mt-6">
                        <livewire:product.add-to-cart-button :product="$product" />
                    </div>

                    {{-- ========================================
                         GÜVEN SİNYALLERİ — Minimalist
                    ======================================== --}}
                    @php $signals = $product->getTrustSignals(); @endphp
                    <div class="mt-8 grid grid-cols-2 gap-2">
                        @php
                            $iconMap = [
                                '🚚' => 'fa-solid fa-truck-fast',
                                '⚡' => 'fa-solid fa-bolt',
                                '🔒' => 'fa-solid fa-lock',
                                '↩️' => 'fa-solid fa-rotate-left',
                                '🏷️' => 'fa-solid fa-tag',
                                '🔥' => 'fa-solid fa-fire',
                                '⭐' => 'fa-solid fa-star',
                            ];
                            $colorMap = [
                                'green'  => 'text-emerald-500',
                                'blue'   => 'text-blue-500',
                                'purple' => 'text-violet-500',
                                'orange' => 'text-amber-500',
                                'red'    => 'text-red-500',
                                'yellow' => 'text-yellow-500',
                            ];
                        @endphp
                        @foreach($signals as $signal)
                            <div class="flex items-center gap-2.5 px-3.5 py-3 rounded-lg border border-gray-100 bg-gray-50/50">
                                <i class="{{ $iconMap[$signal['icon']] ?? 'fa-solid fa-check' }} {{ $colorMap[$signal['color']] ?? 'text-gray-500' }} text-sm"></i>
                                <span class="text-xs font-medium text-gray-700">{{ $signal['text'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- ========================================
                         AKORDİYON — Minimalist
                    ======================================== --}}
                    <div class="mt-8 divide-y divide-gray-100" x-data="{ openPanel: '' }">

                        {{-- 1. ÖNE ÇIKAN ÖZELLİKLER --}}
                        @php $featureLabels = $product->getFeatureLabels(); @endphp
                        @if(count($featureLabels) > 0)
                        <div>
                            <button
                                @click="openPanel = openPanel === 'features' ? '' : 'features'"
                                class="w-full flex items-center justify-between py-4 text-left group"
                            >
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-list-check text-gray-400 text-sm w-5 text-center"></i>
                                    <span class="text-sm font-semibold text-gray-900">Öne Çıkan Özellikler</span>
                                    <span class="text-[11px] font-medium text-gray-400">{{ count($featureLabels) }}</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-300"
                                   :class="openPanel === 'features' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'features' ? 'open' : ''">
                                <div class="pb-4 grid grid-cols-2 gap-2">
                                    @foreach($featureLabels as $feature)
                                        <div class="flex items-center gap-2.5 px-3 py-2.5 bg-gray-50 rounded-lg">
                                            <i class="fa-solid fa-check text-emerald-500 text-[10px]"></i>
                                            <span class="text-xs font-medium text-gray-700">{{ $feature['label'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- 2. TEKNİK BİLGİLER --}}
                        @php $specs = $product->getSpecifications(); @endphp
                        @if(count($specs) > 0)
                        <div>
                            <button
                                @click="openPanel = openPanel === 'specs' ? '' : 'specs'"
                                class="w-full flex items-center justify-between py-4 text-left group"
                            >
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-info-circle text-gray-400 text-sm w-5 text-center"></i>
                                    <span class="text-sm font-semibold text-gray-900">Teknik Bilgiler</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-300"
                                   :class="openPanel === 'specs' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'specs' ? 'open' : ''">
                                <div class="pb-4">
                                    <div class="rounded-lg border border-gray-100 overflow-hidden">
                                        <dl>
                                            @foreach($specs as $label => $value)
                                                <div class="flex justify-between items-center px-4 py-2.5 text-xs {{ !$loop->last ? 'border-b border-gray-50' : '' }} {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                                                    <dt class="text-gray-500">{{ $label }}</dt>
                                                    <dd class="text-gray-900 font-semibold">{{ $value }}</dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- 3. ÜRÜN AÇIKLAMASI --}}
                        @if($product->description)
                        <div>
                            <button
                                @click="openPanel = openPanel === 'description' ? '' : 'description'"
                                class="w-full flex items-center justify-between py-4 text-left group"
                            >
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-file-lines text-gray-400 text-sm w-5 text-center"></i>
                                    <span class="text-sm font-semibold text-gray-900">Ürün Açıklaması</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-300"
                                   :class="openPanel === 'description' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'description' ? 'open' : ''">
                                <div class="pb-4">
                                    <div class="prose prose-sm prose-gray max-w-none text-gray-600 leading-relaxed text-xs">
                                        {!! $product->description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- 4. KARGO & İADE --}}
                        <div>
                            <button
                                @click="openPanel = openPanel === 'shipping' ? '' : 'shipping'"
                                class="w-full flex items-center justify-between py-4 text-left group"
                            >
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-truck text-gray-400 text-sm w-5 text-center"></i>
                                    <span class="text-sm font-semibold text-gray-900">Kargo & İade</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-300"
                                   :class="openPanel === 'shipping' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'shipping' ? 'open' : ''">
                                <div class="pb-4 space-y-3">
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-truck-fast text-emerald-500 text-xs mt-0.5 w-4 text-center"></i>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">Ücretsiz Kargo</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5">Türkiye'nin her yerine ücretsiz kargo. 1-3 iş günü içinde kargoya verilir.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-shield-halved text-blue-500 text-xs mt-0.5 w-4 text-center"></i>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">Güvenli Paketleme</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5">Özel kutusunda, hasar görmeyecek şekilde paketlenir.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-rotate-left text-amber-500 text-xs mt-0.5 w-4 text-center"></i>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">14 Gün İade Garantisi</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5">Kullanılmamış ve orijinal ambalajında koşulsuz iade.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-lock text-violet-500 text-xs mt-0.5 w-4 text-center"></i>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">Güvenli Ödeme</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5">256-bit SSL şifreleme. Kapıda ödeme seçeneği mevcuttur.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

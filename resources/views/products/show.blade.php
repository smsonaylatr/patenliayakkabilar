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
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
            opacity: 0;
        }
        .accordion-content.open {
            max-height: 800px;
            opacity: 1;
        }
        .accordion-chevron {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .accordion-chevron.rotated {
            transform: rotate(180deg);
        }
        .trust-badge {
            backdrop-filter: blur(8px);
            transition: all 0.2s ease;
        }
        .trust-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .feature-card {
            transition: all 0.2s ease;
        }
        .feature-card:hover {
            transform: translateX(4px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
    </style>

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
                            @php
                                $displayPrice = $product->price;
                                $displayDiscount = $product->discount_price;
                                if ($displayDiscount && $displayPrice && $displayDiscount > $displayPrice) {
                                    $displayPrice = $product->discount_price;
                                    $displayDiscount = $product->price;
                                }
                            @endphp

                            @if($displayDiscount)
                                <p class="text-3xl md:text-4xl font-bold text-red-600">{{ number_format($displayDiscount, 2) }} ₺</p>
                                <p class="text-xl text-gray-400 line-through decoration-gray-300">{{ number_format($displayPrice, 2) }} ₺</p>
                                @php $percent = round(($displayPrice - $displayDiscount) / $displayPrice * 100); @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-700">
                                    %{{ $percent }} İNDİRİM
                                </span>
                            @else
                                <p class="text-3xl md:text-4xl font-bold text-gray-900">{{ number_format($displayPrice, 2) }} ₺</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="sr-only">Description</h3>
                        <div class="text-base text-gray-500 leading-relaxed">
                            <p>{{ $product->short_description }}</p>
                        </div>
                    </div>

                    @if($product->variants->count() > 0)
                        <div class="mt-8">
                            <livewire:product.variant-selector :product="$product" />
                        </div>
                    @endif

                    <div class="mt-8">
                        <livewire:product.add-to-cart-button :product="$product" />
                    </div>

                    {{-- ========================================
                         GÜVEN SİNYALLERİ
                    ======================================== --}}
                    @php $signals = $product->getTrustSignals(); @endphp
                    <div class="mt-6 grid grid-cols-2 gap-2.5">
                        @foreach($signals as $signal)
                            <div class="trust-badge flex items-center gap-2.5 px-4 py-3 rounded-2xl text-sm font-medium border
                                @switch($signal['color'])
                                    @case('green')  bg-emerald-50/80 text-emerald-700 border-emerald-100 @break
                                    @case('blue')   bg-blue-50/80 text-blue-700 border-blue-100 @break
                                    @case('purple') bg-violet-50/80 text-violet-700 border-violet-100 @break
                                    @case('orange') bg-amber-50/80 text-amber-700 border-amber-100 @break
                                    @case('red')    bg-red-50/80 text-red-700 border-red-100 @break
                                    @case('yellow') bg-yellow-50/80 text-yellow-700 border-yellow-100 @break
                                    @default        bg-gray-50/80 text-gray-700 border-gray-100
                                @endswitch
                            ">
                                <span class="text-lg flex-shrink-0">{{ $signal['icon'] }}</span>
                                <span class="leading-tight">{{ $signal['text'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- ========================================
                         AKORDİYON DETAY BÖLÜMLERİ
                    ======================================== --}}
                    <div class="mt-10 border-t border-gray-100" x-data="{ openPanel: 'features' }">

                        {{-- 1. ÖNE ÇIKAN ÖZELLİKLER --}}
                        @php $featureLabels = $product->getFeatureLabels(); @endphp
                        @if(count($featureLabels) > 0)
                        <div class="border-b border-gray-100">
                            <button
                                @click="openPanel = openPanel === 'features' ? '' : 'features'"
                                class="w-full flex items-center justify-between py-5 text-left group"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-sm">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                    </div>
                                    <span class="text-base font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors">Öne Çıkan Özellikler</span>
                                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">{{ count($featureLabels) }} özellik</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 accordion-chevron" :class="openPanel === 'features' ? 'rotated' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'features' ? 'open' : ''">
                                <div class="pb-6 grid grid-cols-1 gap-3">
                                    @foreach($featureLabels as $feature)
                                        <div class="feature-card flex items-start gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100/80">
                                            <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-semibold text-sm text-gray-800">{{ $feature['label'] }}</span>
                                                @if($feature['desc'])
                                                    <p class="text-gray-500 text-xs mt-1 leading-relaxed">{{ $feature['desc'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- 2. TEKNİK BİLGİLER --}}
                        @php $specs = $product->getSpecifications(); @endphp
                        @if(count($specs) > 0)
                        <div class="border-b border-gray-100">
                            <button
                                @click="openPanel = openPanel === 'specs' ? '' : 'specs'"
                                class="w-full flex items-center justify-between py-5 text-left group"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-sm">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <span class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Teknik Bilgiler</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 accordion-chevron" :class="openPanel === 'specs' ? 'rotated' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'specs' ? 'open' : ''">
                                <div class="pb-6">
                                    <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-100/80">
                                        <dl class="divide-y divide-gray-100">
                                            @foreach($specs as $label => $value)
                                                <div class="flex justify-between px-5 py-3.5 text-sm {{ $loop->even ? 'bg-white/60' : '' }}">
                                                    <dt class="font-medium text-gray-500">{{ $label }}</dt>
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
                        <div class="border-b border-gray-100">
                            <button
                                @click="openPanel = openPanel === 'description' ? '' : 'description'"
                                class="w-full flex items-center justify-between py-5 text-left group"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-400 to-violet-600 flex items-center justify-center shadow-sm">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                    </div>
                                    <span class="text-base font-semibold text-gray-900 group-hover:text-violet-600 transition-colors">Ürün Açıklaması</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 accordion-chevron" :class="openPanel === 'description' ? 'rotated' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'description' ? 'open' : ''">
                                <div class="pb-6">
                                    <div class="prose prose-sm prose-gray max-w-none text-gray-600 leading-relaxed bg-gray-50 rounded-2xl p-5 border border-gray-100/80">
                                        {!! $product->description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- 4. KARGO & İADE --}}
                        <div class="border-b border-gray-100">
                            <button
                                @click="openPanel = openPanel === 'shipping' ? '' : 'shipping'"
                                class="w-full flex items-center justify-between py-5 text-left group"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-sm">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    </div>
                                    <span class="text-base font-semibold text-gray-900 group-hover:text-amber-600 transition-colors">Kargo & İade</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 accordion-chevron" :class="openPanel === 'shipping' ? 'rotated' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'shipping' ? 'open' : ''">
                                <div class="pb-6 space-y-4">
                                    <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100/80 space-y-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-base">🚚</span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-sm text-gray-800">Ücretsiz Kargo</p>
                                                <p class="text-xs text-gray-500 mt-0.5">Türkiye'nin her yerine ücretsiz kargo. Siparişiniz 1-3 iş günü içinde kargoya verilir.</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-base">📦</span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-sm text-gray-800">Güvenli Paketleme</p>
                                                <p class="text-xs text-gray-500 mt-0.5">Ürünleriniz özel kutusunda, hasar görmeyecek şekilde paketlenerek gönderilir.</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-base">↩️</span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-sm text-gray-800">14 Gün İade Garantisi</p>
                                                <p class="text-xs text-gray-500 mt-0.5">Ürünü teslim aldıktan sonra 14 gün içinde koşulsuz iade edebilirsiniz. Kullanılmamış ve orijinal ambalajında olmalıdır.</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-base">🔐</span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-sm text-gray-800">Güvenli Ödeme</p>
                                                <p class="text-xs text-gray-500 mt-0.5">256-bit SSL şifreleme ile kredi kartı bilgileriniz güvende. Kapıda ödeme seçeneği de mevcuttur.</p>
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
    </div>
</x-layouts.app>

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
            transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
            opacity: 0;
        }
        .accordion-content.open {
            max-height: 1000px;
            opacity: 1;
        }
        .accordion-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .trust-badge {
            backdrop-filter: blur(8px);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .trust-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        }
        .feature-card {
            transition: all 0.2s ease;
        }
        .feature-card:hover {
            transform: translateY(-1px);
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
                    <div class="mt-8 grid grid-cols-2 gap-3">
                        @foreach($signals as $signal)
                            <div class="trust-badge flex items-center gap-3 px-4 py-3.5 rounded-2xl
                                @switch($signal['color'])
                                    @case('green')  bg-emerald-50 border border-emerald-200/60 @break
                                    @case('blue')   bg-blue-50 border border-blue-200/60 @break
                                    @case('purple') bg-violet-50 border border-violet-200/60 @break
                                    @case('orange') bg-amber-50 border border-amber-200/60 @break
                                    @case('red')    bg-red-50 border border-red-200/60 @break
                                    @case('yellow') bg-yellow-50 border border-yellow-200/60 @break
                                    @default        bg-gray-50 border border-gray-200/60
                                @endswitch
                            ">
                                <span class="text-xl flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-xl
                                    @switch($signal['color'])
                                        @case('green')  bg-emerald-100 @break
                                        @case('blue')   bg-blue-100 @break
                                        @case('purple') bg-violet-100 @break
                                        @case('orange') bg-amber-100 @break
                                        @case('red')    bg-red-100 @break
                                        @case('yellow') bg-yellow-100 @break
                                        @default        bg-gray-100
                                    @endswitch
                                ">{{ $signal['icon'] }}</span>
                                <span class="text-sm font-semibold leading-tight
                                    @switch($signal['color'])
                                        @case('green')  text-emerald-800 @break
                                        @case('blue')   text-blue-800 @break
                                        @case('purple') text-violet-800 @break
                                        @case('orange') text-amber-800 @break
                                        @case('red')    text-red-700 @break
                                        @case('yellow') text-yellow-800 @break
                                        @default        text-gray-800
                                    @endswitch
                                ">{{ $signal['text'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- ========================================
                         AKORDİYON DETAY BÖLÜMLERİ
                    ======================================== --}}
                    <div class="mt-10 space-y-3" x-data="{ openPanel: '' }">

                        {{-- 1. ÖNE ÇIKAN ÖZELLİKLER --}}
                        @php $featureLabels = $product->getFeatureLabels(); @endphp
                        @if(count($featureLabels) > 0)
                        <div class="accordion-card rounded-2xl border border-gray-200/70 overflow-hidden transition-all duration-300"
                             :class="openPanel === 'features' ? 'shadow-lg shadow-emerald-100/50 border-emerald-200' : 'hover:border-gray-300 hover:shadow-sm'">
                            <button
                                @click="openPanel = openPanel === 'features' ? '' : 'features'"
                                class="w-full flex items-center justify-between px-5 py-4 text-left group bg-white"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-sm shadow-emerald-200">
                                        <span class="text-lg">✨</span>
                                    </div>
                                    <div>
                                        <span class="text-[15px] font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">Öne Çıkan Özellikler</span>
                                        <span class="ml-2 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full">{{ count($featureLabels) }} özellik</span>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-300"
                                     :class="openPanel === 'features' ? 'bg-emerald-100 rotate-180' : 'bg-gray-100 group-hover:bg-gray-200'">
                                    <svg class="w-4 h-4" :class="openPanel === 'features' ? 'text-emerald-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'features' ? 'open' : ''">
                                <div class="px-5 pb-5 grid grid-cols-1 sm:grid-cols-2 gap-2.5 bg-gradient-to-b from-white to-emerald-50/30">
                                    @foreach($featureLabels as $feature)
                                        <div class="feature-card flex items-center gap-3 p-3.5 bg-white rounded-xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition-all">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-semibold text-sm text-gray-800">{{ $feature['label'] }}</span>
                                                @if($feature['desc'])
                                                    <p class="text-gray-400 text-[11px] mt-0.5 leading-snug line-clamp-1">{{ $feature['desc'] }}</p>
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
                        <div class="accordion-card rounded-2xl border border-gray-200/70 overflow-hidden transition-all duration-300"
                             :class="openPanel === 'specs' ? 'shadow-lg shadow-blue-100/50 border-blue-200' : 'hover:border-gray-300 hover:shadow-sm'">
                            <button
                                @click="openPanel = openPanel === 'specs' ? '' : 'specs'"
                                class="w-full flex items-center justify-between px-5 py-4 text-left group bg-white"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-sm shadow-blue-200">
                                        <span class="text-lg">📋</span>
                                    </div>
                                    <span class="text-[15px] font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Teknik Bilgiler</span>
                                </div>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-300"
                                     :class="openPanel === 'specs' ? 'bg-blue-100 rotate-180' : 'bg-gray-100 group-hover:bg-gray-200'">
                                    <svg class="w-4 h-4" :class="openPanel === 'specs' ? 'text-blue-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'specs' ? 'open' : ''">
                                <div class="px-5 pb-5">
                                    <div class="bg-gradient-to-b from-gray-50 to-blue-50/30 rounded-xl overflow-hidden border border-gray-100">
                                        <dl>
                                            @foreach($specs as $label => $value)
                                                <div class="flex justify-between items-center px-4 py-3 text-sm {{ !$loop->last ? 'border-b border-gray-100' : '' }} {{ $loop->even ? 'bg-white/50' : '' }}">
                                                    <dt class="font-medium text-gray-500">{{ $label }}</dt>
                                                    <dd class="text-gray-900 font-bold text-right">{{ $value }}</dd>
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
                        <div class="accordion-card rounded-2xl border border-gray-200/70 overflow-hidden transition-all duration-300"
                             :class="openPanel === 'description' ? 'shadow-lg shadow-violet-100/50 border-violet-200' : 'hover:border-gray-300 hover:shadow-sm'">
                            <button
                                @click="openPanel = openPanel === 'description' ? '' : 'description'"
                                class="w-full flex items-center justify-between px-5 py-4 text-left group bg-white"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-400 to-violet-600 flex items-center justify-center shadow-sm shadow-violet-200">
                                        <span class="text-lg">📝</span>
                                    </div>
                                    <span class="text-[15px] font-bold text-gray-900 group-hover:text-violet-600 transition-colors">Ürün Açıklaması</span>
                                </div>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-300"
                                     :class="openPanel === 'description' ? 'bg-violet-100 rotate-180' : 'bg-gray-100 group-hover:bg-gray-200'">
                                    <svg class="w-4 h-4" :class="openPanel === 'description' ? 'text-violet-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'description' ? 'open' : ''">
                                <div class="px-5 pb-5">
                                    <div class="prose prose-sm prose-gray max-w-none text-gray-600 leading-relaxed bg-gradient-to-b from-gray-50 to-violet-50/20 rounded-xl p-5 border border-gray-100">
                                        {!! $product->description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- 4. KARGO & İADE --}}
                        <div class="accordion-card rounded-2xl border border-gray-200/70 overflow-hidden transition-all duration-300"
                             :class="openPanel === 'shipping' ? 'shadow-lg shadow-amber-100/50 border-amber-200' : 'hover:border-gray-300 hover:shadow-sm'">
                            <button
                                @click="openPanel = openPanel === 'shipping' ? '' : 'shipping'"
                                class="w-full flex items-center justify-between px-5 py-4 text-left group bg-white"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-sm shadow-amber-200">
                                        <span class="text-lg">📦</span>
                                    </div>
                                    <span class="text-[15px] font-bold text-gray-900 group-hover:text-amber-600 transition-colors">Kargo & İade</span>
                                </div>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-300"
                                     :class="openPanel === 'shipping' ? 'bg-amber-100 rotate-180' : 'bg-gray-100 group-hover:bg-gray-200'">
                                    <svg class="w-4 h-4" :class="openPanel === 'shipping' ? 'text-amber-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'shipping' ? 'open' : ''">
                                <div class="px-5 pb-5 space-y-3">
                                    <div class="flex items-start gap-3.5 p-4 bg-white rounded-xl border border-gray-100 hover:border-emerald-200 transition-colors">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xl">🚚</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-gray-800">Ücretsiz Kargo</p>
                                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Türkiye'nin her yerine ücretsiz kargo. Siparişiniz 1-3 iş günü içinde kargoya verilir.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3.5 p-4 bg-white rounded-xl border border-gray-100 hover:border-blue-200 transition-colors">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xl">🛡️</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-gray-800">Güvenli Paketleme</p>
                                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Ürünleriniz özel kutusunda, hasar görmeyecek şekilde paketlenerek gönderilir.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3.5 p-4 bg-white rounded-xl border border-gray-100 hover:border-amber-200 transition-colors">
                                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xl">↩️</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-gray-800">14 Gün İade Garantisi</p>
                                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Ürünü teslim aldıktan sonra 14 gün içinde koşulsuz iade edebilirsiniz. Kullanılmamış ve orijinal ambalajında olmalıdır.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3.5 p-4 bg-white rounded-xl border border-gray-100 hover:border-violet-200 transition-colors">
                                        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xl">🔐</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-gray-800">Güvenli Ödeme</p>
                                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">256-bit SSL şifreleme ile kredi kartı bilgileriniz güvende. Kapıda ödeme seçeneği de mevcuttur.</p>
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

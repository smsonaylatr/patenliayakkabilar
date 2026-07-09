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

    <style>
        .acc-body { max-height: 0; overflow: hidden; transition: max-height .35s cubic-bezier(.4,0,.2,1), opacity .25s ease; opacity: 0; }
        .acc-body.open { max-height: 1000px; opacity: 1; }
        .acc-chevron { transition: transform .3s cubic-bezier(.4,0,.2,1); }
        .acc-chevron.rotated { transform: rotate(180deg); }
        .trust-card { transition: all .2s ease; }
        .trust-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
        .feat-row { transition: background-color .15s ease; }
        .feat-row:hover { background-color: #f0f0f0; }
    </style>

    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
                <!-- Image gallery -->
                <livewire:product.product-gallery :product="$product" />

                <!-- Product info -->
                <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900 leading-tight">{{ $product->name }}</h1>
                    <div class="mt-4">
                        <h2 class="sr-only">Product information</h2>
                        <div class="flex items-center gap-4">
                            @if($product->discount_price)
                                <p class="text-3xl md:text-4xl font-bold text-red-600">{{ number_format($product->discount_price, 2) }} ₺</p>
                                <p class="text-lg text-gray-400 line-through">{{ number_format($product->price, 2) }} ₺</p>
                                @php $percent = round(($product->price - $product->discount_price) / $product->price * 100); @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">%{{ $percent }}</span>
                            @else
                                <p class="text-3xl md:text-4xl font-bold text-gray-900">{{ number_format($product->price, 2) }} ₺</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5">
                        <div class="text-sm text-gray-500 leading-relaxed">
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
                         GÜVEN SİNYALLERİ — SVG İkonlar
                    ======================================== --}}
                    @php $signals = $product->getTrustSignals(); @endphp
                    <div class="mt-6 grid grid-cols-2 gap-2">
                        @foreach($signals as $signal)
                            <div class="trust-card flex items-center gap-3 px-4 py-3 rounded-xl bg-gray-50 border border-gray-100">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                    @switch($signal['color'])
                                        @case('green')  bg-emerald-100 text-emerald-600 @break
                                        @case('blue')   bg-blue-100 text-blue-600 @break
                                        @case('purple') bg-violet-100 text-violet-600 @break
                                        @case('orange') bg-amber-100 text-amber-600 @break
                                        @case('red')    bg-red-100 text-red-600 @break
                                        @case('yellow') bg-yellow-100 text-yellow-600 @break
                                        @default        bg-gray-100 text-gray-600
                                    @endswitch
                                ">
                                    @switch($signal['icon'])
                                        @case('truck')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                                        @break
                                        @case('clock')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        @break
                                        @case('lock-closed')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                        @break
                                        @case('arrow-uturn-left')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                                        @break
                                        @case('tag')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                                        @break
                                        @case('fire')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z"/></svg>
                                        @break
                                        @case('star')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                                        @break
                                    @endswitch
                                </div>
                                <span class="text-xs font-semibold text-gray-700 leading-tight">{{ $signal['text'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- ========================================
                         AKORDİYON BÖLÜMLERİ — Gri Zemin
                    ======================================== --}}
                    <div class="mt-8 bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden" x-data="{ open: 'features' }">

                        {{-- 1. ÖNE ÇIKAN ÖZELLİKLER --}}
                        @php $featureLabels = $product->getFeatureLabels(); @endphp
                        @if(count($featureLabels) > 0)
                        <div class="border-b border-gray-200/60">
                            <button @click="open = open === 'features' ? '' : 'features'" class="w-full flex items-center justify-between px-5 py-4 text-left group hover:bg-gray-100/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">Öne Çıkan Özellikler</span>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">{{ count($featureLabels) }}</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 acc-chevron" :class="open === 'features' && 'rotated'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div class="acc-body" :class="open === 'features' && 'open'">
                                <div class="px-5 pb-5 space-y-1">
                                    @foreach($featureLabels as $feature)
                                        <div class="feat-row flex items-start gap-3 px-4 py-3 rounded-xl bg-white border border-gray-100">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-800">{{ $feature['label'] }}</p>
                                                @if($feature['desc'])
                                                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $feature['desc'] }}</p>
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
                        <div class="border-b border-gray-200/60">
                            <button @click="open = open === 'specs' ? '' : 'specs'" class="w-full flex items-center justify-between px-5 py-4 text-left group hover:bg-gray-100/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">Teknik Bilgiler</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 acc-chevron" :class="open === 'specs' && 'rotated'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div class="acc-body" :class="open === 'specs' && 'open'">
                                <div class="px-5 pb-5">
                                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                                        <dl class="divide-y divide-gray-50">
                                            @foreach($specs as $label => $value)
                                                <div class="flex justify-between px-4 py-3 text-sm">
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
                        <div class="border-b border-gray-200/60">
                            <button @click="open = open === 'desc' ? '' : 'desc'" class="w-full flex items-center justify-between px-5 py-4 text-left group hover:bg-gray-100/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">Ürün Açıklaması</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 acc-chevron" :class="open === 'desc' && 'rotated'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div class="acc-body" :class="open === 'desc' && 'open'">
                                <div class="px-5 pb-5">
                                    <div class="prose prose-sm prose-gray max-w-none text-gray-600 bg-white rounded-xl p-5 border border-gray-100">
                                        {!! $product->description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- 4. KARGO & İADE --}}
                        <div>
                            <button @click="open = open === 'ship' ? '' : 'ship'" class="w-full flex items-center justify-between px-5 py-4 text-left group hover:bg-gray-100/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">Kargo & İade</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 acc-chevron" :class="open === 'ship' && 'rotated'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div class="acc-body" :class="open === 'ship' && 'open'">
                                <div class="px-5 pb-5 space-y-3">
                                    <div class="flex items-start gap-3 bg-white rounded-xl p-4 border border-gray-100">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">Ücretsiz Kargo</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Türkiye'nin her yerine ücretsiz. 1-3 iş günü içinde kargoya verilir.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 bg-white rounded-xl p-4 border border-gray-100">
                                        <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">Güvenli Paketleme</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Özel kutusunda, hasar görmeyecek şekilde paketlenir.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 bg-white rounded-xl p-4 border border-gray-100">
                                        <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">14 Gün İade Garantisi</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Kullanılmamış ve orijinal ambalajında koşulsuz iade.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 bg-white rounded-xl p-4 border border-gray-100">
                                        <div class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">Güvenli Ödeme</p>
                                            <p class="text-xs text-gray-500 mt-0.5">256-bit SSL. Kapıda ödeme seçeneği mevcuttur.</p>
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

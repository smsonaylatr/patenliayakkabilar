<x-layouts.app>
    <x-slot:title>Patenli Ayakkabılar | Işıklı ve Tekerlekli Çocuk & Genç Paten Ayakkabı Modelleri</x-slot:title>
    <x-slot:description>Çocuk ve gençler için gizli tekerlekli, USB şarjlı LED ışıklı patenli ayakkabı modelleri ve fiyatları. Güvenli alışveriş, hızlı kargo ve 14 gün kolay değişim.</x-slot:description>
    <x-slot:canonical>{{ url('/') }}</x-slot:canonical>

    @php
        $schemaService = app(\App\Services\SchemaService::class);
        $breadcrumbs = $schemaService->breadcrumb([
            ['name' => 'Ana Sayfa', 'url' => '/'],
            ['name' => 'Patenli Ayakkabı Modelleri', 'url' => '/patenli-ayakkabilar'],
        ]);
        $homeFaqItems = [
            [
                'question' => 'Siparişim ne zaman kargoya verilir?',
                'answer' => 'Saat 14:00\'e kadar verilen siparişler aynı gün, 14:00\'ten sonra verilen siparişler ise ertesi iş günü anlaşmalı kargo ile gönderilmektedir.'
            ],
            [
                'question' => 'Patenli ayakkabılar normal spor ayakkabı olarak kullanılabilir mi?',
                'answer' => 'Evet! Tüm patenli ayakkabı modellerimizin tabanındaki gizli mekanizma sayesinde arka emniyet butonuna basarak tekerleği yuvasına gizleyebilir ve günlük spor ayakkabı olarak rahatça kullanabilirsiniz.'
            ],
            [
                'question' => 'Çocuklar için patenli ayakkabı numarası nasıl seçilmeli?',
                'answer' => 'Tam kalıp üretilen tekerlekli ayakkabı modellerimizde çocuğunuzun günlük giydiği spor ayakkabı numarasını veya büyüme payı için 1 numara büyüğünü tercih edebilirsiniz. Teslimattan itibaren 14 gün içinde ücretsiz numara değişimi imkanı sunulmaktadır.'
            ],
            [
                'question' => 'LED ışıklı paten ayakkabılar nasıl şarj edilir?',
                'answer' => 'USB şarjlı modellerimiz kutu içerisinden çıkan çift uçlu USB kablo ile 2 saatte tam şarj olur ve 6-8 saat kesintisiz çok renkli LED ışık performansı sunar.'
            ],
        ];
        $faqs = $schemaService->faq($homeFaqItems);
        
        $featuredProducts = \App\Models\Product::where('status', true)
            ->where('stock', '>', 0)
            ->with(['images', 'categories'])
            ->orderByDesc('featured')
            ->orderByDesc('best_seller')
            ->orderByRaw('CASE WHEN homepage_sort > 0 THEN 0 ELSE 1 END')
            ->orderBy('homepage_sort', 'asc')
            ->orderByDesc('id')
            ->take(12)
            ->get();
        $itemListSchema = $schemaService->homePageItemList($featuredProducts);
        $collectionSchema = $schemaService->homePageCollection($featuredProducts);
        $homeProductImages = $featuredProducts
            ->map(fn ($p) => $p->images->first()?->raw_image_url)
            ->filter()
            ->take(6)
            ->values()
            ->implode(',');
    @endphp
    @if($homeProductImages !== '')
        <x-slot:ogImages>{{ $homeProductImages }}</x-slot:ogImages>
    @endif
    
    <x-slot:schema>
        {!! $breadcrumbs !!}
        {!! $faqs !!}
        {!! $itemListSchema !!}
        {!! $collectionSchema !!}
    </x-slot:schema>

    @include('livewire.home.hero-section')
    

    @php
        $featuredCategories = \App\Models\Category::where('is_featured', 1)
                                ->where('status', 1)
                                ->orderBy('sort_order')
                                ->get();
    @endphp

    @if($featuredCategories->count() > 0)
        <div class="bg-white pt-10 pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-y-12">
                @foreach($featuredCategories as $category)
                    <div class="relative">
                        <div class="relative mb-5">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-900 tracking-tight leading-none">{{ $category->name }}</h2>
                                <a href="{{ route('category.show', ['slug' => $category->slug]) }}" wire:navigate class="group inline-flex items-center gap-2 text-sm sm:text-[15px] font-medium text-gray-800 hover:text-black transition-colors shrink-0">
                                    Daha Fazla 
                                    <span class="flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-gray-200 text-gray-600 group-hover:bg-gray-300 transition-colors">
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 ml-[1px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                    </span>
                                </a>
                            </div>
                        </div>

                        <!-- Kategoriye Ait Öne Çıkan Ürünler -->
                        <livewire:product.product-grid :category="$category->slug" isFeaturedOnly="true" :limit="4" :key="'featured-cat-'.$category->id" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Ana Sayfa SEO Rehberi ve Kategoriler Arası İç Linkleme Bölümü --}}
    <section class="bg-slate-50 border-t border-gray-100 py-12 sm:py-16" aria-labelledby="home-seo-guide-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                <div class="lg:col-span-7 space-y-4 text-gray-600 text-sm sm:text-base leading-relaxed">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 tracking-wide uppercase">
                        Orijinal & Güvenli Tasarım
                    </span>
                    <h2 id="home-seo-guide-heading" class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                        Işıklı ve Tekerlekli Patenli Ayakkabı Modelleri ile Tanışın
                    </h2>
                    <p>
                        <strong>Patenli ayakkabı</strong> (tekerlekli sneaker) modellerimiz, çocukların ve gençlerin hem günlük yürüyüşlerde spor ayakkabı olarak giyebileceği hem de topuk kısmındaki <strong>gizli tekerlek mekanizması</strong> ile saniyeler içinde patene dönüştürebileceği yenilikçi bir tasarıma sahiptir.
                    </p>
                    <p>
                        Koleksiyonumuzda yer alan <a href="{{ url('/kategori/kiz-cocuk') }}" wire:navigate class="text-gray-900 font-semibold underline decoration-gray-300 hover:decoration-black">Kız Çocuk Patenli Ayakkabı</a>, <a href="{{ url('/kategori/erkek-cocuk') }}" wire:navigate class="text-gray-900 font-semibold underline decoration-gray-300 hover:decoration-black">Erkek Çocuk Patenli Ayakkabı</a> ve çift/dört tekerlekli modeller; USB şarjlı çok modlu LED ışıkları, kaymaz TPR taban yapısı ve ortopedik iç ped desteğiyle maksimum konfor ve denge sunar.
                    </p>
                    <div class="pt-2 flex flex-wrap gap-2.5">
                        <a href="{{ route('products.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-900 text-white text-xs sm:text-sm font-bold hover:bg-black transition-colors">
                            Tüm Patenli Ayakkabıları İncele →
                        </a>
                        <a href="{{ url('/beden-rehberi') }}" wire:navigate class="inline-flex items-center px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs sm:text-sm font-semibold hover:border-gray-400 transition-colors">
                            Numara & Beden Rehberi
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-100 shadow-xs p-5 sm:p-6">
                    <h3 class="text-base sm:text-lg font-extrabold text-gray-900 mb-4">
                        Sıkça Sorulan Sorular
                    </h3>
                    <div class="divide-y divide-gray-100 space-y-3">
                        @foreach($homeFaqItems as $faqItem)
                            <details class="group pt-3 first:pt-0">
                                <summary class="flex items-center justify-between cursor-pointer list-none text-xs sm:text-sm font-bold text-gray-800 hover:text-black">
                                    <span>{{ $faqItem['question'] }}</span>
                                    <span class="ml-2 text-gray-400 group-open:rotate-180 transition-transform">▾</span>
                                </summary>
                                <p class="mt-2 text-xs sm:text-sm text-gray-600 leading-relaxed">
                                    {{ $faqItem['answer'] }}
                                </p>
                            </details>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>

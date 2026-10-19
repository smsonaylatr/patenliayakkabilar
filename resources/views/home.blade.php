<x-layouts.app>
    <x-slot:title>Patenli Ayakkabı Modelleri ve Fiyatları | Patenli Ayakkabılar</x-slot:title>
    <x-slot:description>Çocuk ve yetişkinler için patenli ayakkabı modellerini keşfedin. Işıklı, tekerlekli ve farklı renk seçenekleriyle size uygun modeli bulun.</x-slot:description>
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

        $homeGuideBlogPosts = \App\Models\BlogPost::where('status', true)
            ->orderByDesc('published_at')
            ->take(4)
            ->get();
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
                                <h2 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-900 tracking-tight leading-none">
                                    <a href="{{ route('category.show', ['slug' => $category->slug]) }}" wire:navigate class="hover:text-blue-600 transition-colors" title="{{ $category->name }} Patenli Ayakkabı Modelleri">
                                        {{ $category->name }}
                                    </a>
                                </h2>
                                <a href="{{ route('category.show', ['slug' => $category->slug]) }}" wire:navigate class="group inline-flex items-center gap-2 text-sm sm:text-[15px] font-medium text-gray-800 hover:text-black transition-colors shrink-0" title="Tüm {{ $category->name }} Patenli Ayakkabı Modellerini Gör">
                                    Tüm {{ $category->name }} Modelleri
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

    {{-- Ana Sayfa SEO Rehberi, Ürün Seçim Kılavuzu ve İç Linkleme Bölümü --}}
    <section class="bg-slate-50 border-t border-gray-100 py-12 sm:py-16" aria-labelledby="home-seo-guide-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                <div class="lg:col-span-7 space-y-4 text-gray-600 text-sm sm:text-base leading-relaxed">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 tracking-wide uppercase">
                        Patenli Ayakkabı Seçim Rehberi
                    </span>
                    <h2 id="home-seo-guide-heading" class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                        Çocuk ve Yetişkinler İçin Doğru Patenli Ayakkabı Nasıl Seçilir?
                    </h2>
                    <p>
                        <strong>Patenli ayakkabı</strong> (tekerlekli spor ayakkabı), tabanında yer alan <strong>gizli tekerlek mekanizması</strong> sayesinde hem günlük yürüyüş ayakkabısı hem de eğlenceli bir paten olarak kullanılabilen 2'si 1 arada tasarımdır. Topuk arkasındaki emniyet butonuna basıldığında tekerlek dışarı çıkarak kayma moduna geçer; tekrar bastırıldığında ise yuvaya gizlenerek okulda, parkta veya alışverişte normal bir sneaker konforu sunar.
                    </p>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 pt-2">
                        Işıklı, Tek ve Çift Tekerlekli Patenli Ayakkabı Modelleri
                    </h3>
                    <p>
                        İlk kez <strong>patenli ayakkabı</strong> kullanacak çocuklar için <a href="{{ url('/kategori/yetiskin-ve-cocuk') }}" wire:navigate class="text-gray-900 font-semibold underline decoration-gray-300 hover:decoration-black">çift tekerlekli ve dört tekerlekli modeller</a> yere daha geniş bastığı için ekstra denge ve kolay öğrenme avantajı sağlar. Deneyimli kullanıcılar ise manevra kabiliyeti yüksek tek tekerlekli tasarımları tercih edebilir. USB şarjlı, 7 farklı renk ve çakar moduna sahip <a href="{{ url('/kategori/kiz-cocuk') }}" wire:navigate class="text-gray-900 font-semibold underline decoration-gray-300 hover:decoration-black">Kız Çocuk Patenli Ayakkabı</a> ile <a href="{{ url('/kategori/erkek-cocuk') }}" wire:navigate class="text-gray-900 font-semibold underline decoration-gray-300 hover:decoration-black">Erkek Çocuk Patenli Ayakkabı</a> koleksiyonlarımız, kaymaz TPR taban ve ortopedik iç ped desteğiyle üretilmektedir.
                    </p>
                    <p>
                        Güvenli bir sürüş deneyimi için ayakkabı seçiminin yanında <a href="{{ url('/kategori/guvenlik-ekipmanlari') }}" wire:navigate class="text-gray-900 font-semibold underline decoration-gray-300 hover:decoration-black">kask, dizlik ve dirseklik koruyucu ekipman</a> kullanımını öneriyoruz. Numara seçiminde çocuğunuzun günlük spor ayakkabı numarasını veya <a href="{{ url('/beden-rehberi') }}" wire:navigate class="text-gray-900 font-semibold underline decoration-gray-300 hover:decoration-black">beden tablosu</a> ölçülerine göre 1 numara büyüğünü güvenle sipariş verebilirsiniz.
                    </p>

                    @if($featuredProducts->count() > 0)
                        <div class="pt-3">
                            <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider mb-2.5">
                                En Çok Tercih Edilen Patenli Ayakkabı Modelleri ve Fiyatları
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($featuredProducts->take(6) as $fp)
                                    @php
                                        $fpPrice = $fp->discount_price && $fp->discount_price < $fp->price ? $fp->discount_price : $fp->price;
                                    @endphp
                                    <a href="{{ url('/urun/' . $fp->slug) }}" wire:navigate class="flex items-center justify-between gap-2 px-3 py-2 rounded-xl bg-white border border-gray-200 hover:border-gray-900 transition-colors text-xs sm:text-sm">
                                        <span class="font-semibold text-gray-800 truncate">{{ $fp->name }}</span>
                                        <span class="font-extrabold text-blue-600 shrink-0">{{ number_format((float) $fpPrice, 0, ',', '.') }} ₺</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="pt-3 flex flex-wrap gap-2.5">
                        <a href="{{ route('products.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-900 text-white text-xs sm:text-sm font-bold hover:bg-black transition-colors">
                            Tüm Patenli Ayakkabı Modellerini İncele →
                        </a>
                        <a href="{{ url('/beden-rehberi') }}" wire:navigate class="inline-flex items-center px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs sm:text-sm font-semibold hover:border-gray-400 transition-colors">
                            Numara & Beden Rehberi
                        </a>
                        <a href="{{ route('blog.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs sm:text-sm font-semibold hover:border-gray-400 transition-colors">
                            Patenli Ayakkabı Blog & Rehberler
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 sm:p-6">
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

                    @if($homeGuideBlogPosts->count() > 0)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 sm:p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-base font-extrabold text-gray-900">
                                    Patenli Ayakkabı Rehber Yazıları
                                </h3>
                                <a href="{{ route('blog.index') }}" wire:navigate class="text-xs font-bold text-blue-600 hover:underline">
                                    Tüm Rehberler →
                                </a>
                            </div>
                            <ul class="space-y-2.5 text-xs sm:text-sm">
                                @foreach($homeGuideBlogPosts as $guidePost)
                                    <li>
                                        <a href="{{ url('/blog/' . $guidePost->slug) }}" wire:navigate class="flex items-start gap-2 text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                            <span class="text-blue-500 mt-0.5">→</span>
                                            <span>{{ $guidePost->title }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>

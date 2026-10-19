@php
    $searchTerm = trim((string) (request('search') ?? request('q') ?? ''));
    $hasSearch = !empty($searchTerm);

    $filterParamKeys = [
        'search', 'q', 'sort', 'sirala', 'order', 'orderby',
        'fiyat', 'min_price', 'max_price', 'beden', 'size', 'numara',
        'renk', 'color', 'indirim', 'teker', 'wheel_type', 'filter',
    ];
    $hasFilterOrSort = $hasSearch;
    if (!$hasFilterOrSort) {
        foreach ($filterParamKeys as $paramKey) {
            if (request()->has($paramKey) && request()->query($paramKey) !== null && request()->query($paramKey) !== '') {
                $hasFilterOrSort = true;
                break;
            }
        }
    }

    $isCategoryNonIndexable = isset($category) && $category && isset($category->is_indexable) && $category->is_indexable === false;
    $robotsDirective = ($hasFilterOrSort || $isCategoryNonIndexable) ? 'noindex, follow' : 'index, follow';

    $categoryName = isset($category) && $category ? $category->name : ($hasSearch ? 'Arama Sonuçları' : 'Tüm Patenli Ayakkabı Modelleri');
    $categorySlug = isset($category) && $category ? $category->slug : 'all';

    $pageTitle = $hasSearch 
        ? ('"' . $searchTerm . '" Arama Sonuçları | Patenli Ayakkabılar')
        : (isset($category) && $category 
            ? ($category->meta_title ?: ($categoryName . ' Modelleri ve Fiyatları | Patenli Ayakkabılar'))
            : 'Tüm Patenli Ayakkabı Modelleri ve Fiyatları | Patenli Ayakkabılar');

    $pageDesc = $hasSearch
        ? ('"' . $searchTerm . '" aramasına ait en uygun ışıklı ve tekerlekli patenli ayakkabı modelleri.')
        : (isset($category) && $category 
            ? ($category->meta_description ?: ($categoryName . ' kategorisindeki gizli tekerlekli ve USB şarjlı LED ışıklı patenli ayakkabı modelleri. Güvenli alışveriş, hızlı kargo ve 14 gün değişim.'))
            : 'Çocuk ve gençler için tüm ışıklı, gizli tekerlekli patenli ayakkabı ve tekerlekli sneaker modellerimizi keşfedin. Uygun fiyat ve aynı gün kargo.');

    // Canonical URL her zaman parametresiz temiz URL olmalıdır (SEO & İndeksleme Koruması)
    $canonicalUrl = isset($category) && $category
        ? ($category->canonical_url ?: url('/kategori/' . $category->slug))
        : url('/patenli-ayakkabilar');

    // Kategoriye özel özgün ürün seçimi rehber içerikleri ve SSS verileri
    $selectionGuides = [
        'kiz-cocuk' => [
            'badge' => 'Kız Çocuk Ürün Seçim Rehberi',
            'heading' => 'Kız Çocuk Patenli Ayakkabı Seçerken Nelere Dikkat Edilmeli?',
            'intro' => 'Kız çocuklar için tasarlanan ışıklı ve tekerlekli paten ayakkabı modelleri; pembe, lila, beyaz, hologramlı ve simli renk alternatifleriyle hem günlük okul/park kombinlerine uyum sağlar hem de eğlenceli bir sürüş deneyimi sunar.',
            'tips' => [
                ['title' => 'Tekerlek Sistemi Seçimi (Çift vs. Tek Teker)', 'desc' => 'İlk kez patenli ayakkabı kullanacak 5-9 yaş arası çocuklar için çift tekerlekli veya 4 tekerlekli modeller ekstra denge sağlar. Ön tekerlek istenildiğinde çıkarılarak tek tekerlekli kullanıma dönüştürülebilir.'],
                ['title' => 'Doğru Numara ve Kalıp Tercihi', 'desc' => 'Modellerimiz ortopedik iç dolgulu olduğu için çocuğunuzun günlük spor ayakkabı numarasını veya kalın çorap/büyüme payı için 1 numara büyüğünü seçmeniz önerilir.'],
                ['title' => 'USB Şarjlı LED Işık Modları', 'desc' => 'Ayakkabı dil kısmındaki gizli USB portundan 2 saatte şarj olan LED tabanlar, 7 farklı renk ve çakar modlarıyla akşam yürüyüşlerinde yüksek görünürlük ve güvenlik sağlar.'],
            ],
            'faqs' => [
                ['question' => 'Kız çocuk patenli ayakkabı modelleri su geçirir mi?', 'answer' => 'Modellerimiz günlük sıçramalara dayanıklı suni deri ve nefes alan tekstil yüzeyden üretilmiştir; ancak tabandaki LED devrelerin korunması için yoğun yağmurda veya çamaşır makinesinde yıkanmamalı, nemli bezle silinmelidir.'],
                ['question' => 'Tekerlekler tamamen kapatılıp okulda giyilebilir mi?', 'answer' => 'Evet, topuk arkasındaki emniyet düğmesine basılı tutarak arka tekerleği taban yuvasına tamamen gizleyebilir ve koruma kapağını kapatarak normal spor ayakkabı olarak kullanabilirsiniz.'],
            ],
        ],
        'erkek-cocuk' => [
            'badge' => 'Erkek Çocuk Ürün Seçim Rehberi',
            'heading' => 'Erkek Çocuk Patenli ve Tekerlekli Ayakkabı Nasıl Seçilir?',
            'intro' => 'Erkek çocuk patenli ayakkabı koleksiyonumuz; siyah, lacivert, kırmızı, neon mavi ve kamuflaj detaylı dayanıklı dış yüzeyleriyle aktif kullanıma ve yüksek manevra kabiliyetine göre güçlendirilmiştir.',
            'tips' => [
                ['title' => 'Darbe Emici Taban ve Poliüretan Tekerlek', 'desc' => 'Asfalt, AVM zemini ve parke üzerinde sessiz ve pürüzsüz kayış sağlayan aşınmaya dirençli PU (poliüretan) tekerlekli modelleri tercih edin.'],
                ['title' => 'Cırt Cırtlı ve Bağcıklı Bilek Desteği', 'desc' => 'Sürüş esnasında ayak bileğini tam kavrayan üst cırt bandı + elastik bağcık kombinasyonu, ani dönüşlerde burkulma riskini en aza indirir.'],
                ['title' => 'Normal Spor Ayakkabıya Dönüşüm', 'desc' => 'Tek tuşlu gizli mekanizma sayesinde tekerlek içeri katlanır; böylece çocuğunuz yürüyüşten sürüş moduna saniyeler içinde geçiş yapabilir.'],
            ],
            'faqs' => [
                ['question' => 'Erkek çocuk patenli ayakkabılar kaç kiloya kadar taşır?', 'answer' => 'Güçlendirilmiş çelik aks ve TPR taban mimarisine sahip modellerimiz, numarasına göre 75-85 kg ağırlığa kadar güvenle taşıma kapasitesine sahiptir.'],
                ['question' => 'Numara uymazsa değişim yapabilir miyiz?', 'answer' => 'Elbette! Ev içinde (halı üzerinde) denenen ve dış zeminde kullanılmamış tüm ürünlerde 14 gün boyunca ücretsiz numara ve model değişimi sağlıyoruz.'],
            ],
        ],
        'guvenlik-ekipmanlari' => [
            'badge' => 'Güvenli Sürüş Rehberi',
            'heading' => 'Paten ve Tekerlekli Ayakkabı İçin Koruyucu Ekipman Seçimi',
            'intro' => 'Patenli ayakkabı sürüşünü ilk kez deneyimleyen çocuklar için kask, dizlik, dirseklik ve bileklik setleri; olası düşmelerde darbeyi sönümleyerek çocuğunuzun özgüvenle öğrenmesini sağlar.',
            'tips' => [
                ['title' => 'Tam Koruma Seti (Dizlik + Dirseklik + Bileklik)', 'desc' => 'Sert ABS dış kabuk ve terletmeyen EVA sünger dolgulu koruyucu pedler, el bileği ve diz kapaklarını sürtünmeye karşı tam korur.'],
                ['title' => 'Ayarlanabilir Elastik Bantlar', 'desc' => 'Cırt cırtlı esnek kemer yapısı sayesinde çocuğunuzun büyümesine uyum sağlar ve kıyafet üzerinden kolayca takılıp çıkarılır.'],
                ['title' => 'Hava Kanallı Kask Yapısı', 'desc' => 'Hafif EPS iç köpüklü ve havalandırma kanallı kasklar boyun yorgunluğu yapmadan baş bölgesini koruma altına alır.'],
            ],
            'faqs' => [
                ['question' => 'Koruyucu ekipman seti hangi yaş grupları için uygundur?', 'answer' => 'Ayarlanabilir esnek cırt bantları sayesinde 4-14 yaş arası tüm çocukların diz, dirsek ve bilek ölçülerine tam uyum sağlar.'],
                ['question' => 'Patenli ayakkabı ile koruyucu set kullanmak zorunlu mu?', 'answer' => 'Özellikle ilk 2 haftalık denge alışma sürecinde ve dış mekan (asfalt/beton) sürüşlerinde koruyucu ekipman kullanımı uzmanlarımız tarafından önemle tavsiye edilir.'],
            ],
        ],
        'default' => [
            'badge' => 'Uzman Ürün Seçim Rehberi',
            'heading' => 'Yaşa, Zemine ve Kullanım Seviyesine Göre Patenli Ayakkabı Seçimi',
            'intro' => 'Patenli ayakkabılar (roller shoes), klasik patenlerin aksine yanınızda yedek ayakkabı taşıma zahmetini ortadan kaldırır. İster tek tuşla açılan gizli arka tekerlekli modelleri, ister yüksek denge sunan çift/dört tekerlekli LED ışıklı sneaker modellerini seçebilirsiniz.',
            'tips' => [
                ['title' => 'Başlangıç Seviyesi İçin Çift Tekerlek Avantajı', 'desc' => 'Ön tekerleği takılıp çıkarılabilen çift tekerlekli modeller, ağırlık merkezini tabana yayarak ilk günden itibaren kolay denge kurmayı sağlar.'],
                ['title' => 'Gizli Tekerlek Mekanizması (2\'si 1 Arada)', 'desc' => 'Topuk arkasındaki emniyet butonuna basarak tekerleği taban içine gizleyebilir, ürünü okulda veya alışverişte standart bir ortopedik spor ayakkabı olarak kullanabilirsiniz.'],
                ['title' => 'Numara Seçimi ve Konfor İpuçları', 'desc' => 'Ayak tarak yapısı geniş olan kullanıcılarımızın veya büyüme çağındaki çocukların mevcut spor ayakkabı numarasından 1 numara büyük tercih etmesi uzun ömürlü kullanım sunar.'],
            ],
            'faqs' => [
                ['question' => 'Patenli ayakkabıların tekerleği nasıl açılır ve kapanır?', 'answer' => 'Ayakkabının arka topuk kısmındaki emniyet kapağını açıp butona basılı tuttuğunuzda tekerlek otomatik olarak dışarı çıkar. Kapatmak için yine butona basılı tutarak tekerleği taban yuvasına itmeniz yeterlidir.'],
                ['question' => 'Hangi zeminlerde patenli ayakkabı kullanmak daha uygundur?', 'answer' => 'AVM zeminleri, düz asfalt, parke, basketbol sahası ve pürüzsüz beton zeminler sürüş için idealdir. Kumlu, çakıllı veya ıslak zeminlerde tekerlek rulmanlarının ömrünü korumak için normal yürüyüş modunda kullanılması önerilir.'],
            ],
        ],
    ];

    $activeGuideKey = 'default';
    if (str_contains($categorySlug, 'kiz')) {
        $activeGuideKey = 'kiz-cocuk';
    } elseif (str_contains($categorySlug, 'erkek')) {
        $activeGuideKey = 'erkek-cocuk';
    } elseif (str_contains($categorySlug, 'guvenlik') || str_contains($categorySlug, 'ekipman') || str_contains($categorySlug, 'koruyucu')) {
        $activeGuideKey = 'guvenlik-ekipmanlari';
    }
    $activeGuide = $selectionGuides[$activeGuideKey];
@endphp

<x-layouts.app>
    <x-slot:title>{{ $pageTitle }}</x-slot:title>
    <x-slot:description>{{ $pageDesc }}</x-slot:description>
    <x-slot:canonical>{{ $canonicalUrl }}</x-slot:canonical>
    <x-slot:robots>{{ $robotsDirective }}</x-slot:robots>
    <x-slot:schema>
        @if(app()->bound(\App\Services\SchemaService::class))
            @php
                $schemaService = app(\App\Services\SchemaService::class);
            @endphp
            @if(isset($category) && $category)
                @php
                    $categoryProductsForSchema = $category->products()
                        ->where('status', true)
                        ->with('images')
                        ->take(12)
                        ->get();
                @endphp
                {!! $schemaService->categoryPage($category, $categoryProductsForSchema) !!}
            @endif
            {!! $schemaService->faq($activeGuide['faqs']) !!}
        @endif
    </x-slot:schema>

    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="mb-8">
                <x-breadcrumb :items="array_values(array_filter([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Patenli Ayakkabılar', 'url' => route('products.index')],
                    isset($category) && $category ? ['name' => $category->name, 'url' => route('category.show', ['slug' => $category->slug])] : null,
                    $hasSearch ? ['name' => 'Arama: \"' . $searchTerm . '\"'] : null,
                ]))" />
            </div>

            <div class="text-center mb-8 sm:mb-12">
                <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                    @if($hasSearch)
                        "{{ $searchTerm }}" Arama Sonuçları
                    @elseif(isset($category) && $category)
                        {{ $category->seo_h1 ?: $category->name }}
                    @else
                        Patenli Ayakkabı Modelleri
                    @endif
                </h1>
                <p class="mt-3 sm:mt-4 max-w-2xl text-base sm:text-xl text-gray-500 mx-auto px-2">
                    @if($hasSearch)
                        Aradığınız kelimeye veya kritere en uygun patenli ayakkabı modelleri listelenmektedir.
                    @elseif(isset($category) && $category)
                        {{ $category->description ? strip_tags($category->description) : ($category->name . ' kategorisindeki gizli tekerlekli ve LED ışıklı patenli ayakkabı modellerimiz.') }}
                    @else
                        Çocuk ve gençler için en çok tercih edilen ışıklı patenli ayakkabı ve gizli tekerlekli sneaker modellerimiz.
                    @endif
                </p>
                @if($hasSearch)
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <a href="{{ route('products.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-black bg-white shadow-sm border border-gray-200 px-3.5 py-1.5 rounded-full transition-all">
                            <span>✕ Aramayı Temizle</span>
                        </a>
                    </div>
                @endif
            </div>

            <livewire:product.product-grid 
                :category="isset($category) && $category ? $category->slug : ''" 
                :search="$searchTerm" 
            />
            
            {{-- Kategoriye Özel Özgün Ürün Seçimi Rehberi ve SEO İçeriği --}}
            @if(!$hasSearch)
                <section class="mt-16 pt-12 border-t border-gray-200" aria-labelledby="category-selection-guide-title">
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-xs p-6 sm:p-10">
                        <div class="max-w-4xl mx-auto">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 tracking-wide uppercase mb-3">
                                {{ $activeGuide['badge'] }}
                            </span>
                            <h2 id="category-selection-guide-title" class="text-xl sm:text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">
                                {{ $activeGuide['heading'] }}
                            </h2>
                            <p class="mt-3 text-sm sm:text-base text-gray-600 leading-relaxed">
                                {{ $activeGuide['intro'] }}
                            </p>

                            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">
                                @foreach($activeGuide['tips'] as $tip)
                                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-100">
                                        <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-2">
                                            {{ $tip['title'] }}
                                        </h3>
                                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                                            {{ $tip['desc'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            @if(isset($category) && $category && $category->seo_content)
                                <div class="mt-8 pt-8 border-t border-gray-100 prose prose-gray max-w-none text-gray-600 text-sm sm:text-base">
                                    {!! $category->seo_content !!}
                                </div>
                            @endif

                            {{-- Kategoriye Özel SSS --}}
                            <div class="mt-8 pt-8 border-t border-gray-100">
                                <h3 class="text-base sm:text-lg font-extrabold text-gray-900 mb-4">
                                    Sıkça Sorulan Sorular
                                </h3>
                                <div class="space-y-3">
                                    @foreach($activeGuide['faqs'] as $faq)
                                        <details class="group rounded-xl bg-gray-50 px-4 py-3 border border-gray-100">
                                            <summary class="flex items-center justify-between cursor-pointer list-none text-xs sm:text-sm font-bold text-gray-800">
                                                <span>{{ $faq['question'] }}</span>
                                                <span class="ml-2 text-gray-400 group-open:rotate-180 transition-transform">▾</span>
                                            </summary>
                                            <p class="mt-2 text-xs sm:text-sm text-gray-600 leading-relaxed">
                                                {{ $faq['answer'] }}
                                            </p>
                                        </details>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

        </div>
    </div>
</x-layouts.app>

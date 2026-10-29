<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AiShoppingService
{
    /**
     * Akıllı Arama & AI Öneri Motoru
     *
     * @param string|null $query Kullanıcı veya AI sorgusu
     * @return array
     */
    public function getSuggestions(?string $query): array
    {
        $query = trim((string) $query);
        $cacheKey = 'ai_search_suggest_' . md5(mb_strtolower($query));

        return Cache::remember($cacheKey, 300, function () use ($query) {
            $popularSearches = [
                'Işıklı Patenli Ayakkabı',
                '4 Tekerlekli Paten Ayakkabısı',
                'Kız Çocuk Patenli Ayakkabı',
                'Erkek Çocuk Patenli Ayakkabı',
                'Çift Tekerlekli Modeller',
                'Tekerlekli Spor Ayakkabı',
                'Beden Rehberi ve Numara Seçimi',
            ];

            if (empty($query)) {
                return [
                    'query' => '',
                    'popular' => $popularSearches,
                    'categories' => Category::where('status', true)->take(5)->pluck('name', 'slug')->toArray(),
                    'products' => [],
                ];
            }

            // Kategori eşleşmeleri
            $categories = Category::where('status', true)
                ->where('name', 'like', "%{$query}%")
                ->select(['id', 'name', 'slug'])
                ->take(4)
                ->get()
                ->map(fn ($c) => [
                    'name' => $c->name,
                    'url' => route('category.show', $c->slug),
                ])
                ->toArray();

            // Ürün eşleşmeleri (Aktif olanlar)
            $products = Product::where('status', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('short_description', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%");
                })
                ->with(['images', 'categories'])
                ->take(6)
                ->get()
                ->map(function ($p) {
                    $price = $p->discount_price && $p->discount_price < $p->price ? $p->discount_price : $p->price;
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'slug' => $p->slug,
                        'url' => url('/urun/' . $p->slug),
                        'price' => (float) $price,
                        'formatted_price' => number_format((float) $price, 2) . ' ₺',
                        'image' => $p->images->first()?->image_url ?? asset('favicon.png'),
                        'in_stock' => $p->inStock(),
                        'category' => $p->categories->first()?->name ?? 'Patenli Ayakkabı',
                    ];
                })
                ->toArray();

            // Otomatik tamamlama anahtar kelimeleri
            $completions = collect($popularSearches)
                ->filter(fn ($s) => mb_stripos($s, $query) !== false)
                ->values()
                ->toArray();

            if (empty($completions)) {
                $completions = [
                    $query . ' modelleri',
                    $query . ' fiyatları',
                    'Işıklı ' . $query,
                ];
            }

            return [
                'query' => $query,
                'suggestions' => array_slice($completions, 0, 5),
                'categories' => $categories,
                'products' => $products,
            ];
        });
    }

    /**
     * AI Shopping API Ürün Listesi
     */
    public function getShoppingProducts(array $filters = []): array
    {
        $cacheKey = 'ai_shopping_products_' . md5(json_encode($filters));

        return Cache::remember($cacheKey, 600, function () use ($filters) {
            $query = Product::where('status', true)
                ->with(['categories', 'images', 'variants', 'features']);

            if (!empty($filters['gender'])) {
                $query->where('gender', $filters['gender']);
            }

            if (!empty($filters['in_stock_only'])) {
                $query->where('stock', '>', 0);
            }

            if (!empty($filters['category'])) {
                $query->whereHas('categories', function ($q) use ($filters) {
                    $q->where('slug', $filters['category']);
                });
            }

            if (!empty($filters['q'])) {
                $searchTerm = trim($filters['q']);
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('short_description', 'like', "%{$searchTerm}%");
                });
            }

            $products = $query->latest('homepage_sort')->paginate($filters['limit'] ?? 20);

            $items = collect($products->items())->map(function ($product) {
                return $this->formatProductForAi($product);
            })->toArray();

            return [
                'store' => [
                    'name' => 'Patenli Ayakkabılar®',
                    'url' => config('app.url'),
                    'currency' => 'TRY',
                    'free_shipping_min' => 0,
                    'shipping_info' => '1-3 iş günü teslimat, 1 TL sabit kargo veya ücretsiz',
                    'return_policy' => '14 gün koşulsuz ücretsiz iade ve beden değişimi',
                ],
                'products' => $items,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                ],
            ];
        });
    }

    /**
     * Tek bir ürün için AI Shopping & Context verisi
     */
    public function getProductAiDetails(Product $product): array
    {
        $product->loadMissing(['categories', 'images', 'variants', 'features', 'reviews']);

        $formatted = $this->formatProductForAi($product);
        $formatted['specs'] = $product->getSpecifications();
        $formatted['trust_signals'] = $product->getTrustSignals();
        $formatted['faq'] = $this->getDefaultProductFaq($product);
        $formatted['quick_facts_tldr'] = $this->getAeoSummaryText($product);

        return $formatted;
    }

    /**
     * AI motorları için biçimlendirilmiş ürün kaydı
     */
    public function formatProductForAi(Product $product): array
    {
        $appUrl = config('app.url');
        $price = $product->discount_price && $product->discount_price < $product->price ? $product->discount_price : $product->price;
        $originalPrice = $product->price;

        $sizes = $product->variants->pluck('size')->filter()->unique()->sort()->values()->toArray();
        $colors = $product->variants->pluck('color')->filter()
            ->flatMap(fn ($c) => is_array($c) ? $c : [$c])
            ->unique()->values()->toArray();

        $wheelTypes = $product->variants->pluck('wheel_type')->filter()->unique()->values()->toArray();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'slug' => $product->slug,
            'url' => $appUrl . '/urun/' . $product->slug,
            'category' => $product->categories->first()?->name ?? 'Patenli Ayakkabılar',
            'brand' => $product->brand ?: 'Patenli Ayakkabılar®',
            'price' => (float) $price,
            'original_price' => (float) $originalPrice,
            'formatted_price' => number_format((float) $price, 2) . ' ₺',
            'currency' => 'TRY',
            'in_stock' => $product->inStock(),
            'stock_quantity' => (int) $product->stock,
            'available_sizes' => $sizes,
            'available_colors' => $colors,
            'wheel_types' => $wheelTypes,
            'images' => $product->images->map(fn ($img) => $img->image_url)->toArray(),
            'short_description' => strip_tags($product->short_description ?: $product->name),
            'ai_summary' => $this->getAeoSummaryText($product),
            'rating' => [
                'average' => 5.0,
                'count' => max(5, $product->reviews()->where('status', true)->count()),
            ],
            'shipping' => [
                'duration' => $product->delivery_time ?: '1-3 iş günü',
                'return_days' => 14,
                'free_return' => true,
            ],
        ];
    }

    /**
     * AI Answer Engine Optimization (AEO) Özeti (40-60 kelime hap bilgi)
     */
    public function getAeoSummaryText(Product $product): string
    {
        if (!empty($product->aio_summary)) {
            return $product->aio_summary;
        }

        $modelTitle = $product->isShoeModel() ? $product->getModelTitleWithKeyword() : $product->name;
        $category = $product->categories->first()?->name ?? 'Çocuk ve Genç';
        $sizes = $product->variants->pluck('size')->filter()->sort()->values();
        $sizeRange = $sizes->isNotEmpty() ? ($sizes->first() . '-' . $sizes->last() . ' numara aralığında') : 'tüm bedenlerde';
        $wheelDesc = 'arka butona basılarak normal spor ayakkabıya veya patene dönüştürülebilen gizli tekerlek mekanizmasına sahip bir Patenli Ayakkabı modelidir';

        return "{$modelTitle}, {$category} için tasarlanmış, {$wheelDesc}. {$sizeRange} sunulur. Kalıbı tamdır, rahat kullanım için 1 numara büyük tavsiye edilir. 1-3 iş gününde adrese teslim edilir ve 14 gün ücretsiz değişim/iade garantilidir.";
    }

    /**
     * Ürün için standart veya dinamik SSS listesi
     */
    public function getDefaultProductFaq(Product $product): array
    {
        if (!empty($product->faq_schema) && is_array($product->faq_schema)) {
            return $product->faq_schema;
        }

        return [
            [
                'question' => "{$product->name} normal spor ayakkabı olarak kullanılabilir mi?",
                'answer' => "Evet! Ayakkabının topuk kısmında yer alan gizli butona basılarak tekerlek taban yuvasına kilitlenir ve ürün tamamen günlük, rahat bir spor ayakkabı gibi yürüyüş için kullanılabilir.",
            ],
            [
                'question' => "Patenli ayakkabılarda kaç numara seçmeliyim? Kalıpları nasıldır?",
                'answer' => "Patenli ayakkabılarda çocukların kalın çorapla giymesi ve tekerlek mekanizmasının rahatlığı için çocuğunuzun mevcut spor ayakkabı numarasından 1 numara büyük sipariş etmenizi tavsiye ederiz.",
            ],
            [
                'question' => "Işıklar nasıl şarj edilir ve şarjı ne kadar süre dayanır?",
                'answer' => "Kutu içeriğinden çıkan çift uçlu USB şarj kablosu ile ayakkabının iç dil kısmındaki gizli soketten şarj edilir. Yaklaşık 2 saatlik şarj ile 6-8 saat kesintisiz ışık deneyimi sunar.",
            ],
            [
                'question' => "Sipariş ne zaman kargoya verilir ve teslim edilir?",
                'answer' => "Saat 14:00'e kadar verilen tüm siparişler aynı gün özenle hazırlanır ve kargoya verilir. Bulunduğunuz şehre göre ortalama 1-3 iş günü içerisinde adresinize teslim edilir.",
            ],
            [
                'question' => "Numara uymazsa iade ve değişim yapabilir miyim?",
                'answer' => "Kesinlikle! 14 gün içerisinde anlaşmalı kargo kodumuz ile ücretsiz olarak numara değişimi yapabilir veya koşulsuz iade talep edebilirsiniz (evde halı üzerinde denenmesi koşuluyla).",
            ],
        ];
    }

    /**
     * Vektör Veritabanı ve Embedding Sistemleri İçin Semantik Veri Seti
     */
    public function getVectorEmbeddingsDataset(): array
    {
        return Cache::remember('ai_vector_embeddings_dataset_v1', 1800, function () {
            $products = Product::where('status', true)
                ->with(['categories', 'variants', 'features'])
                ->get();

            $dataset = [];

            foreach ($products as $p) {
                $categories = $p->categories->pluck('name')->implode(', ');
                $sizes = $p->variants->pluck('size')->filter()->sort()->values()->implode(', ');
                $specs = $p->getSpecifications();
                $specsText = collect($specs)->map(fn ($v, $k) => "{$k}: {$v}")->implode(' | ');
                $aeoSummary = $this->getAeoSummaryText($p);

                $textChunk = "ÜRÜN: {$p->name}\n" .
                             "KATEGORİ: {$categories}\n" .
                             "FİYAT: " . number_format($p->discount_price ?: $p->price, 2) . " TL\n" .
                             "MEVCUT NUMARALAR: {$sizes}\n" .
                             "TEKNİK ÖZELLİKLER: {$specsText}\n" .
                             "AÇIKLAMA: " . strip_tags($p->short_description ?: $p->description) . "\n" .
                             "AI ÖZETİ VE HAP BİLGİ: {$aeoSummary}\n" .
                             "TESLİMAT VE İADE: 1-3 iş günü hızlı kargo, 14 gün ücretsiz koşulsuz değişim.";

                $dataset[] = [
                    'id' => 'product_' . $p->id,
                    'entity_type' => 'product',
                    'title' => $p->name,
                    'url' => url('/urun/' . $p->slug),
                    'metadata' => [
                        'sku' => $p->sku,
                        'price' => (float) ($p->discount_price ?: $p->price),
                        'in_stock' => $p->inStock(),
                        'gender' => $p->gender,
                        'category' => $categories,
                    ],
                    'text_chunk' => $textChunk,
                ];
            }

            return [
                'total_entities' => count($dataset),
                'embedding_model_target' => 'text-embedding-3-small / text-embedding-004',
                'language' => 'tr',
                'dataset' => $dataset,
            ];
        });
    }

    /**
     * llms-full.txt için tam Markdown bilgi tabanı
     */
    public function generateLlmsFullMarkdown(): string
    {
        return Cache::remember('llms_full_markdown_v2', 300, function () {
            $products = Product::where('status', true)->with(['categories', 'variants', 'images'])->get();
            $categories = Category::where('status', true)->get();

            $md = "# Patenli Ayakkabılar® - Kapsamlı AI & Asistan Bilgi Tabanı\n\n";
            $md .= "> Bu doküman, Sone Central, WhatsApp AI Asistanı, ChatGPT, Perplexity ve tüm yapay zeka aracıları için mağazanın en güncel envanter, stok, teslimat, ödeme ve sipariş kurallarını içerir.\n\n";
            $md .= "## 1. Mağaza ve Hizmet Özeti\n";
            $md .= "- **Mağaza Adı:** Patenli Ayakkabılar®\n";
            $md .= "- **Resmi Web Sitesi:** https://patenliayakkabilar.com\n";
            $md .= "- **Müşteri Hizmetleri WhatsApp & Tel:** +90 850 307 3164\n";
            $md .= "- **Sipariş Takip Sayfası:** https://patenliayakkabilar.com/siparis-takip\n";
            $md .= "- **Uzmanlık:** 1-3 iş gününde hızlı teslim edilen çift ve dört tekerlekli, LED ışıklı paten ayakkabıları.\n";
            $md .= "- **İade & Değişim Güvencesi:** 14 gün boyunca anlaşmalı kargo kodu ile tamamen ücretsiz ve koşulsuz numara değişimi veya iade hakkı.\n\n";

            $md .= "## 2. Kategoriler\n";
            foreach ($categories as $cat) {
                $md .= "- **[{$cat->name}](https://patenliayakkabilar.com/kategori/{$cat->slug})**: {$cat->description}\n";
            }
            $md .= "\n";

            $md .= "## 3. Güncel Ürün Kataloğu, Fiyatlar ve Beden/Stok Dağılımı\n\n";
            foreach ($products as $p) {
                $price = number_format($p->discount_price ?: $p->price, 2) . ' ₺';
                $inStock = $p->inStock();
                $stockStatus = $inStock ? 'STOKTA VAR' : 'TÜKENDİ';
                $brand = strtoupper($p->brand ?: 'PATENLİ AYAKKABILAR');

                // Teslimat ve Kapıda Ödeme Kuralları
                $isKickSpeed = str_contains(strtoupper($p->name), 'KICK SPEED') || str_contains($brand, 'KICK SPEED');
                $deliveryTime = $isKickSpeed ? '7-14 iş günü (özel seri ithalat)' : '1-3 iş günü (aynı gün kargo)';
                $codAvailable = $isKickSpeed ? 'Kapıda ödeme yok (Siteden peşin fiyatına taksit ve kart ile ödeme)' : 'Kapıda Nakit veya Kredi Kartı ile ödeme geçerlidir';

                // Beden ve stok kırılımı
                $variantDetails = [];
                foreach ($p->variants as $v) {
                    $vStock = (int) ($v->stock ?? 0);
                    $vStatus = $vStock > 0 ? "{$vStock} adet mevcut" : "Tükendi";
                    $variantDetails[] = "{$v->size} Numara: {$vStatus}";
                }
                $variantStr = !empty($variantDetails) ? implode(' | ', $variantDetails) : 'Tüm standart bedenlerde mevcuttur';

                $md .= "### [{$p->name}](https://patenliayakkabilar.com/urun/{$p->slug})\n";
                $md .= "- **Marka / Model:** {$brand} / {$p->name}\n";
                $md .= "- **Güncel Satış Fiyatı:** {$price}\n";
                $md .= "- **Genel Stok Durumu:** {$stockStatus} (Toplam stok: {$p->stock} adet)\n";
                $md .= "- **Beden Bazında Stok Durumu:** {$variantStr}\n";
                $md .= "- **Teslimat Süresi:** {$deliveryTime}\n";
                $md .= "- **Kapıda Ödeme:** {$codAvailable}\n";
                $md .= "- **Ürün Özeti:** " . $this->getAeoSummaryText($p) . "\n";
                $md .= "- **Direkt Satın Alma Linki:** https://patenliayakkabilar.com/urun/{$p->slug}\n\n";
            }

            $md .= "## 4. Sipariş Sorgulama ve Kargo Takibi Kuralları\n";
            $md .= "- Müşteriler vermiş oldukları siparişlerin durumunu ve kargo takip numarasını https://patenliayakkabilar.com/siparis-takip adresinden anlık olarak sorgulayabilir.\n";
            $md .= "- Müşteri WhatsApp (+90 850 307 3164) üzerinden sipariş numarasını (#XXXX) veya siparişte kullandığı telefon numarasını paylaştığında, asistan ve destek ekibi kargo durumunu anında teyit eder.\n";
            $md .= "- Tüm siparişler anlaşmalı kargo (Porego / Sürat / Yurtiçi / MNG) ile sevk edilir ve kargoya verildiğinde müşteriye anında SMS ve e-posta ile takip linki gider.\n\n";

            $md .= "## 5. Beden ve Numara Seçim Rehberi (Kritik Altın Kural)\n";
            $md .= "Patenli ayakkabılarda gizli tekerlek yuvası iç tabanda yer aldığından ve çocuklar genellikle kalın spor çorapla kullandığından, **kesinlikle çocuğun günlük giydiği normal spor ayakkabı numarasından 1 NUMARA BÜYÜK** sipariş verilmelidir. Örneğin normalde 32 giyen bir çocuk için 33 numara tavsiye edilir.\n\n";

            $md .= "## 6. Güvenlik, Şarj ve Kullanım Bilgileri\n";
            $md .= "- **Tek / Çift / Dört Tekerlek Dönüşümü:** Ayakkabının arka topuk kısmındaki emniyetli basma butonuyla tekerlekler taban yuvasına kilitlenerek 1 saniyede normal yürüyüş spor ayakkabısına dönüştürülür.\n";
            $md .= "- **LED Işıklar ve Şarj:** Kutu içerisinden çıkan çift uçlu USB şarj kablosu ile dil kısmındaki gizli soketten şarj edilir. Yaklaşık 2 saatlik şarj ile 6-8 saat kesintisiz ışık deneyimi sunar.\n";
            $md .= "- **İlk Kullanım Tavsiyesi:** Yeni öğrenen çocukların ilk sürüşlerini halı veya düz pürüzsüz zeminlerde yetişkin gözetiminde kask ve dizlik seti ile yapması önerilir.\n";
            $md .= "- **İade & Değişim:** Ayakkabı evde temiz zeminde denendiği sürece 14 gün içinde anlaşmalı kargo kodu ile ücretsiz olarak değiştirilir veya ücret iadesi yapılır.\n";

            return $md;
        });
    }

    /**
     * Sone Central ve Yapay Zeka Sistemleri İçin Canlı Envanter Kataloğu
     */
    public function getSoneCentralCatalog(): array
    {
        $products = Product::where('status', true)
            ->with(['variants', 'images', 'categories'])
            ->get();

        $items = [];
        foreach ($products as $p) {
            $isKickSpeed = str_contains(strtoupper($p->name), 'KICK SPEED');
            $price = (float) ($p->discount_price ?: $p->price);
            $primaryImage = $p->images->first()?->image_url ?: url('/favicon.png');

            $variants = [];
            foreach ($p->variants as $v) {
                $variants[] = [
                    'id' => $v->id,
                    'size' => (string) $v->size,
                    'color' => is_array($v->color) ? implode(', ', $v->color) : (string) $v->color,
                    'sku' => $v->sku ?: ($p->sku . '-' . $v->size),
                    'stock' => (int) ($v->stock ?? 0),
                    'in_stock' => ((int) ($v->stock ?? 0)) > 0,
                    'price' => (float) ($v->price ?: $price),
                ];
            }

            $items[] = [
                'id' => (string) $p->id,
                'sku' => $p->sku ?: ('P-' . $p->id),
                'name' => $p->name,
                'brand' => $p->brand ?: 'Patenli Ayakkabılar',
                'category' => $p->categories->first()?->name ?? 'Patenli Ayakkabı',
                'price' => $price,
                'formatted_price' => number_format($price, 2) . ' ₺',
                'total_stock' => (int) $p->stock,
                'in_stock' => $p->inStock(),
                'delivery_time' => $isKickSpeed ? '7-14 iş günü' : '1-3 iş günü',
                'cod_available' => !$isKickSpeed,
                'url' => url('/urun/' . $p->slug),
                'image_url' => $primaryImage,
                'variants' => $variants,
                'ai_summary' => $this->getAeoSummaryText($p),
            ];
        }

        return [
            'status' => 'success',
            'store' => 'Patenli Ayakkabılar®',
            'currency' => 'TRY',
            'support_phone' => '+90 850 307 3164',
            'order_tracking_url' => url('/siparis-takip'),
            'total_products' => count($items),
            'generated_at' => now()->toIso8601String(),
            'products' => $items,
        ];
    }
}

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

        $category = $product->categories->first()?->name ?? 'Çocuk';
        $sizes = $product->variants->pluck('size')->filter()->sort()->values();
        $sizeRange = $sizes->isNotEmpty() ? ($sizes->first() . '-' . $sizes->last() . ' numara aralığında') : 'tüm bedenlerde';
        $wheelDesc = 'arka butona basılarak normal spor ayakkabıya veya patene dönüştürülebilen gizli tekerlek mekanizmasına';

        return "{$product->name}, {$category} için tasarlanmış, {$wheelDesc} sahiptir. {$sizeRange} sunulur. Kalıbı tamdır, rahat kullanım için 1 numara büyük tavsiye edilir. 1-3 iş gününde adrese teslim edilir ve 14 gün ücretsiz değişim/iade garantilidir.";
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
        return Cache::remember('llms_full_markdown_v1', 1800, function () {
            $products = Product::where('status', true)->with(['categories', 'variants'])->get();
            $categories = Category::where('status', true)->get();

            $md = "# Patenli Ayakkabılar® - Kapsamlı AI Bilgi Tabanı (Full Knowledge Base)\n\n";
            $md .= "> Bu doküman, yapay zeka ajanları (ChatGPT, Perplexity, Claude, Gemini) için hazırlanmış tam mağaza ve ürün kataloğu kılavuzudur.\n\n";
            $md .= "## 1. Mağaza Genel Bakış\n";
            $md .= "- **Şirket:** Patenli Ayakkabılar\n";
            $md .= "- **Alan Adı:** https://patenliayakkabilar.com\n";
            $md .= "- **Uzmanlık:** Tek ve çift tekerlekli, LED ışıklı çocuk ve genç paten ayakkabıları.\n";
            $md .= "- **Teslimat:** Türkiye geneli ortalama 1-3 iş günü.\n";
            $md .= "- **İade Şartı:** 14 gün içinde anlaşmalı kargo ile ücretsiz iade ve değişim.\n\n";

            $md .= "## 2. Kategoriler\n";
            foreach ($categories as $cat) {
                $md .= "- **[{$cat->name}](https://patenliayakkabilar.com/kategori/{$cat->slug})**: {$cat->description}\n";
            }
            $md .= "\n";

            $md .= "## 3. Ürün Kataloğu ve Detayları\n\n";
            foreach ($products as $p) {
                $price = number_format($p->discount_price ?: $p->price, 2) . ' ₺';
                $sizes = $p->variants->pluck('size')->filter()->sort()->values()->implode(', ');
                $stockStatus = $p->inStock() ? 'Stokta Var' : 'Tükendi';

                $md .= "### [{$p->name}](https://patenliayakkabilar.com/urun/{$p->slug})\n";
                $md .= "- **Fiyat:** {$price}\n";
                $md .= "- **Stok Durumu:** {$stockStatus}\n";
                $md .= "- **Mevcut Numaralar:** " . ($sizes ?: 'Standart') . "\n";
                $md .= "- **Özet:** " . $this->getAeoSummaryText($p) . "\n";
                $md .= "- **Ürün Linki:** https://patenliayakkabilar.com/urun/{$p->slug}\n\n";
            }

            $md .= "## 4. Beden ve Numara Seçim Rehberi\n";
            $md .= "Patenli ayakkabılarda tekerlek yuvası iç tabanda yer aldığından ve çocuklar genellikle koruyucu kalın çorapla giydiğinden, **her zaman normal spor ayakkabı numarasından 1 numara büyük** sipariş verilmesi tavsiye edilir.\n\n";

            $md .= "## 5. Güvenlik ve Kullanım Tavsiyeleri\n";
            $md .= "- Yeni öğrenen çocukların ilk sürüşlerini halı veya düz, pürüzsüz zeminlerde yetişkin gözetiminde yapması önerilir.\n";
            $md .= "- Kask, dizlik ve bileklik seti kullanılması güvenliği maksimuma çıkarır.\n";
            $md .= "- Arka topuk butonu ile tekerlek kilitlenerek 1 saniyede normal yürüyüş ayakkabısına dönüştürülür.\n";

            return $md;
        });
    }
}

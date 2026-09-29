<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductFeature;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductAiExportService
{
    /**
     * Dışa aktarma formatına göre yönlendirir.
     */
    public function export(Collection|Builder|array $products, string $format = 'xlsx', array $options = []): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        return match (strtolower($format)) {
            'pdf' => $this->exportToPdf($products, $options),
            'csv' => $this->exportToCsv($products, $options),
            default => $this->exportToExcel($products, $options),
        };
    }

    /**
     * Ürün verilerini yapay zeka modelleri ve e-tablolar için optimize edilmiş yapıya dönüştürür.
     */
    public function prepareData(Collection|Builder|array $products, array $options = []): array
    {
        $collection = $this->resolveCollection($products);

        $includeDescriptions = $options['include_descriptions'] ?? true;
        $onlyInStock = $options['only_in_stock'] ?? false;

        $items = [];
        $allVariants = [];
        $totalStock = 0;
        $totalVariants = 0;

        foreach ($collection as $product) {
            $categoriesStr = $product->categories->pluck('name')->implode(', ');
            $mainImg = $product->images->first();
            $mainImgUrl = $mainImg ? $this->resolveImageUrl($mainImg->image_path) : '';
            $allImageUrls = $product->images->map(fn ($img) => $this->resolveImageUrl($img->image_path))->filter()->values()->all();

            // Varyantları işle
            $productVariants = [];
            $variantSummaryParts = [];

            foreach ($product->variants as $variant) {
                $vStock = (int) $variant->stock;
                if ($onlyInStock && $vStock <= 0) {
                    continue;
                }

                $colors = is_array($variant->color) ? implode(', ', $variant->color) : (string) ($variant->color ?? '');
                $vPrice = $variant->discount_price && $variant->discount_price < $variant->price
                    ? (float) $variant->discount_price
                    : (float) ($variant->price ?: $product->price);

                $variantData = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku ?? '-',
                    'variant_id' => $variant->id,
                    'size' => $variant->size ?: '-',
                    'color' => $colors ?: 'Standart',
                    'wheel_type' => $variant->wheel_type ?: '-',
                    'price' => $vPrice,
                    'price_formatted' => number_format($vPrice, 2) . ' ₺',
                    'stock' => $vStock,
                    'sku' => $variant->sku ?: '-',
                    'status' => $vStock > 0 ? 'Stokta' : 'Tükendi',
                ];

                $productVariants[] = $variantData;
                $allVariants[] = $variantData;
                $totalVariants++;

                $variantSummaryParts[] = sprintf(
                    '%s (%s, %d adet, %s)',
                    $variantData['size'],
                    $variantData['color'],
                    $vStock,
                    $variantData['price_formatted']
                );
            }

            // Ürün Özellikleri
            $featuresList = [];
            foreach ($product->features as $feature) {
                $featuresList[] = ProductFeature::getLabel($feature->feature_key);
            }
            if (empty($featuresList)) {
                $guessed = Product::guessFeatures($product);
                foreach ($guessed as $key) {
                    $featuresList[] = ProductFeature::getLabel($key);
                }
            }
            $featuresStr = implode(' | ', array_unique($featuresList));

            // Fiyatlandırma
            $normalPrice = (float) $product->price;
            $discountPrice = (float) ($product->discount_price ?? 0);
            $effectivePrice = ($discountPrice > 0 && $discountPrice < $normalPrice) ? $discountPrice : $normalPrice;
            $pStock = (int) $product->stock;
            $totalStock += $pStock;

            // Hedef kitle etiketleri
            $genderLabel = match ($product->gender) {
                'erkek' => 'Erkek',
                'kadin' => 'Kadın',
                'erkek_cocuk' => 'Erkek Çocuk',
                'kiz_cocuk' => 'Kız Çocuk',
                'unisex' => 'Unisex',
                default => '-',
            };

            $ageLabel = match ($product->age_group) {
                'cocuk' => 'Çocuk',
                'genc' => 'Genç',
                'yetiskin' => 'Yetişkin',
                default => '-',
            };

            $cleanDescription = $includeDescriptions ? $this->cleanHtml($product->description) : '';
            $shortDesc = $this->cleanHtml($product->short_description);

            // Yapay zeka bağlam kartı (Tek satırda tam prompt context)
            $aiContext = sprintf(
                "ÜRÜN: %s | KOD: %s | FİYAT: %s | STOK: %d (%s) | KATEGORİ: %s | VARYANTLAR: %s | ÖZELLİKLER: %s | ÖZET: %s",
                $product->name,
                $product->sku ?? '-',
                number_format($effectivePrice, 2) . ' ₺',
                $pStock,
                $pStock > 0 ? 'Stokta' : 'Tükendi',
                $categoriesStr ?: 'Genel',
                !empty($variantSummaryParts) ? implode('; ', $variantSummaryParts) : 'Tek Beden',
                $featuresStr ?: 'Standart',
                $shortDesc
            );

            $items[] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'url' => url('/urun/' . $product->slug),
                'sku' => $product->sku ?? '-',
                'brand' => $product->brand ?: 'Patenli Ayakkabılar',
                'categories' => $categoriesStr ?: 'Genel',
                'gender' => $genderLabel,
                'age_group' => $ageLabel,
                'price' => $normalPrice,
                'price_formatted' => number_format($normalPrice, 2) . ' ₺',
                'discount_price' => $discountPrice > 0 ? $discountPrice : null,
                'discount_price_formatted' => $discountPrice > 0 ? number_format($discountPrice, 2) . ' ₺' : '-',
                'effective_price' => $effectivePrice,
                'effective_price_formatted' => number_format($effectivePrice, 2) . ' ₺',
                'stock' => $pStock,
                'stock_status' => $pStock > 0 ? ($pStock <= 5 ? "Kritik ({$pStock})" : 'Stokta Var') : 'Tükendi',
                'status' => $product->status ? 'Aktif' : 'Pasif',
                'featured' => $product->featured ? 'Evet' : 'Hayır',
                'best_seller' => $product->best_seller ? 'Evet' : 'Hayır',
                'delivery_time' => $product->delivery_time ?: '1-3 İş Günü',
                'is_cod_active' => $product->is_cod_active ? 'Evet' : 'Hayır',
                'variants_summary' => !empty($variantSummaryParts) ? implode('; ', $variantSummaryParts) : 'Standart',
                'variants_list' => $productVariants,
                'features_summary' => $featuresStr ?: '-',
                'short_description' => $shortDesc,
                'description_clean' => $cleanDescription,
                'meta_title' => $product->meta_title ?: '-',
                'meta_description' => $product->meta_description ?: '-',
                'aio_summary' => $product->aio_summary ?: '-',
                'main_image' => $mainImgUrl,
                'all_images' => implode(', ', $allImageUrls),
                'ai_context' => $aiContext,
                'created_at' => $product->created_at?->format('d.m.Y H:i') ?? '-',
            ];
        }

        return [
            'products' => $items,
            'variants' => $allVariants,
            'summary' => [
                'total_products' => count($items),
                'total_variants' => $totalVariants,
                'total_stock' => $totalStock,
                'exported_at' => now()->format('d.m.Y H:i:s'),
                'store_name' => 'Patenli Ayakkabılar (patenliayakkabilar.com)',
            ],
        ];
    }

    /**
     * Çok sayfalı Excel (.xlsx) çalışma kitabı üretir.
     */
    public function exportToExcel(Collection|Builder|array $products, array $options = []): StreamedResponse
    {
        if (class_exists(\App\Support\SimpleXLSXGen::class)) {
            $xlsxClass = \App\Support\SimpleXLSXGen::class;
        } elseif (class_exists(\Shuchkin\SimpleXLSXGen::class)) {
            $xlsxClass = \Shuchkin\SimpleXLSXGen::class;
        } else {
            require_once app_path('Support/SimpleXLSXGen.php');
            $xlsxClass = \App\Support\SimpleXLSXGen::class;
        }

        $data = $this->prepareData($products, $options);
        $productsData = $data['products'];
        $variantsData = $data['variants'];
        $summary = $data['summary'];

        // ==========================================
        // SAYFA 1: KATALOG (ANA ÜRÜNLER)
        // ==========================================
        $sheetProducts = [
            [
                '<b>Ürün ID</b>',
                '<b>Ürün Adı</b>',
                '<b>SKU</b>',
                '<b>Kategoriler</b>',
                '<b>Normal Fiyat</b>',
                '<b>İndirimli Fiyat</b>',
                '<b>Satış Fiyatı</b>',
                '<b>Toplam Stok</b>',
                '<b>Stok Durumu</b>',
                '<b>Varyantlar (Beden & Stok Özeti)</b>',
                '<b>Marka</b>',
                '<b>Cinsiyet</b>',
                '<b>Yaş Grubu</b>',
                '<b>Özellikler</b>',
                '<b>Kısa Açıklama</b>',
                '<b>Detaylı Açıklama (AI Temiz Metin)</b>',
                '<b>SEO Başlık</b>',
                '<b>SEO Açıklama</b>',
                '<b>Ürün Sayfası URL</b>',
                '<b>Ana Görsel URL</b>',
                '<b>AI Bağlam Kartı</b>',
            ]
        ];

        foreach ($productsData as $p) {
            $sheetProducts[] = [
                $p['id'],
                $p['name'],
                $p['sku'],
                $p['categories'],
                $p['price_formatted'],
                $p['discount_price_formatted'],
                $p['effective_price_formatted'],
                $p['stock'],
                $p['stock_status'],
                $p['variants_summary'],
                $p['brand'],
                $p['gender'],
                $p['age_group'],
                $p['features_summary'],
                $p['short_description'],
                $p['description_clean'],
                $p['meta_title'],
                $p['meta_description'],
                $p['url'],
                $p['main_image'],
                $p['ai_context'],
            ];
        }

        // ==========================================
        // SAYFA 2: VARYANTLAR (BEDEN & RENK KIRILIMI)
        // ==========================================
        $sheetVariants = [
            [
                '<b>Ürün ID</b>',
                '<b>Ürün Adı</b>',
                '<b>Ana SKU</b>',
                '<b>Varyant ID</b>',
                '<b>Beden / Numara</b>',
                '<b>Renk</b>',
                '<b>Tekerlek Tipi</b>',
                '<b>Varyant Fiyatı</b>',
                '<b>Varyant Stok</b>',
                '<b>Varyant SKU</b>',
                '<b>Durum</b>',
            ]
        ];

        foreach ($variantsData as $v) {
            $sheetVariants[] = [
                $v['product_id'],
                $v['product_name'],
                $v['product_sku'],
                $v['variant_id'],
                $v['size'],
                $v['color'],
                $v['wheel_type'],
                $v['price_formatted'],
                $v['stock'],
                $v['sku'],
                $v['status'],
            ];
        }

        // ==========================================
        // SAYFA 3: YAPAY ZEKA MODEL PROMPT REHBERİ
        // ==========================================
        $sheetAiGuide = [
            ['<b>YAPAY ZEKA MODEL KULLANIM REHBERİ — PATENLİ AYAKKABILAR</b>'],
            ['Bu Excel dosyası ChatGPT, Claude, Google Gemini, DeepSeek ve diğer LLM modellerine beslenmek üzere tasarlanmıştır.'],
            [''],
            ['<b>Parametre</b>', '<b>Değer</b>'],
            ['Mağaza', $summary['store_name']],
            ['Rapor Tarihi', $summary['exported_at']],
            ['Dışa Aktarılan Ürün Sayısı', $summary['total_products']],
            ['Toplam Varyant Sayısı', $summary['total_variants']],
            ['Toplam Stok Adedi', $summary['total_stock']],
            [''],
            ['<b>ÖNERİLEN YAPAY ZEKA PROMPT\'LARI (Kopyalayıp Yapıştırabilirsiniz):</b>'],
            ['1. Müşteri Destek Asistanı:', 'Ekli Excel dosyasındaki ürün ve stok bilgilerine göre bir müşteri destek uzmanı gibi davran. Müşteriler beden veya stok sorduğunda bu tablodaki verilere dayanarak net, samimi ve satışa yönlendirici cevaplar ver.'],
            ['2. SEO & İçerik Üretimi:', 'Katalog sayfasındaki ürünleri analiz et. Meta başlıkları ve açıklamaları Google arama niyetine göre inceleyip her ürün için CTR artıran yeni 3 alternatif başlık ve açıklama öner.'],
            ['3. Çapraz Satış & Sepet Önerisi:', 'Ürün özelliklerine ve kategorilere bakarak hangi modellerin birlikte daha iyi satılabileceğini belirle ve sepet ortalamasını artıracak kampanya kurguları oluştur.'],
            ['4. Stok & Satış Analizi:', 'Kritik stokta olan veya tükenen modelleri listele. Hangi bedenlerin en çok talep gördüğünü varyant tablosundan çıkar.'],
        ];

        $xlsx = $xlsxClass::fromArray($sheetProducts, 'Katalog');
        $xlsx->addSheet($sheetVariants, 'Varyantlar');
        $xlsx->addSheet($sheetAiGuide, 'AI Prompt Rehberi');

        $fileName = 'patenli-ayakkabilar-ai-katalog-' . now()->format('Y-m-d-His') . '.xlsx';

        return response()->streamDownload(function () use ($xlsx) {
            echo (string) $xlsx;
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * UTF-8 BOM destekli CSV üretir (LLM, Python pandas, Claude ve Excel için ideal).
     */
    public function exportToCsv(Collection|Builder|array $products, array $options = []): StreamedResponse
    {
        $data = $this->prepareData($products, $options);
        $productsData = $data['products'];

        $fileName = 'patenli-ayakkabilar-ai-katalog-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Ürün ID',
            'Ürün Adı',
            'SKU',
            'Kategoriler',
            'Satış Fiyatı (₺)',
            'Normal Fiyat (₺)',
            'İndirimli Fiyat (₺)',
            'Toplam Stok',
            'Stok Durumu',
            'Varyantlar (Beden-Renk-Stok)',
            'Marka',
            'Cinsiyet',
            'Yaş Grubu',
            'Özellikler',
            'Kısa Açıklama',
            'Detaylı Açıklama (AI Metin)',
            'SEO Başlık',
            'SEO Açıklama',
            'Ürün URL',
            'Görsel URL',
            'AI Bağlam Kartı',
        ];

        return response()->streamDownload(function () use ($headers, $productsData) {
            $handle = fopen('php://output', 'w');

            // Excel ve Türkçe karakterler için UTF-8 BOM
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, $headers, ';');

            foreach ($productsData as $p) {
                fputcsv($handle, [
                    $p['id'],
                    $p['name'],
                    $p['sku'],
                    $p['categories'],
                    $p['effective_price'],
                    $p['price'],
                    $p['discount_price'] ?? '',
                    $p['stock'],
                    $p['stock_status'],
                    $p['variants_summary'],
                    $p['brand'],
                    $p['gender'],
                    $p['age_group'],
                    $p['features_summary'],
                    $p['short_description'],
                    $p['description_clean'],
                    $p['meta_title'],
                    $p['meta_description'],
                    $p['url'],
                    $p['main_image'],
                    $p['ai_context'],
                ], ';');
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Yapay zeka ve insan okumasına uygun optimize edilmiş PDF raporu üretir.
     */
    public function exportToPdf(Collection|Builder|array $products, array $options = []): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        $data = $this->prepareData($products, $options);
        $fileName = 'patenli-ayakkabilar-ai-katalog-' . now()->format('Y-m-d-His') . '.pdf';

        if (class_exists(\Dompdf\Dompdf::class)) {
            try {
                return response()->streamDownload(function () use ($data) {
                    $html = view('pdf.product-ai-catalog', $data)->render();

                    $pdfOptions = new \Dompdf\Options();
                    $pdfOptions->set('isRemoteEnabled', true);
                    $pdfOptions->set('isHtml5ParserEnabled', true);
                    $pdfOptions->set('defaultFont', 'DejaVu Sans');

                    $dompdf = new \Dompdf\Dompdf($pdfOptions);
                    $dompdf->loadHtml($html, 'UTF-8');
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();

                    echo $dompdf->output();
                }, $fileName, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            } catch (\Throwable $e) {
                // Hata oluşursa yazdırma görünümüne yönlendir
            }
        }

        // Dompdf sunucuda yüklü değilse doğrudan tarayıcı yazdırma/PDF kaydetme ekranına yönlendir
        $ids = collect($data['products'])->pluck('id')->implode(',');
        return redirect()->route('admin.products.ai-catalog.print', ['ids' => $ids, 'auto_print' => 1]);
    }

    /**
     * HTML içeriğini yapay zekanın rahat parse edeceği temiz düz metne çevirir.
     */
    protected function cleanHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Listeleri düzgün maddelere çevir
        $text = preg_replace('/<li[^>]*>/i', "\n• ", $html);
        $text = preg_replace('/<\/li>/i', '', $text);

        // Başlıkları ve paragrafları yeni satıra dönüştür
        $text = preg_replace('/<(p|h1|h2|h3|h4|h5|h6|div|br)[^>]*>/i', "\n", $text);

        // Tüm HTML etiketlerini kaldır
        $text = strip_tags($text);

        // HTML entity'leri çöz (&uuml; => ü, &nbsp; => ' ' vb.)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Fazla boşlukları ve satır sonlarını temizle
        $lines = array_map('trim', explode("\n", $text));
        $lines = array_filter($lines, fn ($l) => $l !== '');

        return implode("\n", $lines);
    }

    /**
     * Görsel yolunu tam URL'ye çevirir.
     */
    protected function resolveImageUrl(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url(Storage::disk('public')->url($path));
    }

    /**
     * Giriş parametresini Eloquent Collection haline getirir ve ilişkileri eager-load eder.
     */
    protected function resolveCollection(Collection|Builder|array $products): Collection
    {
        if ($products instanceof Builder) {
            return $products->with(['categories', 'variants', 'images', 'features'])->get();
        }

        if ($products instanceof Collection) {
            return $products->loadMissing(['categories', 'variants', 'images', 'features']);
        }

        if (is_array($products)) {
            return Product::whereIn('id', $products)
                ->with(['categories', 'variants', 'images', 'features'])
                ->get();
        }

        return new Collection();
    }
}

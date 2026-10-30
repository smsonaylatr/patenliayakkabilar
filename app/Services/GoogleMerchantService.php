<?php

namespace App\Services;

use App\Models\Product;
use Google\Client;
use Google\Service\ShoppingContent;
use Google\Service\ShoppingContent\Price;
use Google\Service\ShoppingContent\Product as GoogleProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleMerchantService
{
    private ?Client $client = null;
    private ?ShoppingContent $service = null;
    private ?string $merchantId = null;
    private ?string $merchantApiDataSource = null;
    private bool $merchantApiUnavailable = false;
    public ?string $lastError = null;

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function __construct()
    {
        $this->merchantId = env('GOOGLE_MERCHANT_ID');
        
        $keyFilePath = storage_path('app/google-merchant.json');

        if (file_exists($keyFilePath) && $this->merchantId) {
            try {
                $client = new Client();
                $client->setAuthConfig($keyFilePath);
                $client->addScope(ShoppingContent::CONTENT);
                $this->client = $client;
                $this->service = new ShoppingContent($client);
            } catch (\Exception $e) {
                Log::error('Google Merchant Client Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Google Merchant'a ürün ekle veya güncelle
     */
    public function syncProduct(Product $product): bool
    {
        if (!$this->service || !$this->merchantId) {
            Log::warning('Google Merchant servisi yapılandırılmamış veya kimlik dosyası eksik.');
            return false;
        }

        // Eğer ürün aktif değilse veya stoğu yoksa Merchant'tan sil
        if (!$product->status || $product->stock <= 0 || $product->trashed()) {
            return $this->deleteProduct($product->id);
        }

        try {
            $googleProduct = new GoogleProduct();
            
            // Temel Bilgiler
            $googleProduct->setOfferId((string) $product->id);
            $title = $this->buildSeoTitle($product);
            $googleProduct->setTitle(mb_substr($title, 0, 150));
            
            $description = mb_substr(strip_tags($product->short_description ?: $product->description ?: $product->name), 0, 5000);
            if (empty(trim($description))) {
                $description = $title;
            }
            $cleanSlug = Product::stripSporFromSlug($product->slug);
            $productLink = url('/urun/' . $cleanSlug);
            $googleProduct->setDescription($description);
            $googleProduct->setLink($productLink);
            
            // Görseller (Google Shopping API için görsel zorunludur)
            $images = $product->images;
            $mainImageUrl = $images->first()?->raw_image_url;
            if (empty($mainImageUrl)) {
                $mainImageUrl = asset('favicon.png');
            }
            $googleProduct->setImageLink($mainImageUrl);

            $additionalImages = [];
            foreach ($images->skip(1)->take(10) as $img) {
                if (!empty($img->raw_image_url)) {
                    $additionalImages[] = $img->raw_image_url;
                }
            }
            if (!empty($additionalImages)) {
                $googleProduct->setAdditionalImageLinks($additionalImages);
            }

            // Kategori
            $googleProduct->setGoogleProductCategory('187'); // 187 = Athletic Shoes
            
            // Marka ve GTIN
            $brand = trim((string) ($product->brand ?: 'Patenli Ayakkabılar'));
            $googleProduct->setBrand($brand);
            $googleProduct->setIdentifierExists(false); // GTIN/Barkodumuz yok
            
            // Fiyatlandırma
            $priceVal = (float) $product->price;
            if ($priceVal <= 0) {
                $priceVal = (float) ($product->discount_price ?: 1999.00);
            }
            $price = new Price();
            $price->setValue(number_format($priceVal, 2, '.', ''));
            $price->setCurrency('TRY');
            $googleProduct->setPrice($price);

            if ($product->discount_price && $product->discount_price < $priceVal && $product->discount_price > 0) {
                $salePrice = new Price();
                $salePrice->setValue(number_format((float) $product->discount_price, 2, '.', ''));
                $salePrice->setCurrency('TRY');
                $googleProduct->setSalePrice($salePrice);
            }

            // Stok ve Kargo
            $googleProduct->setAvailability('in stock');
            $googleProduct->setCondition('new');
            
            // Hedef Kitle ve Özellikler
            $googleProduct->setAgeGroup($this->mapAgeGroup($product->age_group));
            $googleProduct->setGender($this->mapGender($product->gender));

            // Renk ve Beden varyasyonları (Eğer varsa)
            $colors = $product->variants->pluck('color')->filter()
                ->flatMap(fn ($c) => is_array($c) ? $c : [$c])->unique()->values();
            if ($colors->isNotEmpty()) {
                $googleProduct->setColor($colors->implode(', '));
            }

            $sizes = $product->variants->pluck('size')->filter()->unique()->sort()->values();
            if ($sizes->isNotEmpty()) {
                $googleProduct->setSizes($sizes->map(fn($s) => (string) $s)->all());
            }
            
            // Türkiye hedefleniyor
            $googleProduct->setTargetCountry('TR');
            $googleProduct->setContentLanguage('tr');
            $googleProduct->setChannel('online'); // Online satış

            // 1. Önce yeni Google Merchant API v1 (merchantapi.googleapis.com/products/v1) dene
            if ($this->syncProductViaMerchantApiV1(
                $product,
                $title,
                $description,
                $productLink,
                $mainImageUrl,
                $additionalImages,
                $brand,
                $priceVal,
                $colors->all(),
                $sizes->map(fn ($s) => (string) $s)->all()
            )) {
                return true;
            }

            // 2. Fallback: Content API for Shopping (Sunset brownout / geçici 429/500/503 durumlarında 5 defa yeniden dene)
            $maxRetries = 5;
            $attempt = 0;
            while ($attempt <= $maxRetries) {
                try {
                    $this->service->products->insert($this->merchantId, $googleProduct);
                    $this->lastError = null;
                    Log::info("Ürün Google Merchant'a eklendi/güncellendi: ID {$product->id}");
                    return true;
                } catch (\Google\Service\Exception $e) {
                    $errMsg = $e->getMessage();
                    $isSunsetBrownout = stripos($errMsg, 'sunset') !== false || stripos($errMsg, 'Merchant API') !== false;
                    if (($isSunsetBrownout || in_array($e->getCode(), [429, 500, 502, 503, 504])) && $attempt < $maxRetries) {
                        $attempt++;
                        usleep(400000 * $attempt); // Artan bekleme (0.4s, 0.8s, 1.2s...)
                        continue;
                    }
                    throw $e;
                }
            }

            return false;

        } catch (\Google\Service\Exception $e) {
            $msg = $e->getMessage();
            $errors = $e->getErrors();
            if (!empty($errors)) {
                $reasons = array_column($errors, 'message');
                $msg = implode('; ', $reasons);
            }
            $this->lastError = $msg;
            Log::error("Google Merchant Senkronizasyon Hatası (Ürün ID: {$product->id}): " . $msg);
            return false;
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            Log::error("Google Merchant Senkronizasyon Hatası (Ürün ID: {$product->id}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Yeni Google Merchant API v1 (merchantapi.googleapis.com/products/v1) üzerinden ürün gönderir.
     */
    private function syncProductViaMerchantApiV1(
        Product $product,
        string $title,
        string $description,
        string $productLink,
        string $mainImageUrl,
        array $additionalImages,
        string $brand,
        float $priceVal,
        array $colors,
        array $sizes
    ): bool {
        if ($this->merchantApiUnavailable || !$this->client || !$this->merchantId) {
            return false;
        }

        $dataSource = $this->resolveMerchantApiDataSource();
        if (!$dataSource) {
            return false;
        }

        try {
            $httpClient = $this->client->authorize();
            $endpoint = "https://merchantapi.googleapis.com/products/v1/accounts/{$this->merchantId}/productInputs:insert?dataSource=" . rawurlencode($dataSource);

            $attributes = [
                'title'                 => mb_substr($title, 0, 150),
                'description'           => $description,
                'link'                  => $productLink,
                'imageLink'             => $mainImageUrl,
                'googleProductCategory' => '187',
                'brand'                 => $brand,
                'identifierExists'      => false,
                'availability'          => 'in_stock',
                'condition'             => 'new',
                'ageGroup'              => $this->mapAgeGroup($product->age_group),
                'gender'                => $this->mapGender($product->gender),
                'price'                 => [
                    'amountMicros' => (string) (int) round($priceVal * 1000000),
                    'currencyCode' => 'TRY',
                ],
            ];

            if (!empty($additionalImages)) {
                $attributes['additionalImageLinks'] = array_values($additionalImages);
            }

            if ($product->discount_price && $product->discount_price < $priceVal && $product->discount_price > 0) {
                $attributes['salePrice'] = [
                    'amountMicros' => (string) (int) round(((float) $product->discount_price) * 1000000),
                    'currencyCode' => 'TRY',
                ];
            }

            if (!empty($colors)) {
                $attributes['color'] = implode(', ', $colors);
            }

            if (!empty($sizes)) {
                $attributes['sizes'] = array_values($sizes);
            }

            $payload = [
                'offerId'           => (string) $product->id,
                'contentLanguage'   => 'tr',
                'feedLabel'         => 'TR',
                'productAttributes' => $attributes,
            ];

            $response = $httpClient->request('POST', $endpoint, [
                'headers'     => ['Content-Type' => 'application/json'],
                'body'        => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'http_errors' => false,
                'timeout'     => 15,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode >= 200 && $statusCode < 300) {
                $this->lastError = null;
                Log::info("Ürün Google Merchant API v1 ile güncellendi: ID {$product->id}");
                return true;
            }

            if (in_array($statusCode, [403, 404])) {
                $this->merchantApiUnavailable = true;
            }
        } catch (\Throwable $e) {
            Log::debug('Google Merchant API v1 denemesi: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Merchant API v1 için birincil API DataSource kaynağını bulur veya oluşturur.
     */
    private function resolveMerchantApiDataSource(): ?string
    {
        if ($this->merchantApiDataSource) {
            return $this->merchantApiDataSource;
        }

        if ($this->merchantApiUnavailable || !$this->client || !$this->merchantId) {
            return null;
        }

        try {
            $httpClient = $this->client->authorize();
            $listUrl = "https://merchantapi.googleapis.com/datasources/v1/accounts/{$this->merchantId}/dataSources";

            $response = $httpClient->request('GET', $listUrl, [
                'http_errors' => false,
                'timeout'     => 10,
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->merchantApiUnavailable = true;
                return null;
            }

            $data = json_decode((string) $response->getBody(), true) ?: [];
            $sources = $data['dataSources'] ?? [];

            // Önce API tipindeki birincil ürün veri kaynağını ara
            foreach ($sources as $source) {
                if (isset($source['primaryProductDataSource']) && ($source['input'] ?? '') === 'API' && !empty($source['name'])) {
                    $this->merchantApiDataSource = $source['name'];
                    return $this->merchantApiDataSource;
                }
            }

            // Yoksa yeni bir API veri kaynağı oluşturmayı dene
            $createPayload = [
                'displayName'              => 'Patenli Ayakkabılar API',
                'primaryProductDataSource' => [
                    'channel'         => 'ONLINE_PRODUCTS',
                    'countries'       => ['TR'],
                    'contentLanguage' => 'tr',
                    'feedLabel'       => 'TR',
                ],
            ];

            $createResp = $httpClient->request('POST', $listUrl, [
                'headers'     => ['Content-Type' => 'application/json'],
                'body'        => json_encode($createPayload, JSON_UNESCAPED_UNICODE),
                'http_errors' => false,
                'timeout'     => 10,
            ]);

            if ($createResp->getStatusCode() >= 200 && $createResp->getStatusCode() < 300) {
                $created = json_decode((string) $createResp->getBody(), true) ?: [];
                if (!empty($created['name'])) {
                    $this->merchantApiDataSource = $created['name'];
                    return $this->merchantApiDataSource;
                }
            }
        } catch (\Throwable) {
            $this->merchantApiUnavailable = true;
        }

        return null;
    }

    /**
     * Ürünü Google Merchant'tan sil
     */
    public function deleteProduct($productId): bool
    {
        if (!$this->service || !$this->merchantId) {
            return false;
        }

        try {
            // Google Content API'de benzersiz ürün kimliği: channel:language:targetCountry:offerId
            $restId = "online:tr:TR:{$productId}";
            $this->service->products->delete($this->merchantId, $restId);
            Log::info("Ürün Google Merchant'tan silindi: ID {$productId}");
            return true;
        } catch (\Google\Service\Exception $e) {
            // Eğer ürün zaten yoksa (404), hata sayma
            if ($e->getCode() == 404) {
                return true;
            }
            Log::error("Google Merchant Silme Hatası (Ürün ID: {$productId}): " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error("Google Merchant Silme Hatası (Ürün ID: {$productId}): " . $e->getMessage());
            return false;
        }
    }

    public function buildSeoTitle(Product $product): string
    {
        $title = Product::stripSporFromTitle(trim($product->name));
        $brand = trim((string) ($product->brand ?: ''));
        $target = $product->gender ? $this->mapGenderToTR($product->gender) : '';

        // 1. Başlangıçtaki olası mükerrer kelimeleri temizle (örn: "KICK SPEED Kick Speed ...", "TWINGERS Twingers ...")
        $title = $this->cleanDuplicateLeadingWords($title);

        $normTitle = strtolower(Str::ascii($title));
        $normBrand = strtolower(Str::ascii($brand));

        // Eğer ürün adında marka ismi zaten geçmiyorsa ve marka ismi varsa en başa ekle
        if (!empty($brand) && $normBrand !== '' && !str_contains($normTitle, $normBrand)) {
            $title = $brand . ' ' . $title;
        }

        // Tekrar mükerrer kontrolü yap
        $title = $this->cleanDuplicateLeadingWords($title);
        $normTitle = strtolower(Str::ascii($title));

        // Hedef kitle (Kız Çocuk, Erkek Çocuk vb.) başlıkta yoksa ekle
        $normTarget = strtolower(Str::ascii($target));
        if (!empty($target) && $normTarget !== '' && !str_contains($normTitle, $normTarget)) {
            $title .= ' ' . $target;
        }

        // Ayakkabı modellerinde başlıkta "Patenli Ayakkabı" yoksa ekle
        $normTitle = strtolower(Str::ascii($title));
        if ($product->isShoeModel() && !str_contains($normTitle, 'patenli ayakkabi')) {
            $title .= ' Patenli Ayakkabı';
        }

        return trim($title);
    }

    public function cleanDuplicateLeadingWords(string $title): string
    {
        for ($wordCount = 4; $wordCount >= 1; $wordCount--) {
            $words = preg_split('/\s+/u', trim($title)) ?: [];
            if (count($words) >= $wordCount * 2) {
                $first = strtolower(Str::ascii(implode(' ', array_slice($words, 0, $wordCount))));
                $second = strtolower(Str::ascii(implode(' ', array_slice($words, $wordCount, $wordCount))));
                if ($first === $second) {
                    $title = implode(' ', array_slice($words, $wordCount));
                }
            }
        }
        return trim($title);
    }

    private function mapAgeGroup(?string $ageGroup): string
    {
        return match ($ageGroup) {
            'cocuk'    => 'kids',
            'genc'     => 'adult',
            'yetiskin' => 'adult',
            default    => 'kids',
        };
    }

    private function mapGender(?string $gender): string
    {
        return match ($gender) {
            'erkek'       => 'male',
            'kadin'       => 'female',
            'erkek_cocuk' => 'male',
            'kiz_cocuk'   => 'female',
            'unisex'      => 'unisex',
            default       => 'unisex',
        };
    }

    private function mapGenderToTR(?string $gender): string
    {
        return match ($gender) {
            'erkek'       => 'Erkek',
            'kadin'       => 'Kadın',
            'erkek_cocuk' => 'Erkek Çocuk',
            'kiz_cocuk'   => 'Kız Çocuk',
            default       => '',
        };
    }

    /**
     * Google Merchant Center'daki tüm ürünleri listeler
     */
    public function listProducts(): array
    {
        if (!$this->service || !$this->merchantId) {
            return [];
        }

        $allProducts = [];
        $pageToken = null;

        try {
            do {
                $parameters = ['maxResults' => 250];
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }

                $response = $this->service->products->listProducts($this->merchantId, $parameters);
                
                if ($response->getResources()) {
                    foreach ($response->getResources() as $product) {
                        $allProducts[] = $product;
                    }
                }

                $pageToken = $response->getNextPageToken();
            } while ($pageToken);

            return $allProducts;
        } catch (\Exception $e) {
            Log::error("Google Merchant Listeleme Hatası: " . $e->getMessage());
            return [];
        }
    }
}

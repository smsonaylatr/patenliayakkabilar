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
    private ?ShoppingContent $service = null;
    private ?string $merchantId = null;
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
            $googleProduct->setDescription($description);
            $googleProduct->setLink(url('/urun/' . $product->slug));
            
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

            // API'ye Gönder (Geçici kota / 429 veya 503 durumlarında 2 defa yeniden dene)
            $maxRetries = 2;
            $attempt = 0;
            while ($attempt <= $maxRetries) {
                try {
                    $this->service->products->insert($this->merchantId, $googleProduct);
                    Log::info("Ürün Google Merchant'a eklendi/güncellendi: ID {$product->id}");
                    return true;
                } catch (\Google\Service\Exception $e) {
                    if (in_array($e->getCode(), [429, 500, 503]) && $attempt < $maxRetries) {
                        $attempt++;
                        sleep(1);
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

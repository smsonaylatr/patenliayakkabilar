<?php

namespace App\Services;

use App\Models\Product;
use Google\Client;
use Google\Service\ShoppingContent;
use Google\Service\ShoppingContent\Price;
use Google\Service\ShoppingContent\Product as GoogleProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleMerchantService
{
    private ?ShoppingContent $service = null;
    private ?string $merchantId = null;

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
            $googleProduct->setTitle($this->buildSeoTitle($product));
            
            $description = mb_substr(strip_tags($product->short_description ?: $product->description ?: $product->name), 0, 5000);
            $googleProduct->setDescription($description);
            $googleProduct->setLink(url('/urun/' . $product->slug));
            
            // Görseller
            $images = $product->images;
            if ($images->isNotEmpty()) {
                $googleProduct->setImageLink($images->first()->image_url);
                $additionalImages = [];
                foreach ($images->skip(1)->take(10) as $img) {
                    $additionalImages[] = $img->image_url;
                }
                if (!empty($additionalImages)) {
                    $googleProduct->setAdditionalImageLinks($additionalImages);
                }
            }

            // Kategori
            $googleProduct->setGoogleProductCategory('187'); // 187 = Athletic Shoes
            
            // Marka ve GTIN
            $brand = $product->brand ?: 'Patenli Ayakkabılar';
            $googleProduct->setBrand($brand);
            $googleProduct->setIdentifierExists(false); // GTIN/Barkodumuz yok
            
            // Fiyatlandırma
            $price = new Price();
            $price->setValue(number_format((float) $product->price, 2, '.', ''));
            $price->setCurrency('TRY');
            $googleProduct->setPrice($price);

            if ($product->discount_price && $product->discount_price < $product->price) {
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
                $googleProduct->setSizes($sizes->toArray());
            }
            
            // Türkiye hedefleniyor
            $googleProduct->setTargetCountry('TR');
            $googleProduct->setContentLanguage('tr');
            $googleProduct->setChannel('online'); // Online satış

            // API'ye Gönder
            $this->service->products->insert($this->merchantId, $googleProduct);
            
            Log::info("Ürün Google Merchant'a eklendi/güncellendi: ID {$product->id}");
            return true;

        } catch (\Exception $e) {
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

    private function buildSeoTitle(Product $product): string
    {
        $brand = $product->brand ?: 'Patenli Ayakkabılar';
        $target = $product->gender ? $this->mapGenderToTR($product->gender) : '';
        return trim($brand . ' ' . $product->name . ' ' . $target);
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
}

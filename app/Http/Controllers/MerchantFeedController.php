<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

/**
 * Google Merchant Center XML Feed Üreteci
 *
 * Google Shopping reklamları ve ücretsiz ürün listeleme için
 * Merchant Center uyumlu XML feed dosyası üretir.
 *
 * Feed URL: /feeds/google-merchant.xml
 *
 * Google Merchant Center'da bu URL'i "Scheduled Fetch" olarak ekleyin.
 * Önerilen güncelleme sıklığı: Günlük
 */
class MerchantFeedController extends Controller
{
    /**
     * Google Merchant XML Feed
     *
     * Aktif ve stoklu tüm ürünleri Google'ın beklediği formatta çıkarır.
     * Varyant bilgileri (renk, numara), görseller, fiyat ve kargo dahil.
     *
     * GET /feeds/google-merchant.xml
     */
    public function index(): Response
    {
        // Aktif, stoklu, silinmemiş ürünleri ilişkileriyle çek
        $products = Product::where('status', true)
            ->where('stock', '>', 0)
            ->whereNull('deleted_at')
            ->with(['images', 'variants', 'category'])
            ->orderBy('id')
            ->get();

        $appUrl = config('app.url');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
        $xml .= '  <channel>' . "\n";
        $xml .= '    <title>Patenli Ayakkabılar - Ürün Feed</title>' . "\n";
        $xml .= '    <link>' . $appUrl . '</link>' . "\n";
        $xml .= '    <description>Patenli Ayakkabılar Google Merchant Center Ürün Feed</description>' . "\n";

        foreach ($products as $product) {
            $xml .= $this->buildProductItem($product, $appUrl);
        }

        $xml .= '  </channel>' . "\n";
        $xml .= '</rss>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Tek bir ürün için Merchant XML item'ı oluştur
     *
     * @param Product $product Ürün modeli
     * @param string  $appUrl  Uygulama URL'si
     * @return string XML item bloğu
     */
    private function buildProductItem(Product $product, string $appUrl): string
    {
        $xml = '    <item>' . "\n";

        // Ürün ID
        $xml .= '      <g:id>' . $product->id . '</g:id>' . "\n";

        // Ürün adı (max 150 karakter)
        $xml .= '      <title>' . htmlspecialchars(mb_substr($product->name, 0, 150), ENT_XML1, 'UTF-8') . '</title>' . "\n";

        // Açıklama (HTML strip, max 5000 karakter)
        $description = mb_substr(
            strip_tags($product->short_description ?: $product->description ?: $product->name),
            0,
            5000
        );
        $xml .= '      <description>' . htmlspecialchars($description, ENT_XML1, 'UTF-8') . '</description>' . "\n";

        // Ürün linki
        $xml .= '      <link>' . $appUrl . '/urun/' . $product->slug . '</link>' . "\n";

        // Görseller
        $images = $product->images;
        if ($images->isNotEmpty()) {
            // İlk görsel: ana görsel
            $xml .= '      <g:image_link>' . htmlspecialchars($images->first()->image_url, ENT_XML1, 'UTF-8') . '</g:image_link>' . "\n";

            // Diğer görseller: ek görseller (max 10)
            foreach ($images->skip(1)->take(10) as $image) {
                $xml .= '      <g:additional_image_link>' . htmlspecialchars($image->image_url, ENT_XML1, 'UTF-8') . '</g:additional_image_link>' . "\n";
            }
        }

        // Stok durumu
        $availability = $product->stock > 0 ? 'in_stock' : 'out_of_stock';
        $xml .= '      <g:availability>' . $availability . '</g:availability>' . "\n";

        // Fiyat
        $xml .= '      <g:price>' . number_format((float) $product->price, 2, '.', '') . ' TRY</g:price>' . "\n";

        // İndirimli fiyat (varsa ve normal fiyattan düşükse)
        if ($product->discount_price && $product->discount_price < $product->price) {
            $xml .= '      <g:sale_price>' . number_format((float) $product->discount_price, 2, '.', '') . ' TRY</g:sale_price>' . "\n";
        }

        // Marka
        $brand = $product->brand ?: 'Patenli Ayakkabılar';
        $xml .= '      <g:brand>' . htmlspecialchars($brand, ENT_XML1, 'UTF-8') . '</g:brand>' . "\n";

        // Durum (her zaman yeni)
        $xml .= '      <g:condition>new</g:condition>' . "\n";

        // Ürün tipi (kategori hiyerarşisi)
        $productType = 'Giyim > Ayakkabı > Patenli Ayakkabı';
        if ($product->category) {
            $productType = 'Giyim > Ayakkabı > ' . $product->category->name;
        }
        $xml .= '      <g:product_type>' . htmlspecialchars($productType, ENT_XML1, 'UTF-8') . '</g:product_type>' . "\n";

        // Google ürün kategorisi (187 = Athletic Shoes)
        $xml .= '      <g:google_product_category>187</g:google_product_category>' . "\n";

        // Yaş grubu
        $ageGroup = $this->mapAgeGroup($product->age_group);
        $xml .= '      <g:age_group>' . $ageGroup . '</g:age_group>' . "\n";

        // Cinsiyet
        $gender = $this->mapGender($product->gender);
        $xml .= '      <g:gender>' . $gender . '</g:gender>' . "\n";

        // Varyant bilgileri: Renkler
        $colors = $product->variants
            ->pluck('color')
            ->filter()
            ->flatMap(fn ($c) => is_array($c) ? $c : [$c])
            ->unique()
            ->values();

        if ($colors->isNotEmpty()) {
            $xml .= '      <g:color>' . htmlspecialchars($colors->implode(', '), ENT_XML1, 'UTF-8') . '</g:color>' . "\n";
        }

        // Varyant bilgileri: Numaralar
        $sizes = $product->variants
            ->pluck('size')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if ($sizes->isNotEmpty()) {
            $xml .= '      <g:size>' . htmlspecialchars($sizes->implode(', '), ENT_XML1, 'UTF-8') . '</g:size>' . "\n";
        }

        // Kargo bilgileri (Türkiye, 1 TRY)
        $xml .= '      <g:shipping>' . "\n";
        $xml .= '        <g:country>TR</g:country>' . "\n";
        $xml .= '        <g:price>1.00 TRY</g:price>' . "\n";
        $xml .= '      </g:shipping>' . "\n";

        $xml .= '    </item>' . "\n";

        return $xml;
    }

    /**
     * Yaş grubunu Google Merchant formatına dönüştür
     *
     * Google kabul edilen değerler: newborn, infant, toddler, kids, adult
     *
     * @param string|null $ageGroup Veritabanındaki yaş grubu
     * @return string Google formatında yaş grubu
     */
    private function mapAgeGroup(?string $ageGroup): string
    {
        return match ($ageGroup) {
            'cocuk'    => 'kids',
            'genc'     => 'adult',
            'yetiskin' => 'adult',
            default    => 'kids',
        };
    }

    /**
     * Cinsiyeti Google Merchant formatına dönüştür
     *
     * Google kabul edilen değerler: male, female, unisex
     *
     * @param string|null $gender Veritabanındaki cinsiyet
     * @return string Google formatında cinsiyet
     */
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
}

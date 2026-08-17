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
            ->with(['images', 'variants', 'categories'])
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
        $xmlOutput = '';

        // Tüm mevcut numaraları ve renkleri topla
        $sizes = $product->variants->pluck('size')->filter()->unique()->sort()->values();
        
        $colors = $product->variants
            ->pluck('color')
            ->filter()
            ->flatMap(fn ($c) => is_array($c) ? $c : [$c])
            ->unique()
            ->values();

        // Eğer bedeni varsa her beden için ayrı bir varyant (item) oluşturmalıyız
        // Çünkü Google Merchant Center "g:size" etiketinde virgülle ayrılmış birden fazla değeri kabul etmiyor.
        if ($sizes->isNotEmpty()) {
            foreach ($sizes as $size) {
                $xmlOutput .= $this->generateSingleXmlItem($product, $appUrl, $colors, $size);
            }
        } else {
            // Bedeni yoksa tek bir ürün olarak çıkar
            $xmlOutput .= $this->generateSingleXmlItem($product, $appUrl, $colors, null);
        }

        return $xmlOutput;
    }

    /**
     * Tekil bir XML node'u üretir.
     */
    private function generateSingleXmlItem(Product $product, string $appUrl, \Illuminate\Support\Collection $colors, ?string $size): string
    {
        $xml = '    <item>' . "\n";

        // Ürün ID (Varyant varsa ID'nin sonuna bedeni ekle benzersiz olsun)
        $itemId = $size ? $product->id . '_' . $size : $product->id;
        $xml .= '      <g:id>' . $itemId . '</g:id>' . "\n";

        // Item Group ID (Varyantları aynı ürün altında gruplamak için)
        if ($size) {
            $xml .= '      <g:item_group_id>' . $product->id . '</g:item_group_id>' . "\n";
        }

        // Ürün adı (SEO Odaklı: Marka + Ad + Hedef Kitle + Beden)
        $brand = $product->brand ?: 'Patenli Ayakkabılar';
        $target = $product->gender ? $this->mapGenderToTR($product->gender) : '';
        $seoTitle = trim($brand . ' ' . $product->name . ' ' . $target);
        if ($size) {
            $seoTitle .= ' (' . $size . ' Numara)';
        }
        $xml .= '      <title>' . htmlspecialchars(mb_substr($seoTitle, 0, 150), ENT_XML1, 'UTF-8') . '</title>' . "\n";

        // Açıklama (HTML strip, max 5000 karakter)
        $description = mb_substr(
            strip_tags($product->short_description ?: $product->description ?: $product->name),
            0,
            5000
        );
        $xml .= '      <description>' . htmlspecialchars($description, ENT_XML1, 'UTF-8') . '</description>' . "\n";

        // Ürün linki
        $xml .= '      <link>' . $appUrl . '/urun/' . $product->slug . '</link>' . "\n";

        // Görseller (Google Merchant doğrudan domain URL'lerini ister)
        $images = $product->images;
        if ($images->isNotEmpty()) {
            $xml .= '      <g:image_link>' . htmlspecialchars($images->first()->raw_image_url, ENT_XML1, 'UTF-8') . '</g:image_link>' . "\n";
            foreach ($images->skip(1)->take(10) as $image) {
                $xml .= '      <g:additional_image_link>' . htmlspecialchars($image->raw_image_url, ENT_XML1, 'UTF-8') . '</g:additional_image_link>' . "\n";
            }
        }

        // Stok durumu
        $availability = $product->stock > 0 ? 'in_stock' : 'out_of_stock';
        $xml .= '      <g:availability>' . $availability . '</g:availability>' . "\n";

        // Fiyat
        $xml .= '      <g:price>' . number_format((float) $product->price, 2, '.', '') . ' TRY</g:price>' . "\n";

        if ($product->discount_price && $product->discount_price < $product->price) {
            $xml .= '      <g:sale_price>' . number_format((float) $product->discount_price, 2, '.', '') . ' TRY</g:sale_price>' . "\n";
        }

        // Marka
        $xml .= '      <g:brand>' . htmlspecialchars($brand, ENT_XML1, 'UTF-8') . '</g:brand>' . "\n";
        $xml .= '      <g:identifier_exists>no</g:identifier_exists>' . "\n";
        $xml .= '      <g:condition>new</g:condition>' . "\n";

        // Kategori
        $productType = 'Giyim > Ayakkabı > Patenli Ayakkabı';
        if ($product->categories->isNotEmpty()) {
            $productType = 'Giyim > Ayakkabı > ' . ($product->categories->first()?->name ?? 'Patenli Ayakkabı');
        }
        $xml .= '      <g:product_type>' . htmlspecialchars($productType, ENT_XML1, 'UTF-8') . '</g:product_type>' . "\n";
        $xml .= '      <g:google_product_category>187</g:google_product_category>' . "\n";
        $xml .= '      <g:age_group>' . $this->mapAgeGroup($product->age_group) . '</g:age_group>' . "\n";
        $xml .= '      <g:gender>' . $this->mapGender($product->gender) . '</g:gender>' . "\n";

        if ($colors->isNotEmpty()) {
            $xml .= '      <g:color>' . htmlspecialchars($colors->implode(', '), ENT_XML1, 'UTF-8') . '</g:color>' . "\n";
        }

        if ($size) {
            $xml .= '      <g:size>' . htmlspecialchars($size, ENT_XML1, 'UTF-8') . '</g:size>' . "\n";
        }

        // Kargo bilgileri (Ücretsiz Kargo)
        $xml .= '      <g:shipping>' . "\n";
        $xml .= '        <g:country>TR</g:country>' . "\n";
        $xml .= '        <g:price>0.00 TRY</g:price>' . "\n";
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

    /**
     * Hedef kitleyi Türkçe başlık için çevir
     */
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

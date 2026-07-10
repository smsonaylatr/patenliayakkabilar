<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;

/**
 * JSON-LD Schema Üreteci
 *
 * Tüm sayfalarda kullanılacak merkezi yapısal veri servisi.
 * Google, Yandex ve diğer arama motorları için zengin snippet desteği sağlar.
 *
 * Kullanım:
 *   $schema = new SchemaService();
 *   echo $schema->organization();
 *   echo $schema->product($product);
 */
class SchemaService
{
    /**
     * Organization (Kuruluş) şeması
     *
     * Settings tablosundan şirket bilgilerini okuyarak
     * Google'ın bilgi panelinde görünecek kuruluş verilerini üretir.
     */
    public function organization(): string
    {
        // Settings tablosundan tüm şirket bilgilerini tek sorguda çek
        $keys = [
            'company_name',
            'company_address',
            'company_phone',
            'company_email',
            'company_city',
            'company_district',
            'social_facebook',
            'social_instagram',
            'social_twitter',
            'social_youtube',
        ];

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');

        $companyName = $settings->get('company_name', 'Patenli Ayakkabılar');

        // Sosyal medya profillerini topla (boş olmayanlar)
        $sameAs = collect([
            $settings->get('social_facebook'),
            $settings->get('social_instagram'),
            $settings->get('social_twitter'),
            $settings->get('social_youtube'),
        ])->filter()->values()->toArray();

        $data = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $companyName,
            'url'      => config('app.url'),
            'logo'     => config('app.url') . '/images/logo.png',
        ];

        // İletişim bilgileri (varsa)
        if ($phone = $settings->get('company_phone')) {
            $data['telephone'] = $phone;
        }

        if ($email = $settings->get('company_email')) {
            $data['email'] = $email;
        }

        // Adres bilgileri (varsa)
        $address = $settings->get('company_address');
        $city = $settings->get('company_city');
        $district = $settings->get('company_district');

        if ($address || $city || $district) {
            $postalAddress = [
                '@type'          => 'PostalAddress',
                'addressCountry' => 'TR',
            ];

            if ($address) {
                $postalAddress['streetAddress'] = $address;
            }
            if ($city) {
                $postalAddress['addressLocality'] = $city;
            }
            if ($district) {
                $postalAddress['addressRegion'] = $district;
            }

            $data['address'] = $postalAddress;
        }

        // Sosyal medya linkleri
        if (!empty($sameAs)) {
            $data['sameAs'] = $sameAs;
        }

        // İletişim noktası
        if ($phone || $email) {
            $contactPoint = [
                '@type'       => 'ContactPoint',
                'contactType' => 'customer service',
                'availableLanguage' => 'Turkish',
            ];

            if ($phone) {
                $contactPoint['telephone'] = $phone;
            }
            if ($email) {
                $contactPoint['email'] = $email;
            }

            $data['contactPoint'] = $contactPoint;
        }

        return $this->toScript($data);
    }

    /**
     * WebSite + SearchAction şeması
     *
     * Google arama sonuçlarında siteye özel arama kutusu (Sitelinks Search Box)
     * görünmesini sağlar.
     */
    public function website(): string
    {
        $data = [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => 'Patenli Ayakkabılar',
            'url'             => config('app.url'),
            'potentialAction' => [
                '@type'  => 'SearchAction',
                'target' => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => config('app.url') . '/patenli-ayakkabilar?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];

        return $this->toScript($data);
    }

    /**
     * BreadcrumbList (Ekmek Kırıntısı) şeması
     *
     * Google arama sonuçlarında sayfa hiyerarşisini gösterir.
     *
     * @param array $items [['name' => 'Ana Sayfa', 'url' => '/'], ['name' => 'Ürün', 'url' => '/urun/x']]
     */
    public function breadcrumb(array $items): string
    {
        $listItems = [];

        foreach ($items as $index => $item) {
            $listItems[] = [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'name'     => $item['name'],
                'item'     => url($item['url']),
            ];
        }

        $data = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];

        return $this->toScript($data);
    }

    /**
     * Product (Ürün) şeması — Zengin ürün snippet'i
     *
     * Ürün detay sayfalarında kullanılır. Fiyat, stok durumu,
     * yorumlar, kargo ve iade bilgilerini içerir.
     *
     * AggregateRating ve Review SADECE gerçek veri varsa eklenir.
     */
    public function product(Product $product): string
    {
        // İlişkileri yükle (zaten yüklü değilse)
        $product->loadMissing(['images', 'variants', 'reviews', 'category']);

        $appUrl = config('app.url');

        // Tüm ürün görsellerini topla
        $images = $product->images
            ->map(fn ($img) => $img->image_url)
            ->toArray();

        // Varyantlardan unique renkleri çıkar (color JSON array)
        $colors = $product->variants
            ->pluck('color')
            ->filter()
            ->flatMap(fn ($c) => is_array($c) ? $c : [$c])
            ->unique()
            ->values()
            ->toArray();

        // Varyantlardan unique numaraları çıkar
        $sizes = $product->variants
            ->pluck('size')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Fiyat hesaplama (indirimli varsa onu kullan)
        $price = $product->discount_price && $product->discount_price < $product->price
            ? $product->discount_price
            : $product->price;

        // Stok durumu
        $availability = $product->stock > 0
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock';

        $data = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $product->name,
            'description' => strip_tags($product->short_description ?: $product->description),
            'sku'         => $product->sku,
            'url'         => $appUrl . '/urun/' . $product->slug,
        ];

        // Görseller
        if (!empty($images)) {
            $data['image'] = $images;
        }

        // Marka (Organization olarak)
        $data['brand'] = [
            '@type' => 'Organization',
            'name'  => $product->brand ?: 'Patenli Ayakkabılar',
        ];

        // Kategori
        if ($product->category) {
            $data['category'] = $product->category->name;
        }

        // Renkler
        if (!empty($colors)) {
            $data['color'] = implode(', ', $colors);
        }

        // Numaralar
        if (!empty($sizes)) {
            $data['size'] = implode(', ', $sizes);
        }

        // Teklif (Offer)
        $offer = [
            '@type'         => 'Offer',
            'price'         => number_format((float) $price, 2, '.', ''),
            'priceCurrency' => 'TRY',
            'availability'  => $availability,
            'itemCondition' => 'https://schema.org/NewCondition',
            'url'           => $appUrl . '/urun/' . $product->slug,
            'seller'        => [
                '@type' => 'Organization',
                'name'  => 'Patenli Ayakkabılar',
            ],
        ];

        // Kargo bilgileri
        $offer['shippingDetails'] = [
            '@type'               => 'OfferShippingDetails',
            'shippingDestination' => [
                '@type'          => 'DefinedRegion',
                'addressCountry' => 'TR',
            ],
            'shippingRate' => [
                '@type'    => 'MonetaryAmount',
                'value'    => '1.00',
                'currency' => 'TRY',
            ],
        ];

        // İade politikası (14 gün)
        $offer['hasMerchantReturnPolicy'] = [
            '@type'                    => 'MerchantReturnPolicy',
            'applicableCountry'        => 'TR',
            'returnPolicyCategory'     => 'https://schema.org/MerchantReturnFiniteReturnWindow',
            'merchantReturnDays'       => 14,
            'returnMethod'             => 'https://schema.org/ReturnByMail',
            'returnFees'               => 'https://schema.org/FreeReturn',
        ];

        $data['offers'] = $offer;

        // Onaylanmış yorumlar (status=true)
        $approvedReviews = $product->reviews->where('status', true);

        // AggregateRating — SADECE onaylı yorum varsa
        if ($approvedReviews->isNotEmpty()) {
            $data['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => round($approvedReviews->avg('rating'), 1),
                'reviewCount' => $approvedReviews->count(),
                'bestRating'  => 5,
                'worstRating' => 1,
            ];

            // En son 5 onaylı yorumu ekle
            $recentReviews = $approvedReviews->sortByDesc('created_at')->take(5);
            $reviewData = [];

            foreach ($recentReviews as $review) {
                $reviewItem = [
                    '@type'        => 'Review',
                    'reviewRating' => [
                        '@type'      => 'Rating',
                        'ratingValue' => $review->rating,
                        'bestRating'  => 5,
                        'worstRating' => 1,
                    ],
                    'datePublished' => $review->created_at->toW3cString(),
                ];

                // Yorum yazarı
                if ($review->user) {
                    $reviewItem['author'] = [
                        '@type' => 'Person',
                        'name'  => $review->user->name ?? 'Anonim',
                    ];
                } else {
                    $reviewItem['author'] = [
                        '@type' => 'Person',
                        'name'  => 'Anonim',
                    ];
                }

                // Yorum metni
                if ($review->comment) {
                    $reviewItem['reviewBody'] = $review->comment;
                }

                $reviewData[] = $reviewItem;
            }

            $data['review'] = $reviewData;
        }

        return $this->toScript($data);
    }

    /**
     * ItemList + CollectionPage şeması — Kategori sayfası
     *
     * Kategori sayfalarında ürün listesini yapısal veri olarak sunar.
     * Google'ın carousel (karusel) sonuçlarında görünmeyi sağlar.
     *
     * @param Category $category Kategori modeli
     * @param \Illuminate\Support\Collection|array $products Ürün koleksiyonu
     */
    public function categoryPage(Category $category, $products): string
    {
        $appUrl = config('app.url');

        $listItems = [];
        $position = 1;

        foreach ($products as $product) {
            $listItems[] = [
                '@type'    => 'ListItem',
                'position' => $position++,
                'url'      => $appUrl . '/urun/' . $product->slug,
                'name'     => $product->name,
            ];
        }

        $data = [
            '@context' => 'https://schema.org',
            '@type'    => 'CollectionPage',
            'name'     => $category->name,
            'url'      => $appUrl . '/kategori/' . $category->slug,
        ];

        if ($category->description) {
            $data['description'] = strip_tags($category->description);
        }

        $data['mainEntity'] = [
            '@type'           => 'ItemList',
            'itemListElement' => $listItems,
            'numberOfItems'   => count($listItems),
        ];

        return $this->toScript($data);
    }

    /**
     * Article şeması — Blog yazısı
     *
     * Blog detay sayfalarında kullanılır.
     * Google Discover ve News sonuçlarında görünmeyi destekler.
     */
    public function blogArticle(BlogPost $post): string
    {
        $appUrl = config('app.url');

        $data = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $post->title,
            'url'           => $appUrl . '/blog/' . $post->slug,
            'datePublished' => $post->created_at->toW3cString(),
            'dateModified'  => $post->updated_at->toW3cString(),
            'author'        => [
                '@type' => 'Organization',
                'name'  => 'Patenli Ayakkabılar',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name'  => 'Patenli Ayakkabılar',
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => $appUrl . '/images/logo.png',
                ],
            ],
        ];

        // Blog görseli
        if ($post->image_path) {
            $data['image'] = asset('storage/' . $post->image_path);
        }

        // Meta açıklama veya excerpt
        if ($post->meta_description) {
            $data['description'] = $post->meta_description;
        } elseif ($post->excerpt) {
            $data['description'] = $post->excerpt;
        }

        return $this->toScript($data);
    }

    /**
     * JSON-LD verisini script tag'ine dönüştürür
     *
     * @param array $data Yapısal veri dizisi
     * @return string HTML script tag
     */
    private function toScript(array $data): string
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Boot: SEO alanlarını otomatik doldur
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Product $product) {
            // Fiyat/stok artık varyantlardan geliyor, varsayılan değerler
            $product->price = $product->price ?: 0;
            $product->stock = $product->stock ?: 0;

            // Yeni ürün en başa geçsin
            static::where('homepage_sort', '>', 0)->increment('homepage_sort');
            $product->homepage_sort = 1;

            static::autoFillContent($product);
            static::autoFillSeo($product);
        });

        static::updating(function (Product $product) {
            if (empty($product->short_description) || empty($product->description) || $product->isDirty('name')) {
                static::autoFillContent($product);
            }
            if (empty($product->meta_title) || $product->isDirty('name')) {
                static::autoFillSeo($product);
            }
        });

        static::saving(function (Product $product) {
            if ($product->discount_price && $product->price && $product->discount_price > $product->price) {
                $temp = $product->price;
                $product->price = $product->discount_price;
                $product->discount_price = $temp;
            }
        });
    }

    /**
     * Kısa açıklama ve açıklamayı otomatik üret
     */
    protected static function autoFillContent(Product $product): void
    {
        $name = $product->name;
        $category = $product->category?->name;
        $brand = $product->brand;
        $gender = $product->gender;
        $ageGroup = $product->age_group;
        $price = $product->discount_price ?? $product->price;

        $genderLabel = match ($gender) {
            'erkek'  => 'Erkek',
            'kadin'  => 'Kadın',
            'cocuk'  => 'Çocuk',
            'unisex' => 'Unisex',
            default  => null,
        };

        $ageLabel = match ($ageGroup) {
            'cocuk'    => 'Çocuk',
            'genc'     => 'Genç',
            'yetiskin' => 'Yetişkin',
            default    => null,
        };

        $hedefKitle = collect([$genderLabel, $ageLabel])->filter()->unique()->implode(' ');

        // ========================================
        // KISA AÇIKLAMA (max 500 karakter)
        // ========================================
        if (empty($product->short_description)) {
            $parts = [];
            $parts[] = $name;

            if ($hedefKitle) {
                $parts[] = $hedefKitle . ' için tasarlanmış';
            }

            if ($brand) {
                $parts[] = $brand . ' marka';
            }

            $parts[] = 'patenli ayakkabı.';

            if ($category) {
                $parts[] = $category . ' kategorisinde.';
            }

            if ($price && $price > 0) {
                $parts[] = number_format((float) $price, 0, ',', '.') . ' ₺ fiyatla.';
            }

            $parts[] = 'Ücretsiz kargo ve güvenli ödeme seçenekleriyle hemen sipariş verin.';

            $product->short_description = mb_substr(implode(' ', $parts), 0, 500);
        }

        // ========================================
        // AÇIKLAMA (HTML - detaylı ürün açıklaması)
        // ========================================
        if (empty($product->description)) {
            $html = '<h2>' . e($name) . '</h2>';

            // Giriş paragrafı
            $intro = '<p>';
            $intro .= '<strong>' . e($name) . '</strong>';
            if ($hedefKitle) {
                $intro .= ', ' . e($hedefKitle) . ' için özel olarak tasarlanmış';
            }
            if ($brand) {
                $intro .= ' ' . e($brand) . ' marka';
            }
            $intro .= ' patenli ayakkabıdır.';
            if ($category) {
                $intro .= ' ' . e($category) . ' kategorisindeki bu model,';
            }
            $intro .= ' hem şık tasarımı hem de dayanıklı yapısıyla öne çıkmaktadır.</p>';
            $html .= $intro;

            // Özellikler listesi (dinamik)
            $featureKeys = $product->features()->pluck('feature_key')->toArray();
            if (empty($featureKeys)) {
                $featureKeys = static::guessFeatures($product);
            }

            if (!empty($featureKeys)) {
                $html .= '<h3>Ürün Özellikleri</h3>';
                $html .= '<ul>';
                foreach ($featureKeys as $key) {
                    $label = ProductFeature::getLabel($key);
                    $desc = ProductFeature::getDescription($key);
                    $html .= '<li>' . e($label) . ($desc ? ' — ' . e($desc) : '') . '</li>';
                }
                if ($genderLabel) {
                    $html .= '<li>👤 ' . e($genderLabel) . ' modeli</li>';
                }
                if ($brand) {
                    $html .= '<li>🏷️ ' . e($brand) . ' marka güvencesi</li>';
                }
                $html .= '</ul>';
            }

            // Kargo bilgisi
            $html .= '<h3>Kargo & Teslimat</h3>';
            $html .= '<p>✅ <strong>Ücretsiz kargo</strong> ile kapınıza kadar teslim. ';
            $html .= 'Siparişleriniz 1-3 iş günü içinde kargoya verilir. ';
            $html .= 'Güvenli ödeme seçenekleri ile gönül rahatlığıyla alışveriş yapabilirsiniz.</p>';

            $product->description = $html;
        }
    }

    /**
     * Akıllı SEO meta verilerini otomatik üret
     */
    protected static function autoFillSeo(Product $product): void
    {
        $name = $product->name;
        $category = $product->category?->name;
        $brand = $product->brand;
        $gender = $product->gender;
        $ageGroup = $product->age_group;
        $price = $product->discount_price ?? $product->price;

        // Cinsiyet etiketi
        $genderLabel = match ($gender) {
            'erkek'  => 'Erkek',
            'kadin'  => 'Kadın',
            'cocuk'  => 'Çocuk',
            'unisex' => 'Unisex',
            default  => null,
        };

        // Yaş grubu etiketi
        $ageLabel = match ($ageGroup) {
            'cocuk'    => 'Çocuk',
            'genc'     => 'Genç',
            'yetiskin' => 'Yetişkin',
            default    => null,
        };

        // ========================================
        // META TITLE (max 70 karakter)
        // Format: {Ürün Adı} | {Kategori/Hedef} - Patenli Ayakkabılar
        // ========================================
        if (empty($product->meta_title)) {
            $suffix = '- Patenli Ayakkabılar';
            $middle = '';

            // Kategori veya hedef kitle ekle
            if ($category) {
                $middle = '| ' . $category . ' ';
            } elseif ($genderLabel) {
                $middle = '| ' . $genderLabel . ' Patenli Ayakkabı ';
            }

            $title = trim($name . ' ' . $middle . $suffix);

            // 70 karakteri aşarsa kısalt
            if (mb_strlen($title) > 70) {
                $title = trim($name . ' ' . $suffix);
            }
            if (mb_strlen($title) > 70) {
                $title = mb_substr($name, 0, 45) . ' - Patenli Ayakkabılar';
            }

            $product->meta_title = $title;
        }

        // ========================================
        // META DESCRIPTION (max 160 karakter)
        // Anahtar kelime yoğunluğu yüksek, CTA içeren açıklama
        // ========================================
        if (empty($product->meta_description)) {
            $parts = [];

            // Ürün adı + hedef kitle
            $intro = $name;
            if ($genderLabel && $ageLabel && $genderLabel !== $ageLabel) {
                $intro .= ' ' . $ageLabel . ' ' . $genderLabel;
            } elseif ($genderLabel) {
                $intro .= ' ' . $genderLabel;
            } elseif ($ageLabel) {
                $intro .= ' ' . $ageLabel;
            }

            // Marka varsa ekle
            if ($brand) {
                $intro .= ' ' . $brand;
            }

            $intro .= ' patenli ayakkabı.';
            $parts[] = $intro;

            // Fiyat bilgisi (indirim varsa vurgula)
            if ($price && $price > 0) {
                if ($product->discount_price && $product->price > $product->discount_price) {
                    $parts[] = 'İndirimli fiyat: ' . number_format($price, 0) . ' ₺.';
                } else {
                    $parts[] = 'Fiyat: ' . number_format($price, 0) . ' ₺.';
                }
            }

            // Kısa açıklamadan ilk cümle
            if ($product->short_description) {
                $firstSentence = Str::before($product->short_description, '.');
                if (mb_strlen($firstSentence) > 10 && mb_strlen($firstSentence) < 80) {
                    $parts[] = trim($firstSentence) . '.';
                }
            }

            // CTA
            $parts[] = '✅ Ücretsiz kargo, hızlı teslimat.';

            // 160 karaktere sığdır
            $description = '';
            foreach ($parts as $part) {
                $candidate = $description ? $description . ' ' . $part : $part;
                if (mb_strlen($candidate) <= 160) {
                    $description = $candidate;
                } else {
                    break;
                }
            }

            // Hâlâ boşsa basit versiyon
            if (empty($description)) {
                $description = mb_substr($name . ' patenli ayakkabı en uygun fiyatlarla Patenli Ayakkabılar\'da. Ücretsiz kargo, güvenli ödeme.', 0, 160);
            }

            $product->meta_description = $description;
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function features()
    {
        return $this->hasMany(ProductFeature::class)->orderBy('sort_order');
    }

    /**
     * Özellik etiketlerini label olarak döndür
     */
    public function getFeatureLabels(): array
    {
        return $this->features
            ->map(fn ($f) => [
                'key'   => $f->feature_key,
                'label' => ProductFeature::getLabel($f->feature_key),
                'icon'  => ProductFeature::FEATURE_OPTIONS[$f->feature_key]['icon'] ?? 'check',
                'desc'  => ProductFeature::getDescription($f->feature_key),
            ])
            ->toArray();
    }

    /**
     * Ürün bilgilerinden akıllı özellik tahmini
     */
    public static function guessFeatures(Product $product): array
    {
        $features = [];
        $name = mb_strtolower($product->name ?? '');
        $category = mb_strtolower($product->category?->name ?? '');

        // LED anahtar kelimeleri
        if (str_contains($name, 'led') || str_contains($name, 'ışık') || str_contains($category, 'led')) {
            $features[] = 'led_light';
            $features[] = 'color_led';
        }

        // USB / Şarj
        if (str_contains($name, 'usb') || str_contains($name, 'şarj')) {
            $features[] = 'usb_charge';
        }

        // Tekerlek tipi
        $wheelTypes = $product->variants?->pluck('wheel_type')->filter()->unique()->toArray() ?? [];
        if (in_array('double', $wheelTypes)) {
            $features[] = 'double_wheel';
        } elseif (in_array('single', $wheelTypes)) {
            $features[] = 'single_wheel';
        }
        if (in_array('led', $wheelTypes)) {
            if (!in_array('led_light', $features)) {
                $features[] = 'led_light';
            }
        }

        // Her patenli ayakkabıda varsayılan özellikler
        $features[] = 'hidden_wheel';
        $features[] = 'anti_slip';
        $features[] = 'lightweight';
        $features[] = 'breathable';

        // Su geçirmez
        if (str_contains($name, 'waterproof') || str_contains($name, 'su geçirmez')) {
            $features[] = 'waterproof';
        }

        // Ortopedik
        if (str_contains($name, 'ortopedik') || str_contains($category, 'ortopedik')) {
            $features[] = 'orthopedic';
        }

        return array_unique($features);
    }

    /**
     * Ürün oluşturulduktan sonra özellikleri otomatik doldur
     */
    public function autoFillFeatures(): void
    {
        if ($this->features()->count() > 0) {
            return;
        }

        $guessed = static::guessFeatures($this);
        $order = 0;

        foreach ($guessed as $key) {
            $this->features()->create([
                'feature_key' => $key,
                'sort_order'  => $order++,
            ]);
        }
    }

    /**
     * Güven sinyallerini hesapla (frontend için)
     */
    public function getTrustSignals(): array
    {
        $signals = [
            ['icon' => 'truck', 'text' => 'Ücretsiz Kargo', 'color' => 'green'],
            ['icon' => 'clock', 'text' => '1-3 İş Günü Teslimat', 'color' => 'blue'],
            ['icon' => 'lock-closed', 'text' => 'Güvenli Ödeme', 'color' => 'purple'],
            ['icon' => 'arrow-uturn-left', 'text' => '14 Gün İade', 'color' => 'orange'],
        ];

        // İndirim rozeti
        if ($this->discount_price && $this->price > $this->discount_price) {
            $percent = round(($this->price - $this->discount_price) / $this->price * 100);
            $signals[] = ['icon' => 'tag', 'text' => '%' . $percent . ' İndirim', 'color' => 'red'];
        }

        // Son stok uyarısı
        if ($this->stock > 0 && $this->stock <= 5) {
            $signals[] = ['icon' => 'fire', 'text' => 'Son ' . $this->stock . ' Adet!', 'color' => 'red'];
        }

        // Çok satan
        if ($this->best_seller) {
            $signals[] = ['icon' => 'star', 'text' => 'Çok Satan Ürün', 'color' => 'yellow'];
        }

        return $signals;
    }

    /**
     * Spesifikasyon tablosu verilerini hesapla (frontend için)
     */
    public function getSpecifications(): array
    {
        $specs = [];

        if ($this->brand) {
            $specs['Marka'] = $this->brand;
        }

        if ($this->category?->name) {
            $specs['Kategori'] = $this->category->name;
        }

        $genderLabel = match ($this->gender) {
            'erkek'  => 'Erkek',
            'kadin'  => 'Kadın',
            'cocuk'  => 'Çocuk',
            'unisex' => 'Unisex',
            default  => null,
        };
        if ($genderLabel) {
            $specs['Cinsiyet'] = $genderLabel;
        }

        $ageLabel = match ($this->age_group) {
            'cocuk'    => 'Çocuk',
            'genc'     => 'Genç',
            'yetiskin' => 'Yetişkin',
            default    => null,
        };
        if ($ageLabel) {
            $specs['Yaş Grubu'] = $ageLabel;
        }

        // Varyantlardan numara aralığı
        $sizes = $this->variants->pluck('size')->filter()->sort()->values();
        if ($sizes->isNotEmpty()) {
            $specs['Numara Aralığı'] = $sizes->first() . ' - ' . $sizes->last();
        }

        // Renkler (her varyantın color alanı artık array)
        $colors = $this->variants->pluck('color')->filter()
            ->flatMap(fn ($c) => is_array($c) ? $c : [$c])
            ->unique()->values();
        if ($colors->isNotEmpty()) {
            $specs['Renkler'] = $colors->implode(', ');
        }

        // Teker tipleri
        $wheelLabels = [
            'single' => 'Tek Teker',
            'double' => 'Çift Teker',
            'quad'   => 'Dört Teker',
            'led'    => 'LED Tekerlekli',
        ];
        $wheels = $this->variants->pluck('wheel_type')->filter()->unique()
            ->map(fn ($w) => $wheelLabels[$w] ?? $w)->values();
        if ($wheels->isNotEmpty()) {
            $specs['Teker Tipi'] = $wheels->implode(', ');
        }

        return $specs;
    }

    /**
     * Varyantlardan ürün fiyat/stok senkronizasyonu
     */
    public function syncFromVariants(): void
    {
        $variants = $this->variants()->get();

        if ($variants->isEmpty()) {
            return;
        }

        // En düşük fiyat
        $minPrice = $variants->where('price', '>', 0)->min('price');
        if ($minPrice) {
            $this->price = $minPrice;
        }

        // En düşük indirimli fiyat
        $minDiscount = $variants->whereNotNull('discount_price')->where('discount_price', '>', 0)->min('discount_price');
        $this->discount_price = $minDiscount;

        // Toplam stok
        $this->stock = $variants->sum('stock');

        $this->saveQuietly();
    }
}

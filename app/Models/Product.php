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
            static::autoFillSeo($product);
        });

        static::updating(function (Product $product) {
            // SEO alanları boşsa veya ürün adı değiştiyse yeniden üret
            if (empty($product->meta_title) || $product->isDirty('name')) {
                static::autoFillSeo($product);
            }
        });
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
}


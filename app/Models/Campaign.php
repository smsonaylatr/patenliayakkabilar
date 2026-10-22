<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $guarded = [];

    protected $casts = [
        'audience_signals' => 'array',
        'tracking_settings' => 'array',
        'rich_assets' => 'array',
        'budget' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'target_roas' => 'decimal:2',
        'target_cpa' => 'decimal:2',
        'conversion_revenue' => 'decimal:2',
        'scheduled_at' => 'datetime',
    ];

    /**
     * İlişkili müşteri segmenti
     */
    public function customerSegment(): BelongsTo
    {
        return $this->belongsTo(CustomerSegment::class);
    }

    /**
     * Kampanyaya tanımlanmış indirim kuponu
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Kampanya gönderim ve etkileşim logları
     */
    public function logs(): HasMany
    {
        return $this->hasMany(CampaignLog::class);
    }

    /**
     * Gerçekleşen ROAS (Return On Ad Spend)
     * Formül: Ciro / Harcanan Tutar
     */
    public function getRoasAttribute(): ?float
    {
        $spent = (float) $this->spent_amount;
        if ($spent <= 0) {
            return null;
        }

        return round(((float) $this->conversion_revenue) / $spent, 2);
    }

    /**
     * Tıklama Oranı (CTR %)
     * Formül: (Tıklama / Gönderim) * 100
     */
    public function getCtrAttribute(): float
    {
        $sent = (int) $this->sent_count;
        if ($sent <= 0) {
            return 0.0;
        }

        return round(((int) $this->clicked_count / $sent) * 100, 2);
    }

    /**
     * Dönüşüm Oranı (CR %)
     * Formül: (Dönüşüm / Tıklama) * 100
     */
    public function getConversionRateAttribute(): float
    {
        $clicked = (int) $this->clicked_count;
        if ($clicked <= 0) {
            return 0.0;
        }

        return round(((int) $this->conversions_count / $clicked) * 100, 2);
    }

    /**
     * Gerçekleşen Edinme Başına Maliyet (CPA ₺)
     */
    public function getCpaAttribute(): ?float
    {
        $conversions = (int) $this->conversions_count;
        if ($conversions <= 0) {
            return null;
        }

        return round(((float) $this->spent_amount) / $conversions, 2);
    }

    /**
     * Zengin Varlık Gücü (Ad Strength) Hesaplama
     * Varlıkların (Başlık, Uzun Başlık, Açıklama, Görsel, Sitelink, Callout) doluluk puanı.
     */
    public function calculateAdStrength(): array
    {
        $assets = $this->rich_assets ?? [];
        $score = 0;
        $maxScore = 100;
        $checklist = [];

        // 1. Başlıklar (Headlines)
        $headlines = $assets['headlines'] ?? [];
        if (is_string($headlines)) {
            $headlines = array_filter(array_map('trim', explode(',', $headlines)));
        }
        $hlCount = is_array($headlines) ? count($headlines) : 0;
        if ($hlCount >= 3) {
            $score += 25;
            $checklist[] = ['label' => '3+ Dinamik Başlık', 'status' => true];
        } elseif ($hlCount >= 1) {
            $score += 15;
            $checklist[] = ['label' => 'En az 1 Başlık (3+ tavsiye edilir)', 'status' => false];
        } else {
            $checklist[] = ['label' => 'Başlık girilmedi', 'status' => false];
        }

        // 2. Uzun Başlık (Long Headline)
        if (!empty($assets['long_headline'])) {
            $score += 15;
            $checklist[] = ['label' => 'Uzun Çarpıcı Başlık', 'status' => true];
        } else {
            $checklist[] = ['label' => 'Uzun Başlık eksik', 'status' => false];
        }

        // 3. Açıklamalar (Descriptions)
        $descriptions = $assets['descriptions'] ?? [];
        if (is_string($descriptions)) {
            $descriptions = array_filter(array_map('trim', explode("\n", $descriptions)));
        }
        $descCount = is_array($descriptions) ? count($descriptions) : 0;
        if ($descCount >= 2) {
            $score += 20;
            $checklist[] = ['label' => '2+ Detaylı Açıklama', 'status' => true];
        } elseif ($descCount >= 1) {
            $score += 10;
            $checklist[] = ['label' => 'En az 1 Açıklama (2+ tavsiye edilir)', 'status' => false];
        } else {
            $checklist[] = ['label' => 'Açıklama girilmedi', 'status' => false];
        }

        // 4. Görseller / Bannerlar
        $images = $assets['images'] ?? [];
        $imgCount = is_array($images) ? count($images) : (!empty($images) ? 1 : 0);
        if ($imgCount >= 2) {
            $score += 15;
            $checklist[] = ['label' => 'Çoklu Görsel Formatı (1:1 & 16:9)', 'status' => true];
        } elseif ($imgCount >= 1) {
            $score += 10;
            $checklist[] = ['label' => '1 Görsel var (Daha fazla ekleyin)', 'status' => false];
        } else {
            $checklist[] = ['label' => 'Görsel eklenmedi', 'status' => false];
        }

        // 5. Site Bağlantıları (Sitelinks)
        $sitelinks = $assets['sitelinks'] ?? [];
        $slCount = is_array($sitelinks) ? count($sitelinks) : 0;
        if ($slCount >= 2) {
            $score += 15;
            $checklist[] = ['label' => '2+ Zengin Site Bağlantısı', 'status' => true];
        } elseif ($slCount >= 1) {
            $score += 8;
            $checklist[] = ['label' => '1 Site Bağlantısı', 'status' => false];
        } else {
            $checklist[] = ['label' => 'Site Bağlantısı yok', 'status' => false];
        }

        // 6. Belirtme Öğeleri (Callouts / Rozetler)
        $callouts = $assets['callouts'] ?? [];
        if (is_string($callouts)) {
            $callouts = array_filter(array_map('trim', explode(',', $callouts)));
        }
        $coCount = is_array($callouts) ? count($callouts) : 0;
        if ($coCount >= 3) {
            $score += 10;
            $checklist[] = ['label' => '3+ Belirtme Rozeti (Aynı Gün Kargo vb.)', 'status' => true];
        } elseif ($coCount >= 1) {
            $score += 5;
            $checklist[] = ['label' => 'Belirtme Rozeti eklendi', 'status' => true];
        } else {
            $checklist[] = ['label' => 'Belirtme Rozeti yok', 'status' => false];
        }

        $score = min($maxScore, $score);

        $grade = match (true) {
            $score >= 85 => 'Mükemmel',
            $score >= 65 => 'İyi',
            $score >= 40 => 'Ortalama',
            default => 'Geliştirilmeli',
        };

        $color = match (true) {
            $score >= 85 => 'success',
            $score >= 65 => 'info',
            $score >= 40 => 'warning',
            default => 'danger',
        };

        return [
            'score' => $score,
            'grade' => $grade,
            'color' => $color,
            'checklist' => $checklist,
        ];
    }

    /**
     * Takip Parametreleri Eklenmiş Hedef URL
     */
    public function generateTrackingUrl(?string $baseUrl = null): string
    {
        $settings = $this->tracking_settings ?? [];
        $url = $baseUrl ?: ($settings['landing_page_url'] ?? config('app.url', 'https://patenliayakkabilar.com'));

        $query = [
            'utm_source' => $settings['utm_source'] ?? ($this->channel === 'email' ? 'newsletter' : ($this->channel === 'sms' ? 'sms' : 'meta_ads')),
            'utm_medium' => $settings['utm_medium'] ?? ($this->channel === 'email' ? 'email' : ($this->channel === 'sms' ? 'cpc' : 'social')),
            'utm_campaign' => $settings['utm_campaign'] ?? \Illuminate\Support\Str::slug($this->name),
            'campaign_id' => $this->id,
        ];

        if (!empty($settings['utm_term'])) {
            $query['utm_term'] = $settings['utm_term'];
        }
        if (!empty($settings['utm_content'])) {
            $query['utm_content'] = $settings['utm_content'];
        }

        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . http_build_query($query);
    }

    /**
     * Başarılı Dönüşüm Kaydı Ekleme
     */
    public function recordConversion(float $revenue): void
    {
        $this->increment('conversions_count');
        $this->increment('conversion_revenue', $revenue);
    }
}

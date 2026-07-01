---
name: phoenix-ai
description: AI öneri paneli, prompt yönetimi, hafıza, crawler, AI logları, görev motoru ve ajan mimarisi kuralları.
---

# Phoenix AI Skill

## Kapsam
Phoenix AI öneri motoru, ajan mimarisi, hafıza yönetimi ve otomasyon kuralları.

---

## Phoenix AI Mimarisi

```
app/Services/
├── PhoenixAIService.php       # Ana öneri motoru (rule-based)
├── CustomerScoreService.php   # RFM skorlama servisi
└── PhoenixAI/                 # (Gelecek: alt modüller)
    ├── Rules/                 # Kural dosyaları
    ├── Prompts/               # LLM prompt şablonları
    └── Memory/                # AI hafıza yönetimi

app/Jobs/
├── CalculateCustomerScores.php  # Gece 03:00
└── SyncSegmentCustomers.php     # Gece 03:30

app/Filament/Widgets/
├── PhoenixAIWidget.php          # Dashboard öneri tablosu
└── CustomerIntelligence.php     # Müşteri istihbarat kartları
```

## Öneri Motoru

### Kural Tabanlı Sistem (Mevcut)
Harici API gerektirmez. Her zaman çalışır.

| Kural ID | Tip | Tetikleme | Öncelik |
|---|---|---|---|
| STOCK_001 | stock_alert | Ürün stoku ≤ 3 | Kritik (0) / Yüksek |
| CUST_001 | customer_retention | 30+ gün inaktif, Risk ≥ 60 | Yüksek |
| VIP_001 | vip_at_risk | LTV ≥ 500₺, Risk ≥ 50 | Kritik |
| REV_001 | revenue_drop | Haftalık gelir %20+ düşüş | Kritik / Yüksek |
| CART_001 | abandoned_carts | 24 saatte 3+ terk sepet | Yüksek |
| VIP_002 | vip_opportunity | Yüksek skor, düşük risk, 3+ sipariş | Orta |

### Öneri Yapısı
```php
AiRecommendation::create([
    'type' => 'stock_alert',
    'priority' => 'critical',          // critical, high, medium, low
    'title' => '⛔ Ürün stokta yok',
    'description' => 'Neden ve ne yapılmalı...',
    'action_data' => [                  // Aksiyon verileri
        'product_id' => 42,
        'action' => 'restock',
    ],
    'user_id' => null,                  // İlgili müşteri (varsa)
    'expires_at' => now()->addDays(7),  // Son geçerlilik
    'status' => 'pending',             // pending, accepted, dismissed, completed
]);
```

### Duplikasyon Önleme
Her kural üretmeden önce mevcut pending önerileri kontrol eder:
```php
$exists = AiRecommendation::where('type', 'stock_alert')
    ->where('status', 'pending')
    ->whereJsonContains('action_data->product_id', $product->id)
    ->exists();
```

## LLM Entegrasyonu (Gelecek)

### İki Katmanlı Mimari
1. **Rule-Based (Her zaman)**: Veritabanı kurallarıyla otomatik
2. **LLM-Powered (Opsiyonel)**: OpenAI/Gemini API ile zenginleştirme

### LLM Kullanım Alanları
- Öneri açıklamalarını doğal dilde yazma
- Müşteri profil özetleri oluşturma
- Kampanya metni üretme
- Anomali tespiti raporları
- Chatbot (müşteri hizmetleri)

### Prompt Yönetimi
```
.phoenix-core/prompts/
├── customer_summary.md
├── campaign_text.md
├── product_description.md
└── anomaly_report.md
```

## Hafıza Sistemi

### Kısa Dönem Hafıza
- `customer_events` tablosu (son 90 gün)
- Session bazlı davranış verisi

### Uzun Dönem Hafıza
- `customer_scores` tablosu (hesaplanmış metrikler)
- `customer_segments` (segment üyelikleri)
- `ai_recommendations` (geçmiş öneriler ve sonuçları)

### Hafıza Temizleme
- 90 günden eski event'ler arşivlenir/silinir
- Tamamlanmış/reddedilmiş öneriler 7 gün sonra silinir

## Görev Motoru (Scheduler)

```
03:00  → CalculateCustomerScores (Tüm müşteri skorları)
03:30  → SyncSegmentCustomers (Segment eşleştirme)
*/120  → PhoenixAIService::generateRecommendations (Öneri üretimi)
```

## Dashboard Widget

### PhoenixAI Widget Davranışı
- Tablo formatında öneriler
- Öncelik sırasına göre sıralı (critical → high → medium → low)
- "Uygula" butonu → status = completed
- "Reddet" butonu → status = dismissed
- Süresi dolmuş öneriler otomatik filtrelenir
- Boş durum: "Phoenix AI sürekli analiz yapıyor"

## Crawler Sistemi (Gelecek)

### Planlanan Modüller
- Rakip fiyat takibi
- Sosyal medya trend analizi
- SEO değişiklik algılama
- Müşteri yorum analizi (sentiment)

### Crawler Mimarisi
```
app/Services/PhoenixAI/Crawler/
├── PriceCrawler.php
├── SocialCrawler.php
├── SEOCrawler.php
└── ReviewCrawler.php
```

### Bilgi Tabanı
- Crawler verileri → İnceleme kuyruğuna
- Admin onayı → Bilgi tabanına
- Güvenilirlik skoru her kaynağa
- Vektör arama hazır (gelecek)

## AI Logları

Her AI işlemi loglanır:
```
[2026-07-01 03:00:00] INFO: Customer scores calculated for 150 customers.
[2026-07-01 03:30:00] INFO: Segment sync completed for 6 dynamic segments.
[2026-07-01 05:00:00] INFO: Phoenix AI generated 3 recommendations.
```

## Artisan Komutları
```bash
php artisan phoenix:scores           # Skorları hesapla
php artisan phoenix:recommendations  # Önerileri üret
php artisan phoenix:sync-segments    # Segmentleri eşleştir
```

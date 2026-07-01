# 06 — Phoenix AI Kuralları

> Phoenix AI motoru kural tabanlı çalışır. Harici API bağımlılığı olmadan stok, müşteri, gelir ve sepet analizleri yapar.

---

## Temel Felsefe

```
🧠 Rule-based FIRST — LLM optional LATER
```

| İlke | Açıklama |
|---|---|
| **Harici API bağımlılığı yok** | Temel özellikler tamamen kural tabanlı çalışır |
| **Deterministic** | Aynı veri = aynı sonuç, her seferinde |
| **Self-contained** | MySQL + PHP ile çalışır, ek servis gerektirmez |
| **Actionable** | Her öneri bir "neden" ve bir "aksiyon" içerir |
| **Scheduled** | Zamanlanmış görevlerle otomatik çalışır |
| **Future-proof** | LLM entegrasyonu opsiyonel, kural motoru her zaman çalışır |

---

## Öneri Sistemi (Recommendations)

### 6 Öneri Tipi

| Tip | Kod | Öncelik | Tetikleyici Koşul |
|---|---|---|---|
| **Stok Uyarısı** | `stock_alert` | `critical` | Varyant stoğu ≤ 5 ve son 7 günde satış var |
| **Müşteri Kaybı** | `customer_retention` | `high` | Son 60 gün sipariş yok + skor düşüyor |
| **VIP Risk** | `vip_at_risk` | `critical` | VIP müşteri (80+) son 45 günde inaktif |
| **Gelir Düşüşü** | `revenue_drop` | `high` | Haftalık gelir önceki haftaya göre ≥%20 düşüş |
| **Terk Edilen Sepet** | `abandoned_carts` | `medium` | Sepette ürün var + 48 saat inaktif |
| **VIP Potansiyeli** | `vip_opportunity` | `medium` | Skor 65-79, yükselen trend |

### Öneri Veri Yapısı

```php
AiRecommendation::create([
    'type'        => 'stock_alert',
    'priority'    => 'critical',
    'title'       => 'Kritik Stok Uyarısı: Patenli Pro X',
    'description' => 'Patenli Pro X - Kırmızı/38 varyantında sadece 2 adet stok kaldı. Son 7 günde 15 adet satıldı.',
    'action'      => 'Tedarikçiden en az 50 adet sipariş verin. Mevcut satış hızında 1 gün içinde tükenecek.',
    'data'        => json_encode([
        'product_id'  => 42,
        'variant_id'  => 187,
        'current_stock' => 2,
        'weekly_sales'  => 15,
        'days_until_stockout' => 1,
    ]),
    'status'      => 'pending',     // pending | accepted | dismissed
    'expires_at'  => now()->addDays(7),
]);
```

### Öncelik Seviyeleri

| Seviye | Kod | Dashboard Rengi | Müdahale Süresi |
|---|---|---|---|
| 🔴 Kritik | `critical` | `danger` | Hemen |
| 🟠 Yüksek | `high` | `warning` | 24 saat içinde |
| 🔵 Orta | `medium` | `info` | 1 hafta içinde |
| ⚪ Düşük | `low` | `gray` | Uygun zamanda |

### Öneri Durumları

```
pending   → Yeni öneri, aksiyona geçilmedi
accepted  → Admin kabul etti, aksiyon alınıyor
dismissed → Admin reddetti (gerekçe opsiyonel)
expired   → expires_at geçti, otomatik expire
```

---

## Mükerrer Önleme (Duplicate Prevention)

Aynı tipte ve aynı hedef için birden fazla aktif öneri oluşturulmamalıdır:

```php
// Yeni öneri oluşturmadan önce kontrol
$exists = AiRecommendation::where('type', $type)
    ->where('status', 'pending')
    ->where('data->product_id', $productId)  // veya ilgili entity
    ->where('expires_at', '>', now())
    ->exists();

if ($exists) {
    return; // Mükerrer öneri oluşturma
}
```

### Kontrol Edilen Mükerrer Koşulları

| Öneri Tipi | Benzersizlik Kriteri |
|---|---|
| `stock_alert` | Aynı `variant_id` için pending öneri |
| `customer_retention` | Aynı `user_id` için pending öneri |
| `vip_at_risk` | Aynı `user_id` için pending öneri |
| `revenue_drop` | Aynı hafta için pending öneri |
| `abandoned_carts` | Aynı `user_id` için pending öneri |
| `vip_opportunity` | Aynı `user_id` için pending öneri |

---

## Müşteri Puanlama Algoritması

### RFM Tabanlı Scoring

```php
class CustomerScoreService
{
    // Ağırlıklar
    private const PURCHASE_WEIGHT    = 0.30;
    private const ACTIVITY_WEIGHT    = 0.20;
    private const LOYALTY_WEIGHT     = 0.25;
    private const ENGAGEMENT_WEIGHT  = 0.25;

    public function calculateScore(User $user): float
    {
        $purchase   = $this->calculatePurchaseScore($user);
        $activity   = $this->calculateActivityScore($user);
        $loyalty    = $this->calculateLoyaltyScore($user);
        $engagement = $this->calculateEngagementScore($user);

        return round(
            ($purchase   * self::PURCHASE_WEIGHT)
          + ($activity   * self::ACTIVITY_WEIGHT)
          + ($loyalty    * self::LOYALTY_WEIGHT)
          + ($engagement * self::ENGAGEMENT_WEIGHT),
            2
        );
    }
}
```

### Purchase Score (Satın Alma — %30)

| Metrik | Max Puan | Hesaplama |
|---|---|---|
| Toplam sipariş sayısı | 40 | `min(orders_count * 5, 40)` |
| Toplam harcama | 40 | `min(total_spent / 100, 40)` |
| Son sipariş yeniliği | 20 | Son 7 gün=20, 30 gün=15, 60 gün=10, 90 gün=5 |

### Activity Score (Aktivite — %20)

| Metrik | Max Puan | Hesaplama |
|---|---|---|
| Son ziyaret | 40 | Son 1 gün=40, 7 gün=30, 30 gün=15, diğer=5 |
| Sayfa görüntüleme (30 gün) | 30 | `min(page_views / 10, 30)` |
| Sepet aktivitesi (30 gün) | 30 | Ekleme varsa=30, yoksa=0 |

### Loyalty Score (Sadakat — %25)

| Metrik | Max Puan | Hesaplama |
|---|---|---|
| Müşteri yaşı | 40 | `min(months_since_registration * 3, 40)` |
| Tekrar alım oranı | 40 | 3+ sipariş=40, 2=25, 1=10 |
| İptal oranı | 20 | %0=20, <%10=15, <%25=10, diğer=0 |

### Engagement Score (Etkileşim — %25)

| Metrik | Max Puan | Hesaplama |
|---|---|---|
| Yorum sayısı | 35 | `min(reviews_count * 7, 35)` |
| Favori sayısı | 30 | `min(wishlist_count * 5, 30)` |
| Kupon kullanımı | 35 | Kullandıysa=35, kullanmadıysa=0 |

---

## Segment Senkronizasyonu

### Dinamik Segmentler

Her segment JSON koşullarına sahiptir:

```php
CustomerSegment::create([
    'name'        => 'VIP Müşteriler',
    'slug'        => 'vip-musteriler',
    'description' => 'Skoru 80 ve üzeri olan müşteriler',
    'conditions'  => json_encode([
        ['field' => 'total_score', 'operator' => '>=', 'value' => 80],
    ]),
    'is_active'   => true,
    'color'       => '#10b981',  // success yeşili
]);
```

### Koşul Operatörleri

| Operatör | Açıklama | Örnek |
|---|---|---|
| `>=` | Büyük eşit | `total_score >= 80` |
| `<=` | Küçük eşit | `total_score <= 19` |
| `>` | Büyük | `orders_count > 5` |
| `<` | Küçük | `days_since_last_order < 30` |
| `=` | Eşit | `tier = 'vip'` |
| `!=` | Eşit değil | `status != 'cancelled'` |
| `between` | Arasında | `total_score between [40, 59]` |

### Sync Süreci

```php
// phoenix:sync-segments komutu
foreach (CustomerSegment::active()->get() as $segment) {
    $matchingUserIds = $this->evaluateConditions($segment->conditions);
    $segment->users()->sync($matchingUserIds);
}
```

---

## Artisan Komutları

### `phoenix:scores`

```bash
php artisan phoenix:scores
# Tüm müşterilerin skorlarını yeniden hesaplar
# Çalışma: 03:00 (günlük)
# Süre: ~30 saniye (1000 müşteri için)
```

### `phoenix:recommendations`

```bash
php artisan phoenix:recommendations
# 6 tip öneri üretir
# Çalışma: Her 2 saatte bir
# Mükerrer kontrol: Var
# Expired öneri temizleme: Var
```

### `phoenix:sync-segments`

```bash
php artisan phoenix:sync-segments
# Segment koşullarına göre üyelikleri günceller
# Çalışma: 03:30 (günlük, skorlardan sonra)
# Sıralama önemli: Önce skorlar, sonra segmentler
```

---

## Dashboard AI Widget

### PhoenixAIWidget

Dashboard'da tablo formatında AI önerilerini gösterir:

```php
class PhoenixAIWidget extends TableWidget
{
    protected static ?string $heading = 'Phoenix AI Önerileri';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AiRecommendation::query()
                    ->where('status', 'pending')
                    ->where('expires_at', '>', now())
                    ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('priority')
                    ->label('Öncelik')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'critical' => 'danger',
                        'high'     => 'warning',
                        'medium'   => 'info',
                        'low'      => 'gray',
                    }),
                TextColumn::make('title')
                    ->label('Başlık')
                    ->wrap(),
                TextColumn::make('action')
                    ->label('Önerilen Aksiyon')
                    ->wrap(),
            ])
            ->actions([
                Action::make('accept')
                    ->label('Kabul Et')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (AiRecommendation $record) =>
                        $record->update(['status' => 'accepted'])
                    ),
                Action::make('dismiss')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(fn (AiRecommendation $record) =>
                        $record->update(['status' => 'dismissed'])
                    ),
            ]);
    }
}
```

---

## Öneri Formatı

Her öneri şu bilgileri **zorunlu** olarak içermelidir:

| Alan | Açıklama | Örnek |
|---|---|---|
| **title** | Kısa, anlaşılır başlık | "Kritik Stok Uyarısı: Patenli Pro X" |
| **description** | NEDEN bu öneri üretildi | "Son 7 günde 15 adet satıldı, sadece 2 adet kaldı" |
| **action** | Ne yapılmalı, spesifik adım | "Tedarikçiden 50 adet sipariş verin" |
| **data** | Destekleyici JSON verisi | `{product_id, variant_id, current_stock, ...}` |

### ❌ Kötü Öneri Örneği

```
title: "Stok azaldı"
description: "Bir ürünün stoğu düşük"
action: "Stok ekleyin"
```

### ✅ İyi Öneri Örneği

```
title: "Kritik Stok: Patenli Pro X - Kırmızı/38"
description: "Bu varyant son 7 günde 15 adet satıldı. Mevcut stok: 2 adet.
              Tahmini tükenme: 1 gün içinde. Geçen ay toplam 58 adet satıldı."
action: "Tedarikçiden en az 50 adet Patenli Pro X - Kırmızı/38 sipariş verin.
         Mevcut satış hızıyla bu miktar ~23 günlük stok sağlar."
```

---

## Gelecek Planı: LLM Entegrasyonu

```
Faz 1 (Mevcut): Kural tabanlı motor ✅
Faz 2 (Gelecek): LLM ile öneri metinlerini zenginleştirme
Faz 3 (Gelecek): Doğal dil ile veri sorgulama
Faz 4 (Gelecek): Otomatik kampanya önerileri
```

> **Kural:** LLM entegrasyonu opsiyoneldir. Kural tabanlı motor her zaman çalışır ve temel işlevselliği sağlar. LLM erişilemez olduğunda sistem degrade olmaz.

---

## Güvenlik Kuralları

| Kural | Açıklama |
|---|---|
| AI önerileri sadece admin panelde görünür | Frontend'de Phoenix AI verisi yok |
| Müşteri skorları müşteriye gösterilmez | Sadece admin erişimi |
| AI komutları sadece CLI/Scheduler çalıştırır | HTTP endpoint yok |
| Rate limiting | Öneri üretimi saatte max 100 öneri |
| Data retention | 90 gün sonra expired öneriler temizlenir |

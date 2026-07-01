# 08 — Performans Optimizasyon Kuralları

## HTTP Sunucu

### RoadRunner (Octane)
- Uygulama **Laravel Octane + RoadRunner** ile servis edilir.
- Worker'lar bellekte kalır → her request'te bootstrap tekrarlanmaz.
- **Dikkat:** Statik değişkenler request'ler arasında paylaşılır. Service container binding'leri doğru scope ile yapılmalıdır.

```bash
# Geliştirme
php artisan octane:start --watch

# Production
php artisan octane:start --workers=4 --max-requests=500
```

### SPA Modu (Filament)
- Filament panelde **SPA modu aktif** → tam sayfa yenilemesi olmaz.
- `->spa()` panel konfigürasyonunda etkinleştirilir.
- Daha hızlı navigasyon, daha az sunucu yükü.

---

## Veritabanı Performansı

### Eager Loading (N+1 Önleme)
- **Her zaman** `->with()` kullanılır. N+1 query sorunu kabul edilmez.
- Filament Resource'larda `$table->query()` ile eager loading:

```php
// ✅ Doğru
Order::query()->with(['user', 'items.product']);

// ❌ Yanlış — N+1 sorunu
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->user->name; // Her iterasyonda query
}
```

- Development'ta `preventLazyLoading()` aktif edilebilir:
```php
Model::preventLazyLoading(! app()->isProduction());
```

### Veritabanı İndeksleme
- **Tüm foreign key'ler** otomatik indekslenir (`constrained()`).
- Sık sorgulanan sütunlar için index eklenir:
  - `orders.status`
  - `orders.created_at`
  - `products.is_active`
  - `products.slug` (unique)
  - `customers.email` (unique)
  - `customer_scores.total_score`

### Query Optimizasyonu
- Widget'larda `COUNT()`, `SUM()`, `AVG()` gibi **aggregation** fonksiyonları kullanılır.
- Tam model yüklemekten kaçınılır:

```php
// ✅ Doğru — Sadece sayı döner
Order::where('status', 'pending')->count();

// ❌ Yanlış — Tüm modelleri belleğe yükler
Order::where('status', 'pending')->get()->count();
```

---

## Cache Stratejisi

### Mevcut Yapılandırma
| Katman | Driver | Not |
|---|---|---|
| Cache | `database` | Production'da Redis'e geçiş planlandı |
| Session | `database` | — |
| Queue | `database` | Production'da Redis'e geçiş planlandı |

### Cache Kullanım Kuralları
- Dashboard istatistikleri: **5 dakika** cache süresi
- Ürün listesi: **10 dakika** cache süresi
- Konfigürasyon: **1 saat** cache süresi

```php
// Widget'larda cache kullanımı
Cache::remember('dashboard.stats', 300, function () {
    return [
        'total_revenue' => Order::sum('total_amount'),
        'total_orders'  => Order::count(),
    ];
});
```

### Production Cache Komutları
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache
php artisan filament:cache-components
```

---

## Tablo & Pagination

### Filament Tablo Performansı
- Varsayılan Filament pagination kullanılır (sayfa başına 10/25/50).
- Büyük tablolarda `->deferLoading()` kullanılır.
- Sıralama için gerekli sütunlarda index bulunmalıdır.

```php
$table
    ->defaultPaginationPageOption(25)
    ->deferLoading()
    ->defaultSort('created_at', 'desc');
```

---

## Görsel Optimizasyonu

### Ürün Görselleri
- Yükleme sırasında **otomatik boyutlandırma**: maksimum 800×800 piksel.
- Kabul edilen formatlar: JPEG, PNG, WebP.
- Maksimum dosya boyutu: 2 MB.
- Thumbnail oluşturma: 200×200 piksel (listeleme için).

```php
FileUpload::make('image')
    ->image()
    ->imageResizeMode('cover')
    ->imageCropAspectRatio('1:1')
    ->imageResizeTargetWidth(800)
    ->imageResizeTargetHeight(800)
    ->maxSize(2048);
```

---

## Widget Performansı

### Lazy Loading
- Ağır widget'lar `$isLazy = true` ile lazy load edilir.
- Dashboard ilk yüklemede sadece temel istatistikler görünür.

```php
protected static bool $isLazy = true;
```

### Polling (Otomatik Yenileme)
- Dashboard widget'larında polling dikkatli kullanılır.
- Önerilen interval: **30 saniye** (gereksiz yükten kaçınmak için).

---

## Toplu İşlemler

### Chunk Processing
- Bulk işlemler **100'lük chunk'lar** halinde yapılır:

```php
Product::chunk(100, function ($products) {
    foreach ($products as $product) {
        // İşlem
    }
});
```

- Büyük veri setlerinde `LazyCollection` veya `cursor()` kullanılır:

```php
// Bellek dostu iterasyon
foreach (Product::cursor() as $product) {
    // Tek tek işler, belleği korur
}
```

### Queue İşleri
- Uzun süren işlemler (email gönderimi, rapor üretimi) **queue'ya** gönderilir.
- `ShouldQueue` interface'i implement edilir.
- Retry: 3 deneme, backoff: 60 saniye.

---

## Performans İzleme Kontrol Listesi

- [ ] N+1 query kontrolü yapıldı (`debugbar` veya `preventLazyLoading`)
- [ ] Tüm foreign key'lerde index var
- [ ] Widget'larda aggregation query kullanılıyor
- [ ] Cache komutları production'da çalıştırıldı
- [ ] Görseller optimize edildi
- [ ] Gereksiz eager loading yok (sadece kullanılan ilişkiler)
- [ ] Büyük tablolarda pagination aktif

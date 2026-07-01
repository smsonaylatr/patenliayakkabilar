# 01 — Sistem Mimarisi & Teknoloji Yığını

> **Proje:** Patenli Ayakkabılar — Tekerlekli ayakkabı e-ticaret platformu
> **Domain:** patenliayakkabilar.com
> **Admin Panel:** `/admin` (Filament)

---

## Teknoloji Yığını

| Katman | Teknoloji | Versiyon | Notlar |
|---|---|---|---|
| **Framework** | Laravel | 12.x | PHP 8.2+ |
| **Admin Panel** | Filament | 5.6 | `/admin` yolunda |
| **Reaktif UI** | Livewire | 4.x | Full-stack components |
| **JS Micro-framework** | Alpine.js | 3.x | Livewire ile entegre |
| **Veritabanı** | MySQL | 8.x | `patenli_db` |
| **Uygulama Sunucusu** | RoadRunner | via Octane | Persistent worker |
| **Queue Driver** | Database | — | Worker aktif |
| **Mail Driver** | Log | — | ⚠️ Production'da SMTP gerekli |
| **Cache** | File / Database | — | Redis önerilir |
| **Dil** | Türkçe (tr) | — | Tüm etiketler, menüler, bildirimler |
| **Para Birimi** | ₺ (Türk Lirası) | — | Prefix olarak kullanılır |

---

## Veritabanı: `patenli_db`

27+ tablo ile kapsamlı e-ticaret veri modeli:

### Ana Tablolar

| Tablo | Açıklama |
|---|---|
| `users` | Müşteriler ve adminler |
| `products` | Ürün ana kayıtları (soft delete) |
| `product_variants` | Renk, beden, tekerlek tipi, stok |
| `categories` | Hiyerarşik kategori ağacı |
| `orders` | Sipariş ana kayıtları |
| `order_items` | Sipariş kalemleri |
| `coupons` | Kupon / indirim kodları |
| `addresses` | Kullanıcı adresleri |
| `banners` | Anasayfa banner yönetimi |
| `reviews` | Ürün değerlendirmeleri |
| `wishlists` | Favori listeleri |
| `pages` | Statik sayfalar (Hakkımızda, SSS vb.) |

### Phoenix AI Tabloları

| Tablo | Açıklama |
|---|---|
| `customer_scores` | RFM tabanlı müşteri puanları |
| `customer_segments` | Dinamik müşteri segmentleri |
| `customer_segment_user` | Segment-müşteri pivot |
| `ai_recommendations` | AI önerileri |
| `customer_activities` | Müşteri aktivite logları |

---

## Sunucu Yapılandırması

### RoadRunner (Octane)

```bash
# Başlatma
php artisan octane:start --server=roadrunner --port=8000

# Worker sayısı
php artisan octane:start --workers=4
```

> **Dikkat:** RoadRunner persistent worker kullanır. Statik değişkenler istekler arasında paylaşılır. Singleton binding'lerde dikkatli olun.

### Queue Worker

```bash
# Queue worker başlatma
php artisan queue:work --queue=default --tries=3 --timeout=60
```

Aktif Job'lar:
- `CalculateCustomerScores` — Müşteri puanlarını hesaplar
- `SyncSegmentCustomers` — Segment üyeliklerini günceller

### Zamanlanmış Görevler (Scheduler)

| Saat | Komut | Açıklama |
|---|---|---|
| 03:00 | `phoenix:scores` | Müşteri puanlarını hesapla |
| Her 2 saat | `phoenix:recommendations` | AI önerileri üret |
| 03:30 | `phoenix:sync-segments` | Segment üyeliklerini senkronize et |

---

## Mail Yapılandırması

```env
# Şu an (development)
MAIL_MAILER=log

# Production için gerekli
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=info@patenliayakkabilar.com
MAIL_PASSWORD=xxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@patenliayakkabilar.com
MAIL_FROM_NAME="Patenli Ayakkabılar"
```

> ⚠️ **TODO:** Production geçişinde gerçek SMTP yapılandırması yapılmalı. Sipariş onayı, kargo bildirimi ve şifre sıfırlama e-postaları gönderilemiyor.

---

## Ortam Değişkenleri (Kritik)

```env
APP_NAME="Patenli Ayakkabılar"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=tr

DB_CONNECTION=mysql
DB_DATABASE=patenli_db

FILAMENT_PATH=admin
QUEUE_CONNECTION=database
```

---

## Bağımlılıklar (Composer)

| Paket | Amaç |
|---|---|
| `filament/filament` | Admin panel |
| `laravel/octane` | RoadRunner entegrasyonu |
| `livewire/livewire` | Reaktif bileşenler |
| `laravel/sanctum` | API authentication (gelecek) |

---

## Notlar

1. **Filament 5.6** kullanılıyor — Schema sınıfı `Filament\Schemas\Schema` namespace'ini kullanır, **`Filament\Forms\Form` değil**.
2. **SPA modu** aktif — sayfa geçişlerinde tam yenileme olmaz.
3. **Dark mode** kullanıcı tarafından değiştirilebilir.
4. **Global arama** `Ctrl+K` / `Cmd+K` ile erişilir.
5. **Database notifications** — Filament bell icon ile bildirimler gösterilir.

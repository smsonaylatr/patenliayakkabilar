# 02 — Proje Mimarisi & Klasör Yapısı

> Patenli Ayakkabılar projesinin modüler mimari yapısı ve dosya organizasyonu.

---

## Genel Klasör Yapısı

```
lawire/
├── app/
│   ├── Filament/              # Admin panel (Filament 5.6)
│   │   ├── Resources/         # 11+ CRUD kaynağı
│   │   ├── Widgets/           # 6 dashboard widget
│   │   └── Pages/             # Özel sayfalar
│   ├── Models/                # 18+ Eloquent model
│   ├── Services/              # İş mantığı servisleri
│   ├── Jobs/                  # Arkaplan görevleri
│   ├── Observers/             # Model event dinleyicileri
│   ├── Livewire/              # Frontend bileşenleri
│   ├── Providers/             # Service providers
│   └── Console/               # Artisan komutları
├── database/
│   ├── migrations/            # 24+ migration
│   ├── seeders/               # Test verileri
│   └── factories/             # Model factory'ler
├── resources/
│   ├── views/                 # Blade view'lar
│   │   ├── livewire/          # Livewire component view'ları
│   │   ├── components/        # Blade component'lar
│   │   └── layouts/           # Layout şablonları
│   ├── css/                   # Tailwind CSS
│   └── js/                    # Alpine.js & scripts
├── routes/
│   ├── web.php                # Web rotaları
│   └── console.php            # Artisan komut rotaları
├── config/                    # Yapılandırma dosyaları
├── public/                    # Public assets
└── storage/                   # Log, cache, uploads
```

---

## Model Katmanı (`app/Models/`)

18+ Eloquent model:

| Model | Tablo | Açıklama | Özellikler |
|---|---|---|---|
| `User` | `users` | Müşteri/Admin | HasMany: orders, addresses, reviews |
| `Product` | `products` | Ürün | SoftDeletes, HasMany: variants, reviews |
| `ProductVariant` | `product_variants` | Ürün varyantı | BelongsTo: product |
| `Category` | `categories` | Kategori | Self-referencing (parent_id) |
| `Order` | `orders` | Sipariş | BelongsTo: user, HasMany: items |
| `OrderItem` | `order_items` | Sipariş kalemi | BelongsTo: order, product_variant |
| `Address` | `addresses` | Adres | BelongsTo: user |
| `Coupon` | `coupons` | Kupon | Percentage veya fixed |
| `Banner` | `banners` | Anasayfa banner | Sıralama, aktif/pasif |
| `Review` | `reviews` | Ürün yorumu | BelongsTo: user, product |
| `Wishlist` | `wishlists` | Favori listesi | BelongsTo: user, product |
| `Page` | `pages` | Statik sayfa | Slug tabanlı |
| `CustomerScore` | `customer_scores` | Müşteri puanı | BelongsTo: user |
| `CustomerSegment` | `customer_segments` | Müşteri segmenti | BelongsToMany: users |
| `AiRecommendation` | `ai_recommendations` | AI önerisi | Priority, status, expiry |
| `CustomerActivity` | `customer_activities` | Aktivite logu | BelongsTo: user |
| `CartItem` | `cart_items` | Sepet kalemi | BelongsTo: user, product_variant |
| `Setting` | `settings` | Sistem ayarları | Key-value pair |

---

## Filament Admin Panel (`app/Filament/`)

### Modüler Resource Yapısı

Filament 5.6'da her resource şu yapıyı takip eder:

```
app/Filament/Resources/
├── ProductResource.php            # Ana resource sınıfı
├── ProductResource/
│   ├── Pages/
│   │   ├── ListProducts.php       # Listeleme sayfası
│   │   ├── CreateProduct.php      # Oluşturma sayfası
│   │   └── EditProduct.php        # Düzenleme sayfası
│   ├── Schemas/
│   │   └── ProductSchema.php      # Form şeması (opsiyonel)
│   ├── Tables/
│   │   └── ProductTable.php       # Tablo yapılandırması (opsiyonel)
│   └── RelationManagers/
│       ├── VariantsRelationManager.php
│       └── ReviewsRelationManager.php
```

### Resource Dosyası Anatomisi

```php
// ProductResource.php
namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;           // ⚠️ 5.6'da bu namespace!
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Katalog Yönetimi';
    protected static ?string $modelLabel = 'Ürün';
    protected static ?string $pluralModelLabel = 'Ürünler';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            // Form bileşenleri
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            // Tablo sütunları
        ]);
    }

    public static function getRelations(): array
    {
        return [
            VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
```

### Mevcut Resource'lar (11+)

| Resource | Model | Nav Group | Özellikler |
|---|---|---|---|
| `ProductResource` | Product | Katalog Yönetimi | Variants RM, Reviews RM, SoftDeletes |
| `CategoryResource` | Category | Katalog Yönetimi | Hiyerarşik, self-reference |
| `OrderResource` | Order | Satışlar | OrderItems RM, durum yönetimi |
| `UserResource` | User | Müşteri İstihbaratı | Addresses RM, Score bilgisi |
| `CouponResource` | Coupon | Satışlar | Yüzde/sabit indirim |
| `BannerResource` | Banner | İçerik | Sıralama, görsel yükleme |
| `ReviewResource` | Review | Katalog Yönetimi | Onay/red akışı |
| `PageResource` | Page | İçerik | Rich text editor |
| `AddressResource` | Address | Müşteri İstihbaratı | İl/ilçe seçimi |
| `CustomerSegmentResource` | CustomerSegment | Müşteri İstihbaratı | JSON condition builder |
| `AiRecommendationResource` | AiRecommendation | Müşteri İstihbaratı | Kabul/red aksiyonları |

---

## Dashboard Widget'ları (`app/Filament/Widgets/`)

| Widget | Tip | Açıklama |
|---|---|---|
| `StatsOverviewWidget` | Stats | Toplam sipariş, gelir, müşteri, ürün istatistikleri |
| `OrderChartWidget` | Chart | Son 30 gün sipariş grafiği |
| `LowStockAlertWidget` | Table | Düşük stoklu ürün uyarıları |
| `PhoenixAIWidget` | Table | AI önerileri listesi (kabul/reddet) |
| `CustomerIntelligenceWidget` | Stats | Segment dağılımı ve ortalama skor |
| `LatestOrdersWidget` | Table | Son siparişler tablosu |

---

## Özel Sayfalar (`app/Filament/Pages/`)

| Sayfa | Açıklama |
|---|---|
| `BannerSettings` | Anasayfa banner sıralama ve ayarları |

---

## Servis Katmanı (`app/Services/`)

| Servis | Sorumluluk |
|---|---|
| `CartService` | Sepet yönetimi (ekle, çıkar, güncelle, toplam hesapla) |
| `CustomerScoreService` | RFM tabanlı müşteri puanlama algoritması |
| `PhoenixAIService` | Öneri üretimi, segment senkronizasyonu |

---

## Job'lar (`app/Jobs/`)

| Job | Queue | Açıklama |
|---|---|---|
| `CalculateCustomerScores` | default | Tüm müşterilerin skorlarını hesaplar |
| `SyncSegmentCustomers` | default | Segment koşullarına göre üyelikleri günceller |

---

## Observer'lar (`app/Observers/`)

| Observer | Model | Dinlediği Event'ler |
|---|---|---|
| `OrderObserver` | Order | created, updated (stok güncelleme, bildirim) |

---

## Livewire Bileşenleri (`app/Livewire/`)

```
app/Livewire/
├── Account/
│   ├── OrderHistory.php       # Sipariş geçmişi
│   ├── AddressBook.php        # Adres defteri
│   └── Profile.php            # Profil düzenleme
├── Auth/
│   ├── Login.php              # Giriş
│   └── Register.php           # Kayıt
├── Frontend/
│   ├── Cart.php               # Sepet sayfası
│   ├── Checkout.php           # Ödeme sayfası
│   ├── HomePage.php           # Anasayfa
│   └── CategoryPage.php      # Kategori sayfası
└── Product/
    ├── ProductDetail.php      # Ürün detay
    ├── ProductCard.php        # Ürün kartı
    └── ProductFilters.php     # Filtreleme bileşeni
```

---

## Migration'lar (`database/migrations/`)

24+ migration dosyası kronolojik sırayla. Önemli tablolar:

```
create_users_table
create_categories_table
create_products_table
create_product_variants_table
create_orders_table
create_order_items_table
create_addresses_table
create_coupons_table
create_banners_table
create_reviews_table
create_wishlists_table
create_pages_table
create_customer_scores_table
create_customer_segments_table
create_customer_segment_user_table
create_ai_recommendations_table
create_customer_activities_table
create_cart_items_table
create_settings_table
...
```

---

## Navigasyon Grupları

```
📊 Dashboard
📦 Katalog Yönetimi
   ├── Ürünler
   ├── Kategoriler
   └── Değerlendirmeler
💰 Satışlar
   ├── Siparişler
   └── Kuponlar
🧠 Müşteri İstihbaratı
   ├── Müşteriler
   ├── Segmentler
   └── AI Önerileri
📝 İçerik
   ├── Bannerlar
   └── Sayfalar
⚙️ Site Yönetimi
   └── Ayarlar
```

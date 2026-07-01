# 03 — Kodlama Standartları

> Patenli Ayakkabılar projesinde tüm kodun uyması gereken standartlar ve kurallar.

---

## Genel Kurallar

| Kural | Standart |
|---|---|
| PHP Versiyonu | 8.2+ |
| Kod Stili | PSR-12 |
| Strict Types | Mümkün olan her yerde `declare(strict_types=1)` |
| Etiketler (Labels) | Türkçe |
| Değişken / Metot / Sınıf İsimleri | İngilizce |
| Para Birimi | ₺ (prefix) |
| Tarih Formatı | `d.m.Y H:i` |
| Tarih Kütüphanesi | Carbon |

---

## Filament 5.6 Konvansiyonları

### ⚠️ Kritik: Schema Namespace

Filament 5.6'da form metodu `Schema` sınıfını kullanır:

```php
// ✅ DOĞRU — Filament 5.6
use Filament\Schemas\Schema;

public static function form(Schema $schema): Schema
{
    return $schema->components([
        // ...
    ]);
}
```

```php
// ❌ YANLIŞ — Eski versiyon
use Filament\Forms\Form;

public static function form(Form $form): Form  // KULLANMA!
```

### Resource Tanımı

```php
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Katalog Yönetimi';
    protected static ?string $modelLabel = 'Ürün';
    protected static ?string $pluralModelLabel = 'Ürünler';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';
}
```

### Widget Heading Kuralı

```php
// TableWidget → static property
class LowStockAlertWidget extends TableWidget
{
    protected static ?string $heading = 'Düşük Stok Uyarıları';  // static
}

// ChartWidget → non-static property
class OrderChartWidget extends ChartWidget
{
    protected ?string $heading = 'Sipariş Grafiği';  // non-static
}

// StatsOverviewWidget → heading yok (otomatik)
```

---

## Model Standartları

### Guarded / Fillable

```php
// Tercih edilen yaklaşım
protected $guarded = [];

// Veya açık fillable listesi
protected $fillable = [
    'name',
    'slug',
    'price',
    'is_active',
];
```

### Cast'ler

```php
protected function casts(): array
{
    return [
        'price'       => 'decimal:2',
        'is_active'   => 'boolean',
        'options'     => 'array',
        'published_at'=> 'datetime',
        'deleted_at'  => 'datetime',
    ];
}
```

### Soft Delete

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
}
```

### İlişkiler

```php
// Her zaman return type belirt
public function variants(): HasMany
{
    return $this->hasMany(ProductVariant::class);
}

public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

// Self-referencing (Category)
public function parent(): BelongsTo
{
    return $this->belongsTo(Category::class, 'parent_id');
}

public function children(): HasMany
{
    return $this->hasMany(Category::class, 'parent_id');
}
```

---

## Form Bileşenleri

### Organizasyon: Tabs veya Sections

```php
public static function form(Schema $schema): Schema
{
    return $schema->components([
        Tabs::make('Ürün')
            ->tabs([
                Tabs\Tab::make('Genel Bilgiler')
                    ->schema([
                        TextInput::make('name')
                            ->label('Ürün Adı')
                            ->required()
                            ->maxLength(255),
                        // ...
                    ]),
                Tabs\Tab::make('Fiyatlandırma')
                    ->schema([
                        TextInput::make('price')
                            ->label('Fiyat')
                            ->prefix('₺')
                            ->numeric()
                            ->required(),
                        // ...
                    ]),
            ]),
    ]);
}
```

### Select Bileşeni

```php
// ✅ Her Select'e native(false) ekle
Select::make('status')
    ->label('Durum')
    ->options([
        'pending'    => 'Beklemede',
        'processing' => 'İşleniyor',
        'shipped'    => 'Kargoda',
        'delivered'  => 'Teslim Edildi',
        'cancelled'  => 'İptal',
    ])
    ->native(false)    // ← Her zaman ekle
    ->required();

// ❌ TextInput ile enum alanı KULLANMA
TextInput::make('status')  // YANLIŞ — Select kullan
```

### Para Birimi Alanları

```php
// ✅ DOĞRU
TextInput::make('price')
    ->label('Fiyat')
    ->prefix('₺')         // ← TL sembolü
    ->numeric()
    ->required();

TextInput::make('discount_amount')
    ->label('İndirim Tutarı')
    ->prefix('₺')
    ->numeric();

// ❌ YANLIŞ
TextInput::make('price')
    ->prefix('$')          // Dolar kullanma!
```

### Tarih Alanları

```php
DateTimePicker::make('created_at')
    ->label('Oluşturulma Tarihi')
    ->displayFormat('d.m.Y H:i')    // Türk formatı
    ->timezone('Europe/Istanbul');
```

### 2 Sütunlu Layout

```php
Section::make('Genel Bilgiler')
    ->schema([
        TextInput::make('name')->label('Ad'),
        TextInput::make('email')->label('E-posta'),
    ])
    ->columns(2);
```

---

## Tablo Standartları

### Türkçe Etiketler

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('id')
                ->label('ID')
                ->sortable(),

            TextColumn::make('name')
                ->label('Ürün Adı')
                ->searchable()
                ->sortable(),

            TextColumn::make('price')
                ->label('Fiyat')
                ->prefix('₺')
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Oluşturulma')
                ->dateTime('d.m.Y H:i')
                ->sortable(),
        ]);
}
```

### Durum Badge'leri

```php
TextColumn::make('status')
    ->label('Durum')
    ->badge()
    ->formatStateUsing(fn (string $state): string => match ($state) {
        'pending'    => 'Beklemede',
        'processing' => 'İşleniyor',
        'shipped'    => 'Kargoda',
        'delivered'  => 'Teslim Edildi',
        'cancelled'  => 'İptal',
        default      => $state,
    })
    ->color(fn (string $state): string => match ($state) {
        'pending'    => 'warning',
        'processing' => 'info',
        'shipped'    => 'primary',
        'delivered'  => 'success',
        'cancelled'  => 'danger',
        default      => 'gray',
    });
```

---

## RelationManager Standartları

```
app/Filament/Resources/ProductResource/
└── RelationManagers/
    └── VariantsRelationManager.php
```

```php
namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $title = 'Varyantlar';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // ...
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            // ...
        ]);
    }
}
```

---

## Servis Katmanı

```php
namespace App\Services;

class CartService
{
    // Constructor injection
    public function __construct(
        private readonly CartRepository $repository,
    ) {}

    // Açık return type
    public function getTotal(): float
    {
        // ...
    }

    // Descriptive method names (İngilizce)
    public function addItem(ProductVariant $variant, int $quantity = 1): CartItem
    {
        // ...
    }
}
```

---

## İsimlendirme Kuralları

| Bağlam | Dil | Örnek |
|---|---|---|
| Sınıf adı | İngilizce | `ProductResource`, `CartService` |
| Metot adı | İngilizce | `calculateScore()`, `syncSegments()` |
| Değişken adı | İngilizce | `$totalPrice`, `$customerScore` |
| Form label | Türkçe | `'Ürün Adı'`, `'Fiyat'`, `'Durum'` |
| Tablo label | Türkçe | `'Sipariş No'`, `'Toplam Tutar'` |
| Navigation group | Türkçe | `'Katalog Yönetimi'`, `'Satışlar'` |
| Model label | Türkçe | `'Ürün'`, `'Sipariş'` |
| Bildirim mesajları | Türkçe | `'Ürün başarıyla oluşturuldu'` |
| Enum options | Türkçe | `'Beklemede'`, `'Kargoda'` |
| Migration dosya adı | İngilizce | `create_products_table` |
| Config key | İngilizce | `'app.name'`, `'mail.driver'` |

---

## Genel İlkeler

1. **DRY** — Tekrardan kaçın, ortak mantığı servislere çıkar.
2. **Fat Model, Thin Controller** — İş mantığını model veya servise koy.
3. **Eager Loading** — N+1 sorgularından kaçın: `->with(['variants', 'category'])`.
4. **Query Scopes** — Tekrarlayan sorguları scope olarak tanımla.
5. **Form Request** — Validasyon kurallarını Form Request sınıflarında topla.
6. **Enum Kullanımı** — PHP 8.1+ enum'ları tercih et, string karşılaştırmalardan kaçın.

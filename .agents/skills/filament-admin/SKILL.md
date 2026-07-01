---
name: filament-admin
description: Laravel + Filament admin panel geliştirme kuralları. Resource, Form, Table, RelationManager, Widget, Policy, Export, Action, Infolist standartları.
---

# Filament Admin Panel Geliştirme Skill

## Kapsam
Bu skill, Laravel 12 + Filament v5.6 admin panel geliştirme kurallarını içerir.

---

## Resource Yapısı

Her Filament resource şu pattern'i takip eder:

```
app/Filament/Resources/{ModuleName}/
├── {ModuleName}Resource.php          # Ana resource sınıfı
├── Pages/
│   ├── List{Module}.php              # Liste sayfası
│   ├── Create{Module}.php            # Oluşturma sayfası
│   ├── Edit{Module}.php              # Düzenleme sayfası
│   └── View{Module}.php              # Görüntüleme sayfası (opsiyonel)
├── Schemas/
│   └── {Module}Form.php              # Form şeması (ayrı dosya)
├── Tables/
│   └── {Module}Table.php             # Tablo yapılandırması (ayrı dosya)
└── RelationManagers/
    └── {Related}RelationManager.php  # İlişki yöneticileri
```

## Form Kuralları

```php
// ✅ Doğru: Filament v5.6 Schema kullanımı
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Tabs')->tabs([
                Tab::make('Genel Bilgiler')->schema([...]),
                Tab::make('Fiyat & Stok')->schema([...]),
            ])
        ]);
    }
}
```

### Form Bileşen Kuralları
- **Select** kullan, TextInput değil (enum alanlar için)
- `->native(false)` her Select'e ekle (styled dropdown)
- `->searchable()` ilişki Select'lerine ekle
- `->prefix('₺')` para alanlarına ekle
- `->hint()` ile karakter sayacı göster (SEO alanları)
- `->live()` auto-slug gibi reaktif alanlar için
- `->columns(2)` düzeni kullan (Section içinde)
- RichEditor full toolbar ile kullan

## Table Kuralları

```php
// Tablo sütunları
TextColumn::make('name')
    ->label('Ürün Adı')        // Türkçe etiket
    ->searchable()
    ->weight('bold');

TextColumn::make('price')
    ->label('Fiyat')
    ->getStateUsing(fn ($record) => number_format($record->price, 2) . ' ₺');

TextColumn::make('status')
    ->label('Durum')
    ->badge()
    ->color(fn (string $state) => match ($state) {
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
    });
```

### Table Sorgu Optimizasyonu
- `->modifyQueryUsing(fn ($query) => $query->with('relation'))` ile eager loading
- `->counts('relation')` ile ilişki sayısı
- `->defaultSort('created_at', 'desc')` varsayılan sıralama

## Widget Kuralları

```php
// TableWidget: heading STATIC olmalı
class MyTableWidget extends TableWidget
{
    protected static ?string $heading = 'Başlık'; // ✅ STATIC
}

// ChartWidget: heading NON-STATIC olmalı
class MyChartWidget extends ChartWidget
{
    protected ?string $heading = 'Başlık'; // ✅ NON-STATIC
}
```

## RelationManager Kuralları
- Read-only manager'lar: `public function isReadOnly(): bool { return true; }`
- Drag-and-drop sıralama: `->reorderable('sort_order')`
- Status badge renkleri: her zaman match expression kullan

## Genel Kurallar
- Cache temizle: `php artisan view:clear && cache:clear && view:cache`
- Octane restart gerekli: yapısal değişikliklerden sonra
- Tüm label'lar Türkçe
- Navigation group'lar: Türkçe
- Date format: `d.m.Y H:i`
- Para formatı: `number_format($value, 2) . ' ₺'`

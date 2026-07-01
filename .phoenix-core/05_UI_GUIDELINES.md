# 05 — Arayüz Tasarım Rehberi (UI Guidelines)

> Patenli Ayakkabılar admin paneli ve frontend arayüz standartları.

---

## Filament Panel Yapılandırması

```php
// AdminPanelProvider.php
->brandName('Patenli Ayakkabılar')
->path('admin')
->font('Outfit')                           // Font ailesi
->colors([
    'primary' => Color::hex('#ff4e00'),    // Ana renk: Turuncu
])
->grayColor(Color::Slate)                  // Gri paleti
->darkMode()                               // Dark mode aktif (kullanıcı toggle)
->spaMode()                                // SPA modu (tam sayfa yenilemesi yok)
->sidebarCollapsibleOnDesktop()            // Sidebar masaüstünde daraltılabilir
->globalSearchKeys(['Ctrl+K', 'Cmd+K'])   // Global arama kısayolu
->databaseNotifications()                  // Veritabanı bildirimleri (bell icon)
```

---

## Renk Paleti

### Ana Renkler

| Renk | Hex | Kullanım |
|---|---|---|
| **Primary** | `#ff4e00` | Turuncu — ana aksiyon rengi |
| **Gray** | Slate paleti | Arka plan, kenarlıklar, metin |

### Filament Renk Sistemi

| Renk Adı | Kullanım Alanları |
|---|---|
| `primary` | Ana butonlar, aktif sekmeler, bağlantılar |
| `success` | Başarılı işlemler, teslim edildi, aktif |
| `warning` | Beklemede, dikkat gerektiren durumlar |
| `danger` | Hatalar, iptal, sil, kritik uyarılar |
| `info` | Bilgilendirme, işleniyor durumu |
| `gray` | Pasif, devre dışı, ikincil |

---

## Durum Renkleri

### Sipariş Durumları

| Durum | Renk | Badge Görünümü |
|---|---|---|
| `pending` — Beklemede | `warning` | 🟡 Sarı badge |
| `processing` — İşleniyor | `info` | 🔵 Mavi badge |
| `shipped` — Kargoda | `primary` | 🟠 Turuncu badge |
| `delivered` — Teslim Edildi | `success` | 🟢 Yeşil badge |
| `cancelled` — İptal | `danger` | 🔴 Kırmızı badge |

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

### Müşteri Skor Renkleri

Doğru orantılı — yüksek skor = iyi:

| Skor Aralığı | Renk | Anlam |
|---|---|---|
| 70 – 100 | `success` | Yüksek skor, iyi durumda |
| 40 – 69 | `warning` | Orta skor, dikkat |
| 0 – 39 | `danger` | Düşük skor, müdahale gerekli |

```php
TextColumn::make('total_score')
    ->label('Puan')
    ->badge()
    ->color(fn (int $state): string => match (true) {
        $state >= 70 => 'success',
        $state >= 40 => 'warning',
        default      => 'danger',
    });
```

### Risk Renkleri

Ters orantılı — yüksek risk = kötü:

| Risk Aralığı | Renk | Anlam |
|---|---|---|
| 70 – 100 | `danger` | Yüksek risk, kritik |
| 40 – 69 | `warning` | Orta risk, dikkat |
| 0 – 39 | `success` | Düşük risk, güvenli |

```php
TextColumn::make('churn_risk')
    ->label('Kayıp Riski')
    ->badge()
    ->color(fn (int $state): string => match (true) {
        $state >= 70 => 'danger',
        $state >= 40 => 'warning',
        default      => 'success',
    });
```

---

## Navigasyon Yapısı

### Navigasyon Grupları

```
📊 Dashboard                    (sort: -2)
    └── Dashboard sayfası

📦 Katalog Yönetimi             (sort: 0)
    ├── Ürünler                 (sort: 1)
    ├── Kategoriler             (sort: 2)
    └── Değerlendirmeler        (sort: 3)

💰 Satışlar                     (sort: 10)
    ├── Siparişler              (sort: 1)
    └── Kuponlar                (sort: 2)

🧠 Müşteri İstihbaratı          (sort: 20)
    ├── Müşteriler              (sort: 1)
    ├── Segmentler              (sort: 2)
    └── AI Önerileri            (sort: 3)

📝 İçerik                       (sort: 30)
    ├── Bannerlar               (sort: 1)
    └── Sayfalar                (sort: 2)

⚙️ Site Yönetimi                (sort: 40)
    └── Ayarlar                 (sort: 1)
```

### Navigasyon İkonları

| Resource | İkon |
|---|---|
| Ürünler | `heroicon-o-shopping-bag` |
| Kategoriler | `heroicon-o-tag` |
| Siparişler | `heroicon-o-shopping-cart` |
| Müşteriler | `heroicon-o-users` |
| Kuponlar | `heroicon-o-ticket` |
| Bannerlar | `heroicon-o-photo` |
| Sayfalar | `heroicon-o-document-text` |
| Değerlendirmeler | `heroicon-o-star` |
| Segmentler | `heroicon-o-user-group` |
| AI Önerileri | `heroicon-o-light-bulb` |
| Ayarlar | `heroicon-o-cog-6-tooth` |

---

## Form Tasarım Kuralları

### Tab Kullanımı (Çok Alanlı Resource'lar)

```php
Tabs::make('Ürün Bilgileri')
    ->tabs([
        Tabs\Tab::make('Genel')
            ->icon('heroicon-o-information-circle')
            ->schema([...]),
        Tabs\Tab::make('Fiyatlandırma')
            ->icon('heroicon-o-currency-dollar')
            ->schema([...]),
        Tabs\Tab::make('Görseller')
            ->icon('heroicon-o-photo')
            ->schema([...]),
        Tabs\Tab::make('SEO')
            ->icon('heroicon-o-globe-alt')
            ->schema([...]),
    ])
    ->columnSpanFull()
    ->persistTabInQueryString();
```

### Section Kullanımı (Az Alanlı Resource'lar)

```php
Section::make('Genel Bilgiler')
    ->description('Temel bilgileri girin')
    ->icon('heroicon-o-information-circle')
    ->schema([
        TextInput::make('name')->label('Ad'),
        TextInput::make('email')->label('E-posta'),
    ])
    ->columns(2);
```

### 2 Sütunlu Layout

```php
// Form genel layout
->schema([
    Group::make()
        ->schema([
            // Sol kolon — ana içerik
        ])
        ->columnSpan(2),

    Group::make()
        ->schema([
            // Sağ kolon — yan panel (durum, tarih vb.)
        ])
        ->columnSpan(1),
])
->columns(3);
```

### Select Bileşenleri

```php
// Her Select'e native(false) ekle
Select::make('category_id')
    ->label('Kategori')
    ->relationship('category', 'name')
    ->searchable()
    ->preload()
    ->native(false)         // ← Filament styled dropdown
    ->required();
```

---

## Tablo Tasarım Kuralları

### Para Sütunları

```php
TextColumn::make('total')
    ->label('Toplam')
    ->prefix('₺')                    // TL sembolü
    ->numeric(decimalPlaces: 2)      // 2 ondalık
    ->sortable();
```

### Tarih Sütunları

```php
TextColumn::make('created_at')
    ->label('Tarih')
    ->dateTime('d.m.Y H:i')         // Türk tarih formatı
    ->sortable()
    ->toggleable(isToggledHiddenByDefault: true);
```

### Boolean Sütunları

```php
IconColumn::make('is_active')
    ->label('Aktif')
    ->boolean()
    ->trueIcon('heroicon-o-check-circle')
    ->falseIcon('heroicon-o-x-circle')
    ->trueColor('success')
    ->falseColor('danger');
```

### Tablo Filtreleri

```php
->filters([
    SelectFilter::make('status')
        ->label('Durum')
        ->options([
            'pending'    => 'Beklemede',
            'processing' => 'İşleniyor',
            'shipped'    => 'Kargoda',
            'delivered'  => 'Teslim Edildi',
            'cancelled'  => 'İptal',
        ])
        ->native(false),

    TrashedFilter::make()
        ->label('Silinen Kayıtlar'),
])
```

---

## Dashboard Widget Düzeni

```
┌─────────────────────────────────────────────┐
│           StatsOverviewWidget               │
│  [Sipariş] [Gelir] [Müşteri] [Ürün]       │
├──────────────────────┬──────────────────────┤
│   OrderChartWidget   │  PhoenixAIWidget     │
│   (Son 30 Gün)       │  (AI Önerileri)      │
├──────────────────────┼──────────────────────┤
│ CustomerIntelligence │  LowStockAlertWidget │
│  (Segment Dağılımı)  │  (Düşük Stok)        │
├──────────────────────┴──────────────────────┤
│          LatestOrdersWidget                 │
│          (Son Siparişler)                   │
└─────────────────────────────────────────────┘
```

### Widget Column Span

```php
// Full-width widget
protected int | string | array $columnSpan = 'full';

// Yarım genişlik
protected int | string | array $columnSpan = 1;
```

---

## Tipografi

| Kullanım | Font | Boyut |
|---|---|---|
| Genel UI | Outfit | Filament default |
| Kodlar / ID'ler | `font-mono` sınıfı | `sm` |
| Başlıklar | Outfit | `lg` — `2xl` |

---

## Responsive Tasarım

- Filament otomatik responsive.
- Sidebar mobilde hamburger menüye dönüşür.
- Tablolar yatay scroll ile küçük ekranlara uyum sağlar.
- Form'lar tek sütuna düşer: `->columns(['sm' => 1, 'lg' => 2])`.

---

## Dark Mode

- Kullanıcı tarafından değiştirilebilir (sağ üst toggle).
- Tüm bileşenler dark mode uyumlu olmalı.
- Özel bileşenlerde `dark:` prefix'i ile Tailwind stili kullan.

```html
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
    <!-- İçerik -->
</div>
```

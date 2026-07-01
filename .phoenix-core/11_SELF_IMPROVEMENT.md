# 11 — Sürekli İyileştirme Kuralları

## Öğrenme Döngüsü

Her görev tamamlandığında:

```
┌─────────────────┐
│  Görev Tamamlandı│
└────────┬────────┘
         ▼
┌─────────────────┐
│  Değerlendir     │ → İş değeri arttı mı?
└────────┬────────┘    Bir şey kırıldı mı?
         ▼
┌─────────────────┐
│  Öğren           │ → Pattern'leri kaydet
└────────┬────────┘    Hataları belgele
         ▼
┌─────────────────┐
│  İyileştir       │ → Proaktif öneriler üret
└────────┬────────┘    Tech debt azalt
         ▼
┌─────────────────┐
│  Kaydet          │ → 09_PROJECT_MEMORY.md güncelle
└─────────────────┘
```

---

## Değerlendirme Kriterleri

Her görev sonrası şu soruları sor:

| Soru | Beklenen Cevap |
|---|---|
| İş değeri arttı mı? | Evet — ölçülebilir fayda |
| Mevcut işlevsellik bozuldu mu? | Hayır — regresyon yok |
| Kod tekrarı oluştu mu? | Hayır — DRY prensibi |
| Performans etkilendi mi? | Nötr veya pozitif |
| Türkçe etiketler doğru mu? | Evet — tüm UI Türkçe |
| Filament conventions'a uygun mu? | Evet — v5.6 standartları |

---

## Bilinen Filament v5.6 Quirk'leri

> Bu bölüm yaşanan hatalardan öğrenilen teknik notları içerir.

### 1. `$heading` Statik/Non-Statik Farkı

```php
// ⚠️ TableWidget → $heading STATIC
class LowStockWidget extends TableWidget
{
    protected static ?string $heading = 'Düşük Stok Uyarısı';
}

// ⚠️ ChartWidget → $heading NON-STATIC
class OrderChartWidget extends ChartWidget
{
    protected ?string $heading = 'Sipariş Grafiği';
}
```

**Kural:** Widget türüne göre `static` kullanımını kontrol et.

### 2. Schema Sınıf İmportu

```php
// ✅ Doğru (Filament v5.6)
use Filament\Schemas\Schema;

// ❌ Yanlış (eski sürümler)
use Filament\Forms\Form;
```

### 3. Form Method Signature

```php
// ✅ Doğru (Filament v5.6)
public static function configure(Schema $schema): Schema
{
    return $schema->components([
        // form alanları
    ]);
}

// ❌ Yanlış
public static function form(Form $form): Form
```

### 4. Select Bileşeni

```php
// ✅ Styled dropdown
Select::make('category_id')
    ->native(false)           // Filament styled dropdown
    ->searchable()
    ->preload();

// ❌ Browser native dropdown (tutarsız görünüm)
Select::make('category_id'); // native(true) varsayılan
```

### 5. Table Method Signature

```php
// ✅ Doğru (Filament v5.6)
public static function table(Table $table): Table
{
    return $table->columns([...]);
}
```

### 6. RelationManager Tanımı

```php
// Resource'da RelationManager'lar:
public static function getRelations(): array
{
    return [
        ImagesRelationManager::class,
        VariantsRelationManager::class,
    ];
}
```

---

## Pattern Takibi

### Yüksek Etkili Değişiklik Türleri
| Tür | Etki Seviyesi | Örnek |
|---|---|---|
| Dashboard widget ekleme | ⭐⭐⭐⭐⭐ | İstatistik kartları, grafikler |
| Form UX iyileştirme | ⭐⭐⭐⭐ | Tab yapısı, auto-fill, validation |
| RelationManager ekleme | ⭐⭐⭐⭐ | Ürün görselleri, sipariş kalemleri |
| Global arama aktifleştirme | ⭐⭐⭐ | Resource'larda `getGloballySearchableAttributes` |
| Segment/öneri sistemi | ⭐⭐⭐⭐⭐ | Müşteri istihbaratı, AI öneriler |

### Düşük Etkili / Riskli Değişiklik Türleri
| Tür | Risk | Not |
|---|---|---|
| Migration değişikliği | 🔴 Yüksek | Mevcut veriyi etkileyebilir |
| Sütun silme | 🔴 Yüksek | Veri kaybı riski |
| Package güncelleme | 🟡 Orta | Breaking change riski |
| Route değişikliği | 🟡 Orta | Mevcut linkleri kırabilir |

---

## Proaktif İzleme

Her oturumda şunları kontrol et:

### Kod Kalitesi
- [ ] Kullanılmayan import'lar var mı?
- [ ] Tekrarlanan kod blokları var mı? → Trait veya Service'e taşı
- [ ] Eksik validation var mı?
- [ ] Eksik Türkçe etiket var mı?

### Performans
- [ ] N+1 query riski var mı?
- [ ] Index eksik sütun var mı?
- [ ] Gereksiz eager loading var mı?
- [ ] Cache kullanılması gereken ama kullanılmayan query var mı?

### Güvenlik
- [ ] Yetkilendirme kontrolü eksik endpoint var mı?
- [ ] Dosya yükleme validasyonu eksik alan var mı?
- [ ] Hassas veri expose eden response var mı?

---

## Proaktif Öneri Kategorileri

1. **Yeni Özellik Önerileri**: Mevcut veriye dayalı yeni dashboard widget'ları, raporlar
2. **UX İyileştirmeleri**: Form flow optimizasyonu, daha iyi navigasyon, bildirimler
3. **Performans Optimizasyonu**: Cache stratejisi, query optimizasyonu, lazy loading
4. **Güvenlik Güçlendirme**: 2FA, rate limiting, audit logging genişletme
5. **Otomasyon**: E-posta kampanyaları, stok uyarıları, sipariş bildirimleri

---

## 10 Görev Kuralı

> Her 10 görevde bir, genel mimariyi gözden geçir:

- [ ] Teknik borç birikti mi?
- [ ] Kullanılmayan dosyalar var mı?
- [ ] Service katmanı düzgün organize mi?
- [ ] Test coverage yeterli mi?
- [ ] Dokümantasyon güncel mi?
- [ ] Backlog öncelikleri hâlâ doğru mu?

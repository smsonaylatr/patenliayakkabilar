# Phoenix CTO Agent — Workspace Rules

Bu workspace kurallarını her zaman takip et.

## Proje
- **Marka:** Patenli Ayakkabılar (roller shoes e-commerce)
- **Stack:** Laravel 12 + Filament v5.6 + Livewire 4 + MySQL
- **Sunucu:** RoadRunner (Octane) — port 8080
- **Admin:** /admin path
- **Dil:** Türkçe (tüm UI etiketleri, menüler, bildirimler)
- **Para birimi:** ₺

## Kodlama Kuralları
- Filament v5.6: `Filament\Schemas\Schema` kullan (Form değil)
- TableWidget `$heading` → `static`, ChartWidget `$heading` → non-static
- Select bileşenleri → `->native(false)`
- Para gösterimi → `number_format($value, 2) . ' ₺'`
- Tarih formatı → `d.m.Y H:i`
- Resource pattern: Resource.php + Pages/ + Schemas/ + Tables/ + RelationManagers/
- Model isimleri İngilizce, label'lar Türkçe

## İş Akışı
```
Analiz et → Karar ver → Uygula → Test et → Raporla → Devam et
```

## Referanslar
- `.phoenix-core/` → Detaylı kurallar (13 dosya)
- `.agents/skills/` → Özelleştirilmiş skill'ler (4 skill)
- `AGENTS.md` (proje kökünde) → Ana kurallar

# 09 — Proje Hafızası & Karar Günlüğü

> Bu dosya projedeki tüm önemli kararları, mimari değişiklikleri ve dönüm noktalarını kronolojik olarak kaydeder.
> Her yeni karar veya büyük değişiklik sonrasında bu dosya güncellenmelidir.

---

## Zaman Çizelgesi

### 2026-06-27 — Proje Oluşturuldu
- **Karar:** Laravel 12 + Filament v5.6 + Livewire 4 stack seçildi.
- **Gerekçe:** Filament v5.6 en güncel admin panel framework'ü; Livewire 4 ile tam SPA deneyimi sağlanıyor.
- **Veritabanı:** MySQL (`patenli_db`)
- **Sunucu:** RoadRunner (Laravel Octane)
- **Marka:** Patenli Ayakkabılar — e-ticaret platformu

### 2026-06-29 — İçerik Yönetimi Modülleri
- **Eklenenler:**
  - `PageResource` — Statik sayfa yönetimi (Hakkımızda, Gizlilik vb.)
  - `ContactMessageResource` — İletişim formu mesajları
- **Not:** Sayfalar `slug` bazlı, SEO meta alanları ile donatıldı.

### 2026-06-30 — Admin Panel İyileştirmeleri (Faz 1–4)

#### Faz 1: Ürün Yönetimi
- Ürün formu **Tab yapısı** ile organize edildi:
  - Genel Bilgiler, Fiyatlandırma, Stok, Medya, SEO
- `Select` bileşeni ile kategori seçimi (`->native(false)`)
- `RichEditor` ile ürün açıklaması
- `slug` alanı otomatik oluşturulur (`->live()->afterStateUpdated()`)

#### Faz 2: Sipariş Yönetimi
- Sipariş formu **7 bölüm** halinde düzenlendi:
  - Müşteri Bilgileri, Kargo Adresi, Fatura Adresi, Ürünler, Ödeme, Durum, Notlar
- `Select` alanları ile müşteri ve durum seçimi
- Otomatik toplam hesaplama

#### Faz 3: İlişki Yöneticileri (RelationManagers)
| Resource | RelationManager | Açıklama |
|---|---|---|
| Product | `ImagesRelationManager` | Ürün görselleri (çoklu yükleme) |
| Product | `VariantsRelationManager` | Beden/renk varyantları |
| Order | `OrderItemsRelationManager` | Sipariş kalemleri |
| Order | `StatusHistoryRelationManager` | Durum geçmişi |

#### Faz 4: Dashboard & Arama
- **6 istatistik widget:**
  - Toplam Gelir, Toplam Sipariş, Bekleyen Sipariş, Toplam Müşteri, Toplam Ürün, Ort. Sepet Tutarı
- **Sipariş grafiği:** Son 7 günlük sipariş trendi (Chart widget)
- **Düşük stok uyarısı:** Stok < 10 olan ürünler listesi
- **Global arama:** Products, Orders, Users için etkinleştirildi
- **Dark mode:** Panel konfigürasyonunda aktif edildi

### 2026-07-01 — Müşteri İstihbarat Platformu

#### Veritabanı Değişiklikleri
8 yeni tablo oluşturuldu:

| Tablo | Amaç |
|---|---|
| `customer_scores` | RFM skoru ve müşteri puanlaması |
| `customer_segments` | Müşteri segmentleri (VIP, Riskli vb.) |
| `customer_segment_user` | Segment-müşteri pivot tablosu |
| `customer_recommendations` | AI önerileri |
| `customer_activities` | Müşteri aktivite logları |
| `customer_notes` | Manuel müşteri notları |
| `order_status_histories` | Sipariş durum geçmişi (audit) |
| `notifications` | Laravel database bildirimleri |

#### Servisler
- **`CustomerScoreService`**: RFM (Recency, Frequency, Monetary) algoritması ile müşteri puanlama
  - Recency: Son sipariş tarihi (1–5 puan)
  - Frequency: Sipariş sayısı (1–5 puan)
  - Monetary: Toplam harcama (1–5 puan)
  - Toplam skor: Ağırlıklı ortalama

- **`PhoenixAIService`**: Kural tabanlı öneri motoru (6 kural)
  1. Yüksek değerli müşteriye VIP teklifi
  2. Düşen müşteriye geri kazanım kampanyası
  3. Yeni müşteriye hoşgeldin indirimi
  4. Sepet ortalaması yüksek müşteriye çapraz satış
  5. Tek kategoriden alana çeşitlilik önerisi
  6. Uzun süredir alışveriş yapmayan müşteriye hatırlatma

#### Filament Bileşenleri
- **`ViewCustomer` sayfası**: Müşteri profil kartı, RFM skoru, segment bilgisi, aktivite geçmişi, öneriler
- **`CustomerSegmentResource`**: Segment CRUD yönetimi
- **6 varsayılan segment** seeder ile oluşturuldu:
  - 🏆 VIP Müşteriler
  - ⭐ Sadık Müşteriler
  - 🆕 Yeni Müşteriler
  - 📉 Risk Altındaki Müşteriler
  - 💤 Uyuyan Müşteriler
  - 💰 Yüksek Değerli Müşteriler

#### Zamanlanmış Görevler
| Görev | Zamanlama | Açıklama |
|---|---|---|
| Skor hesaplama | Günlük | Tüm müşterilerin RFM skorunu güncelle |
| Öneri üretme | Günlük | AI önerilerini yeniden oluştur |
| Segment senkronizasyonu | Günlük | Müşterileri segmentlere otomatik ata |

#### Diğer
- `OrderObserver`: Sipariş durum değişikliklerini `order_status_histories` tablosuna loglar
- Database notification sistemi aktif edildi
- **Phoenix CTO Agent sistemi** oluşturuldu (`.phoenix-core/` dizini)

---

## Mimari Kararlar Özeti

| Karar | Seçim | Alternatifler | Gerekçe |
|---|---|---|---|
| Admin Framework | Filament v5.6 | Nova, Voyager | Livewire entegrasyonu, aktif geliştirme |
| HTTP Sunucu | RoadRunner | Nginx+FPM, Swoole | Performans, kolay kurulum |
| Veritabanı | MySQL | PostgreSQL, SQLite | Yaygınlık, hosting uyumluluğu |
| Cache Driver | Database | Redis, Memcached | Basitlik (Redis'e geçiş planlandı) |
| AI Motoru | Kural tabanlı | ML modeli, GPT API | İlk aşama için yeterli, maliyet yok |
| Müşteri Skorlama | RFM | NPS, CLV | E-ticaret standardı, anlaşılır |

---

## Backlog & Gelecek Planlar

| Özellik | Öncelik | Durum |
|---|---|---|
| 2FA (iki faktörlü doğrulama) | Yüksek | Planlandı |
| Redis cache geçişi | Yüksek | Planlandı |
| Otomatik e-posta kampanyaları | Orta | Backlog |
| Ürün öneri widget'ı (storefront) | Orta | Backlog |
| Gelişmiş raporlama (PDF export) | Orta | Backlog |
| REST API (mobil uygulama) | Düşük | Backlog |
| Çoklu dil desteği | Düşük | Backlog |

---

> **Son güncelleme:** 2026-07-01
> **Güncelleyen:** Phoenix CTO Agent

---
name: customer-intelligence
description: Müşteri profili, davranış takibi, segmentasyon, doğru zamanda kampanya önerisi, müşteri skoru ve lifetime value kuralları.
---

# Müşteri İstihbaratı Skill

## Kapsam
Müşteri davranış analizi, segmentasyon, skorlama ve kişiselleştirilmiş pazarlama kuralları.

---

## Müşteri Profili

Her müşteri profili şunları içermelidir:

### Temel Bilgiler
- Ad Soyad, E-posta, Telefon
- Kayıt tarihi, Son görülme tarihi
- Rol (admin/customer)

### Finansal Özet
- Toplam harcama (LTV)
- Sipariş sayısı
- Ortalama sipariş tutarı
- Son sipariş tarihi ve tutarı

### Skor Kartları
| Skor | Hesaplama | Ağırlık |
|---|---|---|
| Satın Alma | Sipariş sayısı + harcama + güncellik | %30 |
| Aktivite | Son 30 gün event sayısı | %20 |
| Sadakat | Kayıt süresi + tekrar alışveriş + yorum | %25 |
| Etkileşim | Son 7 gün ürün görüntüleme + sepet + arama | %25 |
| Risk | Son sipariş/aktiviteden bu yana geçen gün | Ayrı |

### Müşteri Tier'ları
| Tier | Skor Aralığı | Renk |
|---|---|---|
| VIP | 80-100 | success (yeşil) |
| Değerli | 60-79 | primary (turuncu) |
| Normal | 40-59 | info (mavi) |
| Düşük | 20-39 | warning (sarı) |
| Yeni | 0-19 | gray (gri) |

## Davranış Takibi

### Olay Türleri (customer_events)
```
page_view       → Sayfa görüntüleme
product_view    → Ürün görüntüleme
add_to_cart     → Sepete ekleme
remove_from_cart → Sepetten çıkarma
checkout_start  → Ödeme başlatma
purchase        → Satın alma
search          → Arama
register        → Kayıt
login           → Giriş
review          → Yorum
cart_abandoned  → Sepet terk
```

### Olay Verisi (event_data JSON)
```json
{
    "product_id": 42,
    "category_id": 3,
    "search_query": "siyah patenli",
    "variant_id": 15,
    "cart_total": 299.90
}
```

## Segmentasyon

### Varsayılan Segmentler
| Segment | Koşullar | Otomatik Güncelleme |
|---|---|---|
| VIP Müşteriler | LTV ≥ 500₺, Risk ≤ 30, 3+ sipariş | Her gece 03:30 |
| Aktif Alıcılar | Son sipariş ≤ 30 gün | Her gece 03:30 |
| Kayıp Riski | Son sipariş ≥ 60 gün, 1+ sipariş | Her gece 03:30 |
| Yeni Müşteriler | Kayıt ≤ 7 gün | Her gece 03:30 |
| Sadık Müşteriler | 5+ sipariş, Risk ≤ 40 | Her gece 03:30 |
| Tek Seferlik | Tam olarak 1 sipariş | Her gece 03:30 |

### Segment Koşulları (JSON Format)
```json
{
    "min_ltv": 500,
    "max_risk": 30,
    "min_orders": 3,
    "max_days_since_order": 30,
    "min_days_since_order": 60,
    "exact_orders": 1,
    "max_days_since_registration": 7
}
```

## Kampanya Zamanlaması

### Doğru Zaman Kuralları
| Müşteri Durumu | Aksiyon | Kanal |
|---|---|---|
| Sepet terk (2 saat) | Hatırlatma | Push / SMS |
| Sepet terk (24 saat) | %10 kupon | E-posta |
| 7 gün inaktif | "Seni özledik" | Push |
| 14 gün inaktif | Özel teklif | E-posta + SMS |
| 30 gün inaktif | Agresif kampanya | Tüm kanallar |
| 60+ gün inaktif | Son şans teklifi | E-posta |
| Doğum günü | Kutlama + kupon | SMS + E-posta |
| VIP risk altında | Kişisel arama | WhatsApp |

### Spam Koruma
- Bir müşteriye günde max 2 bildirim
- Bir müşteriye haftada max 5 bildirim
- Gece 22:00 - 08:00 arası bildirim gönderme
- Abonelikten çıkan müşteriye asla gönderme

## Veritabanı Tabloları

```
customer_events       → Davranış olayları
customer_scores       → RFM skorları
customer_segments     → Segment tanımları
customer_segment_user → Segment-müşteri eşleşmesi
customer_notes        → Admin iç notları
```

## Artisan Komutları
```bash
php artisan phoenix:scores          # Tüm skorları hesapla
php artisan phoenix:sync-segments   # Segmentleri eşleştir
```

## Filament UI
- Müşteri listesinde: Skor, Tier, LTV, Risk sütunları
- Müşteri profil sayfası: ViewCustomer (Infolist)
- Not ekleme: Action modal
- Skor yenileme: Action butonu

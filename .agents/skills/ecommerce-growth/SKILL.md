---
name: ecommerce-growth
description: Satış artırma, dönüşüm optimizasyonu, sepet terk, kupon, kampanya, ürün sayfası, SEO ve müşteri deneyimi kuralları.
---

# E-Ticaret Büyüme Skill

## Kapsam
Bu skill, patenli ayakkabı e-ticaret platformunun satış artırma ve dönüşüm optimizasyonu kurallarını içerir.

---

## Dönüşüm Optimizasyonu

### Ürün Sayfası
- **Yüksek kalite görseller**: Minimum 3, ideal 5-8 görsel
- **Varyant seçimi**: Renk ve numara seçimi kolay ve görsel olmalı
- **Stok durumu**: Açıkça belirt ("Son 3 adet!", "Stokta var")
- **Fiyat gösterimi**: İndirimli fiyat kırmızı, eski fiyat üstü çizili
- **Güven sinyalleri**: Kargo bilgisi, iade politikası, ödeme güvenliği
- **Sosyal kanıt**: Yorum sayısı, ortalama puan, "X kişi satın aldı"

### Sepet & Checkout
- **Sepet hatırlatma**: Terk edilen sepet bildirimi (2 saat sonra)
- **Minimum sepet tutarı**: Kupon kullanımı için alt limit
- **Ücretsiz kargo eşiği**: Sepet tutarına göre progres bar göster
- **Tek sayfa checkout**: Adım sayısını minimize et
- **Misafir checkout**: Kayıt zorunlu olmasın

### Kupon Sistemi
- **Tür**: Yüzde (%) veya sabit tutar (₺)
- **Koşullar**: min_cart_total, usage_limit, expires_at
- **İlk alışveriş kuponu**: Yeni müşterilere otomatik
- **Terk edilen sepet kuponu**: %10-15 indirim
- **Doğum günü kuponu**: Kişiselleştirilmiş

## Kampanya Stratejileri

### Tetikleyiciler (Trigger)
| Tetikleyici | Aksiyon | Zamanlama |
|---|---|---|
| Sepet terk (2 saat) | Hatırlatma bildirimi | Hemen |
| Sepet terk (24 saat) | %10 kupon | 1 gün sonra |
| İlk alışveriş | Hoşgeldin e-postası | Hemen |
| 30 gün inaktif | Geri gel kampanyası | 30. gün |
| 60 gün inaktif | Agresif indirim | 60. gün |
| VIP doğum günü | Özel hediye/kupon | Doğum günü |
| Stok azalma | "Son birkaç adet!" bildirimi | Stok < 5 |

### Mevsimsel Kampanyalar
- Okul dönemi (Eylül): Çocuk patenli ayakkabılar
- Yaz (Haziran-Ağustos): Outdoor modelleri
- Black Friday: Sitewide indirim
- 11.11, 12.12: Flash sales

## SEO Kuralları

### Ürün Sayfaları
- **Title**: `{Ürün Adı} - Patenli Ayakkabılar | {Kategori}`
- **Meta Description**: 155 karakter, ürün özelliklerini içermeli
- **URL**: `/urunler/{slug}` (Türkçe karakter destekli slug)
- **H1**: Ürün adı (tekil)
- **Alt text**: Tüm görsellerde açıklayıcı alt text
- **Structured Data**: Product schema (JSON-LD)
- **Canonical URL**: Her sayfada benzersiz canonical

### Kategori Sayfaları
- **Title**: `{Kategori Adı} | Patenli Ayakkabılar`
- **Breadcrumb**: Yapılandırılmış veri ile
- **Filtreleme**: URL parametreleri ile (canonical dikkat)

## Müşteri Deneyimi

### Hız
- İlk sayfa yüklenme: < 2 saniye
- Ürün sayfası: < 1.5 saniye
- Checkout: < 1 saniye

### Güven
- SSL sertifikası
- Ödeme güvenliği rozeti
- Müşteri yorumları (doğrulanmış)
- İade politikası açıkça görünür
- İletişim bilgileri her sayfada

### Kişiselleştirme
- Son görüntülenen ürünler
- Benzer ürünler
- "Bunu alanlar bunu da aldı"
- Kişiselleştirilmiş anasayfa

## Admin Panel İş Kuralları

### Sipariş Yönetimi
- Yeni sipariş → Admin bildirim
- Durum değişikliği → Müşteri bildirim + Audit log
- Kargo kodu girildiğinde → Müşteri SMS/e-posta
- İptal → Stok geri yükleme + İade süreci

### Stok Yönetimi
- Düşük stok uyarısı: ≤ 5 adet
- Kritik stok: 0 adet → Ürün otomatik pasif
- Varyant bazlı stok takibi
- Stok geçmişi kaydı

### Raporlama
- Günlük satış raporu
- Haftalık trend analizi
- Aylık performans özeti
- Ürün bazlı kar/zarar
- Kategori bazlı satış dağılımı

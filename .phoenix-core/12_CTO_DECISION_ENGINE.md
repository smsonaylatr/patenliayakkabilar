# 12 — CTO Karar Motoru & Önceliklendirme

## Öncelik Matrisi

### Formül

```
Öncelik Skoru = (Etki × Aciliyet ÷ Risk) × Kategori Çarpanı
```

| Parametre | Aralık | Açıklama |
|---|---|---|
| **Etki** | 1–5 | İş değerine katkısı (5 = çok yüksek) |
| **Aciliyet** | 1–5 | Ne kadar acil (5 = hemen yapılmalı) |
| **Risk** | 1–5 | Mevcut sistemi bozma riski (5 = çok riskli) |

### Kategori Çarpanları

| Kategori | Çarpan | Açıklama |
|---|---|---|
| 🔒 Güvenlik düzeltmesi | **2.0×** | Güvenlik açığı varsa her şeyden önce gelir |
| 💰 Gelir etkileyen | **1.8×** | Satışı doğrudan artıran özellikler |
| 🗄️ Veri bütünlüğü | **1.6×** | Veri kaybını veya tutarsızlığını önleyen |
| 🎨 UX iyileştirme | **1.2×** | Kullanıcı deneyimini artıran |
| 🔧 Kod kalitesi | **1.0×** | Refactoring, teknik borç azaltma |
| ✨ Olsa iyi olur | **0.5×** | İsteğe bağlı, acil olmayan özellikler |

### Örnek Hesaplama

```
Görev: Dashboard'a gelir grafiği ekle
├── Etki:    4 (yönetici için değerli)
├── Aciliyet: 3 (bugün olması şart değil)
├── Risk:    1 (yeni dosya, mevcut kodu etkilemez)
├── Kategori: Gelir etkileyen (1.8×)
└── Skor:    (4 × 3 ÷ 1) × 1.8 = 21.6 ✅ Yüksek öncelik

Görev: Kullanılmayan import'ları temizle
├── Etki:    1 (fonksiyonel değişiklik yok)
├── Aciliyet: 1 (hiç acil değil)
├── Risk:    1 (güvenli)
├── Kategori: Kod kalitesi (1.0×)
└── Skor:    (1 × 1 ÷ 1) × 1.0 = 1.0 ❌ Düşük öncelik
```

---

## Karar Akış Şeması

```
Yeni görev geldi
    │
    ▼
┌─────────────────────────────────┐
│ 1. Bug veya güvenlik açığı mı? │
└────────────┬────────────────────┘
             │ EVET → 🔴 HEMEN DÜZELT (Skor: Maksimum)
             │ HAYIR ↓
┌─────────────────────────────────┐
│ 2. Gelir fırsatı mı?           │
└────────────┬────────────────────┘
             │ EVET → 🟠 SIRADAKI GÖREV
             │ HAYIR ↓
┌─────────────────────────────────┐
│ 3. UX sürtünmesi mi?           │
└────────────┬────────────────────┘
             │ EVET → 🟡 MEVCUT SPRİNT'E EKLE
             │ HAYIR ↓
┌─────────────────────────────────┐
│ 4. Teknik borç mu?             │
└────────────┬────────────────────┘
             │ EVET → 🟢 SONRAKI SPRİNT'E PLANLA
             │ HAYIR ↓
             └──── 🔵 BACKLOG'A EKLE
```

---

## Risk Değerlendirme Matrisi

| İşlem Türü | Risk Seviyesi | Gerekli Aksiyon |
|---|---|---|
| Yeni dosya oluşturma | 🟢 **DÜŞÜK** | Hemen uygula |
| Yeni widget / component ekleme | 🟢 **DÜŞÜK** | Hemen uygula |
| Yeni seeder / factory oluşturma | 🟢 **DÜŞÜK** | Hemen uygula |
| Dokümantasyon güncelleme | 🟢 **DÜŞÜK** | Hemen uygula |
| Mevcut dosyada alan ekleme | 🟡 **ORTA** | Mevcut yapıyı koru, ekleme yap |
| Mevcut formu düzenleme | 🟡 **ORTA** | Çalışan alanları bozma |
| Veritabanı migration (yeni tablo) | 🟡 **ORTA** | Schema kontrolü yap |
| Veritabanı migration (sütun değişikliği) | 🟠 **ORTA-YÜKSEK** | Mevcut veriyi kontrol et |
| Mevcut route değişikliği | 🟠 **ORTA-YÜKSEK** | Bağımlılıkları kontrol et |
| Kod silme | 🔴 **YÜKSEK** | Kullanım analizi yap, onay al |
| Tablo silme / sütun kaldırma | 🔴 **YÜKSEK** | Yedek al, onay al |
| Package versiyonu değiştirme | 🔴 **YÜKSEK** | Breaking change kontrolü yap |

---

## Rollback Stratejileri

### Dosya Değişiklikleri
```bash
# Son commit'e geri dön
git checkout -- <dosya_adı>

# Belirli bir commit'e geri dön
git revert <commit_hash>
```

### Migration Geri Alma
```bash
# Son migration'ı geri al
php artisan migrate:rollback --step=1

# Belirli batch'i geri al
php artisan migrate:rollback --batch=<N>
```

### Veri Değişiklikleri
```bash
# MySQL yedek alma
mysqldump -u root -p patenli_db > backup_$(date +%Y%m%d).sql

# Yedekten geri yükleme
mysql -u root -p patenli_db < backup_YYYYMMDD.sql
```

### Cache Temizleme (Sorun Çözümü)
```bash
php artisan optimize:clear
php artisan filament:clear-cached-components
php artisan icons:clear
```

---

## Görev Seçim Süreci

Her oturum başında:

1. **Mevcut backlog'u oku** (`09_PROJECT_MEMORY.md`)
2. **Her görev için öncelik skoru hesapla**
3. **En yüksek skorlu görevi seç**
4. **Risk değerlendirmesi yap**
5. **Uygulamaya başla**

```
Backlog → Skorla → Sırala → En yüksek → Risk kontrol → Uygula
```

---

## Çoklu Görev Durumu

Eğer birden fazla görev aynı skora sahipse:

1. **Daha düşük riskli** olanı seç
2. Risk eşitse, **daha az dosya etkileyen** olanı seç
3. O da eşitse, **kullanıcıya en çok fayda sağlayan** olanı seç

---

## Karar Loglaması

Her önemli karar şu formatta loglanır:

```markdown
### [TARİH] — [KARAR BAŞLIĞI]
- **Bağlam:** Neden bu karar gerekti?
- **Alternatifler:** Başka hangi seçenekler vardı?
- **Seçim:** Ne seçildi?
- **Gerekçe:** Neden bu seçildi?
- **Risk:** Potansiyel riskler neler?
- **Sonuç:** Ne oldu? (sonradan güncellenir)
```

Bu loglar `09_PROJECT_MEMORY.md` dosyasına eklenir.

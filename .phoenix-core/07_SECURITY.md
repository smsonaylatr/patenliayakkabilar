# 07 — Güvenlik Politikaları

## Kimlik Doğrulama & Yetkilendirme

### Admin Panel Erişimi
- `/admin` paneline **yalnızca `role=admin`** olan kullanıcılar erişebilir.
- Filament `canAccessPanel()` gate'i User modelde tanımlanmalıdır:

```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->role === 'admin';
}
```

### RBAC (Role-Based Access Control)
| Rol | Yetki |
|---|---|
| `admin` | Tüm panel erişimi, CRUD, ayarlar |
| `customer` | Yalnızca storefront, sipariş geçmişi |

- Yeni rol eklenecekse migration + seed ile yapılır.
- Policy sınıfları her Resource için tanımlanmalıdır.

---

## Veri Güvenliği

### Şifreleme
- **Parola hashleme:** `bcrypt` (Laravel varsayılanı, `Hash::make()`)
- Hassas veriler (kredi kartı vb.) veritabanında **asla düz metin** saklanmaz.
- `.env` dosyası `.gitignore` içinde olmalıdır.

### Session Yönetimi
- **Session driver:** `database`
- Session tablosu migration ile oluşturulur.
- Session lifetime: `.env` → `SESSION_LIFETIME=120`

### CSRF Koruması
- Tüm POST/PUT/DELETE isteklerinde Laravel CSRF middleware aktiftir.
- Livewire bileşenleri otomatik olarak CSRF token gönderir.

---

## Input Doğrulama

### Form Validation
- **Tüm formlarda** server-side validation zorunludur.
- Filament form field'larında `->required()`, `->maxLength()`, `->email()` vb. kullanılır.
- Custom validation rule gerekiyorsa `Rules\` namespace altında tanımlanır.

### SQL Injection Önleme
- **Eloquent ORM** kullanılır, **raw query yazılmaz**.
- `DB::raw()` kullanımı yasaktır (zorunlu hallerde parametre binding ile).
- Query builder kullanılıyorsa `->where('column', '=', $value)` formatı zorunludur.

### XSS Önleme
- Blade template'lerde `{{ }}` (escaped) kullanılır.
- `{!! !!}` yalnızca güvenilir, sanitize edilmiş HTML için kullanılır (RichEditor çıktıları).

---

## Dosya Yükleme Güvenliği

```php
FileUpload::make('image')
    ->image()
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
    ->maxSize(2048)        // KB cinsinden (2 MB)
    ->directory('products')
    ->visibility('public');
```

- **İzin verilen MIME türleri** açıkça belirtilir.
- **Maksimum dosya boyutu** her upload alanında tanımlanır.
- Yüklenen dosyalar `storage/app/public/` altına kaydedilir.
- Dosya adları otomatik hash'lenir (UUID).

---

## Denetim & Loglama

### Audit Logging
- `OrderObserver` sipariş durum değişikliklerini loglar.
- `order_status_histories` tablosuna her değişiklik kaydedilir.
- Log kaydı: `user_id`, `old_status`, `new_status`, `changed_at`, `notes`

### Laravel Logging
- Production'da `LOG_CHANNEL=daily` kullanılır.
- Hata logları `storage/logs/` altında tutulur.
- Kritik hatalar için bildirim mekanizması kurulmalıdır.

---

## Yetkilendirme Kuralları

### Silme İşlemleri
- **Silme öncesi yetkilendirme kontrolü zorunludur.**
- İlişkili kaydı olan veriler silinemez (`onDelete: restrict`).
- Soft delete tercih edilir (`SoftDeletes` trait).

### API Güvenliği
- API response'larında hassas veri (parola hash, token vb.) **asla** döndürülmez.
- API Resource sınıflarında `->hidden()` alanlar belirlenir.

---

## Gelecek Planlar

| Özellik | Öncelik | Durum |
|---|---|---|
| İki faktörlü doğrulama (2FA) | Yüksek | Planlandı |
| IP kısıtlama (admin panel) | Orta | Planlandı |
| API key yönetimi | Orta | Planlandı |
| Rate limiting | Orta | Planlandı |
| Güvenlik denetim raporu | Düşük | Backlog |

---

## Kontrol Listesi (Her Deploy Öncesi)

- [ ] `.env` dosyası production değerleriyle güncellendi
- [ ] `APP_DEBUG=false` ayarlandı
- [ ] `APP_KEY` üretildi ve güvende
- [ ] HTTPS zorunluluğu aktif
- [ ] CSRF middleware aktif
- [ ] Dosya izinleri doğru (storage, bootstrap/cache)
- [ ] Gereksiz debug route'ları kaldırıldı

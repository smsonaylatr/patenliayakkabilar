<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFeature extends Model
{
    protected $guarded = [];

    /**
     * Patenli ayakkabılara özel özellik havuzu
     * key => [label, icon, description]
     */
    public const FEATURE_OPTIONS = [
        'led_light'       => ['label' => '💡 LED Işıklı Teker',        'desc' => 'Hareket ettikçe renkli ışıklar yanar, karanlıkta bile göz kamaştırır.'],
        'usb_charge'      => ['label' => '🔌 USB Şarj Edilebilir',     'desc' => 'Dahili şarj edilebilir batarya ile pil masrafı yok.'],
        'hidden_wheel'    => ['label' => '🛞 Gizlenebilir Teker',      'desc' => 'Bas-çek mekanizması ile tekerlek gizlenir, normal ayakkabı gibi kullanılır.'],
        'anti_slip'       => ['label' => '🔒 Anti-kayma Taban',        'desc' => 'Özel kauçuk taban ile kaygan zeminlerde bile güvenli.'],
        'breathable'      => ['label' => '🌬️ Nefes Alan Üst',          'desc' => 'File doku teknolojisi ile ayak terlemesi önlenir.'],
        'orthopedic'      => ['label' => '🦶 Ortopedik Taban',         'desc' => 'Anatomik iç taban ile gün boyu ayak sağlığı korunur.'],
        'waterproof'      => ['label' => '💧 Su Geçirmez',             'desc' => 'Yağmurlu havalarda bile ayaklar kuru kalır.'],
        'reflective'      => ['label' => '🔆 Yansıtıcı Şerit',        'desc' => 'Gece yürüyüşlerinde araç farlarıyla parlayan güvenlik şeridi.'],
        'double_wheel'    => ['label' => '⚙️ Çift Teker',              'desc' => 'İki tekerlek ile daha fazla denge ve kolay öğrenme.'],
        'single_wheel'    => ['label' => '🎯 Tek Teker',               'desc' => 'Tek tekerlek ile hız ve manevra kabiliyeti.'],
        'easy_remove'     => ['label' => '🔧 Kolay Çıkarılır Teker',   'desc' => 'Anahtar gerektirmeden saniyeler içinde tekerlek söküp takılır.'],
        'lightweight'     => ['label' => '🪶 Ultra Hafif Yapı',        'desc' => 'Sadece 350g ile gün boyu yorulmadan kullanım.'],
        'shock_absorb'    => ['label' => '🛡️ Darbe Emici',             'desc' => 'EVA köpük ara taban ile zıplama ve inişlerde eklem koruması.'],
        'color_led'       => ['label' => '🌈 7 Renk LED Modu',         'desc' => '7 farklı renk modu ve yanıp sönme efekti.'],
        'rubber_sole'     => ['label' => '👟 Kauçuk Taban',            'desc' => 'Doğal kauçuk taban ile uzun ömürlü dayanıklılık.'],
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Tüm özellik seçeneklerini Filament CheckboxList formatında döndür
     */
    public static function getOptionsForSelect(): array
    {
        return collect(self::FEATURE_OPTIONS)
            ->mapWithKeys(fn ($v, $k) => [$k => $v['label']])
            ->toArray();
    }

    /**
     * Bir feature_key'in label'ını döndür
     */
    public static function getLabel(string $key): string
    {
        return self::FEATURE_OPTIONS[$key]['label'] ?? $key;
    }

    /**
     * Bir feature_key'in açıklamasını döndür
     */
    public static function getDescription(string $key): string
    {
        return self::FEATURE_OPTIONS[$key]['desc'] ?? '';
    }
}

<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Setting;
use App\Services\VatanSmsService;

class VatanSmsSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'heroicon-o-chat-bubble-oval-left-ellipsis';
    }

    public static function getNavigationLabel(): string
    {
        return 'VatanSMS (Müşteri)';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'VatanSMS Ayarları';
    }

    protected string $view = 'filament.pages.vatan-sms-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'vatansms_api_id',
            'vatansms_api_key',
            'vatansms_sender',
            'vatansms_active',
            'vatansms_abandoned_cart_message',
            'vatansms_new_order_message',
            'vatansms_shipped_message',
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'vatansms_api_id' => $settings['vatansms_api_id'] ?? '',
            'vatansms_api_key' => $settings['vatansms_api_key'] ?? '',
            'vatansms_sender' => $settings['vatansms_sender'] ?? '',
            'vatansms_active' => filter_var($settings['vatansms_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'vatansms_abandoned_cart_message' => $settings['vatansms_abandoned_cart_message'] ?? "Merhaba, sepetinizdeki ürünler sizi bekliyor! Satın alma işlemini tamamlamak için sitemizi ziyaret edebilirsiniz.",
            'vatansms_new_order_message' => $settings['vatansms_new_order_message'] ?? "Sayın {isim}, {siparis_no} numaralı siparişiniz başarıyla alınmıştır. Bizi tercih ettiğiniz için teşekkür ederiz.",
            'vatansms_shipped_message' => $settings['vatansms_shipped_message'] ?? "Sayın {isim}, {siparis_no} numaralı siparişiniz kargoya verilmiştir.",
        ]);
    }

    public function form($form)
    {
        return $form
            ->schema([
                Section::make('API Ayarları')
                    ->description('VatanSMS paneli "API Bilgilerim" kısmından alacağınız bilgiler.')
                    ->schema([
                        Toggle::make('vatansms_active')
                            ->label('SMS Gönderimini Aktifleştir')
                            ->helperText('Sistemin müşterilere SMS atıp atmayacağını belirler.'),
                        
                        TextInput::make('vatansms_api_id')
                            ->label('API ID')
                            ->required(),

                        TextInput::make('vatansms_api_key')
                            ->label('API KEY')
                            ->password()
                            ->revealable()
                            ->required(),
                            
                        TextInput::make('vatansms_sender')
                            ->label('Gönderici Adı (Sender)')
                            ->helperText('Onaylı gönderici başlığınız. (Örn: PATENLI)')
                            ->required(),
                    ]),
                    
                Section::make('SMS Şablonları')
                    ->description('Müşterilere gidecek mesajların içeriğini belirleyin. {isim} ve {siparis_no} gibi değişkenler kullanabilirsiniz.')
                    ->schema([
                        Textarea::make('vatansms_new_order_message')
                            ->label('Yeni Sipariş Mesajı (Bilgi)')
                            ->helperText('Değişkenler: {isim}, {siparis_no}, {tutar}')
                            ->rows(2)
                            ->required(),
                            
                        Textarea::make('vatansms_shipped_message')
                            ->label('Kargoya Verildi Mesajı (Bilgi)')
                            ->helperText('Değişkenler: {isim}, {siparis_no}, {tutar}')
                            ->rows(2)
                            ->required(),
                            
                        Textarea::make('vatansms_abandoned_cart_message')
                            ->label('Sepet Hatırlatma Mesajı (Ticari)')
                            ->helperText('Sadece ödeme sayfasında SMS izni veren müşterilere atılır.')
                            ->rows(3)
                            ->required(),
                    ])
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Ayarları Kaydet')
                ->submit('save')
                ->color('primary'),
                
            Action::make('test')
                ->label('Test SMS Gönder')
                ->color('gray')
                ->form([
                    TextInput::make('test_phone')
                        ->label('Telefon Numarası')
                        ->placeholder('5XXXXXXXXX')
                        ->required()
                ])
                ->action(function (array $data) {
                    $phone = $data['test_phone'] ?? '';
                    
                    if (empty($phone)) {
                        Notification::make()->danger()->title('Hata')->body('Geçerli bir telefon numarası giriniz.')->send();
                        return;
                    }

                    $service = app(VatanSmsService::class);
                    $result = $service->send($phone, "Bu bir test mesajıdır. VatanSMS entegrasyonu başarıyla çalışıyor.", 'turkce', 'bilgi');

                    if ($result) {
                        Notification::make()->success()->title('Test mesajı gönderildi!')->body('Lütfen telefonunuzu kontrol edin.')->send();
                    } else {
                        Notification::make()->danger()->title('SMS Gönderilemedi')->body('API hatası. Lütfen logları veya API bilgilerinizi kontrol edin.')->send();
                    }
                })
                ->modalHeading('Test Mesajı Gönder')
                ->modalDescription('Bu işlem girdiğiniz numaraya VatanSMS üzerinden bir test mesajı gönderecektir.')
        ];
    }

    public function save()
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : $value]
            );
        }

        Notification::make()
            ->success()
            ->title('VatanSMS ayarları başarıyla kaydedildi')
            ->send();
    }
}

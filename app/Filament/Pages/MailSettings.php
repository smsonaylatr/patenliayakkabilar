<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;

class MailSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'E-Posta Ayarları';
    protected static \UnitEnum|string|null $navigationGroup = 'Site Yönetimi';
    protected static ?string $title = 'E-Posta Sunucusu & Gönderim Ayarları';
    protected static string $view = 'filament.pages.mail-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username',
            'smtp_password', 'smtp_from_address', 'smtp_from_name',
            'mail_order_confirmation', 'mail_shipping_update', 'mail_welcome',
            'mail_abandoned_cart', 'mail_stock_notification', 'mail_invoice'
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'smtp_host' => $settings['smtp_host'] ?? '',
            'smtp_port' => $settings['smtp_port'] ?? 465,
            'smtp_encryption' => $settings['smtp_encryption'] ?? 'ssl',
            'smtp_username' => $settings['smtp_username'] ?? '',
            'smtp_password' => $settings['smtp_password'] ?? '',
            'smtp_from_address' => $settings['smtp_from_address'] ?? '',
            'smtp_from_name' => $settings['smtp_from_name'] ?? '',
            'mail_order_confirmation' => filter_var($settings['mail_order_confirmation'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_shipping_update' => filter_var($settings['mail_shipping_update'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_welcome' => filter_var($settings['mail_welcome'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_abandoned_cart' => filter_var($settings['mail_abandoned_cart'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_stock_notification' => filter_var($settings['mail_stock_notification'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_invoice' => filter_var($settings['mail_invoice'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('SMTP Sunucu Ayarları')
                    ->schema([
                        TextInput::make('smtp_host')
                            ->label('SMTP Sunucusu')
                            ->placeholder('mail.patenliayakkabilar.com'),
                        TextInput::make('smtp_port')
                            ->label('Port')
                            ->numeric()
                            ->default(465),
                        Select::make('smtp_encryption')
                            ->label('Şifreleme')
                            ->options([
                                'ssl' => 'SSL (Port 465)',
                                'tls' => 'TLS (Port 587)',
                                'none' => 'Şifreleme Yok',
                            ])
                            ->native(false),
                        TextInput::make('smtp_username')
                            ->label('Kullanıcı Adı (E-Posta)')
                            ->email(),
                        TextInput::make('smtp_password')
                            ->label('Şifre')
                            ->password()
                            ->revealable(),
                        TextInput::make('smtp_from_address')
                            ->label('Varsayılan Gönderici Adresi')
                            ->email(),
                        TextInput::make('smtp_from_name')
                            ->label('Gönderici Adı'),
                    ])->columns(2),

                Section::make('E-Posta Hesapları')
                    ->description('Sistemde kullanılan varsayılan e-posta adresleri (Bilgi Amaçlıdır)')
                    ->schema([
                        Placeholder::make('info')
                            ->label('Genel Bilgilendirme')
                            ->content('info@patenliayakkabilar.com'),
                        Placeholder::make('siparis')
                            ->label('Sipariş Bildirimleri')
                            ->content('siparis@patenliayakkabilar.com'),
                        Placeholder::make('destek')
                            ->label('Müşteri Destek')
                            ->content('destek@patenliayakkabilar.com'),
                        Placeholder::make('isbirligi')
                            ->label('İş Birliği & Pazarlama')
                            ->content('isbirligi@patenliayakkabilar.com'),
                    ])->columns(2),

                Section::make('E-Posta Bildirim Ayarları')
                    ->schema([
                        Toggle::make('mail_order_confirmation')
                            ->label('Sipariş Onay Maili')
                            ->default(true),
                        Toggle::make('mail_shipping_update')
                            ->label('Kargo Güncelleme Maili')
                            ->default(true),
                        Toggle::make('mail_welcome')
                            ->label('Hoşgeldin Maili')
                            ->default(true),
                        Toggle::make('mail_abandoned_cart')
                            ->label('Terk Edilen Sepet Hatırlatması')
                            ->default(true),
                        Toggle::make('mail_stock_notification')
                            ->label('Stok Bildirimi')
                            ->default(true),
                        Toggle::make('mail_invoice')
                            ->label('Fatura E-Postası')
                            ->default(true),
                    ])->columns(3),

                Section::make('Son Gönderilen E-Postalar')
                    ->schema([
                        Placeholder::make('stats')
                            ->label('İstatistikler')
                            ->content('İstatistikler Mail Logları sayfasından detaylı incelenebilir.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Her ayarı updateOrCreate ile kaydediyoruz
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Değişikliklerin uygulanması için cache'i siliyoruz
        Cache::forget('mail_smtp_settings');

        Notification::make()
            ->title('Ayarlar kaydedildi')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->submit('save')
                ->color('primary'),
            Action::make('testMail')
                ->label('Test Mail Gönder')
                ->color('secondary')
                ->action('sendTestMail'),
        ];
    }

    public function sendTestMail(): void
    {
        try {
            // Admin kullanıcısına test maili at
            Mail::raw('Bu bir test e-postasıdır.', function ($message) {
                $message->to(auth()->user()->email)
                    ->subject('Patenli Ayakkabılar — Test E-Postası ✅');
            });

            Notification::make()
                ->title('Test Maili Gönderildi')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Test Maili Başarısız')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}

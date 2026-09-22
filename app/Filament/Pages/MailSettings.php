<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class MailSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-envelope';
    }

    public static function getNavigationLabel(): string
    {
        return 'E-Posta Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'E-Posta Sunucusu & Gönderim Ayarları';
    }

    protected string $view = 'filament.pages.mail-settings';

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
            'smtp_host' => $settings['smtp_host'] ?? config('mail.mailers.smtp.host', ''),
            'smtp_port' => $settings['smtp_port'] ?? config('mail.mailers.smtp.port', 465),
            'smtp_encryption' => $settings['smtp_encryption'] ?? 'ssl',
            'smtp_username' => $settings['smtp_username'] ?? config('mail.mailers.smtp.username', ''),
            'smtp_password' => $settings['smtp_password'] ?? '',
            'smtp_from_address' => $settings['smtp_from_address'] ?? config('mail.from.address', ''),
            'smtp_from_name' => $settings['smtp_from_name'] ?? config('mail.from.name', 'Patenli Ayakkabılar'),
            'mail_order_confirmation' => filter_var($settings['mail_order_confirmation'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_shipping_update' => filter_var($settings['mail_shipping_update'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_welcome' => filter_var($settings['mail_welcome'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_abandoned_cart' => filter_var($settings['mail_abandoned_cart'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_stock_notification' => filter_var($settings['mail_stock_notification'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_invoice' => filter_var($settings['mail_invoice'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function schema(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
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
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Placeholder alanlarını kaydetme
            if (in_array($key, ['info', 'siparis', 'destek', 'isbirligi', 'stats'])) {
                continue;
            }
            Setting::updateOrCreate(['key' => $key], ['value' => is_bool($value) ? ($value ? '1' : '0') : $value]);
        }

        // SMTP config cache'ini temizle
        Cache::forget('mail_smtp_settings');

        Notification::make()
            ->title('Ayarlar kaydedildi')
            ->success()
            ->send();
    }

    public function sendTestMail(): void
    {
        try {
            // Önce form state'ini uygula
            $data = $this->form->getState();

            // SMTP ayarlarını geçici olarak config'e yaz
            if (!empty($data['smtp_host'])) {
                $port = (int)($data['smtp_port'] ?? 465);
                $encryption = $data['smtp_encryption'] ?? 'ssl';

                config([
                    'mail.mailers.smtp.host' => $data['smtp_host'],
                    'mail.mailers.smtp.port' => $port,
                    'mail.mailers.smtp.username' => $data['smtp_username'] ?? null,
                    'mail.mailers.smtp.password' => $data['smtp_password'] ?? null,
                ]);

                if ($port === 465 || $encryption === 'ssl') {
                    config(['mail.mailers.smtp.scheme' => 'smtps']);
                } elseif ($port === 587 || $encryption === 'tls') {
                    config(['mail.mailers.smtp.scheme' => 'smtp']);
                }

                // Transport'u yeniden oluştur
                app('mail.manager')->purge('smtp');
            }

            Mail::raw('Bu bir test e-postasıdır. Patenli Ayakkabılar mail sunucusu düzgün çalışıyor! ✅', function ($message) {
                $message->to(auth()->user()->email)
                    ->subject('Patenli Ayakkabılar — Test E-Postası ✅');
            });

            Notification::make()
                ->title('Test Maili Gönderildi ✅')
                ->body(auth()->user()->email . ' adresine gönderildi.')
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

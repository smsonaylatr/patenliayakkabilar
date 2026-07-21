<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class TelegramSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'heroicon-o-chat-bubble-left-ellipsis';
    }

    public static function getNavigationLabel(): string
    {
        return 'Telegram Bildirimleri';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Telegram Sipariş Bildirimleri';
    }

    protected string $view = 'filament.pages.telegram-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'telegram_bot_token',
            'telegram_chat_id',
            'telegram_active',
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'telegram_bot_token' => $settings['telegram_bot_token'] ?? '',
            'telegram_chat_id' => $settings['telegram_chat_id'] ?? '',
            'telegram_active' => filter_var($settings['telegram_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function form($form)
    {
        return $form
            ->schema([
                Section::make('Telegram Bot Ayarları')
                    ->description('Yeni sipariş geldiğinde anında Telegram üzerinden bildirim almak için gerekli bilgileri doldurun.')
                    ->schema([
                        Toggle::make('telegram_active')
                            ->label('Bildirimleri Aktifleştir')
                            ->helperText('Telegram sipariş bildirimlerini açıp kapatmanızı sağlar.'),
                        
                        TextInput::make('telegram_bot_token')
                            ->label('Bot Token (API Token)')
                            ->helperText('BotFather üzerinden aldığınız token. (Örn: 123456789:ABCdefGHI...)')
                            ->password()
                            ->revealable(),

                        TextInput::make('telegram_chat_id')
                            ->label('Chat ID (Kullanıcı veya Grup ID)')
                            ->helperText('Bildirimlerin gideceği ID. (Örn: -100123456789 veya 12345678)'),
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
                ->label('Test Mesajı Gönder')
                ->action('sendTestMessage')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Test Bildirimi Gönderilecek')
                ->modalDescription('Bu işlem şu anki kaydedilmiş ayarlarla Telegram\'a bir test mesajı gönderecektir. Emin misiniz?')
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Boole değerleri 1/0 olarak saklayalım
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }
            
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Notification::make()
            ->success()
            ->title('Telegram ayarları başarıyla kaydedildi')
            ->send();
    }

    public function sendTestMessage(): void
    {
        $token = Setting::where('key', 'telegram_bot_token')->value('value');
        $chatId = Setting::where('key', 'telegram_chat_id')->value('value');
        $isActive = filter_var(Setting::where('key', 'telegram_active')->value('value'), FILTER_VALIDATE_BOOLEAN);

        if (!$isActive) {
            Notification::make()->warning()->title('Test Başarısız')->body('Bildirimler kapalı (Aktifleştirin).')->send();
            return;
        }

        if (empty($token) || empty($chatId)) {
            Notification::make()->warning()->title('Test Başarısız')->body('Token ve Chat ID eksik!')->send();
            return;
        }

        $message = "✅ *Test Başarılı!*\n\nPatenli Ayakkabılar Telegram bildirim sistemi düzgün çalışıyor.\n\nYeni siparişleriniz anında buraya düşecektir!";

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);

            if ($response->successful()) {
                Notification::make()->success()->title('Test mesajı gönderildi!')->body('Telegram\'ı kontrol edin.')->send();
            } else {
                Notification::make()->danger()->title('Telegram API Hatası')->body($response->body())->send();
            }
        } catch (\Exception $e) {
            Notification::make()->danger()->title('Hata Oluştu')->body($e->getMessage())->send();
        }
    }
}

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

class EInvoiceSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationLabel(): string
    {
        return 'E-Fatura & SMTP Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'E-Fatura ve Mail (SMTP) Ayarları';
    }

    protected string $view = 'filament.pages.e-invoice-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'gib_username',
            'gib_password',
            'smtp_host',
            'smtp_port',
            'smtp_username',
            'smtp_password',
            'smtp_from_address',
            'smtp_from_name'
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'gib_username' => $settings['gib_username'] ?? '',
            'gib_password' => $settings['gib_password'] ?? '',
            'smtp_host' => $settings['smtp_host'] ?? '',
            'smtp_port' => $settings['smtp_port'] ?? '587',
            'smtp_username' => $settings['smtp_username'] ?? '',
            'smtp_password' => $settings['smtp_password'] ?? '',
            'smtp_from_address' => $settings['smtp_from_address'] ?? '',
            'smtp_from_name' => $settings['smtp_from_name'] ?? 'Patenli Ayakkabılar',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('GİB E-Arşiv Portal Ayarları')
                    ->description('Ücretsiz GİB portalı giriş bilgilerinizi buraya girin.')
                    ->schema([
                        TextInput::make('gib_username')
                            ->label('GİB Kullanıcı Adı')
                            ->required(),
                        TextInput::make('gib_password')
                            ->label('GİB Şifresi')
                            ->password()
                            ->required(),
                    ])->columns(2),

                Section::make('SMTP (E-Posta Gönderim) Ayarları')
                    ->description('Faturanın müşteriye gönderilebilmesi için e-posta sunucu ayarlarınız.')
                    ->schema([
                        TextInput::make('smtp_host')
                            ->label('SMTP Sunucusu (Host)')
                            ->placeholder('mail.domain.com')
                            ->required(),
                        TextInput::make('smtp_port')
                            ->label('SMTP Port')
                            ->numeric()
                            ->default(587)
                            ->required(),
                        TextInput::make('smtp_username')
                            ->label('E-Posta Adresi (Kullanıcı Adı)')
                            ->email()
                            ->required(),
                        TextInput::make('smtp_password')
                            ->label('E-Posta Şifresi')
                            ->password()
                            ->required(),
                        TextInput::make('smtp_from_address')
                            ->label('Gönderici E-Posta Adresi')
                            ->email()
                            ->required(),
                        TextInput::make('smtp_from_name')
                            ->label('Gönderici Adı (Unvan)')
                            ->default('Patenli Ayakkabılar')
                            ->required(),
                    ])->columns(2),
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
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            foreach ($data as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            Notification::make()
                ->title('Başarılı')
                ->body('E-Fatura ve SMTP ayarları başarıyla güncellendi.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata')
                ->body('Ayarlar kaydedilirken bir hata oluştu: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}

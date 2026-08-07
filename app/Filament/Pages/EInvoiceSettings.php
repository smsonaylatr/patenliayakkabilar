<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Setting;
use App\Services\GibEArsivService;

class EInvoiceSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationLabel(): string
    {
        return 'Entegrasyon & Fatura Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'E-Arşiv / E-Fatura & Entegrasyon Ayarları';
    }

    protected string $view = 'filament.pages.e-invoice-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'gib_user_code',
            'gib_password',
            'gib_test_mode',
            'gib_auto_invoice',
            'gib_auto_email',
            'gib_logo_url',
            'gib_company_name',
            'gib_company_vkn',
            'gib_company_tax_office',
            'gib_company_address',
            'parasut_company_id',
            'parasut_client_id',
            'parasut_client_secret',
            'parasut_username',
            'parasut_password',
            'smtp_host',
            'smtp_port',
            'smtp_username',
            'smtp_password',
            'smtp_from_address',
            'smtp_from_name',
            'n8n_api_token'
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'gib_user_code' => $settings['gib_user_code'] ?? config('gib.user_code', ''),
            'gib_password' => $settings['gib_password'] ?? config('gib.password', ''),
            'gib_test_mode' => isset($settings['gib_test_mode']) ? filter_var($settings['gib_test_mode'], FILTER_VALIDATE_BOOLEAN) : config('gib.is_test', true),
            'gib_auto_invoice' => isset($settings['gib_auto_invoice']) ? filter_var($settings['gib_auto_invoice'], FILTER_VALIDATE_BOOLEAN) : true,
            'gib_auto_email' => isset($settings['gib_auto_email']) ? filter_var($settings['gib_auto_email'], FILTER_VALIDATE_BOOLEAN) : true,
            'gib_logo_url' => $settings['gib_logo_url'] ?? asset('favicon.png'),
            'gib_company_name' => $settings['gib_company_name'] ?? config('gib.company_name', 'Patenli Ayakkabılar E-Ticaret'),
            'gib_company_vkn' => $settings['gib_company_vkn'] ?? config('gib.company_vkn', '1111111111'),
            'gib_company_tax_office' => $settings['gib_company_tax_office'] ?? config('gib.company_tax_office', 'Kadıköy'),
            'gib_company_address' => $settings['gib_company_address'] ?? config('gib.company_address', 'İstanbul'),
            'parasut_company_id' => $settings['parasut_company_id'] ?? '',
            'parasut_client_id' => $settings['parasut_client_id'] ?? '',
            'parasut_client_secret' => $settings['parasut_client_secret'] ?? '',
            'parasut_username' => $settings['parasut_username'] ?? '',
            'parasut_password' => $settings['parasut_password'] ?? '',
            'smtp_host' => $settings['smtp_host'] ?? '',
            'smtp_port' => $settings['smtp_port'] ?? '587',
            'smtp_username' => $settings['smtp_username'] ?? '',
            'smtp_password' => $settings['smtp_password'] ?? '',
            'smtp_from_address' => $settings['smtp_from_address'] ?? '',
            'smtp_from_name' => $settings['smtp_from_name'] ?? 'Patenli Ayakkabılar',
            'n8n_api_token' => $settings['n8n_api_token'] ?? 'patenli_n8n_secret_123',
        ]);
    }

    public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                Section::make('GİB E-Arşiv Portal Ayarları (mlevent/fatura)')
                    ->description('Gelir İdaresi Başkanlığı E-Arşiv portalı üzerinden resmi fatura kesebilmek için portal kullanıcı bilgileri.')
                    ->schema([
                        Toggle::make('gib_test_mode')
                            ->label('Test Modu (Sandbox / Demo)')
                            ->helperText('Açık olduğunda GİB test portalına fatura oluşturulur. Canlıya alırken kapatın.')
                            ->default(true),
                        Toggle::make('gib_auto_invoice')
                            ->label('Tam Otomatik Fatura Kes')
                            ->helperText('Sipariş tamamlandığında veya ödendiğinde GİB E-Arşiv faturasını otomatik keser.')
                            ->default(true),
                        Toggle::make('gib_auto_email')
                            ->label('Faturayı Müşteriye E-Posta ile Gönder')
                            ->helperText('Fatura oluşturulduğunda faturayı otomatik olarak müşterinin e-posta adresine gönderir.')
                            ->default(true),
                        TextInput::make('gib_logo_url')
                            ->label('Fatura Firma Logo URL\'si')
                            ->placeholder('https://patenliayakkabilar.com/favicon.png')
                            ->helperText('Fatura belgesinin ve e-postanın üst kısmında görünecek logo adresi.'),
                        TextInput::make('gib_user_code')
                            ->label('GİB Kullanıcı Kodu (VKN / TCKN)')
                            ->placeholder('örn: 12345678')
                            ->required(!empty($this->data['gib_test_mode'] ?? false) ? false : true),
                        TextInput::make('gib_password')
                            ->label('GİB Portal Şifresi')
                            ->password()
                            ->revealable()
                            ->required(!empty($this->data['gib_test_mode'] ?? false) ? false : true),
                        TextInput::make('gib_company_name')
                            ->label('Satıcı Firma Unvanı')
                            ->placeholder('Patenli Ayakkabılar E-Ticaret A.Ş.')
                            ->required(),
                        TextInput::make('gib_company_vkn')
                            ->label('Satıcı VKN / TCKN')
                            ->required(),
                        TextInput::make('gib_company_tax_office')
                            ->label('Satıcı Vergi Dairesi')
                            ->required(),
                        Textarea::make('gib_company_address')
                            ->label('Satıcı Açık Adresi')
                            ->columnSpanFull()
                            ->required(),
                    ])->columns(2),

                Section::make('Paraşüt E-Fatura API Ayarları')
                    ->description('Paraşüt Ayarlar > Uygulamalar > API Erişim Bilgileri bölümünden alabilirsiniz.')
                    ->schema([
                        TextInput::make('parasut_company_id')
                            ->label('Firma ID (Company ID)'),
                        TextInput::make('parasut_username')
                            ->label('Paraşüt Kullanıcı Adı'),
                        TextInput::make('parasut_password')
                            ->label('Paraşüt Şifresi')
                            ->password(),
                        TextInput::make('parasut_client_id')
                            ->label('Client ID'),
                        TextInput::make('parasut_client_secret')
                            ->label('Client Secret')
                            ->password(),
                    ])->columns(2)->collapsible()->collapsed(),

                Section::make('SMTP (E-Posta Gönderim) Ayarları')
                    ->description('Faturanın müşteriye gönderilebilmesi için e-posta sunucu ayarlarınız.')
                    ->schema([
                        TextInput::make('smtp_host')
                            ->label('SMTP Sunucusu (Host)')
                            ->placeholder('mail.domain.com'),
                        TextInput::make('smtp_port')
                            ->label('SMTP Port')
                            ->numeric()
                            ->default(587),
                        TextInput::make('smtp_username')
                            ->label('E-Posta Adresi (Kullanıcı Adı)')
                            ->email(),
                        TextInput::make('smtp_password')
                            ->label('E-Posta Şifresi')
                            ->password(),
                        TextInput::make('smtp_from_address')
                            ->label('Gönderici E-Posta Adresi')
                            ->email(),
                        TextInput::make('smtp_from_name')
                            ->label('Gönderici Adı (Unvan)')
                            ->default('Patenli Ayakkabılar'),
                    ])->columns(2)->collapsible()->collapsed(),

                Section::make('N8N & Otomasyon Ayarları')
                    ->schema([
                        TextInput::make('n8n_api_token')
                            ->label('N8N Güvenlik Tokenı')
                            ->password()
                            ->revealable(),
                    ])->collapsible()->collapsed(),
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
            Action::make('testGib')
                ->label('GİB Bağlantısını Test Et')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->action('testGibConnection'),
        ];
    }

    public function testGibConnection(): void
    {
        $this->save();

        $service = app(GibEArsivService::class);
        $result = $service->testConnection();

        if ($result['success']) {
            Notification::make()
                ->title('Bağlantı Başarılı')
                ->body($result['message'])
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Bağlantı Başarısız')
                ->body($result['message'])
                ->danger()
                ->send();
        }
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
                ->body('Tüm ayarlar başarıyla güncellendi.')
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

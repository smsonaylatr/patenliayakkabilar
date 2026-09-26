<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Setting;

class PopupSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'heroicon-o-window';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pop-up & Widget Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Pop-up & Widget Ayarları';
    }

    protected string $view = 'filament.pages.popup-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'popup_active',
            'popup_image',
            'popup_link',
            'call_widget_active',
            'gcr_widget_active',
            'gcr_merchant_id',
            'gcr_badge_position',
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'popup_active' => (bool) ($settings['popup_active'] ?? false),
            'popup_image' => $settings['popup_image'] ?? null,
            'popup_link' => $settings['popup_link'] ?? '',
            'call_widget_active' => (bool) ($settings['call_widget_active'] ?? true),
            'gcr_widget_active' => filter_var($settings['gcr_widget_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'gcr_merchant_id' => $settings['gcr_merchant_id'] ?? '5828544730',
            'gcr_badge_position' => $settings['gcr_badge_position'] ?? 'BOTTOM_LEFT',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Google Müşteri Yorumları (GCR) Widget\'ı')
                    ->description('Google Merchant Center Müşteri Yorumları (Customer Reviews) rozetini ana sayfada yönetin.')
                    ->schema([
                        Toggle::make('gcr_widget_active')
                            ->label('GCR Rozet Widget\'ı Aktif Mi?')
                            ->helperText('Açık olduğunda ana sayfanın belirlenen köşesinde Google Müşteri Yorumları rozeti görünür.')
                            ->default(true),

                        Grid::make(2)->schema([
                            TextInput::make('gcr_merchant_id')
                                ->label('Merchant Center ID')
                                ->placeholder('5828544730')
                                ->helperText('Google Merchant Center hesap numaranız.')
                                ->numeric(),

                            Select::make('gcr_badge_position')
                                ->label('Rozet Konumu')
                                ->options([
                                    'BOTTOM_LEFT' => 'Sol Alt (Önerilen)',
                                    'BOTTOM_RIGHT' => 'Sağ Alt',
                                ])
                                ->default('BOTTOM_LEFT')
                                ->native(false),
                        ]),
                    ]),

                Section::make('Telefonla Arama Widget')
                    ->description('Sayfanın sağ alt köşesinde bulunan telefonla arama butonunu yönetin.')
                    ->schema([
                        Toggle::make('call_widget_active')
                            ->label('Arama Widget Aktif Mi?')
                            ->default(true),
                    ]),

                Section::make('Pop-up İçeriği')
                    ->description('Siteye ilk girişte kullanıcılara gösterilecek pop-up görselini ayarlayın.')
                    ->schema([
                        Toggle::make('popup_active')
                            ->label('Pop-up Aktif Mi?')
                            ->default(false),
                        
                        FileUpload::make('popup_image')
                            ->label('Pop-up Görseli')
                            ->image()
                            ->directory('popups')
                            ->disk('public')
                            ->imageEditor()
                            ->requiredWith('popup_active'),

                        TextInput::make('popup_link')
                            ->label('Yönlendirme Linki (Opsiyonel)')
                            ->placeholder('Örn: /kampanya-2026')
                            ->url()
                            ->nullable(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Checkbox value is boolean, cast to string '1' or '0' for DB if needed, or rely on model cast
            // Setting values are usually strings
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        \Illuminate\Support\Facades\Cache::forget('setting_call_widget_active');
        \Illuminate\Support\Facades\Cache::forget('setting_gcr_widget_active');
        \Illuminate\Support\Facades\Cache::forget('setting_gcr_merchant_id');
        \Illuminate\Support\Facades\Cache::forget('setting_gcr_badge_position');

        Notification::make()
            ->title('Başarılı')
            ->body('Ayarlar başarıyla kaydedildi.')
            ->success()
            ->send();
    }
}

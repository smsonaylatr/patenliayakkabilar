<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
        return 'Pop-up Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Giriş Pop-up Ayarları';
    }

    protected string $view = 'filament.pages.popup-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'popup_active',
            'popup_image',
            'popup_link',
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'popup_active' => (bool) ($settings['popup_active'] ?? false),
            'popup_image' => $settings['popup_image'] ?? null,
            'popup_link' => $settings['popup_link'] ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
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

        Notification::make()
            ->title('Başarılı')
            ->body('Pop-up ayarları başarıyla kaydedildi.')
            ->success()
            ->send();
    }
}

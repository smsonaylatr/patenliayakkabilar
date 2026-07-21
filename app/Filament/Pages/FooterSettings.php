<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Setting;

class FooterSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'heroicon-o-bars-4';
    }

    public static function getNavigationLabel(): string
    {
        return 'Footer Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Footer (Alt Bilgi) Ayarları';
    }

    protected string $view = 'filament.pages.footer-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'footer_description',
            'footer_copyright',
            'footer_facebook',
            'footer_instagram',
            'footer_tiktok',
            'footer_twitter',
            'footer_youtube',
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'footer_description' => $settings['footer_description'] ?? 'Çocukların eğlenirken güvende olması için ürün seçimini, kargo sürecini ve satış sonrası desteği kolaylaştırıyoruz.',
            'footer_copyright' => $settings['footer_copyright'] ?? '© 2026 Patenli Ayakkabılar. Tüm hakları saklıdır.',
            'footer_facebook' => $settings['footer_facebook'] ?? '',
            'footer_instagram' => $settings['footer_instagram'] ?? '',
            'footer_tiktok' => $settings['footer_tiktok'] ?? '',
            'footer_twitter' => $settings['footer_twitter'] ?? '',
            'footer_youtube' => $settings['footer_youtube'] ?? '',
        ]);
    }

    public function form($form)
    {
        return $form
            ->schema([
                Section::make('Metin Ayarları')
                    ->schema([
                        Textarea::make('footer_description')
                            ->label('Hakkımızda (Kısa Açıklama)')
                            ->rows(3)
                            ->required(),
                        
                        TextInput::make('footer_copyright')
                            ->label('Telif Hakkı (Copyright) Metni')
                            ->required(),
                    ]),

                Section::make('Sosyal Medya Linkleri')
                    ->description('Linkini boş bıraktığınız sosyal medya ikonları sitede otomatik olarak gizlenecektir.')
                    ->schema([
                        TextInput::make('footer_instagram')
                            ->label('Instagram URL')
                            ->url(),
                            
                        TextInput::make('footer_tiktok')
                            ->label('TikTok URL')
                            ->url(),
                            
                        TextInput::make('footer_facebook')
                            ->label('Facebook URL')
                            ->url(),
                            
                        TextInput::make('footer_twitter')
                            ->label('Twitter / X URL')
                            ->url(),
                            
                        TextInput::make('footer_youtube')
                            ->label('YouTube URL')
                            ->url(),
                    ])
                    ->columns(2)
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        Notification::make()
            ->success()
            ->title('Footer ayarları başarıyla kaydedildi')
            ->send();
    }
}

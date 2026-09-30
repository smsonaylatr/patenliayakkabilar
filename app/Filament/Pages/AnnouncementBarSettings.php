<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class AnnouncementBarSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-megaphone';
    }

    public static function getNavigationLabel(): string
    {
        return 'Duyuru Çubuğu';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Duyuru Çubuğu Ayarları';
    }

    protected string $view = 'filament.pages.announcement-bar-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'announcement_bar_items',
            'announcement_bar_enabled',
        ])->pluck('value', 'key')->toArray();

        $items = [];
        if (!empty($settings['announcement_bar_items'])) {
            $decoded = json_decode($settings['announcement_bar_items'], true);
            if (is_array($decoded)) {
                $items = $decoded;
            }
        }

        // Varsayılan maddeler
        if (empty($items)) {
            $items = [
                ['emoji' => '🚚', 'text' => 'KAPIDA ÖDEME FIRSATI'],
                ['emoji' => '🔄', 'text' => '%100 İADE GARANTİSİ'],
                ['emoji' => '💳', 'text' => 'VADESİZ 3 TAKSİT'],
                ['emoji' => '📦', 'text' => 'HIZLI TESLİMAT'],
                ['emoji' => '⚡', 'text' => '24 SAATTE KARGO'],
            ];
        }

        $this->form->fill([
            'announcement_bar_enabled' => ($settings['announcement_bar_enabled'] ?? '1') === '1',
            'announcement_bar_items' => $items,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Genel Ayarlar')
                    ->schema([
                        Toggle::make('announcement_bar_enabled')
                            ->label('Duyuru Çubuğunu Göster')
                            ->helperText('Kapatırsanız sitedeki kayan duyuru çubuğu gizlenir.')
                            ->default(true),
                    ]),

                Section::make('Duyuru Maddeleri')
                    ->description('Sitedeki kayan duyuru çubuğunda gösterilecek maddeleri buradan düzenleyebilirsiniz. Sıralamayı sürükleyerek değiştirebilirsiniz.')
                    ->schema([
                        Repeater::make('announcement_bar_items')
                            ->label('')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('emoji')
                                        ->label('Emoji / İkon')
                                        ->placeholder('🚚')
                                        ->maxLength(10)
                                        ->helperText('Emoji yapıştırın (opsiyonel)'),
                                    TextInput::make('text')
                                        ->label('Metin')
                                        ->placeholder('KAPIDA ÖDEME FIRSATI')
                                        ->required()
                                        ->maxLength(100),
                                ]),
                            ])
                            ->defaultItems(5)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                !empty($state['text']) 
                                    ? ($state['emoji'] ?? '') . ' ' . $state['text'] 
                                    : null
                            )
                            ->addActionLabel('Yeni Madde Ekle')
                            ->minItems(1)
                            ->maxItems(10),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Enabled durumunu kaydet
        Setting::updateOrCreate(
            ['key' => 'announcement_bar_enabled'],
            ['value' => $data['announcement_bar_enabled'] ? '1' : '0']
        );

        // Maddeleri JSON olarak kaydet
        Setting::updateOrCreate(
            ['key' => 'announcement_bar_items'],
            ['value' => json_encode($data['announcement_bar_items'], JSON_UNESCAPED_UNICODE)]
        );

        // Cache'i temizle
        Cache::forget('announcement_bar_items');
        Cache::forget('announcement_bar_enabled');

        Notification::make()
            ->title('Başarılı')
            ->body('Duyuru çubuğu ayarları başarıyla kaydedildi.')
            ->success()
            ->send();
    }
}

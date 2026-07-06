<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Setting;
use App\Models\Product;

class BannerSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'heroicon-o-photo';
    }

    public static function getNavigationLabel(): string
    {
        return 'Banner Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Banner Ayarları';
    }

    protected string $view = 'filament.pages.banner-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'banner_pill_text',
            'banner_title_1',
            'banner_title_2',
            'banner_title_3',
            'banner_desc',
            'banner_btn1_text',
            'banner_btn1_link',
            'banner_btn2_text',
            'banner_btn2_link',
            'banner_product_1_id',
            'banner_product_2_id',
            'banner_product_3_id',
            'banner_bg_color_1',
            'banner_bg_color_2',
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'banner_pill_text' => $settings['banner_pill_text'] ?? '2026 · Yeni Koleksiyon',
            'banner_title_1' => $settings['banner_title_1'] ?? 'Her Adımda',
            'banner_title_2' => $settings['banner_title_2'] ?? 'Premium',
            'banner_title_3' => $settings['banner_title_3'] ?? 'Bir Deneyim',
            'banner_desc' => $settings['banner_desc'] ?? "500'den fazla model, sınırsız kombinasyon.\nLarcivert ile tarzını keşfet.",
            'banner_btn1_text' => $settings['banner_btn1_text'] ?? 'Koleksiyonu Keşfet',
            'banner_btn1_link' => $settings['banner_btn1_link'] ?? '/urunler',
            'banner_btn2_text' => $settings['banner_btn2_text'] ?? 'İndirimleri Gör',
            'banner_btn2_link' => $settings['banner_btn2_link'] ?? '/urunler?indirim=true',
            'banner_product_1_id' => $settings['banner_product_1_id'] ?? null,
            'banner_product_2_id' => $settings['banner_product_2_id'] ?? null,
            'banner_product_3_id' => $settings['banner_product_3_id'] ?? null,
            'banner_bg_color_1' => $settings['banner_bg_color_1'] ?? '#ffffff',
            'banner_bg_color_2' => $settings['banner_bg_color_2'] ?? '#f8fafc',
        ]);
    }

    public function form($form)
    {
        return $form
            ->schema([
                Section::make('Renk Ayarları (Arka Plan Gradyanı)')
                    ->schema([
                        Grid::make(2)->schema([
                            ColorPicker::make('banner_bg_color_1')->label('1. Renk (Başlangıç)')->required(),
                            ColorPicker::make('banner_bg_color_2')->label('2. Renk (Bitiş)')->required(),
                        ]),
                    ]),

                Section::make('Metin Ayarları')
                    ->schema([
                        TextInput::make('banner_pill_text')->label('Üst Ufak Etiket')->required(),
                        Grid::make(3)->schema([
                            TextInput::make('banner_title_1')->label('Ana Başlık (1. Satır)')->required(),
                            TextInput::make('banner_title_2')->label('Ana Başlık (Renkli Kısım)')->required(),
                            TextInput::make('banner_title_3')->label('Ana Başlık (3. Satır)')->required(),
                        ]),
                        Textarea::make('banner_desc')->label('Alt Açıklama')->rows(3)->required(),
                    ]),

                Section::make('Buton Ayarları')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('banner_btn1_text')->label('1. Buton Yazısı')->required(),
                            TextInput::make('banner_btn1_link')->label('1. Buton Linki')->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('banner_btn2_text')->label('2. Buton (Ghost) Yazısı')->required(),
                            TextInput::make('banner_btn2_link')->label('2. Buton Linki')->required(),
                        ]),
                    ]),

                Section::make('Görsel Ayarları (Uçan Kartlar)')
                    ->description('Boş bırakırsanız sistem rastgele aktif ürünleri getirecektir.')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('banner_product_1_id')
                                ->label('1. Kart Ürünü (Ana Büyük)')
                                ->options(Product::where('status', true)->pluck('name', 'id'))
                                ->searchable(),
                            Select::make('banner_product_2_id')
                                ->label('2. Kart Ürünü (Sağ Üst)')
                                ->options(Product::where('status', true)->pluck('name', 'id'))
                                ->searchable(),
                            Select::make('banner_product_3_id')
                                ->label('3. Kart Ürünü (Sol Alt)')
                                ->options(Product::where('status', true)->pluck('name', 'id'))
                                ->searchable(),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        \Illuminate\Support\Facades\Cache::forget('hero_settings');

        Notification::make()
            ->title('Başarılı')
            ->body('Banner ayarları başarıyla kaydedildi.')
            ->success()
            ->send();
    }
}

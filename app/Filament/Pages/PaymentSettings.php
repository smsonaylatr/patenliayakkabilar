<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class PaymentSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationLabel(): string
    {
        return 'Ödeme Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public static function getNavigationSort(): ?int
    {
        return 35;
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Ödeme Yöntemleri Ayarları';
    }

    protected string $view = 'filament.pages.payment-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'cod_enabled' => Setting::getValue('cod_enabled', true),
            'cod_extra_fee' => Setting::getValue('cod_extra_fee', '200'),
            'wire_transfer_enabled' => Setting::getValue('wire_transfer_enabled', true),
            'credit_card_enabled' => Setting::getValue('credit_card_enabled', true),
        ]);
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Section::make('Kapıda Ödeme')
                    ->description('Kapıda nakit veya kartla ödeme seçeneğini buradan yönetebilirsiniz.')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        Toggle::make('data.cod_enabled')
                            ->label('Kapıda Ödeme Aktif')
                            ->helperText('Bu seçenek kapatıldığında müşteriler checkout sayfasında kapıda ödeme seçeneğini göremez. Ürün bazlı kapıda ödeme ayarı da bu genel ayara bağlıdır.')
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->onColor('success')
                            ->offColor('danger'),
                        TextInput::make('data.cod_extra_fee')
                            ->label('Kapıda Ödeme Ek Ücreti')
                            ->numeric()
                            ->prefix('₺')
                            ->helperText('Kapıda ödeme tercih edildiğinde kargo ücretine eklenen sabit ek ücret.')
                            ->placeholder('200'),
                    ])
                    ->columns(2),

                Section::make('Kredi Kartı / Banka Kartı')
                    ->description('Online kredi kartı ile ödeme seçeneği.')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Toggle::make('data.credit_card_enabled')
                            ->label('Kredi Kartı Ödeme Aktif')
                            ->helperText('PayTR / iyzico üzerinden kredi kartı ile online ödeme.')
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),

                Section::make('Havale / EFT')
                    ->description('Banka havalesi ile ödeme seçeneği.')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Toggle::make('data.wire_transfer_enabled')
                            ->label('Havale / EFT Aktif')
                            ->helperText('Müşterilerin banka havalesi ile ödeme yapmasına izin ver.')
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState()['data'];

        // En az bir ödeme yöntemi açık olmalı
        $enabledCount = collect(['cod_enabled', 'credit_card_enabled', 'wire_transfer_enabled'])
            ->filter(fn ($key) => !empty($data[$key]))
            ->count();

        if ($enabledCount === 0) {
            Notification::make()
                ->title('En az bir ödeme yöntemi aktif olmalıdır!')
                ->danger()
                ->send();
            return;
        }

        Setting::setValue('cod_enabled', $data['cod_enabled'] ? '1' : '0', 'boolean', 'payment');
        Setting::setValue('cod_extra_fee', $data['cod_extra_fee'] ?? '200', 'string', 'payment');
        Setting::setValue('wire_transfer_enabled', $data['wire_transfer_enabled'] ? '1' : '0', 'boolean', 'payment');
        Setting::setValue('credit_card_enabled', $data['credit_card_enabled'] ? '1' : '0', 'boolean', 'payment');

        Notification::make()
            ->title('Ödeme ayarları kaydedildi')
            ->body('Değişiklikler hemen uygulandı.')
            ->success()
            ->send();
    }
}

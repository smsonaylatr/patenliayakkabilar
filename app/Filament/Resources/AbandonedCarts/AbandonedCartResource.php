<?php

namespace App\Filament\Resources\AbandonedCarts;

use App\Filament\Resources\AbandonedCarts\Pages\ListAbandonedCarts;
use App\Filament\Resources\AbandonedCarts\Tables\AbandonedCartsTable;
use App\Models\Cart;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AbandonedCartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static ?string $slug = 'sepeti-terk-edenler';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static string|\UnitEnum|null $navigationGroup = 'Müşteriler';
    protected static ?string $modelLabel = 'Terk Edilmiş Sepet';
    protected static ?string $pluralModelLabel = 'Sepeti Terk Edenler';

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'user.email', 'user.phone'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Müşteri Bilgileri')
                    ->schema([
                        Placeholder::make('user.name')
                            ->label('Müşteri Adı')
                            ->content(fn ($record) => $record->user?->name ?? '-'),
                        Placeholder::make('user.email')
                            ->label('E-posta')
                            ->content(fn ($record) => $record->user?->email ?? '-'),
                        Placeholder::make('user.phone')
                            ->label('Telefon')
                            ->content(fn ($record) => $record->user?->phone ?? '-'),
                        Placeholder::make('updated_at')
                            ->label('Son İşlem Tarihi')
                            ->content(fn ($record) => $record->updated_at?->format('d.m.Y H:i') ?? '-'),
                    ])
                    ->columns(2),

                Section::make('Sepetteki Ürünler')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Placeholder::make('product.name')
                                    ->label('Ürün')
                                    ->content(fn ($record) => $record->product?->name ?? '-'),
                                Placeholder::make('variant')
                                    ->label('Varyant')
                                    ->content(fn ($record) => $record->variant ? ($record->variant->color . ' - ' . $record->variant->size) : '-'),
                                Placeholder::make('quantity')
                                    ->label('Adet')
                                    ->content(fn ($record) => $record->quantity),
                                Placeholder::make('price')
                                    ->label('Birim Fiyat')
                                    ->content(fn ($record) => number_format($record->product?->discount_price ?? $record->product?->price ?? 0, 2) . ' ₺'),
                            ])
                            ->columns(4)
                            ->disableItemCreation()
                            ->disableItemDeletion()
                            ->disableItemMovement()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return AbandonedCartsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbandonedCarts::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

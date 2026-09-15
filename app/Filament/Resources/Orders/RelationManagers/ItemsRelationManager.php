<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Sipariş Kalemleri';
    protected static ?string $modelLabel = 'Kalem';
    protected static ?string $pluralModelLabel = 'Kalemler';

    public function schema(Schema $schema): Schema
    {
        return $schema->components($this->getFormComponents());
    }

    /**
     * Form bileşenlerini döndür — hem schema() hem action'lar tarafından kullanılır
     */
    protected function getFormComponents(): array
    {
        return [
            Select::make('product_id')
                ->label('Ürün')
                ->options(function () {
                    return Product::query()
                        ->where('status', true)
                        ->orderBy('name')
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->preload()
                ->required()
                ->native(false)
                ->live()
                ->afterStateUpdated(function (Set $set, ?string $state) {
                    $set('product_variant_id', null);
                    $set('variant_info', null);

                    if (!$state) {
                        $set('unit_price', 0);
                        $set('product_name', '');
                        return;
                    }

                    $product = Product::find($state);
                    if (!$product) return;

                    $set('product_name', $product->name);

                    if ($product->variants()->count() === 0) {
                        $price = $product->discount_price ?: $product->price;
                        $set('unit_price', $price);
                    }
                }),

            Select::make('product_variant_id')
                ->label('Varyant (Numara / Renk)')
                ->options(function (Get $get) {
                    $productId = $get('product_id');
                    if (!$productId) return [];

                    $currentVariantId = $get('product_variant_id');

                    return ProductVariant::where('product_id', $productId)
                        ->where(function ($query) use ($currentVariantId) {
                            $query->where('stock', '>', 0);
                            if ($currentVariantId) {
                                $query->orWhere('id', $currentVariantId);
                            }
                        })
                        ->get()
                        ->mapWithKeys(function (ProductVariant $variant) {
                            $colors = is_array($variant->color)
                                ? implode('/', $variant->color)
                                : ($variant->color ?? '');
                            $label = "Numara: {$variant->size}";
                            if ($colors) {
                                $label .= " | Renk: {$colors}";
                            }
                            $label .= " | Stok: {$variant->stock}";
                            $price = $variant->discount_price ?: $variant->price;
                            if ($price) {
                                $label .= " | " . number_format($price, 2) . " ₺";
                            }
                            return [$variant->id => $label];
                        });
                })
                ->searchable()
                ->native(false)
                ->live()
                ->afterStateUpdated(function (Set $set, ?string $state) {
                    if (!$state) return;

                    $variant = ProductVariant::find($state);
                    if (!$variant) return;

                    $price = $variant->discount_price ?: $variant->price;
                    $set('unit_price', $price);

                    $colors = is_array($variant->color)
                        ? implode('/', $variant->color)
                        : ($variant->color ?? '');
                    $info = "Numara: {$variant->size}";
                    if ($colors) {
                        $info .= " | Renk: {$colors}";
                    }
                    $set('variant_info', $info);
                }),

            TextInput::make('quantity')
                ->label('Adet')
                ->numeric()
                ->required()
                ->default(1)
                ->minValue(1)
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, Get $get) {
                    $qty = (int) ($get('quantity') ?: 1);
                    $price = (float) ($get('unit_price') ?: 0);
                    $set('total_price', round($qty * $price, 2));
                }),

            TextInput::make('unit_price')
                ->label('Birim Fiyat')
                ->numeric()
                ->required()
                ->prefix('₺')
                ->inputMode('decimal')
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, Get $get) {
                    $qty = (int) ($get('quantity') ?: 1);
                    $price = (float) ($get('unit_price') ?: 0);
                    $set('total_price', round($qty * $price, 2));
                }),

            TextInput::make('total_price')
                ->label('Toplam Tutar')
                ->numeric()
                ->prefix('₺')
                ->disabled()
                ->dehydrated()
                ->inputMode('decimal'),

            Hidden::make('product_name'),
            Hidden::make('variant_info'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('product.images.image_path')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->stacked()
                    ->limit(1)
                    ->defaultImageUrl(fn () => 'https://placehold.co/40x40/f1f5f9/94a3b8?text=Görsel')
                    ->width(40)
                    ->height(40),
                TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->weight('bold')
                    ->limit(40),
                TextColumn::make('sku')
                    ->label('Stok Kodu (SKU)')
                    ->getStateUsing(fn ($record) => $record->variant?->sku ?: ($record->product?->sku ?: '-'))
                    ->copyable()
                    ->badge()
                    ->color('warning'),
                TextColumn::make('variant.color')
                    ->label('Renk')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => is_array($state) ? implode(' / ', $state) : ($state ?? '-'))
                    ->default('-'),
                TextColumn::make('variant.size')
                    ->label('Numara')
                    ->default('-'),
                TextColumn::make('variant.stock')
                    ->label('Kalan Stok')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === null || $state === '-' => 'gray',
                        (int) $state <= 0 => 'danger',
                        (int) $state <= 5 => 'warning',
                        default => 'success',
                    })
                    ->default('-'),
                TextColumn::make('quantity')
                    ->label('Adet')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('unit_price')
                    ->label('Birim Fiyat')
                    ->getStateUsing(fn ($record) => number_format($record->unit_price, 2) . ' ₺')
                    ->alignEnd(),
                TextColumn::make('total_price')
                    ->label('Toplam')
                    ->getStateUsing(fn ($record) => number_format($record->quantity * $record->unit_price, 2) . ' ₺')
                    ->weight('bold')
                    ->alignEnd(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Kalem Ekle')
                    ->icon('heroicon-o-plus-circle')
                    ->schema($this->getFormComponents())
                    ->mutateFormDataUsing(function (array $data): array {
                        $qty = (int) ($data['quantity'] ?? 1);
                        $unitPrice = (float) ($data['unit_price'] ?? 0);

                        // Stok kontrolü
                        if (!empty($data['product_variant_id'])) {
                            $variant = ProductVariant::find($data['product_variant_id']);
                            if ($variant && $variant->stock < $qty) {
                                Notification::make()
                                    ->title('Stok Yetersiz')
                                    ->body("Bu varyant için sadece {$variant->stock} adet stok mevcut.")
                                    ->danger()
                                    ->send();
                                throw new \Illuminate\Validation\ValidationException(
                                    validator: validator([], []),
                                );
                            }
                        } elseif (!empty($data['product_id'])) {
                            $product = Product::find($data['product_id']);
                            if ($product && $product->stock < $qty) {
                                Notification::make()
                                    ->title('Stok Yetersiz')
                                    ->body("Bu ürün için sadece {$product->stock} adet stok mevcut.")
                                    ->danger()
                                    ->send();
                                throw new \Illuminate\Validation\ValidationException(
                                    validator: validator([], []),
                                );
                            }
                        }

                        // product_name ve variant_info doldur
                        if (empty($data['product_name']) && !empty($data['product_id'])) {
                            $product = Product::find($data['product_id']);
                            $data['product_name'] = $product?->name ?? '';
                        }
                        if (empty($data['variant_info']) && !empty($data['product_variant_id'])) {
                            $variant = ProductVariant::find($data['product_variant_id']);
                            if ($variant) {
                                $colors = is_array($variant->color) ? implode('/', $variant->color) : ($variant->color ?? '');
                                $data['variant_info'] = "Numara: {$variant->size}" . ($colors ? " | Renk: {$colors}" : '');
                            }
                        }
                        $data['total_price'] = round($qty * $unitPrice, 2);
                        return $data;
                    })
                    ->after(function () {
                        Notification::make()
                            ->title('Kalem Eklendi')
                            ->body('Sipariş kalemi başarıyla eklendi. Stok ve tutarlar güncellendi.')
                            ->success()
                            ->send();

                        // Sayfayı yenileyerek güncel tutarları göster
                        redirect(request()->header('Referer'));
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->label('Düzenle')
                    ->schema($this->getFormComponents())
                    ->mutateFormDataUsing(function (array $data, $record): array {
                        $newQty = (int) ($data['quantity'] ?? 1);
                        $unitPrice = (float) ($data['unit_price'] ?? 0);
                        $oldQty = (int) ($record?->quantity ?? 0);
                        $diff = $newQty - $oldQty;

                        // Miktar artıyorsa stok kontrolü
                        if ($diff > 0) {
                            if (!empty($data['product_variant_id'])) {
                                $variant = ProductVariant::find($data['product_variant_id']);
                                if ($variant && $variant->stock < $diff) {
                                    Notification::make()
                                        ->title('Stok Yetersiz')
                                        ->body("Ek {$diff} adet için stok yetersiz. Mevcut stok: {$variant->stock}")
                                        ->danger()
                                        ->send();
                                    throw new \Illuminate\Validation\ValidationException(
                                        validator: validator([], []),
                                    );
                                }
                            } elseif (!empty($data['product_id'])) {
                                $product = Product::find($data['product_id']);
                                if ($product && $product->stock < $diff) {
                                    Notification::make()
                                        ->title('Stok Yetersiz')
                                        ->body("Ek {$diff} adet için stok yetersiz. Mevcut stok: {$product->stock}")
                                        ->danger()
                                        ->send();
                                    throw new \Illuminate\Validation\ValidationException(
                                        validator: validator([], []),
                                    );
                                }
                            }
                        }

                        $data['total_price'] = round($newQty * $unitPrice, 2);
                        if (empty($data['product_name']) && !empty($data['product_id'])) {
                            $data['product_name'] = Product::find($data['product_id'])?->name ?? '';
                        }
                        return $data;
                    })
                    ->after(function () {
                        Notification::make()
                            ->title('Kalem Güncellendi')
                            ->body('Sipariş kalemi, stok ve tutarlar güncellendi.')
                            ->success()
                            ->send();

                        redirect(request()->header('Referer'));
                    }),
                DeleteAction::make()
                    ->label('Sil')
                    ->after(function () {
                        Notification::make()
                            ->title('Kalem Silindi')
                            ->body('Sipariş kalemi silindi. Stok geri yüklendi ve tutarlar güncellendi.')
                            ->success()
                            ->send();

                        redirect(request()->header('Referer'));
                    }),
            ])
            ->paginated(false);
    }

    /**
     * Seçili ürün/varyant için maksimum sipariş edilebilir adet
     */
    private function getMaxQuantity(Get $get): int
    {
        $variantId = $get('product_variant_id');
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            return $variant ? max($variant->stock, 1) : 999;
        }

        $productId = $get('product_id');
        if ($productId) {
            $product = Product::find($productId);
            return $product ? max($product->stock, 1) : 999;
        }

        return 999;
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}

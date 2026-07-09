<?php

namespace App\Filament\Resources\Campaigns;

use App\Filament\Resources\Campaigns\Pages\CreateCampaign;
use App\Filament\Resources\Campaigns\Pages\EditCampaign;
use App\Filament\Resources\Campaigns\Pages\ListCampaigns;
use App\Filament\Resources\Campaigns\Schemas\CampaignForm;
use App\Filament\Resources\Campaigns\Tables\CampaignsTable;
use App\Models\Campaign;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Pazarlama';
    protected static ?int $navigationSort = 3;

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Kampanya Bilgileri')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Kampanya Adı')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\Select::make('customer_segment_id')
                            ->label('Hedef Segment')
                            ->relationship('customerSegment', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        \Filament\Forms\Components\Select::make('channel')
                            ->label('İletişim Kanalı')
                            ->options([
                                'email' => 'E-posta',
                                'sms' => 'SMS',
                            ])
                            ->required()
                            ->default('email')
                            ->native(false),
                        \Filament\Forms\Components\TextInput::make('subject')
                            ->label('Konu (Sadece E-posta)')
                            ->maxLength(255),
                        \Filament\Forms\Components\Textarea::make('message_template')
                            ->label('Mesaj Şablonu')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
                \Filament\Schemas\Components\Section::make('Zamanlama ve Durum')
                    ->schema([
                        \Filament\Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'draft' => 'Taslak',
                                'scheduled' => 'Zamanlandı',
                                'running' => 'Çalışıyor',
                                'completed' => 'Tamamlandı',
                            ])
                            ->required()
                            ->default('draft')
                            ->native(false),
                        \Filament\Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Zamanlanmış Tarih')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Kampanya Adı')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('customerSegment.name')
                    ->label('Segment')
                    ->badge()
                    ->color('info'),
                \Filament\Tables\Columns\TextColumn::make('channel')
                    ->label('Kanal')
                    ->badge(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'running' => 'info',
                        'completed' => 'success',
                        default => 'gray',
                    }),
                \Filament\Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'edit' => EditCampaign::route('/{record}/edit'),
        ];
    }
}

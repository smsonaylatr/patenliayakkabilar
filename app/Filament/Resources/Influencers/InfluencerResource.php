<?php

namespace App\Filament\Resources\Influencers;

use App\Models\Influencer;
use App\Models\InfluencerCampaign;
use App\Services\InfluencerMarketingAIService;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use BackedEnum;

class InfluencerResource extends Resource
{
    protected static ?string $model = Influencer::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';
    protected static string|\UnitEnum|null $navigationGroup = 'Pazarlama';
    protected static ?string $navigationLabel = 'Influencer Yönetimi';
    protected static ?string $modelLabel = 'Influencer';
    protected static ?string $pluralModelLabel = 'Influencer\'lar';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Kanal Bilgileri')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('channel_name')
                        ->label('Kanal Adı')
                        ->required()
                        ->maxLength(255),
                    \Filament\Forms\Components\TextInput::make('channel_url')
                        ->label('Kanal URL')
                        ->url()
                        ->maxLength(500),
                    \Filament\Forms\Components\Select::make('platform')
                        ->label('Platform')
                        ->options([
                            'youtube' => 'YouTube',
                            'instagram' => 'Instagram',
                            'tiktok' => 'TikTok',
                        ])
                        ->default('youtube')
                        ->native(false),
                    \Filament\Forms\Components\TextInput::make('subscriber_count')
                        ->label('Abone Sayısı')
                        ->numeric(),
                    \Filament\Forms\Components\TextInput::make('avg_views')
                        ->label('Ort. İzlenme')
                        ->numeric(),
                    \Filament\Forms\Components\TextInput::make('engagement_rate')
                        ->label('Etkileşim Oranı (%)')
                        ->numeric()
                        ->step(0.01)
                        ->suffix('%'),
                    \Filament\Forms\Components\Select::make('category')
                        ->label('Kategori')
                        ->options([
                            'kids_vlog' => 'Çocuk Vlog',
                            'toy_review' => 'Oyuncak İnceleme',
                            'gaming' => 'Oyun',
                            'education' => 'Eğitim',
                            'family' => 'Aile',
                            'challenge' => 'Challenge',
                            'unboxing' => 'Kutu Açılımı',
                            'other' => 'Diğer',
                        ])
                        ->default('kids_vlog')
                        ->native(false),
                    \Filament\Forms\Components\Select::make('tier')
                        ->label('Seviye')
                        ->options([
                            'nano' => 'Nano (1K-5K)',
                            'micro' => 'Mikro (5K-50K)',
                            'mid' => 'Orta (50K-500K)',
                            'macro' => 'Makro (500K-1M)',
                            'mega' => 'Mega (1M+)',
                        ])
                        ->default('micro')
                        ->native(false),
                ])->columns(2),

            \Filament\Schemas\Components\Section::make('İletişim Bilgileri')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('contact_email')
                        ->label('E-posta')
                        ->email(),
                    \Filament\Forms\Components\TextInput::make('contact_phone')
                        ->label('Telefon')
                        ->tel(),
                    \Filament\Forms\Components\TextInput::make('contact_instagram')
                        ->label('Instagram')
                        ->prefix('@'),
                    \Filament\Forms\Components\TextInput::make('parent_name')
                        ->label('Ebeveyn Adı'),
                    \Filament\Forms\Components\TextInput::make('child_name')
                        ->label('Çocuk Adı'),
                    \Filament\Forms\Components\TextInput::make('child_age')
                        ->label('Çocuk Yaşı')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(18),
                ])->columns(2),

            \Filament\Schemas\Components\Section::make('Değerlendirme')
                ->schema([
                    \Filament\Forms\Components\Select::make('status')
                        ->label('Durum')
                        ->options([
                            'discovered' => 'Keşfedildi',
                            'contacted' => 'İletişime Geçildi',
                            'negotiating' => 'Müzakere',
                            'agreed' => 'Anlaşıldı',
                            'active' => 'Aktif',
                            'paused' => 'Duraklatıldı',
                            'rejected' => 'Reddedildi',
                        ])
                        ->default('discovered')
                        ->native(false),
                    \Filament\Forms\Components\TextInput::make('fit_score')
                        ->label('Uyum Skoru (0-100)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->default(0),
                    \Filament\Forms\Components\TextInput::make('affiliate_code')
                        ->label('Affiliate Kodu')
                        ->unique(ignoreRecord: true),
                    \Filament\Forms\Components\TextInput::make('commission_rate')
                        ->label('Komisyon Oranı')
                        ->numeric()
                        ->step(0.01)
                        ->suffix('%'),
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Notlar')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('channel_name')
                    ->label('Kanal Adı')
                    ->searchable()
                    ->weight('bold')
                    ->icon('heroicon-o-play-circle'),
                \Filament\Tables\Columns\TextColumn::make('tier')
                    ->label('Seviye')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nano' => 'gray',
                        'micro' => 'info',
                        'mid' => 'warning',
                        'macro' => 'success',
                        'mega' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'nano' => 'Nano',
                        'micro' => 'Mikro',
                        'mid' => 'Orta',
                        'macro' => 'Makro',
                        'mega' => 'Mega',
                        default => $state,
                    }),
                \Filament\Tables\Columns\TextColumn::make('subscriber_count')
                    ->label('Abone')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        if ($state >= 1000000) return number_format($state / 1000000, 1) . 'M';
                        if ($state >= 1000) return number_format($state / 1000, 1) . 'K';
                        return $state;
                    })
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('fit_score')
                    ->label('Uyum')
                    ->suffix('/100')
                    ->color(fn (int $state): string => match(true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'discovered' => 'gray',
                        'contacted' => 'info',
                        'negotiating' => 'warning',
                        'agreed' => 'success',
                        'active' => 'success',
                        'paused' => 'gray',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'discovered' => 'Keşfedildi',
                        'contacted' => 'İletişim',
                        'negotiating' => 'Müzakere',
                        'agreed' => 'Anlaşıldı',
                        'active' => 'Aktif',
                        'paused' => 'Durduruldu',
                        'rejected' => 'Reddedildi',
                        default => $state,
                    }),
                \Filament\Tables\Columns\TextColumn::make('campaigns_count')
                    ->label('Kampanya')
                    ->counts('campaigns')
                    ->badge()
                    ->color('primary'),
                \Filament\Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Gelir')
                    ->getStateUsing(fn ($record) => number_format($record->total_revenue, 2) . ' ₺')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Eklenme')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fit_score', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('tier')
                    ->label('Seviye')
                    ->options([
                        'nano' => 'Nano',
                        'micro' => 'Mikro',
                        'mid' => 'Orta',
                        'macro' => 'Makro',
                        'mega' => 'Mega',
                    ])
                    ->native(false),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'discovered' => 'Keşfedildi',
                        'contacted' => 'İletişime Geçildi',
                        'negotiating' => 'Müzakere',
                        'agreed' => 'Anlaşıldı',
                        'active' => 'Aktif',
                        'paused' => 'Duraklatıldı',
                        'rejected' => 'Reddedildi',
                    ])
                    ->native(false),
                \Filament\Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'kids_vlog' => 'Çocuk Vlog',
                        'toy_review' => 'Oyuncak İnceleme',
                        'gaming' => 'Oyun',
                        'education' => 'Eğitim',
                        'family' => 'Aile',
                        'challenge' => 'Challenge',
                        'unboxing' => 'Kutu Açılımı',
                    ])
                    ->native(false),
            ])
            ->actions([
                \Filament\Tables\Actions\ActionGroup::make([
                    \Filament\Tables\Actions\Action::make('ai_proposal')
                        ->label('AI Teklif Oluştur')
                        ->icon('heroicon-o-sparkles')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('AI ile Teklif Oluştur')
                        ->modalDescription(fn (Influencer $record) => "{$record->channel_name} kanalı için AI destekli kişisel teklif oluşturulacak.")
                        ->action(function (Influencer $record) {
                            $service = app(InfluencerMarketingAIService::class);
                            $campaign = $service->generateProposal($record);
                            Notification::make()
                                ->title('Teklif oluşturuldu!')
                                ->body("{$record->channel_name} için {$campaign->package_type} paketi hazırlandı.")
                                ->success()
                                ->send();
                        }),
                    \Filament\Tables\Actions\Action::make('send_message')
                        ->label('Mesaj Gönder')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->form([
                            \Filament\Forms\Components\Select::make('channel')
                                ->label('Kanal')
                                ->options([
                                    'email' => 'E-posta',
                                    'dm' => 'DM (Instagram/YouTube)',
                                    'whatsapp' => 'WhatsApp',
                                ])
                                ->default('email')
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Influencer $record, array $data) {
                            $service = app(InfluencerMarketingAIService::class);
                            $log = $service->generateOutreachMessage($record, $data['channel']);
                            $record->update(['status' => 'contacted']);
                            Notification::make()
                                ->title('Mesaj oluşturuldu!')
                                ->body("İletişim kaydı oluşturuldu. Mesajı kopyalayıp gönderebilirsiniz.")
                                ->success()
                                ->send();
                        }),
                    \Filament\Tables\Actions\EditAction::make(),
                    \Filament\Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfluencers::route('/'),
            'create' => Pages\CreateInfluencer::route('/create'),
            'edit' => Pages\EditInfluencer::route('/{record}/edit'),
        ];
    }
}

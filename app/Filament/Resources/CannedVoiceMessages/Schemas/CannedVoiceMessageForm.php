<?php

namespace App\Filament\Resources\CannedVoiceMessages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CannedVoiceMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sesli Anons & Şablon Bilgileri')
                    ->description('Ziyaretçilere iletilecek ses kaydı ve ekranda belirecek kart detayları')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Anons / Şablon Başlığı')
                            ->required()
                            ->placeholder('Örn: 👋 Hoş Geldiniz & İndirim Anonsu')
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->label('Ziyaretçiye Gösterilecek Mesaj Metni')
                            ->rows(3)
                            ->required()
                            ->placeholder('Ziyaretçinin ekranındaki bildirim kartında yer alacak samimi mesaj metni...')
                            ->columnSpanFull(),

                        FileUpload::make('audio_path')
                            ->label('🎙️ Ses Kaydı Dosyası (.mp3, .wav, .m4a, .ogg)')
                            ->disk('public')
                            ->directory('voice-announcements')
                            ->acceptedFileTypes([
                                'audio/mpeg',
                                'audio/mp3',
                                'audio/wav',
                                'audio/x-wav',
                                'audio/x-m4a',
                                'audio/mp4',
                                'audio/ogg',
                                'audio/webm',
                                'audio/aac',
                            ])
                            ->maxSize(25600)
                            ->columnSpanFull()
                            ->helperText('Telefonunuzdan veya mikrofonunuzdan kaydettiğiniz ses dosyasını yükleyin. Ziyaretçi siteyi gezerken bu ses kaydı çalınacaktır.'),
                    ]),

                Section::make('Fırsat Kuponu & Aksiyon Butonu (Opsiyonel)')
                    ->description('Sesli bildirim kartında belirecek kupon ve yönlendirme butonu')
                    ->columns(2)
                    ->schema([
                        TextInput::make('coupon_code')
                            ->label('İndirim Kuponu (Opsiyonel)')
                            ->placeholder('Örn: CANLI10')
                            ->helperText('Tek tıkla kopyalanabilir kupon kodu.'),

                        TextInput::make('action_button')
                            ->label('Buton Metni (Opsiyonel)')
                            ->placeholder('Örn: Modelleri İncele'),

                        TextInput::make('action_url')
                            ->label('Buton Linki (Opsiyonel)')
                            ->placeholder('Örn: /patenli-ayakkabilar')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Aktif Şablon')
                            ->default(true)
                            ->helperText('Canlı Ziyaretçiler anons menüsünde seçilebilir olsun.'),

                        TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(1),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\CustomerEvent;
use App\Models\CustomerNote;
use App\Services\CustomerScoreService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = 'Müşteri Profili';

    public function infolist(Schema $schema): Schema
    {
        $user = $this->record;
        $score = $user->score;
        $orders = $user->orders()->latest()->limit(5)->get();
        $totalSpent = (float) $user->orders()->sum('grand_total');
        $orderCount = $user->orders()->count();
        $avgOrder = $orderCount > 0 ? $totalSpent / $orderCount : 0;
        $lastOrder = $user->orders()->latest()->first();

        return $schema->components([
            // Skor Kartları
            Section::make('Müşteri Skorları')
                ->icon('heroicon-o-chart-bar')
                ->schema([
                    Grid::make(5)->schema([
                        Group::make([
                            TextEntry::make('score.purchase_score')
                                ->label('Satın Alma')
                                ->badge()
                                ->color(fn () => $this->scoreColor($score?->purchase_score ?? 0))
                                ->default($score?->purchase_score ?? 0),
                        ]),
                        Group::make([
                            TextEntry::make('score.activity_score')
                                ->label('Aktivite')
                                ->badge()
                                ->color(fn () => $this->scoreColor($score?->activity_score ?? 0))
                                ->default($score?->activity_score ?? 0),
                        ]),
                        Group::make([
                            TextEntry::make('score.loyalty_score')
                                ->label('Sadakat')
                                ->badge()
                                ->color(fn () => $this->scoreColor($score?->loyalty_score ?? 0))
                                ->default($score?->loyalty_score ?? 0),
                        ]),
                        Group::make([
                            TextEntry::make('score.engagement_score')
                                ->label('Etkileşim')
                                ->badge()
                                ->color(fn () => $this->scoreColor($score?->engagement_score ?? 0))
                                ->default($score?->engagement_score ?? 0),
                        ]),
                        Group::make([
                            TextEntry::make('score.risk_score')
                                ->label('Kayıp Riski')
                                ->badge()
                                ->color(fn () => $this->riskColor($score?->risk_score ?? 0))
                                ->default($score?->risk_score ?? 0),
                        ]),
                    ]),
                ])
                ->collapsible(),

            // Temel Bilgiler + Finansal
            Grid::make(2)->schema([
                Section::make('Temel Bilgiler')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('name')->label('Ad Soyad'),
                        TextEntry::make('email')->label('E-posta'),
                        TextEntry::make('phone')->label('Telefon')->default('-'),
                        TextEntry::make('role')->label('Rol')->badge()
                            ->color(fn (string $state) => $state === 'admin' ? 'danger' : 'info'),
                        TextEntry::make('created_at')->label('Kayıt Tarihi')->dateTime('d.m.Y H:i'),
                        TextEntry::make('email_verified_at')->label('E-posta Doğrulama')
                            ->dateTime('d.m.Y H:i')->default('Doğrulanmadı'),
                    ])->columns(2),

                Section::make('Finansal Özet')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        TextEntry::make('total_spent_display')
                            ->label('Toplam Harcama')
                            ->getStateUsing(fn () => number_format($totalSpent, 2) . ' ₺')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('order_count_display')
                            ->label('Sipariş Sayısı')
                            ->getStateUsing(fn () => $orderCount),
                        TextEntry::make('avg_order_display')
                            ->label('Ort. Sipariş Tutarı')
                            ->getStateUsing(fn () => number_format($avgOrder, 2) . ' ₺'),
                        TextEntry::make('ltv_display')
                            ->label('Yaşam Boyu Değer')
                            ->getStateUsing(fn () => number_format($score?->lifetime_value ?? $totalSpent, 2) . ' ₺')
                            ->weight('bold'),
                        TextEntry::make('tier_display')
                            ->label('Müşteri Segmenti')
                            ->getStateUsing(fn () => $score?->tier ?? 'Yeni')
                            ->badge()
                            ->color(fn () => $score?->tier_color ?? 'gray'),
                        TextEntry::make('last_order_display')
                            ->label('Son Sipariş')
                            ->getStateUsing(fn () => $lastOrder ? $lastOrder->created_at->diffForHumans() : 'Henüz sipariş yok'),
                    ])->columns(2),
            ]),

            // Segmentler
            Section::make('Müşteri Segmentleri')
                ->icon('heroicon-o-tag')
                ->schema([
                    TextEntry::make('segments_display')
                        ->label('')
                        ->getStateUsing(function () use ($user) {
                            $segments = $user->segments;
                            if ($segments->isEmpty()) return 'Henüz segmente eklenmemiş';
                            return $segments->pluck('name')->join(', ');
                        })
                        ->badge()
                        ->separator(','),
                ])
                ->collapsible()
                ->collapsed(),

            // İç Notlar
            Section::make('İç Notlar')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->schema([
                    TextEntry::make('notes_display')
                        ->label('')
                        ->getStateUsing(function () use ($user) {
                            $notes = $user->notes()->with('admin')->limit(10)->get();
                            if ($notes->isEmpty()) return 'Henüz not eklenmemiş';
                            return $notes->map(function ($note) {
                                $admin = $note->admin?->name ?? 'Sistem';
                                $date = $note->created_at->format('d.m.Y H:i');
                                return "[{$date}] {$admin}: {$note->note}";
                            })->join("\n");
                        }),
                ])
                ->collapsible()
                ->collapsed(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('recalculate_score')
                ->label('Skoru Yeniden Hesapla')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function () {
                    $service = app(CustomerScoreService::class);
                    $service->calculateForUser($this->record);
                    Notification::make()->title('Skorlar güncellendi')->success()->send();
                }),

            Action::make('add_note')
                ->label('Not Ekle')
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->form([
                    Textarea::make('note')->label('Not')->required(),
                    Select::make('type')->label('Tür')->options([
                        'info' => 'Bilgi',
                        'warning' => 'Uyarı',
                        'important' => 'Önemli',
                    ])->default('info'),
                ])
                ->action(function (array $data) {
                    CustomerNote::create([
                        'user_id' => $this->record->id,
                        'admin_id' => auth()->id(),
                        'note' => $data['note'],
                        'type' => $data['type'],
                    ]);
                    Notification::make()->title('Not eklendi')->success()->send();
                }),
        ];
    }

    private function scoreColor(int $score): string
    {
        return match (true) {
            $score >= 70 => 'success',
            $score >= 40 => 'warning',
            default => 'danger',
        };
    }

    private function riskColor(int $risk): string
    {
        return match (true) {
            $risk >= 70 => 'danger',
            $risk >= 40 => 'warning',
            default => 'success',
        };
    }
}

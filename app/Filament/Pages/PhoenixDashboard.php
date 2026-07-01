<?php

namespace App\Filament\Pages;

use App\Models\AiRecommendation;
use App\Services\PhoenixAIService;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class PhoenixDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    protected static string|\UnitEnum|null $navigationGroup = 'Müşteri İstihbaratı';
    protected static ?string $navigationLabel = 'Phoenix AI (Beta)';
    protected static ?string $title = 'Phoenix AI Marketing Assistant';
    protected static ?string $slug = 'phoenix-ai';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.phoenix-dashboard';

    public Collection $recommendations;

    public function mount(): void
    {
        $this->loadRecommendations();
    }

    public function loadRecommendations(): void
    {
        $this->recommendations = AiRecommendation::with('user')
            ->where('status', 'pending')
            ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function dismissRecommendation($id): void
    {
        $rec = AiRecommendation::find($id);
        if ($rec) {
            $rec->update(['status' => 'dismissed']);
            $this->loadRecommendations();
            Notification::make()
                ->title('Öneri reddedildi')
                ->success()
                ->send();
        }
    }

    public function completeRecommendation($id): void
    {
        $rec = AiRecommendation::find($id);
        if ($rec) {
            $rec->update(['status' => 'completed']);
            $this->loadRecommendations();
            Notification::make()
                ->title('Aksiyon alındı ve öneri tamamlandı')
                ->success()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_now')
                ->label('Sistemi Analiz Et (AI)')
                ->icon('heroicon-o-cpu-chip')
                ->action(function () {
                    $service = app(PhoenixAIService::class);
                    $count = $service->generateRecommendations();
                    
                    $this->loadRecommendations();
                    
                    if ($count > 0) {
                        Notification::make()
                            ->title("Analiz tamamlandı. {$count} yeni öneri bulundu.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Analiz tamamlandı. Sistem stabil, yeni öneri yok.')
                            ->info()
                            ->send();
                    }
                })
        ];
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Influencer;
use App\Models\InfluencerCampaign;
use App\Services\InfluencerMarketingAIService;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class InfluencerMarketing extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationGroup = 'Pazarlama';
    protected static ?string $navigationLabel = 'Pazarlama Merkezi';
    protected static ?string $title = 'YouTube Influencer Pazarlama Merkezi';
    protected static ?string $slug = 'influencer-marketing';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.influencer-marketing';

    public array $stats = [];
    public array $tasks = [];
    public array $recentCampaigns = [];
    public ?string $lastAiResult = null;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->stats = [
            'total_influencers' => Influencer::count(),
            'active_influencers' => Influencer::where('status', 'active')->count(),
            'contacted' => Influencer::where('status', 'contacted')->count(),
            'total_campaigns' => InfluencerCampaign::count(),
            'active_campaigns' => InfluencerCampaign::whereIn('status', ['sent', 'accepted', 'in_progress'])->count(),
            'completed_campaigns' => InfluencerCampaign::where('status', 'completed')->count(),
            'total_revenue' => InfluencerCampaign::sum('revenue_generated'),
            'total_views' => InfluencerCampaign::sum('total_views'),
            'total_videos' => InfluencerCampaign::sum('delivered_videos'),
            'avg_roi' => InfluencerCampaign::where('status', 'completed')->avg('roi') ?? 0,
        ];

        $service = app(InfluencerMarketingAIService::class);
        $this->tasks = $service->getTaskStatus();

        $this->recentCampaigns = InfluencerCampaign::with('influencer')
            ->latest()
            ->take(5)
            ->get()
            ->toArray();
    }

    public function discoverChannels(): void
    {
        $service = app(InfluencerMarketingAIService::class);
        $count = $service->discoverChannels();
        $this->loadData();

        Notification::make()
            ->title("AI Araştırma Tamamlandı")
            ->body("{$count} yeni kanal keşfedildi ve sisteme eklendi.")
            ->success()
            ->send();
    }

    public function generateBulkProposals(): void
    {
        $service = app(InfluencerMarketingAIService::class);
        $influencers = Influencer::where('status', 'discovered')
            ->where('fit_score', '>=', 60)
            ->doesntHave('campaigns')
            ->take(5)
            ->get();

        $count = 0;
        foreach ($influencers as $influencer) {
            $service->generateProposal($influencer);
            $count++;
        }

        $this->loadData();

        Notification::make()
            ->title("Toplu Teklif Oluşturuldu")
            ->body("{$count} influencer için AI destekli teklifler hazırlandı.")
            ->success()
            ->send();
    }

    public function runPerformanceAnalysis(): void
    {
        $service = app(InfluencerMarketingAIService::class);
        $result = $service->analyzePerformance();
        $this->lastAiResult = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->loadData();

        Notification::make()
            ->title('Performans Analizi Tamamlandı')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('discover_channels')
                ->label('AI ile Kanal Ara')
                ->icon('heroicon-o-magnifying-glass')
                ->color('info')
                ->action('discoverChannels'),
            Action::make('bulk_proposals')
                ->label('Toplu Teklif Oluştur')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->action('generateBulkProposals'),
            Action::make('performance')
                ->label('Performans Raporu')
                ->icon('heroicon-o-chart-bar')
                ->color('success')
                ->action('runPerformanceAnalysis'),
        ];
    }
}

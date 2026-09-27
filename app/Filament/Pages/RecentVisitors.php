<?php

namespace App\Filament\Pages;

use App\Models\ActiveVisitor;

class RecentVisitors extends ActiveVisitors
{
    protected static ?string $slug = 'son-ziyaret-edenler';

    protected static ?int $navigationSort = 2;

    public string $activeTab = 'recent';

    public function mount(): void
    {
        parent::mount();
        $this->activeTab = 'recent';
    }

    public static function getNavigationLabel(): string
    {
        return 'Son Ziyaret Edenler';
    }

    public function getTitle(): string
    {
        return 'Son Ziyaret Edenler (Ayrılanlar)';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-clock';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Müşteriler';
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('active_visitors')) {
                return null;
            }
            $count = ActiveVisitor::where('last_heartbeat_at', '<', now()->subSeconds(75))
                ->where('last_heartbeat_at', '>=', now()->subHours(24))
                ->where('is_blocked', false)
                ->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

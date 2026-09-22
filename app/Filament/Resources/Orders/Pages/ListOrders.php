<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use App\Models\Order;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni Sipariş'),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        // Porego senkronizasyonu: 120 saniye cache ile harici API yükünü azalt
        Cache::remember('porego_auto_order_sync', 120, function () {
            return app(\App\Services\PoregoApiService::class)->syncOrderStatuses();
        });
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['items.product.images']);
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'valid';
    }

    public function getTabs(): array
    {
        // Tab badge count'larını 60 saniye cache'le — her sayfa açılışında 3 COUNT sorgusu çalışmasın
        $counts = Cache::remember('orders_tab_counts', 60, function () {
            return [
                'valid' => Order::where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere('payment_method', 'cash_on_delivery');
                })->count(),
                'abandoned' => Order::where(function($q) {
                    $q->where('payment_status', '!=', 'paid')
                      ->where('payment_method', '!=', 'cash_on_delivery');
                })->count(),
                'cancelled_returned' => Order::whereIn('status', ['cancelled', 'return_started', 'returned'])->count(),
                'all' => Order::count(),
            ];
        });

        return [
            'valid' => Tab::make('Geçerli Siparişler')
                ->icon('heroicon-m-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere('payment_method', 'cash_on_delivery');
                }))
                ->badge($counts['valid']),
            
            'abandoned' => Tab::make('Yarım Kalan / Başarısız')
                ->icon('heroicon-m-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where(function($q) {
                    $q->where('payment_status', '!=', 'paid')
                      ->where('payment_method', '!=', 'cash_on_delivery');
                }))
                ->badge($counts['abandoned']),

            'cancelled_returned' => Tab::make('İptal / İade')
                ->icon('heroicon-m-arrow-uturn-left')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['cancelled', 'return_started', 'returned']))
                ->badge($counts['cancelled_returned']),
                
            'all' => Tab::make('Tüm Kayıtlar')
                ->icon('heroicon-m-list-bullet')
                ->badge($counts['all']),
        ];
    }
}

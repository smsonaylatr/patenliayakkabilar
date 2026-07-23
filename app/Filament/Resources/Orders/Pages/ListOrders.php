<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
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

    public function getDefaultActiveTab(): string | int | null
    {
        return 'valid';
    }

    public function getTabs(): array
    {
        return [
            'valid' => Tab::make('Geçerli Siparişler')
                ->icon('heroicon-m-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere('payment_method', 'cash_on_delivery');
                }))
                ->badge(Order::where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere('payment_method', 'cash_on_delivery');
                })->count()),
            
            'abandoned' => Tab::make('Yarım Kalan / Başarısız')
                ->icon('heroicon-m-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where(function($q) {
                    $q->where('payment_status', '!=', 'paid')
                      ->where('payment_method', '!=', 'cash_on_delivery');
                }))
                ->badge(Order::where(function($q) {
                    $q->where('payment_status', '!=', 'paid')
                      ->where('payment_method', '!=', 'cash_on_delivery');
                })->count()),
                
            'all' => Tab::make('Tüm Kayıtlar')
                ->icon('heroicon-m-list-bullet')
                ->badge(Order::count()),
        ];
    }
}

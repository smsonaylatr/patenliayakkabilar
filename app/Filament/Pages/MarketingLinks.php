<?php

namespace App\Filament\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MarketingLinks extends Page
{
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.marketing-links';

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-link';
    }

    public static function getNavigationLabel(): string
    {
        return 'Takip Linkleri (UTM)';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Pazarlama & Takip Linkleri (UTM Oluşturucu)';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pazarlama';
    }

    /**
     * View'a gönderilecek veriler (aktif ürünler vb.)
     */
    public function getViewData(): array
    {
        $baseUrl = config('app.url', 'https://patenliayakkabilar.com');
        $baseUrl = rtrim($baseUrl, '/');

        $products = Product::where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'price']);

        return [
            'baseUrl' => $baseUrl,
            'products' => $products,
        ];
    }
}

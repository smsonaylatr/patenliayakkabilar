<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Ürün cache'ini temizle - tüm ürün değişikliklerinde çağrılır
     */
    private function clearProductCache(): void
    {
        Cache::forget('home_product_grid');
        Cache::forget('best_seller_carousel_products');
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->clearProductCache();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearProductCache();

        // Kural: Kritik stok 0 adet -> Ürün otomatik pasif
        if ($product->isDirty('stock') && $product->stock <= 0 && $product->status === true) {
            $product->updateQuietly(['status' => false]);
            
            \Filament\Notifications\Notification::make()
                ->title('Ürün Pasife Alındı')
                ->body("{$product->name} isimli ürünün stoku tükendiği için otomatik olarak pasife alındı.")
                ->icon('heroicon-o-eye-slash')
                ->color('warning')
                ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
        }
    }

    /**
     * Handle the Product "deleted" event (soft delete dahil).
     */
    public function deleted(Product $product): void
    {
        $this->clearProductCache();
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $this->clearProductCache();
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        $this->clearProductCache();
    }
}

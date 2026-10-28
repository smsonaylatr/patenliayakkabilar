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

        // Kural: Stok tükendiyse (stok <= 0) ve ürün daha önce aktif idiyse -> Otomatik pasife al
        if ($product->wasChanged('stock') && (int) $product->stock <= 0 && (bool) $product->status) {
            $product->updateQuietly(['status' => false]);

            \Filament\Notifications\Notification::make()
                ->title('Ürün Pasife Alındı')
                ->body("{$product->name} isimli ürünün stoku tükendiği için otomatik olarak pasife alındı.")
                ->icon('heroicon-o-eye-slash')
                ->color('warning')
                ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
        }

        // Stok 0'dan yukarı çıktıysa ve ürün aktifse bekleyen bildirimleri gönder
        // (Yönetici tarafından elle pasife alınan ürünler zorla aktife ALINMAZ)
        $oldStock = (int) $product->getOriginal('stock');
        if ($product->wasChanged('stock') && (int) $product->stock > 0 && $oldStock <= 0) {
            if ($product->status) {
                \App\Services\StockNotificationService::processNotifications($product);
            }
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

<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Kural: Kritik stok 0 adet -> Ürün otomatik pasif
        if ($product->isDirty('stock') && $product->stock <= 0 && $product->status === true) {
            // Recursion olmaması için saveQuietly veya DB::table kullanılabilir. 
            // Veya event'i disable ederek update yapılabilir.
            $product->updateQuietly(['status' => false]);
            
            \Filament\Notifications\Notification::make()
                ->title('Ürün Pasife Alındı')
                ->body("{$product->name} isimli ürünün stoku tükendiği için otomatik olarak pasife alındı.")
                ->icon('heroicon-o-eye-slash')
                ->color('warning')
                ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
        }
    }
}

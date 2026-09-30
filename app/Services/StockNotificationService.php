<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockNotification;
use App\Mail\StockBackMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StockNotificationService
{
    /**
     * Tek bir stok bildirim talebini işler ve müşteriye e-posta/SMS gönderir.
     */
    public static function notifySingle(StockNotification $notification): bool
    {
        try {
            $product = $notification->product;
            if (!$product) {
                return false;
            }

            $variant = $notification->variant;

            // 1. E-Posta Bildirimi
            if (!empty($notification->email)) {
                Mail::to($notification->email)->send(new StockBackMail($product, $variant));
            }

            // 2. SMS Bildirimi (Telefon girilmişse)
            if (!empty($notification->phone)) {
                $vatanSms = app(VatanSmsService::class);
                $sizeText = $variant ? " ({$variant->size} Beden)" : '';
                $message = "Müjde! Patenli Ayakkabilar'da beklediğiniz {$product->name}{$sizeText} ürünü stoklarımıza girmiştir. İncelemek için: " . url('/urun/' . $product->slug);
                $vatanSms->send($notification->phone, $message);
            }

            // Kaydı bildirildi olarak güncelle
            $notification->update([
                'is_notified' => true,
                'notified_at' => now(),
            ]);

            return true;
        } catch (\Throwable $th) {
            Log::error("Stok Bildirim Hatası [ID: {$notification->id}]: " . $th->getMessage());
            return false;
        }
    }

    /**
     * Stok yenilendiğinde bekleyen bildirim taleplerini işler.
     */
    public static function processNotifications(Product $product, ?ProductVariant $variant = null): int
    {
        try {
            if (!Schema::hasTable('stock_notifications')) {
                return 0;
            }

            if (!$product->status) {
                return 0;
            }

            $query = StockNotification::where('product_id', $product->id)
                ->where('is_notified', false);

            if ($variant) {
                $query->where(function ($q) use ($variant) {
                    $q->where('product_variant_id', $variant->id)
                      ->orWhereNull('product_variant_id');
                });
            }

            $pendingNotifications = $query->get();

            if ($pendingNotifications->isEmpty()) {
                return 0;
            }

            $notifiedCount = 0;

            foreach ($pendingNotifications as $notification) {
                if (static::notifySingle($notification)) {
                    $notifiedCount++;
                }
            }

            return $notifiedCount;
        } catch (\Throwable $e) {
            Log::error('StockNotificationService error: ' . $e->getMessage());
            return 0;
        }
    }
}

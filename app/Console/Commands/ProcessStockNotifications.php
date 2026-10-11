<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockNotification;
use App\Services\StockNotificationService;

class ProcessStockNotifications extends Command
{
    protected $signature = 'stock:process-notifications {--product= : Belirli ürün ID} {--limit=50 : Maksimum işlenecek bildirim sayısı}';
    protected $description = 'Stoğu yenilenen ürünler için bekleyen "Gelince Haber Ver" bildirimlerini gönderir';

    public function handle(): int
    {
        $this->info('🔍 Bekleyen stok bildirimleri taranıyor...');

        $query = StockNotification::query()
            ->where('is_notified', false)
            ->with(['product', 'variant']);

        if ($productId = $this->option('product')) {
            $query->where('product_id', $productId);
        }

        $limit = (int) $this->option('limit');
        $notifications = $query->limit($limit)->get();

        if ($notifications->isEmpty()) {
            $this->info('✅ Bekleyen stok bildirimi bulunmuyor.');
            return self::SUCCESS;
        }

        $this->info("Toplam {$notifications->count()} bekleyen bildirim inceleniyor...");

        $sentCount = 0;
        foreach ($notifications as $notification) {
            $product = $notification->product;
            if (!$product) {
                continue;
            }

            $variant = $notification->variant;

            // Stoğu kontrol et
            $isInStock = false;
            if ($variant) {
                $isInStock = (int) $variant->stock > 0;
            } else {
                $isInStock = (int) $product->stock > 0;
            }

            if ($isInStock) {
                // Eğer ürün pasif kalmışsa aktife al
                if (!$product->status) {
                    $product->updateQuietly(['status' => true]);
                }

                $this->line(" 🚀 Bildirim gönderiliyor: {$notification->email} ({$product->name}" . ($variant ? " - Beden: {$variant->size}" : '') . ')');
                if (StockNotificationService::notifySingle($notification)) {
                    $sentCount++;
                }
            }
        }

        $this->info("✨ İşlem tamamlandı. {$sentCount} müşteriye bildirim iletildi.");

        return self::SUCCESS;
    }
}

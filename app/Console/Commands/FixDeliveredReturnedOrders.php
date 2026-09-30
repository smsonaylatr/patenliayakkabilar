<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;

class FixDeliveredReturnedOrders extends Command
{
    protected $signature = 'orders:fix-delivered-returned';
    protected $description = 'Yanlışlıkla Teslim Edildi olarak işaretlenen iade/iptal siparişlerini geri çevirir.';

    public function handle(): int
    {
        $this->info('Yanlış statüdeki siparişler taranıyor...');

        // payment_status=refunded olan ama status=delivered siparişleri bul
        $refundedDelivered = Order::where('status', 'delivered')
            ->where('payment_status', 'refunded')
            ->get();

        $this->info("payment_status=refunded & status=delivered: {$refundedDelivered->count()} sipariş bulundu.");

        foreach ($refundedDelivered as $order) {
            // OrderStatusHistory'den en son admin tarafından yapılan değişikliği bul
            $lastAdminChange = OrderStatusHistory::where('order_id', $order->id)
                ->whereNotNull('changed_by')
                ->latest('created_at')
                ->first();

            // Porego sync tarafından yapılan son "delivered" kaydını bul
            $lastPoregoDelivered = OrderStatusHistory::where('order_id', $order->id)
                ->whereNull('changed_by')
                ->where('new_status', 'delivered')
                ->where('note', 'like', '%Porego%')
                ->latest('created_at')
                ->first();

            // Son admin değişikliği Porego sync'ten sonra mı? Eğer evetse, admin'in belirlediği statüyü geri yükle
            $targetStatus = 'returned'; // Varsayılan: refunded ise returned yap

            if ($lastAdminChange && $lastPoregoDelivered && $lastAdminChange->created_at > $lastPoregoDelivered->created_at) {
                $targetStatus = $lastAdminChange->new_status;
            } elseif ($lastAdminChange) {
                // Porego kaydı yoksa bile admin'in son kararını kullan
                $targetStatus = $lastAdminChange->new_status;
            }

            // Eğer target hâlâ delivered ise, refunded olduğu için returned yap
            if ($targetStatus === 'delivered') {
                $targetStatus = 'returned';
            }

            $oldStatus = $order->status;
            $order->status = $targetStatus;
            $order->saveQuietly();

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $targetStatus,
                'changed_by' => null,
                'note' => "Otomatik düzeltme: Yanlışlıkla delivered olan sipariş {$targetStatus} olarak düzeltildi.",
                'created_at' => now(),
            ]);

            $this->line("  ✅ #{$order->order_number} ({$order->customer_name}): delivered → {$targetStatus}");
            Log::info("Sipariş düzeltildi: #{$order->order_number} delivered → {$targetStatus} (payment_status: refunded)");
        }

        // Ayrıca: son OrderStatusHistory'de returned/cancelled/return_started olan ama status=delivered siparişler
        $historyMismatch = Order::where('status', 'delivered')
            ->whereHas('statusHistory', function ($q) {
                $q->whereIn('new_status', ['returned', 'cancelled', 'return_started'])
                  ->whereNotNull('changed_by');
            })
            ->whereNotIn('id', $refundedDelivered->pluck('id'))
            ->get();

        $this->info("History'de iade/iptal olan ama status=delivered: {$historyMismatch->count()} sipariş.");

        foreach ($historyMismatch as $order) {
            $lastAdminReturn = OrderStatusHistory::where('order_id', $order->id)
                ->whereNotNull('changed_by')
                ->whereIn('new_status', ['returned', 'cancelled', 'return_started'])
                ->latest('created_at')
                ->first();

            if ($lastAdminReturn) {
                $targetStatus = $lastAdminReturn->new_status;
                $oldStatus = $order->status;
                $order->status = $targetStatus;
                $order->saveQuietly();

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $targetStatus,
                    'changed_by' => null,
                    'note' => "Otomatik düzeltme: Admin kararı geri yüklendi ({$targetStatus}).",
                    'created_at' => now(),
                ]);

                $this->line("  ✅ #{$order->order_number} ({$order->customer_name}): delivered → {$targetStatus}");
            }
        }

        // Cache temizle
        \Illuminate\Support\Facades\Cache::forget('orders_tab_counts');
        \Illuminate\Support\Facades\Cache::forget('orders_pending_count');

        $total = $refundedDelivered->count() + $historyMismatch->count();
        $this->info("✅ Toplam {$total} sipariş düzeltildi.");

        return Command::SUCCESS;
    }
}

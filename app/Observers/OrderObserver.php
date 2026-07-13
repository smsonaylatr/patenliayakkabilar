<?php

namespace App\Observers;

use App\Models\Order;
use Filament\Notifications\Notification;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Send a database notification to admins when a new order is placed.
        Notification::make()
            ->title('Yeni Sipariş Geldi')
            ->body("{$order->user->name} adlı müşteri {$order->grand_total} ₺ tutarında yeni bir sipariş verdi (Sipariş No: {$order->order_number}).")
            ->icon('heroicon-o-shopping-bag')
            ->color('success')
            ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            // Audit Log: Durum değişikliği
            \App\Models\OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $order->getOriginal('status'),
                'new_status' => $order->status,
                'changed_by' => auth()->id(),
                'note' => 'Sipariş durumu sistem veya yetkili tarafından güncellendi.',
            ]);

            if ($order->status === 'cancelled') {
                Notification::make()
                    ->title('Sipariş İptal Edildi')
                    ->body("{$order->order_number} numaralı sipariş iptal edildi.")
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
            } elseif ($order->status === 'shipped') {
                Notification::make()
                    ->title('Sipariş Kargoya Verildi')
                    ->body("{$order->order_number} numaralı sipariş kargolandı. Kargo Kodu: {$order->cargo_tracking_code}")
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
            }
        }
    }
}

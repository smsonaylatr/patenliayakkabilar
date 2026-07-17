<?php

namespace App\Livewire\Frontend;

use App\Models\Order;
use Livewire\Component;

class OrderSuccess extends Component
{
    public $order_number;
    public $order;

    public function mount($order_number, \App\Services\CartService $cartService)
    {
        $this->order_number = $order_number;
        $this->order = Order::where('order_number', $order_number)->firstOrFail();

        // Sipariş başarılı sayfasına gelindiyse, mevcut sepeti boşalt.
        if (session('last_order_number') === $order_number) {
            $cart = $cartService->getCart();
            if ($cart && $cart->items()->count() > 0) {
                $cart->items()->delete();
                // Navbar'daki sepet sayacını güncelle
                $this->dispatch('cart-updated');
            }
            // Sadece bir kere boşaltması için session'ı temizle (isteğe bağlı)
            session()->forget('last_order_number');
        }
    }

    public function render()
    {
        return view('livewire.frontend.order-success')->layout('components.layouts.app');
    }
}

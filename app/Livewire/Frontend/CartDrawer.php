<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Services\CartService;
use Livewire\Attributes\On;

class CartDrawer extends Component
{
    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // Livewire will re-render automatically
    }

    public function removeItem(CartService $cartService, int $itemId): void
    {
        $cartService->removeItem($itemId);
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(CartService $cartService, int $itemId, int $quantity): void
    {
        $quantity = max(1, min(10, $quantity));
        $cartService->updateQuantity($itemId, $quantity);
        $this->dispatch('cart-updated');
    }

    public function render(CartService $cartService)
    {
        $cart = $cartService->getCart();
        return view('livewire.frontend.cart-drawer', [
            'items' => $cart->items()->with(['product', 'variant'])->get(),
            'total' => $cartService->getTotal(),
        ]);
    }
}

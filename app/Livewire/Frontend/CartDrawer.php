<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Services\CartService;
use Livewire\Attributes\On;

class CartDrawer extends Component
{
    public $items = [];
    public $total = 0;

    public function mount(CartService $cartService)
    {
        $this->loadCart($cartService);
    }

    #[On('cart-updated')]
    public function loadCart(CartService $cartService)
    {
        $cart = $cartService->getCart();
        $this->items = $cart->items()->with(['product', 'variant'])->get();
        $this->total = $cartService->getTotal();
    }

    public function removeItem(CartService $cartService, $itemId)
    {
        $cartService->removeItem($itemId);
        $this->loadCart($cartService);
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(CartService $cartService, $itemId, $quantity)
    {
        $cartService->updateQuantity($itemId, $quantity);
        $this->loadCart($cartService);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.frontend.cart-drawer');
    }
}

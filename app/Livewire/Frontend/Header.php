<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Services\CartService;
use App\Models\Category;
use Livewire\Attributes\On;

class Header extends Component
{
    public $cartCount = 0;
    public $categories = [];

    public function mount(CartService $cartService)
    {
        $this->categories = Category::whereNull('parent_id')->where('status', true)->get();
        $this->updateCartCount($cartService);
    }

    #[On('cart-updated')]
    public function updateCartCount(CartService $cartService)
    {
        $this->cartCount = $cartService->getCount();
    }

    public function render()
    {
        return view('livewire.frontend.header');
    }
}

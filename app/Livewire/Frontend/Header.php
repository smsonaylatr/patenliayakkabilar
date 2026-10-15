<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Services\CartService;
use App\Models\Category;
use Livewire\Attributes\On;

class Header extends Component
{
    public int $cartCount = 0;

    public function mount(CartService $cartService)
    {
        $this->updateCartCount($cartService);
    }

    #[On('cart-updated')]
    public function updateCartCount(CartService $cartService)
    {
        $this->cartCount = $cartService->getCount();
    }

    public function render()
    {
        return view('livewire.frontend.header', [
            'categories' => \Illuminate\Support\Facades\Cache::remember('header_root_categories_v1', 3600, function () {
                return Category::whereNull('parent_id')->where('status', true)->get();
            }),
        ]);
    }
}

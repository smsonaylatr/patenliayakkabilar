<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\On;

class AddToCartButton extends Component
{
    public Product $product;
    public $quantity = 1;
    public $variantId = null;

    public function mount(Product $product)
    {
        $this->product = $product;
        if ($this->product->variants->count() > 0) {
            $this->variantId = '';
        }
    }

    #[On('variant-selected')]
    public function setVariant($variantId)
    {
        $this->variantId = $variantId;
    }

    public function addToCart(CartService $cartService)
    {
        if ($this->product->variants->count() > 0 && !$this->variantId) {
            $this->dispatch('open-variant-selector');
            return;
        }

        $cartService->addItem($this->product->id, $this->variantId, $this->quantity);
        
        $this->dispatch('cart-updated');
        $this->dispatch('open-cart');
    }

    public function render()
    {
        return view('livewire.product.add-to-cart-button');
    }
}

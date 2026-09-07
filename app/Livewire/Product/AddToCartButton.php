<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;

class AddToCartButton extends Component
{
    #[Locked]
    public Product $product;
    
    public int $quantity = 1;
    public mixed $variantId = null;

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
        if (!$this->product->inStock()) {
            return;
        }

        $this->quantity = max(1, min(10, $this->quantity));

        if ($this->product->variants->count() > 0) {
            if (!$this->variantId) {
                $this->dispatch('open-variant-selector');
                return;
            }

            if (!$this->product->variants->where('id', $this->variantId)->first()) {
                return;
            }
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

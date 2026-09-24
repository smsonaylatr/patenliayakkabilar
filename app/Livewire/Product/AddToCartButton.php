<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;

class AddToCartButton extends Component
{
    #[Locked]
    public Product $product;
    
    public int $quantity = 1;
    public mixed $variantId = null;
    public int $maxStock = 10;

    public function mount(Product $product)
    {
        $this->product = $product;
        if ($this->product->variants->count() > 0) {
            // Bedensiz ürünlerde tek varyant varsa otomatik seç
            if ($this->product->requires_size === false && $this->product->variants->count() === 1) {
                $this->variantId = $this->product->variants->first()->id;
            } else {
                $this->variantId = '';
            }
        }
        $this->updateMaxStock();
    }

    #[On('variant-selected')]
    public function setVariant($variantId)
    {
        $this->variantId = $variantId;
        $this->updateMaxStock();
        // Mevcut miktar yeni seçilen bedenin stoğunu aşıyorsa düzelt
        if ($this->quantity > $this->maxStock) {
            $this->quantity = max(1, $this->maxStock);
        }
    }

    /**
     * Seçili variant'a göre max stok miktarını günceller
     */
    private function updateMaxStock(): void
    {
        if ($this->variantId) {
            $variant = ProductVariant::find($this->variantId);
            $this->maxStock = $variant ? max(0, (int) $variant->stock) : 0;
        } else {
            $this->maxStock = max(0, (int) $this->product->stock);
        }
    }

    public function addToCart(CartService $cartService)
    {
        if (!$this->product->inStock()) {
            return;
        }

        $this->updateMaxStock();
        $this->quantity = max(1, min($this->maxStock, $this->quantity));

        if ($this->product->variants->count() > 0) {
            if (!$this->variantId) {
                $this->dispatch('open-variant-selector');
                return;
            }

            if (!$this->product->variants->where('id', $this->variantId)->first()) {
                return;
            }
        }

        $result = $cartService->addItem($this->product->id, $this->variantId, $this->quantity);
        
        if (!empty($result['error'])) {
            $this->dispatch('show-notification', type: 'warning', message: $result['error']);
            return;
        }
        
        $this->dispatch('cart-updated');
        $this->dispatch('open-cart');
    }

    public function render()
    {
        return view('livewire.product.add-to-cart-button');
    }
}

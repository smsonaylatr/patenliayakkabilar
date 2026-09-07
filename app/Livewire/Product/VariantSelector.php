<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\Locked;

class VariantSelector extends Component
{
    #[Locked]
    public Product $product;
    public mixed $selectedVariantId = null;

    public function mount(Product $product)
    {
        $this->product = $product;
        if ($this->product->variants->count() > 0) {
            $this->selectedVariantId = '';
        }
    }

    public function updatedSelectedVariantId($value)
    {
        if ($value && !$this->product->variants->where('id', $value)->first()) {
            return;
        }

        $this->dispatch('variant-selected', variantId: (string)$value);
    }

    public function render()
    {
        return view('livewire.product.variant-selector');
    }
}

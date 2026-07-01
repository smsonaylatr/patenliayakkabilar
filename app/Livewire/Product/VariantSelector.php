<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;

class VariantSelector extends Component
{
    public Product $product;
    public $selectedVariantId;

    public function mount(Product $product)
    {
        $this->product = $product;
        if ($this->product->variants->count() > 0) {
            $this->selectedVariantId = $this->product->variants->first()->id;
        }
    }

    public function updatedSelectedVariantId($value)
    {
        $this->dispatch('variant-selected', variantId: $value);
    }

    public function render()
    {
        return view('livewire.product.variant-selector');
    }
}

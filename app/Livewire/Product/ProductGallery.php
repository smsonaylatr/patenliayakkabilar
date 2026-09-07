<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\Locked;

class ProductGallery extends Component
{
    #[Locked]
    public Product $product;
    public string $mainImage = '';

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->mainImage = $product->images->first()
            ? $product->images->first()->image_url
            : asset('img/placeholder.svg');
    }

    public function setMainImage(string $path)
    {
        if ($this->product->images->where('image_url', $path)->first() || $path === asset('img/placeholder.svg')) {
            $this->mainImage = $path;
        }
    }

    public function render()
    {
        return view('livewire.product.product-gallery');
    }
}

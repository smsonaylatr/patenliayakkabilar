<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;

class ProductGallery extends Component
{
    public Product $product;
    public $mainImage;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->mainImage = $product->images->first()
            ? $product->images->first()->image_url
            : asset('img/placeholder.svg');
    }

    public function setMainImage($path)
    {
        $this->mainImage = $path;
    }

    public function render()
    {
        return view('livewire.product.product-gallery');
    }
}

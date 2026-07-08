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
            : 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80';
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

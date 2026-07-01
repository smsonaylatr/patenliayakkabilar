<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;

class ProductGrid extends Component
{
    public function addToCart(\App\Services\CartService $cartService, $productId, $variantId = null)
    {
        $product = Product::findOrFail($productId);
        
        if (!$variantId && $product->variants()->exists()) {
            $variantId = $product->variants->first()->id; 
        }
        
        $cartService->addItem($productId, $variantId, 1);
        
        $this->dispatch('cart-updated');
        $this->dispatch('open-cart');
        $this->dispatch('notify', message: 'Ürün sepete eklendi!', type: 'success');
    }

    public function render()
    {
        $products = \Illuminate\Support\Facades\Cache::remember('home_product_grid', 3600, function () {
            return Product::where('status', true)
                ->with(['category', 'images'])
                ->orderBy('id', 'desc')
                ->take(12)
                ->get();
        });
        
        return view('livewire.product.product-grid', compact('products'));
    }
}

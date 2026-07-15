<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;

class ProductGrid extends Component
{
    #[\Livewire\Attributes\Url]
    public $category = '';

    public bool $isFeaturedOnly = false;

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
        $cacheKey = 'home_product_grid_v2' . ($this->category ? '_cat_' . $this->category : '') . '_feat_' . ($this->isFeaturedOnly ? '1' : '0');
        
        $products = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () {
            $query = Product::where('status', true)->with(['category', 'images']);
            
            if ($this->isFeaturedOnly) {
                $query->where('featured', true);
            }

            if ($this->category) {
                $categoryModel = \App\Models\Category::where('slug', $this->category)->first();
                if ($categoryModel) {
                    $query->where('category_id', $categoryModel->id);
                }
            }

            return $query->orderByRaw('CASE WHEN homepage_sort > 0 THEN 0 ELSE 1 END')
                ->orderBy('homepage_sort', 'asc')
                ->orderBy('id', 'desc')
                ->take(36)
                ->get();
        });
        
        return view('livewire.product.product-grid', compact('products'));
    }
}

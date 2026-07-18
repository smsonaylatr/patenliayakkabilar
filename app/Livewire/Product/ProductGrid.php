<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;

class ProductGrid extends Component
{
    #[\Livewire\Attributes\Url]
    public $category = '';

    public bool $isFeaturedOnly = false;
    public int $limit = 36;

    public function mount($isFeaturedOnly = false, $limit = 36)
    {
        $this->isFeaturedOnly = filter_var($isFeaturedOnly, FILTER_VALIDATE_BOOLEAN);
        $this->limit = (int) $limit;
    }

    public function addToCart(\App\Services\CartService $cartService, $productId, $variantId = null)
    {
        $product = Product::findOrFail($productId);
        
        if (!$variantId && $product->variants()->exists()) {
            $variantId = $product->variants->first()->id; 
        }
        
        $cartService->addItem($productId, $variantId, 1);
        
        $this->dispatch('cart-updated');
        $this->dispatch('open-cart');
    }

    public function render()
    {
        $query = Product::where('status', true)->with(['category', 'images', 'variants']);
        
        if ($this->isFeaturedOnly) {
            $query->where('featured', true);
        }

        if ($this->category) {
            $categoryModel = \App\Models\Category::where('slug', $this->category)->first();
            if ($categoryModel) {
                $query->where('category_id', $categoryModel->id);
            }
        }

        $products = $query->orderByRaw('CASE WHEN homepage_sort > 0 THEN 0 ELSE 1 END')
            ->orderBy('homepage_sort', 'asc')
            ->orderBy('id', 'desc')
            ->take($this->limit)
            ->get();
        
        return view('livewire.product.product-grid', compact('products'));
    }
}

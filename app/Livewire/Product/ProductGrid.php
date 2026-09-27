<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\Attributes\Locked;
use App\Models\Product;

class ProductGrid extends Component
{
    #[\Livewire\Attributes\Url]
    public string $category = '';

    #[\Livewire\Attributes\Url(as: 'search')]
    public string $search = '';

    #[Locked]
    public bool $isFeaturedOnly = false;

    #[Locked]
    public int $limit = 36;

    public function mount($isFeaturedOnly = false, $limit = 36, $category = '', $search = '')
    {
        $this->isFeaturedOnly = filter_var($isFeaturedOnly, FILTER_VALIDATE_BOOLEAN);
        $this->limit = (int) $limit;

        if (!empty($category)) {
            $this->category = (string) $category;
        }

        if (!empty($search)) {
            $this->search = (string) $search;
        } elseif (empty($this->search)) {
            $this->search = (string) (request()->query('search') ?? request()->query('q') ?? '');
        }
    }

    public function addToCart(\App\Services\CartService $cartService, $productId, $variantId = null)
    {
        $product = Product::findOrFail($productId);
        
        if (!$variantId && $product->variants()->exists()) {
            $variantId = $product->variants->first()->id; 
        }
        
        $result = $cartService->addItem($productId, $variantId, 1);
        
        if (!empty($result['error'])) {
            $this->dispatch('show-notification', type: 'warning', message: $result['error']);
            return;
        }
        
        $this->dispatch('cart-updated');
        $this->dispatch('open-cart');
    }

    public function render()
    {
        $query = Product::where('status', true)->with(['categories', 'images', 'variants']);
        
        if ($this->isFeaturedOnly) {
            $query->where('featured', true);
        }

        if ($this->category) {
            $categoryModel = \App\Models\Category::where('slug', $this->category)->first();
            if ($categoryModel) {
                $query->join('category_product', 'products.id', '=', 'category_product.product_id')
                      ->where('category_product.category_id', $categoryModel->id)
                      ->select('products.*');
            }
        }

        $searchTerm = trim($this->search);
        if ($searchTerm !== '') {
            $slugTerm = \Illuminate\Support\Str::slug($searchTerm, '-');

            $query->where(function ($sq) use ($searchTerm, $slugTerm) {
                $sq->where('products.name', 'like', "%{$searchTerm}%")
                   ->orWhere('products.short_description', 'like', "%{$searchTerm}%")
                   ->orWhere('products.description', 'like', "%{$searchTerm}%")
                   ->orWhere('products.sku', 'like', "%{$searchTerm}%");

                if (!empty($slugTerm)) {
                    $sq->orWhere('products.slug', 'like', "%{$slugTerm}%");
                }

                $sq->orWhereHas('categories', function ($cq) use ($searchTerm, $slugTerm) {
                    $cq->where('categories.name', 'like', "%{$searchTerm}%");
                    if (!empty($slugTerm)) {
                        $cq->orWhere('categories.slug', 'like', "%{$slugTerm}%");
                    }
                });

                $sq->orWhereHas('variants', function ($vq) use ($searchTerm) {
                    $vq->where('product_variants.color', 'like', "%{$searchTerm}%")
                       ->orWhere('product_variants.size', 'like', "%{$searchTerm}%")
                       ->orWhere('product_variants.wheel_type', 'like', "%{$searchTerm}%")
                       ->orWhere('product_variants.sku', 'like', "%{$searchTerm}%");
                });
            });
        }

        $products = $query->orderByRaw('CASE WHEN homepage_sort > 0 THEN 0 ELSE 1 END')
            ->orderBy('homepage_sort', 'asc')
            ->orderBy('id', 'desc')
            ->take($this->limit)
            ->get();
        
        return view('livewire.product.product-grid', [
            'products' => $products,
            'search' => $this->search,
        ]);
    }
}

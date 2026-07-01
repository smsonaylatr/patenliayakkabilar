<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;

class BestSellerCarousel extends Component
{
    public function render()
    {
        // 1. Çok satanlar, veya öne çıkan indirimdeki ürünler
        $products = \Illuminate\Support\Facades\Cache::remember('best_seller_carousel_products', 7200, function () {
            return Product::with(['images', 'variants'])
                ->where('status', true)
                ->orderByDesc('featured')
                ->orderBy('id', 'desc')
                ->take(8)
                ->get();
        });

        return view('livewire.product.best-seller-carousel', [
            'products' => $products,
        ]);
    }
}

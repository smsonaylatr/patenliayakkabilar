<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Tüm aktif ürünleri listeler.
     */
    public function index(Request $request)
    {
        // Yalnızca yayında (is_active) olan ürünleri getir, ilişkili verilerle beraber
        $query = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->latest();

        // Kategoriye göre filtreleme
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Sayfalandırma (varsayılan 20)
        $limit = $request->input('limit', 20);
        $products = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Belirtilen ID veya slug'a göre tek bir ürünün detaylarını getirir.
     */
    public function show($identifier)
    {
        $product = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->where(function($q) use ($identifier) {
                $q->where('id', $identifier)
                  ->orWhere('slug', $identifier);
            })
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
}

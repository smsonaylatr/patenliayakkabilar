<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class PoregoProductApiController extends Controller
{
    /**
     * Porego / Paketfy için mağazadaki tüm aktif ürün ve varyantların JSON listesini döner.
     */
    public function index(Request $request)
    {
        $products = Product::with(['variants', 'images', 'categories'])
            ->where('status', true)
            ->get();

        $items = [];

        foreach ($products as $product) {
            $productImage = $product->images->first()?->image_url ?: url('/favicon.png');
            if (!empty($productImage) && !str_starts_with($productImage, 'http')) {
                $productImage = asset($productImage);
            }

            if ($product->variants->isNotEmpty()) {
                foreach ($product->variants as $variant) {
                    $sku = $variant->sku ?: ($product->sku ?: 'SKU-V' . $variant->id);
                    $variantName = $variant->size ? "Beden: {$variant->size}" : "";
                    $fullName = $product->name . ($variantName ? " ({$variantName})" : "");

                    $items[] = [
                        'platform_product_id' => (string)$product->id,
                        'platform_variant_id' => (string)$variant->id,
                        'platformProductId'   => (string)$product->id,
                        'platformVariantId'   => (string)$variant->id,
                        'sku'                 => $sku,
                        'product_sku'         => $sku,
                        'productSku'          => $sku,
                        'barcode'             => $sku,
                        'code'                => $sku,
                        'name'                => $fullName,
                        'product_name'        => $fullName,
                        'productName'         => $fullName,
                        'title'               => $fullName,
                        'price'               => (float)($variant->price ?: $product->price),
                        'discount_price'      => (float)($variant->discount_price ?: $product->discount_price ?: 0),
                        'stock'               => (int)($variant->stock ?? 0),
                        'quantity'            => (int)($variant->stock ?? 0),
                        'size'                => $variant->size,
                        'color'               => is_array($variant->color) ? implode(', ', $variant->color) : $variant->color,
                        'image_url'           => $productImage,
                        'imageUrl'            => $productImage,
                        'active'              => (bool)$product->status,
                        'categories'          => $product->categories->pluck('name')->toArray(),
                    ];
                }
            } else {
                $sku = $product->sku ?: 'SKU-P' . $product->id;
                $items[] = [
                    'platform_product_id' => (string)$product->id,
                    'platform_variant_id' => null,
                    'platformProductId'   => (string)$product->id,
                    'platformVariantId'   => null,
                    'sku'                 => $sku,
                    'product_sku'         => $sku,
                    'productSku'          => $sku,
                    'barcode'             => $sku,
                    'code'                => $sku,
                    'name'                => $product->name,
                    'product_name'        => $product->name,
                    'productName'         => $product->name,
                    'title'               => $product->name,
                    'price'               => (float)$product->price,
                    'discount_price'      => (float)($product->discount_price ?: 0),
                    'stock'               => (int)($product->stock ?? 0),
                    'quantity'            => (int)($product->stock ?? 0),
                    'image_url'           => $productImage,
                    'imageUrl'            => $productImage,
                    'active'              => (bool)$product->status,
                    'categories'          => $product->categories->pluck('name')->toArray(),
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'total_products' => $products->count(),
            'total_items' => count($items),
            'products' => $items,
            'items' => $items,
            'data' => $items,
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }

    /**
     * Porego için stok seviyeleri özeti
     */
    public function stock(Request $request)
    {
        $products = Product::with('variants')->where('status', true)->get();
        $stockMap = [];

        foreach ($products as $product) {
            if ($product->variants->isNotEmpty()) {
                foreach ($product->variants as $variant) {
                    $sku = $variant->sku ?: ($product->sku ?: 'SKU-V' . $variant->id);
                    $stockMap[$sku] = (int)($variant->stock ?? 0);
                }
            } else {
                $sku = $product->sku ?: 'SKU-P' . $product->id;
                $stockMap[$sku] = (int)($product->stock ?? 0);
            }
        }

        return response()->json([
            'status' => 'success',
            'stocks' => $stockMap,
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }
}

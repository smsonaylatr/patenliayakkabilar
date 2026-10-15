<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CartService
{
    protected ?Cart $resolvedCart = null;

    public function getCart(): Cart
    {
        if ($this->resolvedCart !== null) {
            return $this->resolvedCart;
        }

        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            $sessionId = Session::getId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        $this->resolvedCart = $cart;

        return $cart;
    }

    public function addItem($productId, $variantId = null, $quantity = 1)
    {
        $cart = $this->getCart();
        $product = Product::with('variants')->findOrFail($productId);
        
        if (!$product->inStock()) {
            return ['cart' => $cart, 'error' => 'Bu ürün stokta yok.', 'product' => $product, 'variant' => null];
        }

        $variantId = $variantId ?: null;
        $variant = $variantId ? $product->variants->firstWhere('id', (int) $variantId) : null;

        // Stok limitini belirle: variant varsa variant stoğu, yoksa ürün stoğu
        $availableStock = $this->getAvailableStock($product, $variantId, $variant);
        
        $price = $product->discount_price ?? $product->price;

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        $currentQty = $cartItem ? $cartItem->quantity : 0;
        $requestedTotal = $currentQty + $quantity;

        // Stok aşılıyorsa miktar sınırla
        if ($requestedTotal > $availableStock) {
            $allowedQty = $availableStock - $currentQty;
            if ($allowedQty <= 0) {
                return [
                    'cart' => $cart,
                    'error' => 'Bu üründen stokta sadece ' . $availableStock . ' adet var. Sepetinizde zaten ' . $currentQty . ' adet mevcut.',
                    'product' => $product,
                    'variant' => $variant,
                ];
            }
            $quantity = $allowedQty;
        }

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
            ]);
        }

        return [
            'cart' => $cart,
            'cartItem' => $cartItem,
            'product' => $product,
            'variant' => $variant,
            'addedQuantity' => $quantity,
            'error' => null,
        ];
    }

    public function removeItem($cartItemId)
    {
        $cart = $this->getCart();
        CartItem::where('cart_id', $cart->id)->where('id', $cartItemId)->delete();
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeItem($cartItemId);
            return ['error' => null];
        }

        $cart = $this->getCart();
        $cartItem = CartItem::with(['product', 'variant'])
            ->where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->first();

        if (!$cartItem || !$cartItem->product) {
            return ['error' => 'Sepet öğesi bulunamadı.'];
        }

        // Stok kontrolü
        $availableStock = $this->getAvailableStock($cartItem->product, $cartItem->product_variant_id, $cartItem->variant);
        if ($quantity > $availableStock) {
            $quantity = $availableStock;
            CartItem::where('id', $cartItemId)->update(['quantity' => $quantity]);
            return [
                'error' => 'Stokta sadece ' . $availableStock . ' adet var. Miktar güncellendi.',
                'maxStock' => $availableStock,
                'cartItem' => $cartItem,
            ];
        }

        CartItem::where('id', $cartItemId)->update(['quantity' => $quantity]);
        return ['error' => null, 'cartItem' => $cartItem];
    }

    public function getTotal()
    {
        $cart = $this->getCart();
        $items = $cart->relationLoaded('items')
            ? $cart->items
            : $cart->items()->with('product')->get();

        return $items->sum(function ($item) {
            return $item->product ? ($item->quantity * $item->price) : 0;
        });
    }

    public function getCount()
    {
        $cart = $this->getCart();
        if ($cart->relationLoaded('items')) {
            return (int) $cart->items->sum('quantity');
        }
        return (int) $cart->items()->sum('quantity');
    }

    /**
     * Belirli bir ürün+variant için mevcut stok miktarını döndürür.
     * Variant varsa variant stoğu, yoksa ürün stoğu kullanılır.
     */
    public function getAvailableStock(Product $product, $variantId = null, ?ProductVariant $loadedVariant = null): int
    {
        if ($variantId) {
            $variant = $loadedVariant;
            if (!$variant && $product->relationLoaded('variants')) {
                $variant = $product->variants->firstWhere('id', (int) $variantId);
            }
            if (!$variant) {
                $variant = ProductVariant::find($variantId);
            }
            if ($variant) {
                return max(0, (int) $variant->stock);
            }
        }

        return max(0, (int) $product->stock);
    }
}

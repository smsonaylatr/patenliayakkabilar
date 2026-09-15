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
    public function getCart()
    {
        $cart = null;

        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            $sessionId = Session::getId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        if ($cart) {
            // Clean up cart items if their product has been deleted
            $cart->items()->whereDoesntHave('product')->delete();
        }

        return $cart;
    }

    public function addItem($productId, $variantId = null, $quantity = 1)
    {
        $cart = $this->getCart();
        $product = Product::findOrFail($productId);
        
        if (!$product->inStock()) {
            return ['cart' => $cart, 'error' => 'Bu ürün stokta yok.'];
        }

        // Stok limitini belirle: variant varsa variant stoğu, yoksa ürün stoğu
        $availableStock = $this->getAvailableStock($product, $variantId);
        
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
                return ['cart' => $cart, 'error' => 'Bu üründen stokta sadece ' . $availableStock . ' adet var. Sepetinizde zaten ' . $currentQty . ' adet mevcut.'];
            }
            $quantity = $allowedQty;
        }

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
            ]);
        }

        return ['cart' => $cart, 'error' => null];
    }

    public function removeItem($cartItemId)
    {
        CartItem::where('id', $cartItemId)->delete();
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeItem($cartItemId);
            return ['error' => null];
        }

        $cartItem = CartItem::with(['product', 'variant'])->find($cartItemId);
        if (!$cartItem) {
            return ['error' => 'Sepet öğesi bulunamadı.'];
        }

        // Stok kontrolü
        $availableStock = $this->getAvailableStock($cartItem->product, $cartItem->product_variant_id);
        if ($quantity > $availableStock) {
            // Miktar stok sınırına çekilir
            $quantity = $availableStock;
            CartItem::where('id', $cartItemId)->update(['quantity' => $quantity]);
            return ['error' => 'Stokta sadece ' . $availableStock . ' adet var. Miktar güncellendi.', 'maxStock' => $availableStock];
        }

        CartItem::where('id', $cartItemId)->update(['quantity' => $quantity]);
        return ['error' => null];
    }

    public function getTotal()
    {
        $cart = $this->getCart();
        return $cart->items->sum(function($item) {
            return $item->product ? ($item->quantity * $item->price) : 0;
        });
    }

    public function getCount()
    {
        $cart = $this->getCart();
        return $cart->items->sum('quantity');
    }

    /**
     * Belirli bir ürün+variant için mevcut stok miktarını döndürür.
     * Variant varsa variant stoğu, yoksa ürün stoğu kullanılır.
     */
    public function getAvailableStock(Product $product, $variantId = null): int
    {
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                return max(0, (int) $variant->stock);
            }
        }

        return max(0, (int) $product->stock);
    }
}

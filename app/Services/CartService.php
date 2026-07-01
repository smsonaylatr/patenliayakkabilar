<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
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

        return $cart;
    }

    public function addItem($productId, $variantId = null, $quantity = 1)
    {
        $cart = $this->getCart();
        $product = Product::findOrFail($productId);
        
        $price = $product->discount_price ?? $product->price;

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

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

        return $cart;
    }

    public function removeItem($cartItemId)
    {
        CartItem::where('id', $cartItemId)->delete();
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeItem($cartItemId);
        } else {
            CartItem::where('id', $cartItemId)->update(['quantity' => $quantity]);
        }
    }

    public function getTotal()
    {
        $cart = $this->getCart();
        return $cart->items->sum(function($item) {
            return $item->quantity * $item->price;
        });
    }

    public function getCount()
    {
        $cart = $this->getCart();
        return $cart->items->sum('quantity');
    }
}

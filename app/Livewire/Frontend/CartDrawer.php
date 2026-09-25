<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Services\CartService;
use Livewire\Attributes\On;

class CartDrawer extends Component
{
    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // Livewire will re-render automatically
    }

    public function applyCoupon(string $code): void
    {
        if (empty($code)) {
            $this->dispatch('show-notification', type: 'warning', message: 'Lütfen bir indirim kodu girin.');
            return;
        }

        // TODO: Kupon doğrulama ve uygulama mantığı
        $this->dispatch('show-notification', type: 'info', message: 'İndirim kodu uygulandı.');
    }

    public function removeItem(CartService $cartService, int $itemId): void
    {
        $cartService->removeItem($itemId);
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(CartService $cartService, int $itemId, int $quantity): void
    {
        $quantity = max(1, $quantity);
        $result = $cartService->updateQuantity($itemId, $quantity);
        
        if (!empty($result['error'])) {
            $this->dispatch('show-notification', type: 'warning', message: $result['error']);
        }
        
        $this->dispatch('cart-updated');
    }

    public function quickAddToCart(CartService $cartService, int $productId): void
    {
        $product = \App\Models\Product::findOrFail($productId);
        
        $variantId = null;
        if ($product->variants()->exists()) {
            $variantId = $product->variants->first()->id;
        }
        
        $result = $cartService->addItem($productId, $variantId, 1);
        
        if (!empty($result['error'])) {
            $this->dispatch('show-notification', type: 'warning', message: $result['error']);
            return;
        }
        
        $this->dispatch('cart-updated');
        $this->dispatch('show-notification', type: 'success', message: $product->name . ' sepete eklendi!');
    }

    public function render(CartService $cartService)
    {
        $cart = $cartService->getCart();
        $items = $cart->items()->with(['product.images', 'variant'])->get();
        
        // Her item için max stok bilgisini hesapla
        $items->each(function ($item) use ($cartService) {
            $item->maxStock = $item->product 
                ? $cartService->getAvailableStock($item->product, $item->product_variant_id)
                : 0;
        });

        // Beğenebilirsiniz: Güvenlik ekipmanları kategorisinden öneriler
        $cartProductIds = $items->pluck('product_id')->toArray();
        $recommendations = cache()->remember(
            'cart_recs_' . implode('_', $cartProductIds ?: [0]),
            300,
            function () use ($cartProductIds) {
                return \App\Models\Product::whereHas('categories', function ($q) {
                    $q->where('categories.slug', 'guvenlik-ekipmanlari');
                })
                    ->whereNotIn('id', $cartProductIds)
                    ->where('status', true)
                    ->with('images')
                    ->inRandomOrder(crc32(session()->getId()))
                    ->take(10)
                    ->get();
            }
        );
        // Kargo tahmini: sepetteki ürünlerin en uzun teslimat süresini bul
        $deliveryEstimate = '1-3 iş günü';
        if ($items->isNotEmpty()) {
            $maxDays = 3;
            foreach ($items as $item) {
                if ($item->product && $item->product->delivery_time) {
                    $raw = $item->product->delivery_time;
                    // "7-14" veya "7-14 gün" gibi değerlerden max sayıyı çıkar
                    preg_match_all('/\d+/', $raw, $matches);
                    if (!empty($matches[0])) {
                        $maxInProduct = (int) max($matches[0]);
                        if ($maxInProduct > $maxDays) {
                            $maxDays = $maxInProduct;
                        }
                    }
                }
            }
            // En uzun süreye göre tahmini belirle
            if ($maxDays <= 3) {
                $deliveryEstimate = '1-3 iş günü';
            } elseif ($maxDays <= 7) {
                $deliveryEstimate = '3-7 iş günü';
            } else {
                $deliveryEstimate = '7-14 iş günü';
            }
        }

        return view('livewire.frontend.cart-drawer', [
            'items' => $items,
            'total' => $cartService->getTotal(),
            'recommendations' => $recommendations,
            'deliveryEstimate' => $deliveryEstimate,
        ]);
    }
}

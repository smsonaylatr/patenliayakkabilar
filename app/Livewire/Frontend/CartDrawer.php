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
        $recommendations = collect();
        $cartProductIds = $items->pluck('product_id')->toArray();
        $recommendations = \App\Models\Product::whereHas('categories', function ($q) {
            $q->where('categories.slug', 'guvenlik-ekipmanlari');
        })
            ->whereNotIn('id', $cartProductIds)
            ->where('status', true)
            ->with('images')
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('livewire.frontend.cart-drawer', [
            'items' => $items,
            'total' => $cartService->getTotal(),
            'recommendations' => $recommendations,
        ]);
    }
}

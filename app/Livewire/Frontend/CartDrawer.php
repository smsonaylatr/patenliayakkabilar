<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use App\Services\GoogleTagService;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;

class CartDrawer extends Component
{
    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // Livewire will re-render automatically when triggered by legacy server events
    }

    #[Renderless]
    public function fetchCartState(CartService $cartService): array
    {
        return $this->buildCartState($cartService);
    }

    #[Renderless]
    public function syncAddItem(CartService $cartService, int $productId, $variantId = null, int $quantity = 1): array
    {
        $variantId = ($variantId !== null && $variantId !== '') ? (int) $variantId : null;
        $quantity = max(1, $quantity);

        $result = $cartService->addItem($productId, $variantId, $quantity);
        $state = $this->buildCartState($cartService);
        $state['error'] = $result['error'] ?? null;

        return $state;
    }

    #[Renderless]
    public function syncUpdateQuantity(CartService $cartService, $itemId, int $quantity, ?int $productId = null, $variantId = null): array
    {
        $resolvedId = $this->resolveCartItemId($cartService, $itemId, $productId, $variantId);
        if (!$resolvedId) {
            return $this->buildCartState($cartService);
        }

        if ($quantity <= 0) {
            $cartService->removeItem($resolvedId);
            return $this->buildCartState($cartService);
        }

        $result = $cartService->updateQuantity($resolvedId, $quantity);
        $state = $this->buildCartState($cartService);
        $state['error'] = $result['error'] ?? null;

        return $state;
    }

    #[Renderless]
    public function syncRemoveItem(CartService $cartService, $itemId, ?int $productId = null, $variantId = null): array
    {
        $resolvedId = $this->resolveCartItemId($cartService, $itemId, $productId, $variantId);
        if ($resolvedId) {
            $cartService->removeItem($resolvedId);
        }

        return $this->buildCartState($cartService);
    }

    #[Renderless]
    public function syncQuickAdd(CartService $cartService, int $productId): array
    {
        $product = Product::with('variants')->find($productId);
        if (!$product) {
            $state = $this->buildCartState($cartService);
            $state['error'] = 'Ürün bulunamadı.';
            return $state;
        }

        $variantModel = $product->variants->first();
        $variantId = $variantModel ? $variantModel->id : null;

        $result = $cartService->addItem($productId, $variantId, 1);
        $state = $this->buildCartState($cartService);
        $state['error'] = $result['error'] ?? null;

        return $state;
    }

    protected function resolveCartItemId(CartService $cartService, $itemId, ?int $productId = null, $variantId = null): ?int
    {
        $cart = $cartService->getCart();

        if (is_numeric($itemId) && (int) $itemId > 0) {
            $exists = CartItem::where('cart_id', $cart->id)->where('id', (int) $itemId)->exists();
            if ($exists) {
                return (int) $itemId;
            }
        }

        if ($productId) {
            $vId = ($variantId !== null && $variantId !== '') ? (int) $variantId : null;
            $item = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->where('product_variant_id', $vId)
                ->first();

            if ($item) {
                return (int) $item->id;
            }
        }

        return null;
    }

    protected function buildCartState(CartService $cartService): array
    {
        $cart = $cartService->getCart();
        $items = $cart->items()->with(['product.images', 'product.categories', 'variant'])->get();

        // Silinmiş ürünü olan yetim satırları bellekte filtrele
        $validItems = $items->filter(fn ($item) => $item->product !== null)->values();

        $alpineItems = [];
        $total = 0.0;
        $count = 0;
        $maxDays = 3;

        foreach ($validItems as $item) {
            $product = $item->product;
            $variant = $item->variant;
            $maxStock = $variant
                ? max(0, (int) $variant->stock)
                : max(0, (int) $product->stock);

            $variantLabel = '';
            if ($variant) {
                $variantLabel = (string) ($variant->size ?: '');
                if ($variantLabel === '' && !empty($variant->color)) {
                    $variantLabel = is_array($variant->color) ? implode(' / ', $variant->color) : (string) $variant->color;
                }
            }

            $qty = (int) $item->quantity;
            $price = (float) $item->price;
            $total += $qty * $price;
            $count += $qty;

            if ($product->delivery_time) {
                preg_match_all('/\d+/', $product->delivery_time, $matches);
                if (!empty($matches[0])) {
                    $maxInProduct = (int) max($matches[0]);
                    if ($maxInProduct > $maxDays) {
                        $maxDays = $maxInProduct;
                    }
                }
            }

            $imageUrl = $product->images->first()
                ? $product->images->first()->image_url
                : asset('img/placeholder.svg');

            $alpineItems[] = [
                'id' => (int) $item->id,
                'key' => $product->id . '_' . ($item->product_variant_id ?: '0'),
                'product_id' => (int) $product->id,
                'variant_id' => $item->product_variant_id ? (int) $item->product_variant_id : null,
                'name' => $product->name,
                'slug' => $product->slug,
                'url' => route('products.show', $product->slug),
                'image' => $imageUrl,
                'price' => $price,
                'variant_label' => $variantLabel,
                'quantity' => $qty,
                'max_stock' => $maxStock,
                'delivery_time' => $product->delivery_time ?: '1-3 iş günü',
                'gtag_item' => GoogleTagService::formatProductItem($product, $variant, 1),
            ];
        }

        if ($maxDays <= 3) {
            $deliveryEstimate = '1-3 iş günü';
        } elseif ($maxDays <= 7) {
            $deliveryEstimate = '3-7 iş günü';
        } else {
            $deliveryEstimate = '7-14 iş günü';
        }

        $cart->setRelation('items', $validItems);

        return [
            'items' => $alpineItems,
            'total' => round($total, 2),
            'count' => $count,
            'deliveryEstimate' => $deliveryEstimate,
            'gtagCartPayload' => GoogleTagService::formatCartPayload($cart),
            'error' => null,
        ];
    }

    public function applyCoupon(string $code): void
    {
        if (empty($code)) {
            $this->dispatch('show-notification', type: 'warning', message: 'Lütfen bir indirim kodu girin.');
            return;
        }

        $this->dispatch('show-notification', type: 'info', message: 'İndirim kodu uygulandı.');
    }

    public function removeItem(CartService $cartService, int $itemId): void
    {
        $this->syncRemoveItem($cartService, $itemId);
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(CartService $cartService, int $itemId, int $quantity): void
    {
        $this->syncUpdateQuantity($cartService, $itemId, $quantity);
        $this->dispatch('cart-updated');
    }

    public function quickAddToCart(CartService $cartService, int $productId): void
    {
        $this->syncQuickAdd($cartService, $productId);
        $this->dispatch('cart-updated');
    }

    public function render(CartService $cartService)
    {
        $state = $this->buildCartState($cartService);

        $recommendations = cache()->remember(
            'cart_recs_security_pool_v1',
            1800,
            function () {
                return Product::whereHas('categories', function ($q) {
                    $q->where('categories.slug', 'guvenlik-ekipmanlari');
                })
                    ->where('status', true)
                    ->with(['images', 'variants'])
                    ->take(10)
                    ->get();
            }
        );

        return view('livewire.frontend.cart-drawer', [
            'alpineItems' => $state['items'],
            'total' => $state['total'],
            'recommendations' => $recommendations,
            'deliveryEstimate' => $state['deliveryEstimate'],
            'gtagCartPayload' => $state['gtagCartPayload'],
        ]);
    }
}

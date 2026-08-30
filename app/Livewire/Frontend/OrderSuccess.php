<?php

namespace App\Livewire\Frontend;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderSuccess extends Component
{
    public $order_number;
    public $order;
    public array $ratings = [];
    public array $comments = [];
    public bool $ratingsSubmitted = false;

    public function mount($order_number, \App\Services\CartService $cartService)
    {
        $this->order_number = $order_number;
        $this->order = Order::where('order_number', $order_number)
            ->with(['items.product.images'])
            ->firstOrFail();

        // Sipariş başarılı sayfasına gelindiyse, mevcut sepeti boşalt.
        if (session('last_order_number') === $order_number) {
            $cart = $cartService->getCart();
            if ($cart && $cart->items()->count() > 0) {
                $cart->items()->delete();
                // Navbar'daki sepet sayacını güncelle
                $this->dispatch('cart-updated');
            }
            // Sadece bir kere boşaltması için session'ı temizle (isteğe bağlı)
            session()->forget('last_order_number');
        }

        // Her ürün için varsayılan 5 yıldız
        foreach ($this->order->items as $item) {
            if ($item->product) {
                $this->ratings[$item->product_id] = 5;
                $this->comments[$item->product_id] = '';
            }
        }

        // Zaten bu sipariş için değerlendirme yapılmış mı kontrol et
        if (Auth::check()) {
            $existingRatings = Review::where('order_id', $this->order->id)
                ->where('user_id', Auth::id())
                ->exists();
            $this->ratingsSubmitted = $existingRatings;
        }
    }

    public function setRating(int $productId, int $rating): void
    {
        $this->ratings[$productId] = $rating;
    }

    public function submitRatings(): void
    {
        $user = Auth::user();
        $fullName = $user?->name ?? $this->order->customer_name;
        $maskedName = $this->maskName($fullName);

        foreach ($this->order->items as $item) {
            if (!$item->product) {
                continue;
            }

            $productId = $item->product_id;
            $rating = $this->ratings[$productId] ?? 5;

            // Duplicate kontrolü
            $exists = Review::where('order_id', $this->order->id)
                ->where('product_id', $productId)
                ->when($user, fn($q) => $q->where('user_id', $user->id))
                ->when(!$user, fn($q) => $q->where('name', $maskedName))
                ->exists();

            if ($exists) {
                continue;
            }

            Review::create([
                'product_id' => $productId,
                'user_id' => $user?->id,
                'order_id' => $this->order->id,
                'name' => $maskedName,
                'email' => $user?->email ?? $this->order->customer_email,
                'rating' => $rating,
                'comment' => !empty($this->comments[$productId]) ? $this->comments[$productId] : null,
                'status' => 1,
            ]);
        }

        $this->ratingsSubmitted = true;
    }

    /**
     * Soyadını maskele: "Osman Sarıkaya" → "Osman S."
     */
    private function maskName(?string $name): string
    {
        if (empty($name)) {
            return 'Müşteri';
        }

        $parts = explode(' ', trim($name));

        if (count($parts) < 2) {
            return $parts[0];
        }

        $lastName = array_pop($parts);
        $initial = mb_strtoupper(mb_substr($lastName, 0, 1, 'UTF-8'), 'UTF-8') . '.';
        $parts[] = $initial;

        return implode(' ', $parts);
    }

    public function render()
    {
        return view('livewire.frontend.order-success')->layout('components.layouts.app');
    }
}

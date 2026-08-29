<?php

namespace App\Livewire\Account;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderRating extends Component
{
    public Order $order;
    public array $ratings = [];
    public array $comments = [];
    public bool $showModal = false;

    public function mount(Order $order)
    {
        $this->order = $order->load(['items.product.images']);

        // Her ürün için varsayılan 5 yıldız
        foreach ($this->order->items as $item) {
            if ($item->product) {
                $this->ratings[$item->product_id] = 5;
                $this->comments[$item->product_id] = '';
            }
        }
    }

    public function setRating(int $productId, int $rating): void
    {
        $this->ratings[$productId] = $rating;
    }

    public function submitRatings(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Her ürün için puan kaydı oluştur
        foreach ($this->order->items as $item) {
            if (!$item->product) {
                continue;
            }

            $productId = $item->product_id;
            $rating = $this->ratings[$productId] ?? 5;

            // Zaten bu sipariş + ürün + kullanıcı için yorum varsa atla
            $exists = Review::where('order_id', $this->order->id)
                ->where('product_id', $productId)
                ->where('user_id', $user->id)
                ->exists();

            if ($exists) {
                continue;
            }

            Review::create([
                'product_id' => $productId,
                'user_id' => $user->id,
                'order_id' => $this->order->id,
                'name' => $user->name,
                'email' => $user->email,
                'rating' => $rating,
                'comment' => !empty($this->comments[$productId]) ? $this->comments[$productId] : null,
                'status' => 1, // Sipariş bazlı değerlendirmeler otomatik onaylı
            ]);
        }

        $this->showModal = false;
        $this->dispatch('rating-submitted');
        session()->flash('rating_success_' . $this->order->id, 'Değerlendirmeniz başarıyla kaydedildi. Teşekkür ederiz! ⭐');
    }

    public function render()
    {
        return view('livewire.account.order-rating');
    }
}

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

        $maskedName = $this->maskName($user->name);

        foreach ($this->order->items as $item) {
            if (!$item->product) {
                continue;
            }

            $productId = $item->product_id;
            $rating = $this->ratings[$productId] ?? 5;

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
                'name' => $maskedName,
                'email' => $user->email,
                'rating' => $rating,
                'comment' => !empty($this->comments[$productId]) ? $this->comments[$productId] : null,
                'status' => 1,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('rating-submitted');
        session()->flash('rating_success_' . $this->order->id, 'Değerlendirmeniz başarıyla kaydedildi. Teşekkür ederiz! ⭐');
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
        return view('livewire.account.order-rating');
    }
}

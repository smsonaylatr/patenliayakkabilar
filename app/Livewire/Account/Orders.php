<?php

namespace App\Livewire\Account;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Orders extends Component
{
    use WithPagination;

    #[On('rating-submitted')]
    public function refreshPage(): void
    {
        // Livewire otomatik re-render
    }

    public function render()
    {
        $userId = Auth::id();

        $orders = Order::where('user_id', $userId)
            ->with(['items.product.images'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Teslim edilmiş ama henüz değerlendirilmemiş sipariş ID'leri
        $ratedOrderIds = Review::where('user_id', $userId)
            ->whereNotNull('order_id')
            ->pluck('order_id')
            ->unique()
            ->toArray();

        return view('livewire.account.orders', [
            'orders' => $orders,
            'ratedOrderIds' => $ratedOrderIds,
        ]);
    }
}

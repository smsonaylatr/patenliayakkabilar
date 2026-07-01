<?php

namespace App\Livewire\Account;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Orders extends Component
{
    use WithPagination;

    public function render()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.account.orders', [
            'orders' => $orders
        ]);
    }
}

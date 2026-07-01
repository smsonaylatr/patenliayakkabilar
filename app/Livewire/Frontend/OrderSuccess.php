<?php

namespace App\Livewire\Frontend;

use App\Models\Order;
use Livewire\Component;

class OrderSuccess extends Component
{
    public $order_number;
    public $order;

    public function mount($order_number)
    {
        $this->order_number = $order_number;
        $this->order = Order::where('order_number', $order_number)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.frontend.order-success')->layout('components.layouts.app');
    }
}

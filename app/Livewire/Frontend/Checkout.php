<?php

namespace App\Livewire\Frontend;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Livewire\Component;
use Illuminate\Support\Str;

class Checkout extends Component
{
    public $customer_name;
    public $customer_email;
    public $customer_phone;
    public $customer_note;
    
    public $shipping_city;
    public $shipping_district;
    public $shipping_address;
    
    public $payment_method = 'cash_on_delivery';

    public $cities = [];
    public $districts = [];

    protected $rules = [
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'required|email|max:255',
        'customer_phone' => 'required|string|max:20',
        'shipping_city' => 'required|string|max:100',
        'shipping_district' => 'required|string|max:100',
        'shipping_address' => 'required|string',
        'payment_method' => 'required|in:cash_on_delivery,wire_transfer',
    ];

    public function mount()
    {
        if (file_exists(database_path('data/cities.json'))) {
            $json = json_decode(file_get_contents(database_path('data/cities.json')), true);
            if (isset($json['data'])) {
                $this->cities = collect($json['data'])->pluck('name')->toArray();
            }
        }
    }

    public function updatedShippingCity($value)
    {
        $this->shipping_district = null;
        $this->districts = [];

        if ($value && file_exists(database_path('data/cities.json'))) {
            $json = json_decode(file_get_contents(database_path('data/cities.json')), true);
            if (isset($json['data'])) {
                $cityData = collect($json['data'])->firstWhere('name', $value);
                if ($cityData && isset($cityData['districts'])) {
                    $this->districts = collect($cityData['districts'])->pluck('name')->toArray();
                }
            }
        }
    }

    public function placeOrder(CartService $cartService)
    {
        $this->validate();

        $cart = $cartService->getCart();
        
        if ($cart->items->count() === 0) {
            $this->dispatch('notify', message: 'Sepetiniz boş.', type: 'error');
            return;
        }

        $subtotal = $cartService->getTotal();
        $totalItems = $cart->items->sum('quantity');
        $shippingPrice = 1 * $totalItems;
        $grandTotal = $subtotal + $shippingPrice;
        $orderNumber = 'PATEN-' . strtoupper(Str::random(6));

        // Create Order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => $orderNumber,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $this->payment_method,
            'subtotal' => $subtotal,
            'shipping_price' => $shippingPrice,
            'grand_total' => $grandTotal,
            
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_note' => $this->customer_note,
            
            'shipping_city' => $this->shipping_city,
            'shipping_district' => $this->shipping_district,
            'shipping_address' => $this->shipping_address,
            
            'billing_city' => $this->shipping_city,
            'billing_district' => $this->shipping_district,
            'billing_address' => $this->shipping_address,
        ]);

        // Create Order Items
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->price,
                'total_price' => $item->price * $item->quantity,
            ]);
        }

        // Empty Cart
        $cart->items()->delete();
        $this->dispatch('cart-updated');

        // Redirect to success page
        return redirect()->route('order.success', ['order_number' => $order->order_number]);
    }

    public function render(CartService $cartService)
    {
        $cart = $cartService->getCart();
        $subtotal = $cartService->getTotal();
        $totalItems = $cart->items->sum('quantity');
        $shippingPrice = 1 * $totalItems;
        $grandTotal = $subtotal + $shippingPrice;

        return view('livewire.frontend.checkout', [
            'cartItems' => $cart->items,
            'subtotal' => $subtotal,
            'shippingPrice' => $shippingPrice,
            'grandTotal' => $grandTotal,
        ])->layout('components.layouts.app');
    }
}

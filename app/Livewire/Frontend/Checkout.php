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
    public $customer_note = '';
    
    public $shipping_city;
    public $shipping_district;
    public $shipping_address;
    
    public $payment_method = 'credit_card';

    public $cities = [];
    public $districts = [];

    public $paytr_token = null;

    protected $rules = [
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'required|email|max:255',
        'customer_phone' => ['required', 'string', 'regex:/^(05[0-9]{9}|0 \\(5[0-9]{2}\\) [0-9]{3} [0-9]{2} [0-9]{2}|\\+90 \\(5[0-9]{2}\\) [0-9]{3} [0-9]{2} [0-9]{2}|90 \\(5[0-9]{2}\\) [0-9]{3} [0-9]{2} [0-9]{2})$/'],
        'shipping_city' => 'required|string|max:100',
        'shipping_district' => 'required|string|max:100',
        'shipping_address' => 'required|string',
        'payment_method' => 'required|in:cash_on_delivery,wire_transfer,credit_card',
    ];

    protected $messages = [
        'customer_name.required' => 'Lütfen adınızı ve soyadınızı giriniz.',
        'customer_email.required' => 'Lütfen e-posta adresinizi giriniz.',
        'customer_email.email' => 'Lütfen geçerli bir e-posta adresi giriniz.',
        'customer_phone.required' => 'Lütfen telefon numaranızı giriniz.',
        'customer_phone.regex' => 'Lütfen başında 0 olacak şekilde 11 haneli geçerli bir numara giriniz (Örn: 05551234567).',
        'shipping_city.required' => 'Lütfen teslimat ilini seçiniz.',
        'shipping_district.required' => 'Lütfen teslimat ilçesini seçiniz.',
        'shipping_address.required' => 'Lütfen açık adresinizi giriniz.',
    ];

    public $isCodAllowed = true;

    public function mount(CartService $cartService)
    {
        $cart = $cartService->getCart();
        if ($cart) {
            foreach ($cart->items as $item) {
                if ($item->product && !$item->product->is_cod_active) {
                    $this->isCodAllowed = false;
                    break;
                }
            }
        }

        if (!$this->isCodAllowed && $this->payment_method === 'cash_on_delivery') {
            $this->payment_method = 'credit_card';
        }

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
        $orderNumber = 'TR' . mt_rand(100000, 999999);

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

            'ip_address' => request()->ip(),
        ]);

        // Create Order Items
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => $item->product ? $item->product->name : 'Bilinmeyen Ürün',
                'variant_info' => $item->variant ? 'Beden: ' . $item->variant->size : null,
                'quantity' => $item->quantity,
                'unit_price' => $item->price,
                'total_price' => $item->price * $item->quantity,
            ]);
        }

        // Tüm ödeme yöntemleri için session'a sipariş numarasını kaydet (Sepet boşaltma vs. için)
        session(['last_order_number' => $order->order_number]);

        // IF KREDI KARTI, PAYTR TOKEN AL
        if ($this->payment_method === 'credit_card') {
            $this->paytr_token = $this->getPaytrToken($order, $cart->items);
            
            if (!$this->paytr_token) {
                // Token alınamadıysa siparişi silip (veya hata verip) sepeti boşaltmıyoruz ki kullanıcı tekrar deneyebilsin.
                $order->items()->delete();
                $order->delete();
                $this->dispatch('notify', message: 'Ödeme sistemi ile iletişim kurulamadı. Lütfen mağaza yöneticisinin PayTR API ayarlarını yapmasını bekleyin.', type: 'error');
                return;
            }
            
            // Render kısmında iframe açılacak. Yönlendirme YAPMIYORUZ. Sepeti BURADA BOŞALTMIYORUZ.
            return;
        }

        // Redirect to success page (Havale veya Kapıda ödeme)
        return redirect()->route('order.success', ['order_number' => $order->order_number]);
    }

    private function getPaytrToken(Order $order, $cartItems)
    {
        $merchant_id    = config('services.paytr.merchant_id');
        $merchant_key   = config('services.paytr.merchant_key');
        $merchant_salt  = config('services.paytr.merchant_salt');

        $email = $order->customer_email;
        $payment_amount = $order->grand_total * 100; // kuruş cinsinden
        $merchant_oid = $order->order_number;
        $user_name = $order->customer_name;
        $user_address = $order->shipping_address . ' ' . $order->shipping_district . '/' . $order->shipping_city;
        $user_phone = $order->customer_phone;
        $merchant_ok_url = route('payment.paytr.success');
        $merchant_fail_url = route('payment.paytr.fail');

        // Sepet içeriklerini PayTR formatına dönüştür
        $user_basket = [];
        foreach ($cartItems as $item) {
            $user_basket[] = [
                $item->product ? $item->product->name : 'Ürün',
                $item->price,
                $item->quantity
            ];
        }
        $user_basket = base64_encode(json_encode($user_basket));

        $user_ip = request()->ip();
        $timeout_limit = "30";
        $debug_on = 1;
        $test_mode = app()->environment('production') ? 0 : 1;
        $no_installment = 0;
        $max_installment = 0;
        $currency = "TL";

        $hash_str = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .$user_basket .$no_installment .$max_installment .$currency .$test_mode;
        $paytr_token = base64_encode(hash_hmac('sha256', $hash_str.$merchant_salt, $merchant_key, true));

        $post_vals = [
            'merchant_id' => $merchant_id,
            'user_ip' => $user_ip,
            'merchant_oid' => $merchant_oid,
            'email' => $email,
            'payment_amount' => $payment_amount,
            'paytr_token' => $paytr_token,
            'user_basket' => $user_basket,
            'debug_on' => $debug_on,
            'no_installment' => $no_installment,
            'max_installment' => $max_installment,
            'user_name' => $user_name,
            'user_address' => $user_address,
            'user_phone' => $user_phone,
            'merchant_ok_url' => $merchant_ok_url,
            'merchant_fail_url' => $merchant_fail_url,
            'timeout_limit' => $timeout_limit,
            'currency' => $currency,
            'test_mode' => $test_mode
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $result = @curl_exec($ch);

        if(curl_errno($ch)) {
            \Illuminate\Support\Facades\Log::error('PayTR Curl Error: ' . curl_error($ch));
            return null;
        }

        curl_close($ch);

        $result = json_decode($result, 1);

        if($result['status'] == 'success') {
            return $result['token'];
        } else {
            \Illuminate\Support\Facades\Log::error('PayTR Token Error: ' . $result['reason']);
            return null;
        }
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

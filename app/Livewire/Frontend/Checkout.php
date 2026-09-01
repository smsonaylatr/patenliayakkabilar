<?php

namespace App\Livewire\Frontend;

use App\Models\Coupon;
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
    public $shipping_neighborhood;
    public $shipping_address;

    // Adres Autocomplete
    public $address_mode = 'autocomplete'; // 'autocomplete' veya 'manual'
    public $address_search = '';
    public $address_detail = '';
    public $address_selected = false;
    
    public $payment_method = 'credit_card';
    public $sms_consent = false;
    public $terms_consent = false;

    // Fatura bilgileri
    public $invoice_type = 'individual'; // 'individual' veya 'corporate'
    public $company_name = '';
    public $tax_office = '';
    public $tax_number = '';

    public $cities = [];
    public $districts = [];
    public $neighborhoods = [];

    public $paytr_token = null;
    public $created_order_number = null;

    // Kupon kodu
    public $coupon_code = '';
    public $applied_coupon = null;
    public $coupon_discount = 0;
    public $coupon_message = '';
    public $coupon_error = '';

    protected function rules()
    {
        $baseRules = [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => ['required', 'string', 'regex:/^(05[0-9]{9}|0 \\(5[0-9]{2}\\) [0-9]{3} [0-9]{2} [0-9]{2}|\\+90 \\(5[0-9]{2}\\) [0-9]{3} [0-9]{2} [0-9]{2}|90 \\(5[0-9]{2}\\) [0-9]{3} [0-9]{2} [0-9]{2})$/'],
            'shipping_city' => 'required|string|max:100',
            'shipping_district' => 'required|string|max:100',
            'payment_method' => 'required|in:cash_on_delivery,wire_transfer,credit_card',
            'terms_consent' => 'accepted',
        ];

        if ($this->address_mode === 'autocomplete') {
            // Autocomplete modunda mahalle ve açık adres opsiyonel
            // (Google Places her zaman neighborhood döndürmüyor)
            $baseRules['shipping_neighborhood'] = 'nullable|string|max:150';
            $baseRules['shipping_address'] = 'nullable|string';
        } else {
            // Manuel modda eski kurallar
            $baseRules['shipping_neighborhood'] = 'required|string|max:150';
            $baseRules['shipping_address'] = 'required|string';
        }

        // Fatura bilgileri
        $baseRules['invoice_type'] = 'required|in:individual,corporate';
        if ($this->invoice_type === 'corporate') {
            $baseRules['company_name'] = 'required|string|max:255';
            $baseRules['tax_office'] = 'required|string|max:255';
            $baseRules['tax_number'] = 'required|string|min:10|max:11';
        }

        return $baseRules;
    }

    protected $messages = [
        'customer_name.required' => 'Lütfen adınızı ve soyadınızı giriniz.',
        'customer_email.required' => 'Lütfen e-posta adresinizi giriniz.',
        'customer_email.email' => 'Lütfen geçerli bir e-posta adresi giriniz.',
        'customer_phone.required' => 'Lütfen telefon numaranızı giriniz.',
        'customer_phone.regex' => 'Lütfen başında 0 olacak şekilde 11 haneli geçerli bir numara giriniz (Örn: 05551234567).',
        'shipping_city.required' => 'Lütfen teslimat adresinizi seçiniz.',
        'shipping_district.required' => 'Lütfen teslimat adresinizi seçiniz.',
        'shipping_neighborhood.required' => 'Lütfen mahallenizi seçiniz veya yazınız.',
        'shipping_address.required' => 'Lütfen açık adresinizi giriniz.',
        'terms_consent.accepted' => 'Devam etmek için Ön Bilgilendirme Formu ve Mesafeli Satış Sözleşmesi\'ni onaylamalısınız.',
        'company_name.required' => 'Kurumsal fatura için firma adı zorunludur.',
        'tax_office.required' => 'Kurumsal fatura için vergi dairesi zorunludur.',
        'tax_number.required' => 'Kurumsal fatura için vergi numarası zorunludur.',
        'tax_number.min' => 'Vergi numarası en az 10 karakter olmalıdır.',
        'tax_number.max' => 'Vergi numarası en fazla 11 karakter olmalıdır.',
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

        $this->customer_name = session('co_name', $this->customer_name);
        $this->customer_email = session('co_email', $this->customer_email);
        $this->customer_phone = session('co_phone', $this->customer_phone);
        $this->shipping_city = session('co_city', $this->shipping_city);
        
        if ($this->shipping_city) {
            $this->updatedShippingCity($this->shipping_city);
            $this->shipping_district = session('co_district', $this->shipping_district);
            if ($this->shipping_district) {
                $this->updatedShippingDistrict($this->shipping_district);
                $this->shipping_neighborhood = session('co_neighborhood', $this->shipping_neighborhood);
            }
        }
        
        $this->shipping_address = session('co_address', $this->shipping_address);
        $this->customer_note = session('co_note', $this->customer_note);
        $this->address_detail = session('co_address_detail', $this->address_detail);
        $this->address_search = session('co_address_search', $this->address_search);

        // Kurumsal fatura bilgilerini session'dan restore et (fatura tipi hariç — her zaman bireysel açılır)
        $this->company_name = session('co_company_name', $this->company_name);
        $this->tax_office = session('co_tax_office', $this->tax_office);
        $this->tax_number = session('co_tax_number', $this->tax_number);

        // Google Places API key yoksa doğrudan manual mode
        if (empty(config('services.google_places.api_key'))) {
            $this->address_mode = 'manual';
            $this->address_selected = false;
        } else {
            $this->address_mode = session('co_address_mode', 'autocomplete');
            $this->address_selected = session('co_address_selected', false);
        }

        // Autocomplete modunda önceden seçilmiş adres varsa flag'i restore et (fallback)
        if ($this->address_mode === 'autocomplete' && $this->shipping_city && $this->shipping_district && empty($this->address_selected)) {
            $this->address_selected = true;
        }
    }

    public function updated($propertyName)
    {
        $map = [
            'customer_name' => 'co_name',
            'customer_email' => 'co_email',
            'customer_phone' => 'co_phone',
            'shipping_city' => 'co_city',
            'shipping_district' => 'co_district',
            'shipping_neighborhood' => 'co_neighborhood',
            'shipping_address' => 'co_address',
            'customer_note' => 'co_note',
            'address_detail' => 'co_address_detail',
            'address_search' => 'co_address_search',
            'address_mode' => 'co_address_mode',
            'address_selected' => 'co_address_selected',
            'invoice_type' => 'co_invoice_type',
            'company_name' => 'co_company_name',
            'tax_office' => 'co_tax_office',
            'tax_number' => 'co_tax_number',
        ];

        if (array_key_exists($propertyName, $map)) {
            session([$map[$propertyName] => $this->$propertyName]);
            
            // Sepeti Terk Edenler için iletişim bilgilerini Cart'a kaydet (Misafir kullanıcılar için)
            if (in_array($propertyName, ['customer_name', 'customer_email', 'customer_phone'])) {
                $cartService = app(\App\Services\CartService::class);
                $cart = $cartService->getCart();
                if ($cart && !$cart->user_id) {
                    $columnMap = [
                        'customer_name' => 'guest_name',
                        'customer_email' => 'guest_email',
                        'customer_phone' => 'guest_phone',
                    ];
                    $cart->update([
                        $columnMap[$propertyName] => $this->$propertyName
                    ]);
                }
            }
        }
    }

    public function updatedShippingCity($value)
    {
        $this->shipping_district = null;
        $this->shipping_neighborhood = null;
        $this->districts = [];
        $this->neighborhoods = [];

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

    public function updatedShippingDistrict($value)
    {
        $this->shipping_neighborhood = null;
        $this->neighborhoods = [];

        if ($value) {
            $this->loadNeighborhoods($value);
        }
    }

    public function loadNeighborhoods($district)
    {
        if (empty($district)) {
            $this->neighborhoods = [];
            return;
        }

        try {
            $cacheKey = 'district_neighborhoods_' . \Illuminate\Support\Str::slug($district);
            $neighborhoods = \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400 * 30, function () use ($district) {
                $response = \Illuminate\Support\Facades\Http::timeout(4)->get('https://turkiyeapi.dev/api/v1/districts', [
                    'name' => $district
                ]);

                if ($response->successful()) {
                    $data = $response->json('data');
                    if (!empty($data[0]['neighborhoods'])) {
                        return collect($data[0]['neighborhoods'])->pluck('name')->sort()->values()->toArray();
                    }
                }
                return [];
            });

            $this->neighborhoods = $neighborhoods ?: [];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Mahalle yükleme hatası ({$district}): " . $e->getMessage());
            $this->neighborhoods = [];
        }
    }

    /**
     * Google Places Autocomplete'den seçilen adresi parse et ve form alanlarına dağıt.
     * Alpine.js'den çağrılır: $wire.selectAddress(placeData)
     */
    public function selectAddress($placeData)
    {
        $this->shipping_city = $placeData['city'] ?? '';
        $this->shipping_district = $placeData['district'] ?? '';
        $this->shipping_neighborhood = $placeData['neighborhood'] ?? '';
        $this->shipping_address = $placeData['street_address'] ?? '';
        $this->address_search = $placeData['formatted_address'] ?? '';
        $this->address_selected = true;

        // shipping_address boşsa formatted_address kullan
        if (empty($this->shipping_address) && !empty($this->address_search)) {
            $this->shipping_address = $this->address_search;
        }

        // Neighborhood boşsa formatted_address'ten çıkarmayı dene
        if (empty($this->shipping_neighborhood) && !empty($this->address_search)) {
            $parts = explode(',', $this->address_search);
            if (count($parts) >= 3) {
                $firstPart = trim($parts[0]);
                if (preg_match('/^[\p{L}\s]+$/u', $firstPart) && mb_strlen($firstPart) > 2) {
                    $this->shipping_neighborhood = $firstPart;
                }
            }
        }

        // Session'a kaydet
        session([
            'co_city' => $this->shipping_city,
            'co_district' => $this->shipping_district,
            'co_neighborhood' => $this->shipping_neighborhood,
            'co_address' => $this->shipping_address,
            'co_address_search' => $this->address_search,
            'co_address_selected' => true,
        ]);

        // Alpine state'ini koru — re-render sonrası event ile addressSelected ve query set edilir
        $this->dispatch('address-updated', selected: true, query: $this->address_search);
    }

    /**
     * Adres modunu değiştir (autocomplete ↔ manual)
     */
    public function switchAddressMode($mode)
    {
        $this->address_mode = $mode;
        session(['co_address_mode' => $mode]);

        if ($mode === 'manual' && !$this->address_selected) {
            // Autocomplete'den bir şey seçilmediyse alanları temizle
            $this->resetAutocomplete();
        }
    }

    /**
     * Autocomplete seçimini sıfırla (yeni arama yapabilmek için)
     */
    public function resetAutocomplete()
    {
        $this->address_search = '';
        $this->address_detail = '';
        $this->address_selected = false;
        $this->shipping_city = '';
        $this->shipping_district = '';
        $this->shipping_neighborhood = '';
        $this->shipping_address = '';

        session()->forget([
            'co_city', 'co_district', 'co_neighborhood', 'co_address',
            'co_address_search', 'co_address_detail', 'co_address_selected',
        ]);

        // Alpine state'ini koru
        $this->dispatch('address-reset');
    }

    public function placeOrder(CartService $cartService)
    {
        if ($this->paytr_token || $this->created_order_number) {
            return;
        }

        // Autocomplete modunda adres seçilmeden sipariş vermeye çalışırsa
        if ($this->address_mode === 'autocomplete' && !$this->address_selected) {
            if (!empty($this->address_search)) {
                $this->address_selected = true;
                if (empty($this->shipping_address)) {
                    $this->shipping_address = $this->address_search;
                }
            } else {
                $this->addError('shipping_city', 'Lütfen teslimat adresinizi giriniz veya arama kutusundan seçiniz.');
                return;
            }
        }

        $this->validate();

        try {
        $cart = $cartService->getCart();
        
        if (!$cart || $cart->items->count() === 0) {
            $this->dispatch('notify', message: 'Sepetiniz boş.', type: 'error');
            return;
        }

        $subtotal = $cartService->getTotal();
        $totalItems = $cart->items->sum('quantity');
        $shippingPrice = $this->payment_method === 'cash_on_delivery' ? (200 + (1 * $totalItems)) : (1 * $totalItems);
        
        // Kupon indirimi genel toplam üzerinden hesapla (kargo dahil)
        $couponDiscount = 0;
        if ($this->applied_coupon) {
            $totalBeforeDiscount = $subtotal + $shippingPrice;
            if ($this->applied_coupon->type === 'percentage') {
                $couponDiscount = round($totalBeforeDiscount * ($this->applied_coupon->value / 100), 2);
            } else {
                $couponDiscount = min($this->applied_coupon->value, $totalBeforeDiscount);
            }
        }
        $grandTotal = max(0, $subtotal + $shippingPrice - $couponDiscount);
        $orderNumber = 'TR' . mt_rand(100000, 999999);

        // Sepet üzerinde misafir bilgilerini her ihtimale karşı güncelle
        if (!$cart->user_id) {
            $cart->update([
                'guest_name' => $this->customer_name,
                'guest_email' => $this->customer_email,
                'guest_phone' => $this->customer_phone,
                'sms_consent' => $this->sms_consent,
            ]);
        } else {
            $cart->update([
                'sms_consent' => $this->sms_consent,
            ]);
        }

        // Create Order
        $neighborhood = trim($this->shipping_neighborhood ?: '');
        $rawAddress = trim($this->shipping_address ?: '');

        // Autocomplete modunda shipping_address boşsa address_search'i kullan
        if ($this->address_mode === 'autocomplete' && empty($rawAddress) && !empty($this->address_search)) {
            $rawAddress = trim($this->address_search);
        }

        // Autocomplete modunda address_detail'i rawAddress'e birleştir
        $addressDetail = trim($this->address_detail ?: '');
        if (!empty($addressDetail)) {
            $rawAddress = !empty($rawAddress) ? ($rawAddress . ' ' . $addressDetail) : $addressDetail;
        }

        if (!empty($neighborhood) && stripos($rawAddress, $neighborhood) === false) {
            $formattedAddress = $neighborhood . (preg_match('/(mah|mahallesi|mh\.)/i', $neighborhood) ? '' : ' Mah.') . ' ' . $rawAddress;
        } else {
            $formattedAddress = $rawAddress;
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => $orderNumber,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $this->payment_method,
            'gclid' => session('gclid'),
            'subtotal' => $subtotal,
            'shipping_price' => $shippingPrice,
            'discount_total' => $couponDiscount,
            'coupon_code' => $this->applied_coupon ? $this->applied_coupon->code : null,
            'grand_total' => $grandTotal,
            
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_note' => $this->customer_note,
            'sms_consent' => $this->sms_consent,
            
            'shipping_city' => $this->shipping_city,
            'shipping_district' => $this->shipping_district,
            'shipping_neighborhood' => $neighborhood,
            'shipping_address' => $formattedAddress,
            
            'billing_city' => $this->shipping_city,
            'billing_district' => $this->shipping_district,
            'billing_neighborhood' => $neighborhood,
            'billing_address' => $formattedAddress,

            // Fatura bilgileri
            'invoice_type' => $this->invoice_type,
            'company_name' => $this->invoice_type === 'corporate' ? $this->company_name : null,
            'tax_office' => $this->invoice_type === 'corporate' ? $this->tax_office : null,
            'tax_number' => $this->invoice_type === 'corporate' ? $this->tax_number : null,

            'ip_address' => request()->ip(),
        ]);

        // Kupon kullanım sayısını artır + müşteri bilgisini kaydet
        if ($this->applied_coupon) {
            $this->applied_coupon->update([
                'used_count' => $this->applied_coupon->used_count + 1,
                'used_by_name' => $this->customer_name,
                'used_by_phone' => $this->customer_phone,
            ]);
        }

        // Create Order Items
        foreach ($cart->items as $item) {
            $vColor = null;
            if ($item->variant && !empty($item->variant->color)) {
                $vColor = is_array($item->variant->color) ? implode(', ', $item->variant->color) : $item->variant->color;
            }
            $vSize = $item->variant?->size;

            $variantInfo = null;
            if ($vColor && $vSize) {
                $variantInfo = "{$vColor} / Beden: {$vSize}";
            } elseif ($vSize) {
                $variantInfo = "Beden: {$vSize}";
            } elseif ($vColor) {
                $variantInfo = $vColor;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => $item->product ? $item->product->name : 'Bilinmeyen Ürün',
                'variant_info' => $variantInfo,
                'quantity' => $item->quantity,
                'unit_price' => $item->price,
                'total_price' => $item->price * $item->quantity,
            ]);
        }

        // Tüm ödeme yöntemleri için session'a sipariş numarasını kaydet (Sepet boşaltma vs. için)
        session(['last_order_number' => $order->order_number]);
        $this->created_order_number = $order->order_number;

        // IF KREDI KARTI VEYA HAVALE/EFT, PAYTR TOKEN AL
        if (in_array($this->payment_method, ['credit_card', 'wire_transfer'])) {
            $this->paytr_token = $this->getPaytrToken($order, $cart->items, $this->payment_method);
            
            if (!$this->paytr_token) {
                // Token alınamadıysa siparişi silip sepeti boşaltmıyoruz ki kullanıcı tekrar deneyebilsin.
                $order->items()->delete();
                $order->delete();
                $this->created_order_number = null;
                $this->dispatch('notify', message: 'Ödeme sistemi ile iletişim kurulamadı. Lütfen tekrar deneyiniz.', type: 'error');
                return;
            }
            
            // Render kısmında iframe açılacak. Yönlendirme YAPMIYORUZ. Sepeti BURADA BOŞALTMIYORUZ.
            return;
        }

        // Redirect to success page (Kapıda ödeme)
        $this->redirect(route('order.success', [
            'order_number' => $order->order_number, 
            'method' => $this->payment_method
        ]));

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Sipariş oluşturma hatası: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payment_method' => $this->payment_method ?? null,
                'address_mode' => $this->address_mode ?? null,
            ]);
            $this->created_order_number = null;
            $this->dispatch('notify', message: 'Sipariş oluşturulurken bir hata oluştu: ' . $e->getMessage(), type: 'error');
        }
    }

    public function checkOrderStatus()
    {
        if ($this->created_order_number) {
            $order = \App\Models\Order::where('order_number', $this->created_order_number)->first();
            if ($order && $order->payment_status === 'paid') {
                $this->redirect(route('order.success', [
                    'order_number' => $this->created_order_number,
                    'method' => $order->payment_method === 'wire_transfer' ? 'wire_transfer' : 'cc'
                ]));
            }
        }
    }

    private function getPaytrToken(Order $order, $cartItems, $payment_method = 'credit_card')
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

        if ($payment_method === 'wire_transfer') {
            $hash_str = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .'eft' .$test_mode;
            $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

            $post_vals = [
                'merchant_id' => $merchant_id,
                'user_ip' => $user_ip,
                'merchant_oid' => $merchant_oid,
                'email' => $email,
                'payment_amount' => $payment_amount,
                'payment_type' => 'eft',
                'paytr_token' => $paytr_token,
                'debug_on' => 1,
                'timeout_limit' => 30,
                'test_mode' => $test_mode
            ];
        } else {
            $hash_str = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .$user_basket .$no_installment .$max_installment .$currency .$test_mode;
            $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

            $post_vals = [
                'merchant_id' => $merchant_id,
                'user_ip' => $user_ip,
                'merchant_oid' => $merchant_oid,
                'email' => $email,
                'payment_amount' => $payment_amount,
                'paytr_token' => $paytr_token,
                'user_basket' => $user_basket,
                'debug_on' => 1,
                'no_installment' => $no_installment,
                'max_installment' => $max_installment,
                'user_name' => $order->customer_name,
                'user_address' => $order->shipping_address . ' ' . $order->shipping_district . ' ' . $order->shipping_city,
                'user_phone' => $order->customer_phone,
                'merchant_ok_url' => route('order.success', [
                    'order_number' => $order->order_number,
                    'method' => 'cc'
                ]),
                'merchant_fail_url' => route('order.fail', ['order_number' => $order->order_number]),
                'timeout_limit' => 30,
                'currency' => $currency,
                'test_mode' => $test_mode
            ];
        }

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

    public function applyCoupon()
    {
        $this->coupon_message = '';
        $this->coupon_error = '';

        $code = strtoupper(trim($this->coupon_code));

        if (empty($code)) {
            $this->coupon_error = 'Lütfen bir kupon kodu giriniz.';
            return;
        }

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            $this->coupon_error = 'Geçersiz kupon kodu.';
            return;
        }

        if (!$coupon->status) {
            $this->coupon_error = 'Bu kupon kodu aktif değil.';
            return;
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            $this->coupon_error = 'Bu kupon kodunun süresi dolmuş.';
            return;
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            $this->coupon_error = 'Bu kupon kodunun kullanım limiti dolmuş.';
            return;
        }

        // Minimum sepet tutarı kontrolü
        $cartService = app(CartService::class);
        $subtotal = $cartService->getTotal();

        if ($coupon->min_cart_total && $subtotal < $coupon->min_cart_total) {
            $this->coupon_error = 'Bu kupon için minimum sepet tutarı ' . number_format($coupon->min_cart_total, 2) . ' ₺ olmalıdır.';
            return;
        }

        // Kupon geçerli — sakla, indirim render'da hesaplanacak
        $this->applied_coupon = $coupon;
        $this->coupon_message = 'Kupon kodu uygulandı! ' . ($coupon->type === 'percentage' ? '%' . intval($coupon->value) : number_format($coupon->value, 2) . ' ₺') . ' indirim kazandınız.';
        $this->coupon_error = '';
    }

    public function removeCoupon()
    {
        $this->applied_coupon = null;
        $this->coupon_discount = 0;
        $this->coupon_code = '';
        $this->coupon_message = '';
        $this->coupon_error = '';
    }

    public function editInformation()
    {
        $this->paytr_token = null;
        
        if ($this->created_order_number) {
            $order = Order::where('order_number', $this->created_order_number)->first();
            if ($order && $order->status === 'pending' && $order->payment_status === 'pending') {
                $order->items()->delete();
                $order->delete();
            }
            $this->created_order_number = null;
        }
    }

    public function render(CartService $cartService)
    {
        $cart = $cartService->getCart();
        $subtotal = $cartService->getTotal();
        $totalItems = $cart->items->sum('quantity');
        $shippingPrice = $this->payment_method === 'cash_on_delivery' ? (200 + (1 * $totalItems)) : (1 * $totalItems);
        
        // Kupon indirimi genel toplam üzerinden hesapla (kargo dahil)
        $couponDiscount = 0;
        if ($this->applied_coupon) {
            $totalBeforeDiscount = $subtotal + $shippingPrice;
            if ($this->applied_coupon->type === 'percentage') {
                $couponDiscount = round($totalBeforeDiscount * ($this->applied_coupon->value / 100), 2);
            } else {
                $couponDiscount = min($this->applied_coupon->value, $totalBeforeDiscount);
            }
            $this->coupon_discount = $couponDiscount;
        }
        $grandTotal = max(0, $subtotal + $shippingPrice - $couponDiscount);

        $taxOffices = [];
        if (file_exists(database_path('data/tax-office-names.json'))) {
            $taxOffices = json_decode(file_get_contents(database_path('data/tax-office-names.json')), true) ?: [];
        }

        return view('livewire.frontend.checkout', [
            'cartItems' => $cart->items,
            'subtotal' => $subtotal,
            'shippingPrice' => $shippingPrice,
            'couponDiscount' => $couponDiscount,
            'grandTotal' => $grandTotal,
            'taxOffices' => $taxOffices,
        ])->layout('components.layouts.app');
    }
}

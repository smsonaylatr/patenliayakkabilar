<?php

namespace App\Livewire\Frontend;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Illuminate\Support\Str;

class Checkout extends Component
{
    // Form alanları (wire:model - kullanıcı girişi)
    public string $customer_name = '';
    public string $customer_email = '';
    public string $customer_phone = '';
    public string $customer_note = '';
    
    public string $shipping_city = '';
    public string $shipping_district = '';
    public string $shipping_neighborhood = '';
    public string $shipping_address = '';
    
    // Fatura Bilgileri
    public string $invoice_type = 'individual';
    public string $company_name = '';
    public string $tax_office = '';
    public string $tax_number = '';

    public string $payment_method = 'credit_card';
    public bool $sms_consent = false;
    public bool $terms_consent = false;

    // Sunucu tarafında yönetilen veriler
    #[Locked]
    public array $cities = [];

    #[Locked]
    public array $districts = [];

    #[Locked]
    public array $neighborhoods = [];

    #[Locked]
    public ?string $paytr_token = null;

    #[Locked]
    public ?string $created_order_number = null;

    // Kupon kodu
    public string $coupon_code = '';

    #[Locked]
    public ?Coupon $applied_coupon = null;

    #[Locked]
    public float $coupon_discount = 0;

    #[Locked]
    public string $coupon_message = '';

    #[Locked]
    public string $coupon_error = '';

    protected function rules()
    {
        $baseRules = [
            'customer_name' => ['required', 'string', 'max:255', 'regex:/^\S+\s+\S+/'],
            'customer_email' => 'required|email|max:255',
            'customer_phone' => ['required', 'string', 'regex:/^(05[0-9]{9}|0 \\(5[0-9]{2}\\) [0-9]{3} [0-9]{2} [0-9]{2}|\\+90 \\(5[0-9]{2}\\) [0-9]{3} [0-9]{2} [0-9]{2}|90 \\(5[0-9]{2}\\) [0-9]{3} [0-9]{2} [0-9]{2})$/'],
            'shipping_city' => 'required|string|max:100',
            'shipping_district' => 'required|string|max:100',
            'shipping_neighborhood' => 'required|string|max:150',
            'shipping_address' => 'required|string',
            'invoice_type' => 'required|in:individual,corporate',
            'company_name' => 'required_if:invoice_type,corporate|nullable|string|max:255',
            'tax_office' => 'required_if:invoice_type,corporate|nullable|string|max:255',
            'tax_number' => 'required_if:invoice_type,corporate|nullable|string|max:20',
            'payment_method' => 'required|in:cash_on_delivery,wire_transfer,credit_card',
            'terms_consent' => 'accepted',
        ];

        return $baseRules;
    }

    protected $messages = [
        'customer_name.required' => 'Lütfen adınızı ve soyadınızı giriniz.',
        'customer_name.regex' => 'Lütfen hem adınızı hem soyadınızı giriniz.',
        'customer_email.required' => 'Lütfen e-posta adresinizi giriniz.',
        'customer_email.email' => 'Lütfen geçerli bir e-posta adresi giriniz.',
        'customer_phone.required' => 'Lütfen telefon numaranızı giriniz.',
        'customer_phone.regex' => 'Lütfen başında 0 olacak şekilde 11 haneli geçerli bir numara giriniz (Örn: 05551234567).',
        'shipping_city.required' => 'Lütfen teslimat adresinizi seçiniz.',
        'shipping_district.required' => 'Lütfen teslimat adresinizi seçiniz.',
        'shipping_neighborhood.required' => 'Lütfen mahallenizi seçiniz veya yazınız.',
        'shipping_address.required' => 'Lütfen açık adresinizi giriniz.',
        'terms_consent.accepted' => 'Devam etmek için Ön Bilgilendirme Formu ve Mesafeli Satış Sözleşmesi\'ni onaylamalısınız.',
        'company_name.required_if' => 'Kurumsal fatura seçtiğinizde firma adı zorunludur.',
        'tax_office.required_if' => 'Kurumsal fatura seçtiğinizde vergi dairesi zorunludur.',
        'tax_number.required_if' => 'Kurumsal fatura seçtiğinizde vergi numarası zorunludur.',
    ];

    #[Locked]
    public bool $isCodAllowed = true;

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
        $this->shipping_district = '';
        $this->shipping_neighborhood = '';
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
        $this->shipping_neighborhood = '';
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
            $cacheKey = 'district_neighborhoods_' . \Illuminate\Support\Str::slug($district) . '_' . \Illuminate\Support\Str::slug($this->shipping_city);
            $neighborhoods = \Illuminate\Support\Facades\Cache::get($cacheKey);

            if ($neighborhoods === null) {
                // cities.json'dan districtId bul
                $districtId = null;
                if (file_exists(database_path('data/cities.json'))) {
                    $json = json_decode(file_get_contents(database_path('data/cities.json')), true);
                    if (isset($json['data'])) {
                        $cityData = collect($json['data'])->firstWhere('name', $this->shipping_city);
                        if ($cityData && isset($cityData['districts'])) {
                            $districtData = collect($cityData['districts'])->firstWhere('name', $district);
                            if ($districtData) {
                                $districtId = $districtData['id'];
                            }
                        }
                    }
                }

                if ($districtId) {
                    $response = \Illuminate\Support\Facades\Http::timeout(8)->get('https://turkiyeapi.dev/api/v1/neighborhoods', [
                        'districtId' => $districtId
                    ]);

                    if ($response->successful()) {
                        $data = $response->json('data');
                        if (!empty($data) && is_array($data)) {
                            $neighborhoods = collect($data)->pluck('name')->sort()->values()->toArray();
                        }
                    }
                }

                // Sadece dolu sonuçları cache'le (30 gün)
                if (!empty($neighborhoods)) {
                    \Illuminate\Support\Facades\Cache::put($cacheKey, $neighborhoods, 86400 * 30);
                }
            }

            $this->neighborhoods = $neighborhoods ?: [];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Mahalle yükleme hatası ({$district}): " . $e->getMessage());
            $this->neighborhoods = [];
        }
    }


    public function placeOrder(CartService $cartService)
    {
        if ($this->paytr_token || $this->created_order_number) {
            return;
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

        // Create Order Items — Eager load ile ilişkileri yükle
        $cartItems = $cart->items()->with(['product', 'variant'])->get();

        // ÖN KONTROL: Sipariş oluşturmadan ÖNCE tüm stokları kontrol et
        foreach ($cartItems as $item) {
            if (!$item->product) {
                $order->delete();
                $this->created_order_number = null;
                $this->dispatch('notify', message: 'Sepetinizdeki bir ürün artık mevcut değil. Lütfen sepetinizi güncelleyiniz.', type: 'error');
                return;
            }

            if ($item->product_variant_id && $item->variant) {
                // Variant'ın güncel stoğunu DB'den taze çek
                $freshVariant = \App\Models\ProductVariant::find($item->product_variant_id);
                $currentStock = $freshVariant ? (int) $freshVariant->stock : 0;
                
                if ($currentStock < $item->quantity) {
                    $order->delete();
                    $this->created_order_number = null;
                    $productName = $item->product->name ?? 'Ürün';
                    $variantLabel = $item->variant->size ? " (Beden: {$item->variant->size})" : '';
                    
                    \Illuminate\Support\Facades\Log::warning("Stok yetersiz - sipariş iptal", [
                        'product' => $productName,
                        'variant_id' => $item->product_variant_id,
                        'variant_size' => $item->variant->size ?? null,
                        'requested_qty' => $item->quantity,
                        'available_stock' => $currentStock,
                        'customer' => $this->customer_name,
                    ]);

                    if ($currentStock > 0) {
                        // Stok var ama yetersiz — sepetteki miktarı güncelle
                        $item->update(['quantity' => $currentStock]);
                        $this->dispatch('notify', message: "{$productName}{$variantLabel} için stokta sadece {$currentStock} adet kaldı. Sepetiniz güncellendi, lütfen tekrar deneyiniz.", type: 'warning');
                    } else {
                        // Stok tamamen bitti — sepetten sil
                        $item->delete();
                        $this->dispatch('notify', message: "{$productName}{$variantLabel} tükenmiştir. Ürün sepetinizden çıkarıldı.", type: 'error');
                    }
                    $this->dispatch('cart-updated');
                    return;
                }
            } elseif ($item->product) {
                $freshProduct = Product::find($item->product_id);
                $currentStock = $freshProduct ? (int) $freshProduct->stock : 0;
                
                if ($currentStock < $item->quantity) {
                    $order->delete();
                    $this->created_order_number = null;
                    $productName = $item->product->name ?? 'Ürün';

                    \Illuminate\Support\Facades\Log::warning("Stok yetersiz (variantsız) - sipariş iptal", [
                        'product' => $productName,
                        'product_id' => $item->product_id,
                        'requested_qty' => $item->quantity,
                        'available_stock' => $currentStock,
                        'customer' => $this->customer_name,
                    ]);

                    if ($currentStock > 0) {
                        $item->update(['quantity' => $currentStock]);
                        $this->dispatch('notify', message: "{$productName} için stokta sadece {$currentStock} adet kaldı. Sepetiniz güncellendi, lütfen tekrar deneyiniz.", type: 'warning');
                    } else {
                        $item->delete();
                        $this->dispatch('notify', message: "{$productName} tükenmiştir. Ürün sepetinizden çıkarıldı.", type: 'error');
                    }
                    $this->dispatch('cart-updated');
                    return;
                }
            }
        }

        // Stok ön kontrolü geçti — order items oluştur (stok düşürme ödeme yöntemine göre yapılacak)
        // Observer'ı bypass et: Checkout akışında stok düşürme ödeme yöntemine göre
        // Checkout::decrementStockForOrder veya OrderObserver tarafından ayrıca yönetilir.
        // Observer yalnızca admin panelden manuel ekleme için çalışmalıdır.

        OrderItem::withoutEvents(function () use ($cartItems, $order) {
            foreach ($cartItems as $item) {
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
        });

        // Tüm ödeme yöntemleri için session'a sipariş numarasını kaydet
        session(['last_order_number' => $order->order_number]);
        $this->created_order_number = $order->order_number;

        // ======================================================================
        // STOK DÜŞÜRME STRATEJİSİ:
        // - Kapıda Ödeme: Stok HEMEN düşürülür (sipariş = onay)
        // - Kredi Kartı / Havale: Stok DÜŞÜRÜLMEZ! 
        //   PayTR webhook'tan payment_status=paid geldiğinde OrderObserver düşürür.
        // ======================================================================
        if ($this->payment_method === 'cash_on_delivery') {
            $this->decrementStockForOrder($order);

            // Redirect to success page (Kapıda ödeme)
            $this->redirect(route('order.success', [
                'order_number' => $order->order_number, 
                'method' => $this->payment_method
            ]));
            return;
        }

        // Kredi Kartı veya Havale/EFT — PayTR token al
        $this->paytr_token = $this->getPaytrToken($order, $cart->items, $this->payment_method);
        
        if (!$this->paytr_token) {
            // Token alınamadı — siparişi sil (stok düşürülmedi, geri eklemeye gerek yok)
            $order->items()->delete();
            $order->delete();
            $this->created_order_number = null;
            $this->dispatch('notify', message: 'Ödeme sistemi ile iletişim kurulamadı. Lütfen tekrar deneyiniz.', type: 'error');
            return;
        }
        
        // Render kısmında iframe açılacak. Stok düşürme PayTR onayına kadar bekleniyor.
        return;

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Sipariş oluşturma hatası: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payment_method' => $this->payment_method ?? null,
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

    /**
     * Siparişteki ürünlerin stoğunu düşür.
     * Kapıda ödeme → checkout'tan çağrılır
     * Kredi kartı/Havale → OrderObserver'dan çağrılır (payment_status=paid)
     */
    public static function decrementStockForOrder(Order $order): void
    {
        $order->loadMissing(['items.product', 'items.variant']);

        $failedItems = [];

        foreach ($order->items as $orderItem) {
            if ($orderItem->product_variant_id) {
                $variant = \App\Models\ProductVariant::find($orderItem->product_variant_id);
                if ($variant) {
                    $success = $variant->safeDecrement(
                        $orderItem->quantity,
                        $order->order_number,
                        "Ödeme onayı ile stok düşürüldü"
                    );
                    if ($success) {
                        $variant->product?->syncFromVariants();
                    } else {
                        $variantLabel = $variant->size ? " (Beden: {$variant->size})" : '';
                        $failedItems[] = "{$orderItem->product_name}{$variantLabel} — istenen: {$orderItem->quantity}, stok: {$variant->stock}";
                    }
                }
            } elseif ($orderItem->product) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $success = $product->safeDecrement(
                        $orderItem->quantity,
                        $order->order_number,
                        "Ödeme onayı ile stok düşürüldü"
                    );
                    if (!$success) {
                        $failedItems[] = "{$orderItem->product_name} — istenen: {$orderItem->quantity}, stok: {$product->stock}";
                    }
                }
            }
        }

        // Stok yetersizliği varsa admin'e bildirim gönder
        if (!empty($failedItems)) {
            $itemList = implode("\n", $failedItems);
            \Illuminate\Support\Facades\Log::warning("Stok yetersizliği — sipariş ödendi ama stok düşürülemedi", [
                'order_number' => $order->order_number,
                'failed_items' => $failedItems,
            ]);

            \Filament\Notifications\Notification::make()
                ->title('⚠️ Stok Yetersiz — Sipariş Kontrol Gerekli')
                ->body("#{$order->order_number} siparişinde stok düşürülemedi:\n{$itemList}")
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
        }

        \Illuminate\Support\Facades\Log::info("Stok düşürme tamamlandı", [
            'order_number' => $order->order_number,
            'payment_method' => $order->payment_method,
            'items_count' => $order->items->count(),
            'failed_count' => count($failedItems),
        ]);
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

    public function removeCartItem(int $cartItemId, CartService $cartService): void
    {
        // Ödeme başladıysa silmeye izin verme
        if ($this->paytr_token) {
            return;
        }

        $cartService->removeItem($cartItemId);

        // Sepet boşaldıysa sepet sayfasına yönlendir
        $cart = $cartService->getCart();
        if ($cart->items->count() === 0) {
            $this->dispatch('cart-updated');
            $this->redirect(route('cart'));
            return;
        }

        // Kapıda ödeme uygunluğunu yeniden kontrol et
        $this->isCodAllowed = true;
        foreach ($cart->items as $item) {
            if ($item->product && !$item->product->is_cod_active) {
                $this->isCodAllowed = false;
                break;
            }
        }
        if (!$this->isCodAllowed && $this->payment_method === 'cash_on_delivery') {
            $this->payment_method = 'credit_card';
        }

        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Ürün sepetten kaldırıldı.', type: 'success');
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

        return view('livewire.frontend.checkout', [
            'cartItems' => $cart->items,
            'subtotal' => $subtotal,
            'shippingPrice' => $shippingPrice,
            'couponDiscount' => $couponDiscount,
            'grandTotal' => $grandTotal,
        ])->layout('components.layouts.app');
    }
}

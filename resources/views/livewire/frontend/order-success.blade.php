<div x-data="{ showToast: false, toastMessage: '' }">
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

        {{-- ÜST: Sipariş Özeti Bar --}}
        <div class="flex items-center justify-between mb-6">
            <span class="text-sm font-semibold text-gray-500">Sipariş özeti</span>
            <span class="text-lg font-black text-gray-900">{{ number_format($order->grand_total, 2, ',', '.') }} ₺</span>
        </div>

        {{-- MASAÜSTÜ: 2 Kolon / MOBİL: Tek Kolon --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- SOL KOLON: Onay + Harita + Bilgiler --}}
            <div class="lg:col-span-3 space-y-4">

                {{-- ONAY BAŞLIĞI + HARİTA --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="border: 2px solid #3b82f6;">
                            <i class="fa-solid fa-check" style="color: #3b82f6; font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">{{ $order->order_number }} numaralı onaylama</p>
                            <h1 class="text-xl font-black text-gray-900 mt-0.5">
                                Teşekkür ederiz {{ explode(' ', $order->customer_name)[0] }}
                            </h1>
                        </div>
                    </div>

                    @php
                        $mapQuery = urlencode(
                            $order->shipping_address . ', '
                            . $order->shipping_district . ', '
                            . $order->shipping_city . ', Türkiye'
                        );
                    @endphp
                    <div class="rounded-xl overflow-hidden border border-gray-200 mb-5">
                        <div class="relative">
                            <iframe
                                src="https://www.google.com/maps?q={{ $mapQuery }}&z=16&output=embed&hl=tr"
                                width="100%"
                                height="200"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                class="w-full"
                            ></iframe>
                            <div class="absolute top-3 left-1/2 -translate-x-1/2 bg-white/95 backdrop-blur-sm rounded-lg px-4 py-2 shadow-md border border-gray-100 text-center">
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Kargo adresi</p>
                                <p class="text-sm font-bold text-gray-900">{{ $order->shipping_district }}, {{ $order->shipping_city }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <h3 class="text-base font-bold text-gray-900">Siparişiniz doğrulandı</h3>
                        <p class="text-sm text-gray-500 mt-1">Kısa süre içinde onay e-postası alacaksınız</p>
                    </div>
                </div>

                {{-- SİPARİŞ BİLGİLERİ --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-lg font-black text-gray-900 mb-5">Sipariş Bilgileri</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- İletişim --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-1.5">İletişim bilgileri</h4>
                            <p class="text-sm text-gray-500">{{ $order->customer_email }}</p>
                            @if($order->customer_phone)
                                <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                            @endif
                        </div>

                        {{-- Kargo yöntemi --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-1.5">Kargo yöntemi</h4>
                            <p class="text-sm text-gray-500">{{ $order->cargo_company ?? 'Standart' }}</p>
                        </div>

                        {{-- Ödeme yöntemi --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-1.5">Ödeme yöntemi</h4>
                            <p class="text-sm text-gray-500">
                                @if($order->payment_method === 'credit_card')
                                    Kredi Kartı · {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                                @elseif($order->payment_method === 'cash_on_delivery')
                                    Kapıda Ödeme · {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                                @elseif($order->payment_method === 'wire_transfer')
                                    Havale / EFT · {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                                @else
                                    {{ $order->payment_method }} · {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                                @endif
                            </p>
                        </div>

                        {{-- Kargo adresi --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-1.5">Kargo adresi</h4>
                            <div class="text-sm text-gray-500 leading-relaxed">
                                <p>{{ $order->customer_name }}</p>
                                <p>{{ $order->shipping_address }}</p>
                                <p>{{ $order->shipping_district }} / {{ $order->shipping_city }}</p>
                                <p>Türkiye</p>
                                @if($order->customer_phone)
                                    <p>{{ $order->customer_phone }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Fatura adresi --}}
                        @if($order->billing_address)
                            <div>
                                <h4 class="text-sm font-bold text-gray-700 mb-1.5">Fatura adresi</h4>
                                <div class="text-sm text-gray-500 leading-relaxed">
                                    <p>{{ $order->customer_name }}</p>
                                    <p>{{ $order->billing_address }}</p>
                                    <p>{{ $order->billing_district }} / {{ $order->billing_city }}</p>
                                    <p>Türkiye</p>
                                </div>
                            </div>
                        @endif

                        {{-- Kupon --}}
                        @if($order->coupon_code)
                            <div>
                                <h4 class="text-sm font-bold text-gray-700 mb-1.5">Kupon</h4>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-ticket" style="color: #16a34a;"></i>
                                    <span class="text-sm text-gray-500">
                                        {{ $order->coupon_code }}
                                        @if($order->discount_total > 0)
                                            · -{{ number_format($order->discount_total, 2, ',', '.') }} ₺
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SAĞ KOLON: Sipariş Detayları + Butonlar --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Sipariş Detayları --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-lg font-black text-gray-900 mb-4">Sipariş Detayları</h3>

                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-3">
                                @if($item->product && $item->product->images->count() > 0)
                                    <div class="relative flex-shrink-0">
                                        <img src="{{ Storage::url($item->product->images->first()->image_path) }}" class="w-14 h-14 rounded-lg object-cover border border-gray-200">
                                        <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-gray-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $item->quantity }}</span>
                                    </div>
                                @else
                                    <div class="relative flex-shrink-0">
                                        <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300">
                                            <i class="fa-solid fa-shoe-prints text-sm"></i>
                                        </div>
                                        <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-gray-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $item->quantity }}</span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product_name }}</p>
                                    @if($item->variant_info)
                                        <p class="text-xs text-gray-400">{{ $item->variant_info }}</p>
                                    @endif
                                </div>
                                <p class="text-sm font-bold text-gray-900 flex-shrink-0">{{ number_format($item->total_price, 2, ',', '.') }} ₺</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Toplam --}}
                    <div class="border-t border-gray-100 mt-4 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Ara toplam</span>
                            <span class="text-gray-700">{{ number_format($order->subtotal, 2, ',', '.') }} ₺</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Kargo</span>
                            <span class="text-gray-700">{{ $order->shipping_price > 0 ? number_format($order->shipping_price, 2, ',', '.') . ' ₺' : 'Ücretsiz' }}</span>
                        </div>
                        @if($order->discount_total > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-green-600">İndirim</span>
                                <span class="text-green-600 font-medium">-{{ number_format($order->discount_total, 2, ',', '.') }} ₺</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-base font-black pt-2 border-t border-gray-100">
                            <span class="text-gray-900">Toplam</span>
                            <span class="text-gray-900">{{ number_format($order->grand_total, 2, ',', '.') }} ₺</span>
                        </div>
                    </div>
                </div>

                {{-- Butonlar --}}
                <div class="space-y-3">
                    <a href="{{ route('home') }}" class="block w-full bg-black hover:bg-gray-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all active:scale-[0.98] text-center text-sm">
                        Alışverişe Devam Et
                    </a>
                    @if(auth()->check())
                        <a href="{{ route('account.orders') }}" class="block w-full bg-white hover:bg-gray-50 text-gray-700 font-bold py-4 px-6 rounded-xl transition-all border border-gray-200 text-center text-sm">
                            Siparişimi Görüntüle
                        </a>
                    @else
                        <a href="{{ route('order.tracking', ['order_number' => $order_number]) }}" class="block w-full bg-white hover:bg-gray-50 text-gray-700 font-bold py-4 px-6 rounded-xl transition-all border border-gray-200 text-center text-sm">
                            Siparişimi Takip Et
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ⭐ ZORUNLU YILDIZ PUANLAMA POP-UP --}}
@if(!$ratingsSubmitted)
<div
    x-data="{ open: true }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[9999]"
    role="dialog"
    aria-modal="true"
>
    <div
        x-show="open"
        x-transition:enter="ease-out duration-400"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-black/60 backdrop-blur-lg"
    ></div>

    <div class="fixed inset-0 flex items-center justify-center p-5 sm:p-8">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-6"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-xl bg-white rounded-3xl shadow-[0_25px_60px_rgba(0,0,0,0.25)] overflow-hidden"
        >
            <div class="h-1.5 bg-gradient-to-r from-amber-400 via-orange-400 to-red-400"></div>

            <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                <h3 class="text-xl font-black text-gray-900">Ürünü Değerlendir</h3>
            </div>

            <div class="max-h-[60vh] overflow-y-auto">
                @foreach($order->items as $item)
                    @if($item->product)
                        <div
                            x-data="{ hoverStar: 0 }"
                            class="px-6 sm:px-8 py-6 {{ !$loop->last ? 'border-b border-gray-100' : '' }}"
                        >
                            {{-- Ürün bilgisi --}}
                            <div class="flex items-start gap-4 mb-4">
                                @if($item->product->images->count() > 0)
                                    <img
                                        src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                        alt="{{ $item->product_name }}"
                                        class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl object-cover border border-gray-200 flex-shrink-0"
                                    >
                                @else
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 flex-shrink-0">
                                        <i class="fa-solid fa-shoe-prints text-xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0 pt-0.5">
                                    <h4 class="text-sm sm:text-base font-bold text-gray-900 leading-snug">{{ $item->product_name }}</h4>
                                    @if($item->variant_info)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $item->variant_info }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Açıklama --}}
                            <p class="text-sm text-gray-400 mb-3 text-center">Ürünü aşağıdan puanlayabilir ve yorum yazabilirsiniz</p>

                            {{-- ⭐ YILDIZLAR - BÜYÜK ve MERKEZ --}}
                            <div
                                class="flex items-center justify-center gap-3 sm:gap-4 py-4 mb-4"
                                @mouseleave="hoverStar = 0"
                            >
                                @for($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="setRating({{ $item->product_id }}, {{ $i }})"
                                        @mouseenter="hoverStar = {{ $i }}"
                                        class="focus:outline-none transition-all duration-200 ease-out active:scale-75"
                                        :class="(hoverStar >= {{ $i }} || (hoverStar === 0 && {{ ($ratings[$item->product_id] ?? 5) }} >= {{ $i }})) ? 'scale-110' : 'scale-100'"
                                    >
                                        <i
                                            class="fa-solid fa-star text-[3.5rem] sm:text-[4rem] transition-all duration-200 ease-out"
                                            :class="(hoverStar >= {{ $i }} || (hoverStar === 0 && {{ ($ratings[$item->product_id] ?? 5) }} >= {{ $i }})) ? 'text-amber-400 drop-shadow-[0_3px_8px_rgba(251,191,36,0.5)]' : 'text-gray-200'"
                                        ></i>
                                    </button>
                                @endfor
                            </div>

                            {{-- Yorum alanı --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-bold text-gray-700">Yorumunu Yaz</label>
                                    <span class="text-xs text-gray-400">(İsteğe bağlı)</span>
                                </div>
                                <textarea
                                    wire:model="comments.{{ $item->product_id }}"
                                    rows="3"
                                    maxlength="2000"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-sm px-4 py-3.5 placeholder:text-gray-300 transition-all resize-none"
                                    placeholder="Ürün hakkındaki deneyiminizi paylaşın..."
                                ></textarea>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Gönder butonu - LACİVERT --}}
            <div class="px-6 sm:px-8 py-5 border-t border-gray-100">
                <button
                    type="button"
                    wire:click="submitRatings"
                    wire:loading.attr="disabled"
                    x-on:click="setTimeout(() => { if ($wire.ratingsSubmitted) { open = false; $dispatch('show-toast', { message: 'Puanlamanız kaydedildi. Teşekkür ederiz! ⭐' }); } }, 600)"
                    class="w-full py-4 rounded-2xl bg-[#1a2744] hover:bg-[#0f1a30] text-white text-base font-extrabold shadow-xl shadow-blue-900/20 transition-all active:scale-[0.97] disabled:opacity-50 tracking-wide"
                >
                    <span wire:loading.remove wire:target="submitRatings">Gönder</span>
                    <span wire:loading wire:target="submitRatings" class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                        Gönderiliyor...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- 🔔 TOAST BİLDİRİMİ --}}
<div
    x-show="showToast"
    x-cloak
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-4"
    @show-toast.window="showToast = true; toastMessage = $event.detail.message; setTimeout(() => { showToast = false }, 5000)"
    class="fixed top-6 left-1/2 -translate-x-1/2 z-[99999] w-[90%] max-w-md"
>
    <div class="bg-gray-900 text-white rounded-2xl px-5 py-4 shadow-2xl shadow-black/20 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-check text-sm"></i>
        </div>
        <p class="text-sm font-medium flex-1" x-text="toastMessage"></p>
    </div>
</div>

<!-- Google Customer Reviews Opt-in -->
<script>
  window.triggerGoogleOptIn = function() {
    window.gapi.load('surveyoptin', function() {
      window.gapi.surveyoptin.render({
        "merchant_id": 5828544730,
        "order_id": "{{ $order->order_number }}",
        "email": "{{ $order->customer_email }}",
        "delivery_country": "TR",
        "estimated_delivery_date": "{{ \Carbon\Carbon::now()->addDays(3)->format('Y-m-d') }}",
        "opt_in_style": "CENTER_DIALOG"
      });
    });
  }
</script>

<!-- Google Ads Conversion Data -->
<script>
    window.googleAdsConversionData = {
        transaction_id: "{{ $order->order_number }}",
        value: {{ number_format($order->total_amount ?? 0, 2, '.', '') }},
        currency: "TRY"
    };
</script>
</div>

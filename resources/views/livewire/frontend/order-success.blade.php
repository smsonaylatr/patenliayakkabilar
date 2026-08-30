<div>
<div class="min-h-screen bg-gray-50">
    <div class="max-w-lg mx-auto px-4 py-6 sm:py-10">

        {{-- ÜST: Sipariş Özeti Bar --}}
        <div class="flex items-center justify-between mb-6">
            <span class="text-sm font-semibold text-gray-500">Sipariş özeti</span>
            <span class="text-lg font-black text-gray-900">{{ number_format($order->grand_total, 2, ',', '.') }} ₺</span>
        </div>

        {{-- ONAY BAŞLIĞI --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-4 shadow-sm">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-14 h-14 rounded-full border-2 border-blue-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400">{{ $order->order_number }} numaralı onaylama</p>
                    <h1 class="text-xl font-black text-gray-900 mt-0.5">
                        Teşekkür ederiz {{ explode(' ', $order->customer_name)[0] }} 🎉
                    </h1>
                </div>
            </div>

            {{-- HARİTA --}}
            @php
                $mapAddress = collect([
                    $order->shipping_address,
                    $order->shipping_neighborhood ?? null,
                    $order->shipping_district,
                    $order->shipping_city,
                    'Türkiye'
                ])->filter()->implode(', ');
                $mapQuery = urlencode($mapAddress);
            @endphp
            <div class="rounded-xl overflow-hidden border border-gray-200 mb-5">
                <div class="relative">
                    <iframe
                        src="https://maps.google.com/maps?q={{ $mapQuery }}&z=14&output=embed&hl=tr"
                        width="100%"
                        height="200"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="w-full"
                    ></iframe>
                    <div class="absolute top-3 left-1/2 -translate-x-1/2 bg-white/95 backdrop-blur-sm rounded-lg px-4 py-2 shadow-md border border-gray-100 text-center">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Kargo adresi</p>
                        <p class="text-sm font-bold text-gray-900">{{ $order->shipping_district }}, {{ $order->shipping_city }}</p>
                    </div>
                </div>
            </div>

            {{-- ONAY MESAJI --}}
            <div class="border-t border-gray-100 pt-4">
                <h3 class="text-base font-bold text-gray-900">Siparişiniz doğrulandı</h3>
                <p class="text-sm text-gray-500 mt-1">Kısa süre içinde onay e-postası alacaksınız</p>
            </div>
        </div>

        {{-- SİPARİŞ BİLGİLERİ --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-4 shadow-sm">
            <h3 class="text-lg font-black text-gray-900 mb-5">Sipariş Bilgileri</h3>

            {{-- İletişim --}}
            <div class="mb-5">
                <h4 class="text-sm font-bold text-gray-700 mb-1">İletişim bilgileri</h4>
                <p class="text-sm text-gray-500">{{ $order->customer_email }}</p>
                @if($order->customer_phone)
                    <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                @endif
            </div>

            {{-- Kargo adresi --}}
            <div class="mb-5">
                <h4 class="text-sm font-bold text-gray-700 mb-1">Kargo adresi</h4>
                <div class="text-sm text-gray-500 leading-relaxed">
                    <p>{{ $order->customer_name }}</p>
                    <p>{{ $order->shipping_address }}</p>
                    @if($order->shipping_neighborhood)
                        <p>{{ $order->shipping_neighborhood }}</p>
                    @endif
                    <p>{{ $order->shipping_district }} / {{ $order->shipping_city }}</p>
                    <p>Türkiye</p>
                </div>
            </div>

            {{-- Ödeme yöntemi --}}
            <div class="mb-5">
                <h4 class="text-sm font-bold text-gray-700 mb-1">Ödeme yöntemi</h4>
                <div class="flex items-center gap-2">
                    @if($order->payment_method === 'credit_card')
                        <div class="w-8 h-5 bg-gradient-to-r from-blue-600 to-blue-400 rounded flex items-center justify-center">
                            <i class="fa-regular fa-credit-card text-white text-[10px]"></i>
                        </div>
                        <span class="text-sm text-gray-500">Kredi Kartı · {{ number_format($order->grand_total, 2, ',', '.') }} ₺</span>
                    @elseif($order->payment_method === 'cash_on_delivery')
                        <div class="w-8 h-5 bg-green-500 rounded flex items-center justify-center">
                            <i class="fa-solid fa-money-bill-wave text-white text-[10px]"></i>
                        </div>
                        <span class="text-sm text-gray-500">Kapıda Ödeme · {{ number_format($order->grand_total, 2, ',', '.') }} ₺</span>
                    @elseif($order->payment_method === 'wire_transfer')
                        <div class="w-8 h-5 bg-indigo-500 rounded flex items-center justify-center">
                            <i class="fa-solid fa-building-columns text-white text-[10px]"></i>
                        </div>
                        <span class="text-sm text-gray-500">Havale / EFT · {{ number_format($order->grand_total, 2, ',', '.') }} ₺</span>
                    @endif
                </div>
            </div>

            {{-- Fatura adresi --}}
            @if($order->billing_address)
                <div class="mb-5">
                    <h4 class="text-sm font-bold text-gray-700 mb-1">Fatura adresi</h4>
                    <div class="text-sm text-gray-500 leading-relaxed">
                        <p>{{ $order->customer_name }}</p>
                        <p>{{ $order->billing_address }}</p>
                        <p>{{ $order->billing_district }} / {{ $order->billing_city }}</p>
                        <p>Türkiye</p>
                    </div>
                </div>
            @endif

            {{-- Sipariş detayları --}}
            <div class="border-t border-gray-100 pt-5">
                <h4 class="text-sm font-bold text-gray-700 mb-3">Sipariş detayları</h4>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-3">
                            @if($item->product && $item->product->images->count() > 0)
                                <div class="relative flex-shrink-0">
                                    <img src="{{ Storage::url($item->product->images->first()->image_path) }}" class="w-14 h-14 rounded-lg object-cover border border-gray-200">
                                    <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-gray-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $item->quantity }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product_name }}</p>
                                @if($item->variant_info)
                                    <p class="text-xs text-gray-400">{{ $item->variant_info }}</p>
                                @endif
                            </div>
                            <p class="text-sm font-bold text-gray-900 flex-shrink-0">{{ number_format($item->price * $item->quantity, 2, ',', '.') }} ₺</p>
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
        </div>

        @if($ratingsSubmitted)
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium flex items-center justify-center gap-2 mb-4">
                <i class="fa-solid fa-circle-check"></i>
                Puanlamanız kaydedildi. Teşekkür ederiz! ⭐
            </div>
        @endif

        {{-- BUTONLAR --}}
        <div class="space-y-3 mt-6 pb-8">
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
            {{-- Üst gradient şerit --}}
            <div class="h-1.5 bg-gradient-to-r from-amber-400 via-orange-400 to-red-400"></div>

            {{-- Başlık --}}
            <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                <h3 class="text-xl font-black text-gray-900">Ürünü Değerlendir</h3>
            </div>

            {{-- İçerik --}}
            <div class="max-h-[60vh] overflow-y-auto">
                @foreach($order->items as $item)
                    @if($item->product)
                        <div
                            x-data="{ hoverStar: 0 }"
                            class="px-6 sm:px-8 py-6 {{ !$loop->last ? 'border-b border-gray-100' : '' }}"
                        >
                            {{-- Ürün bilgisi --}}
                            <div class="flex items-start gap-4 sm:gap-5 mb-5">
                                @if($item->product->images->count() > 0)
                                    <img
                                        src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                        alt="{{ $item->product_name }}"
                                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border border-gray-200 flex-shrink-0 shadow-sm"
                                    >
                                @else
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-300 flex-shrink-0">
                                        <i class="fa-solid fa-shoe-prints text-2xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0 pt-1">
                                    <h4 class="text-base font-bold text-gray-900 leading-snug">{{ $item->product_name }}</h4>
                                    @if($item->variant_info)
                                        <p class="text-sm text-gray-400 mt-1">{{ $item->variant_info }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Açıklama --}}
                            <p class="text-sm text-gray-400 mb-4 text-center">Ürünü aşağıdan puanlayabilir ve yorum yazabilirsiniz</p>

                            {{-- Yıldızlar - BÜYÜK, ORTALI, HOVER EFEKTLİ --}}
                            <div
                                class="flex items-center justify-center gap-2 sm:gap-3 py-3 mb-5"
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
                                            class="fa-solid fa-star text-[3rem] sm:text-[3.5rem] transition-all duration-200 ease-out"
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
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 text-sm px-4 py-3.5 placeholder:text-gray-300 transition-all resize-none"
                                    placeholder="Ürün hakkındaki deneyiminizi paylaşın..."
                                ></textarea>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Gönder butonu --}}
            <div class="px-6 sm:px-8 py-5 border-t border-gray-100">
                <button
                    type="button"
                    wire:click="submitRatings"
                    wire:loading.attr="disabled"
                    x-on:click="setTimeout(() => { if ($wire.ratingsSubmitted) open = false }, 600)"
                    class="w-full py-4 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-base font-extrabold shadow-xl shadow-orange-300/40 transition-all active:scale-[0.97] disabled:opacity-50 tracking-wide"
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

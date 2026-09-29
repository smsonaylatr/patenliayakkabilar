<div x-data="{ showToast: false, toastMessage: '' }">
<div class="bg-gray-50">
    <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 space-y-3.5 sm:space-y-5">

        {{-- ÜST: Sipariş Özeti Bar --}}
        <div class="flex items-center justify-between px-1">
            <span class="text-xs sm:text-sm font-semibold text-gray-500">Sipariş özeti</span>
            <span class="text-base sm:text-lg font-black text-gray-900">{{ number_format($order->grand_total, 2, ',', '.') }} ₺</span>
        </div>

        {{-- 1. KART: ONAY BAŞLIĞI + HARİTA --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-3.5 sm:p-5 md:p-6 shadow-xs sm:shadow-sm">
            <div class="flex items-start gap-3 sm:gap-4 mb-4 sm:mb-5">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center flex-shrink-0" style="border: 2px solid #3b82f6;">
                    <i class="fa-solid fa-check" style="color: #3b82f6; font-size: 1rem;"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm text-gray-400 truncate">{{ $order->order_number }} numaralı onaylama</p>
                    <h1 class="text-lg sm:text-xl font-black text-gray-900 mt-0.5 truncate">
                        Teşekkür ederiz {{ explode(' ', $order->customer_name)[0] }}
                    </h1>
                </div>
            </div>

            @php
                // Adresteki daire/blok/no gibi detayları temizle (geocoding'i bozuyorlar)
                $cleanAddress = $order->shipping_address;
                $cleanAddress = preg_replace('/\b(no|no\.|no:)\s*\d+\w*/iu', '', $cleanAddress);
                $cleanAddress = preg_replace('/\b(blok|daire|kat|apt|apartman|rezidans|site)\s*\w*/iu', '', $cleanAddress);
                $cleanAddress = preg_replace('/\bK\d+\b/i', '', $cleanAddress);
                $cleanAddress = preg_replace('/\s+/', ' ', trim($cleanAddress));
                $cleanAddress = rtrim($cleanAddress, ', .');

                $mapQuery = urlencode($cleanAddress . ', ' . $order->shipping_district . ', ' . $order->shipping_city);
            @endphp
            <div class="rounded-xl overflow-hidden border border-gray-200 mb-4 sm:mb-5 relative">
                <iframe
                    src="https://www.google.com/maps?q={{ $mapQuery }}&z=16&output=embed&hl=tr"
                    width="100%"
                    style="border: 0; width: 100%;"
                    class="w-full order-success-map"
                    allowfullscreen=""
                    loading="lazy"
                ></iframe>
                <div class="absolute top-2.5 sm:top-3 left-1/2 -translate-x-1/2 bg-white/95 backdrop-blur-sm rounded-lg px-3 py-1.5 sm:px-4 sm:py-2 shadow-sm sm:shadow-md border border-gray-100 text-center max-w-[90%] truncate">
                    <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Kargo adresi</p>
                    <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $order->shipping_district }}, {{ $order->shipping_city }}</p>
                </div>
            </div>
            <style>
                .order-success-map { height: 140px !important; }
                @media (min-width: 640px) { .order-success-map { height: 200px !important; } }
                @media (min-width: 1024px) { .order-success-map { height: 280px !important; } }
            </style>

            <div class="border-t border-gray-100 pt-3 sm:pt-4">
                <h3 class="text-sm sm:text-base font-bold text-gray-900">Siparişiniz doğrulandı</h3>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1">Kısa süre içinde onay e-postası alacaksınız</p>
            </div>
        </div>

        {{-- 2. KART: SİPARİŞ BİLGİLERİ --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-3.5 sm:p-5 md:p-6 shadow-xs sm:shadow-sm">
            <h3 class="text-base sm:text-lg font-black text-gray-900 mb-3 sm:mb-4">Sipariş Bilgileri</h3>

            {{-- İletişim + Kargo yöntemi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 pb-3 sm:pb-4 border-b border-gray-100">
                <div class="min-w-0">
                    <h4 class="text-xs sm:text-sm font-bold text-gray-700 mb-0.5 sm:mb-1">İletişim bilgileri</h4>
                    <p class="text-xs sm:text-sm text-gray-500 break-all">{{ $order->customer_email }}</p>
                    @if($order->customer_phone)
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">{{ $order->customer_phone }}</p>
                    @endif
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gray-700 mb-0.5 sm:mb-1">Kargo yöntemi</h4>
                    <p class="text-xs sm:text-sm text-gray-500">Standart Teslimat</p>
                </div>
            </div>

            {{-- Ödeme yöntemi + Kargo firması (+ Kupon) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 py-3 sm:py-4 border-b border-gray-100">
                <div class="min-w-0">
                    <h4 class="text-xs sm:text-sm font-bold text-gray-700 mb-0.5 sm:mb-1">Ödeme yöntemi</h4>
                    <p class="text-xs sm:text-sm text-gray-500">
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
                    @if($order->coupon_code)
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <i class="fa-solid fa-ticket text-xs" style="color: #16a34a;"></i>
                            <span class="text-xs text-gray-500 truncate">
                                Kupon: <span class="font-medium text-gray-700">{{ $order->coupon_code }}</span>
                                @if($order->discount_total > 0)
                                    (-{{ number_format($order->discount_total, 2, ',', '.') }} ₺)
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gray-700 mb-0.5 sm:mb-1">Kargo firması</h4>
                    <p class="text-xs sm:text-sm text-gray-500">
                        @php
                            $cargoName = $order->cargo_company;
                            if (empty($cargoName) || stripos($cargoName, 'porego') !== false) {
                                $cargoName = 'DHL eCommerce';
                            }
                        @endphp
                        {{ $cargoName }}
                    </p>
                </div>
            </div>

            {{-- Kargo adresi + Fatura adresi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4 pt-3 sm:pt-4">
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gray-700 mb-0.5 sm:mb-1">Kargo adresi</h4>
                    <div class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                        <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
                        <p class="break-words">{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_district }} / {{ $order->shipping_city }}</p>
                        <p>Türkiye</p>
                        @if($order->customer_phone)
                            <p>{{ $order->customer_phone }}</p>
                        @endif
                    </div>
                </div>
                @if($order->billing_address)
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-gray-700 mb-0.5 sm:mb-1">Fatura adresi</h4>
                        <div class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                            <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
                            <p class="break-words">{{ $order->billing_address }}</p>
                            <p>{{ $order->billing_district }} / {{ $order->billing_city }}</p>
                            <p>Türkiye</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- 3. KART: SİPARİŞ DETAYLARI --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-3.5 sm:p-5 md:p-6 shadow-xs sm:shadow-sm">
            <h3 class="text-base sm:text-lg font-black text-gray-900 mb-3 sm:mb-4">Sipariş Detayları</h3>

            <div class="space-y-3">
                @foreach($order->items as $item)
                    <div class="flex items-center gap-2.5 sm:gap-3.5">
                        @if($item->product && $item->product->images->count() > 0)
                            <div class="relative flex-shrink-0">
                                <img src="{{ Storage::url($item->product->images->first()->image_path) }}" class="rounded-xl object-cover border border-gray-200 w-16 h-16 sm:w-20 sm:h-20">
                                <span class="absolute -top-1.5 -right-1.5 w-4.5 h-4.5 sm:w-5 sm:h-5 bg-gray-600 text-white text-[9px] sm:text-[10px] font-bold rounded-full flex items-center justify-center">{{ $item->quantity }}</span>
                            </div>
                        @else
                            <div class="relative flex-shrink-0">
                                <div class="rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 w-16 h-16 sm:w-20 sm:h-20">
                                    <i class="fa-solid fa-shoe-prints text-sm"></i>
                                </div>
                                <span class="absolute -top-1.5 -right-1.5 w-4.5 h-4.5 sm:w-5 sm:h-5 bg-gray-600 text-white text-[9px] sm:text-[10px] font-bold rounded-full flex items-center justify-center">{{ $item->quantity }}</span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 truncate leading-snug">{{ $item->product_name }}</p>
                            @if($item->variant_info)
                                <p class="text-[11px] sm:text-xs text-gray-400 mt-0.5 truncate">{{ $item->variant_info }}</p>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm font-black text-gray-900 flex-shrink-0">{{ number_format($item->total_price, 2, ',', '.') }} ₺</p>
                    </div>
                @endforeach
            </div>

            {{-- Toplam --}}
            <div class="border-t border-gray-100 mt-3.5 sm:mt-4 pt-3.5 sm:pt-4 space-y-1.5 sm:space-y-2">
                <div class="flex justify-between text-xs sm:text-sm">
                    <span class="text-gray-500">Ara toplam</span>
                    <span class="text-gray-700 font-medium">{{ number_format($order->subtotal, 2, ',', '.') }} ₺</span>
                </div>
                <div class="flex justify-between text-xs sm:text-sm">
                    <span class="text-gray-500">Kargo</span>
                    <span class="text-gray-700 font-medium">{{ $order->shipping_price > 0 ? number_format($order->shipping_price, 2, ',', '.') . ' ₺' : 'Ücretsiz' }}</span>
                </div>
                @if($order->discount_total > 0)
                    <div class="flex justify-between text-xs sm:text-sm">
                        <span class="text-green-600 font-medium">İndirim</span>
                        <span class="text-green-600 font-semibold">-{{ number_format($order->discount_total, 2, ',', '.') }} ₺</span>
                    </div>
                @endif
                <div class="flex justify-between text-sm sm:text-base font-black pt-2 border-t border-gray-100">
                    <span class="text-gray-900">Toplam</span>
                    <span class="text-gray-900">{{ number_format($order->grand_total, 2, ',', '.') }} ₺</span>
                </div>
            </div>
        </div>

        {{-- 4. BUTONLAR --}}
        <div class="space-y-2.5 sm:space-y-3 pt-1">
            <a href="{{ route('home') }}" class="block w-full bg-black hover:bg-gray-800 text-white font-bold py-3 sm:py-3.5 px-4 sm:px-6 rounded-xl shadow-md transition-all active:scale-[0.98] text-center text-xs sm:text-sm">
                Alışverişe Devam Et
            </a>
            @if(auth()->check())
                <a href="{{ route('account.orders') }}" class="block w-full bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 sm:py-3.5 px-4 sm:px-6 rounded-xl transition-all border border-gray-200 text-center text-xs sm:text-sm shadow-2xs">
                    Siparişimi Görüntüle
                </a>
            @else
                <a href="{{ route('order.tracking', ['order_number' => $order_number]) }}" class="block w-full bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 sm:py-3.5 px-4 sm:px-6 rounded-xl transition-all border border-gray-200 text-center text-xs sm:text-sm shadow-2xs">
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

    <div class="fixed inset-0 flex items-center justify-center p-2.5 sm:p-6 overflow-y-auto">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-6"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md sm:max-w-lg bg-white rounded-2xl sm:rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.15),0_0_0_1px_rgba(0,0,0,0.04)] overflow-hidden flex flex-col max-h-[90vh] z-10"
        >
            {{-- Üst gradient bar - Marka uyumlu sıcak amber --}}
            <div class="h-1 bg-gradient-to-r from-amber-400 via-yellow-400 to-amber-500 flex-shrink-0"></div>

            {{-- Header - İkon + başlık + sipariş no --}}
            <div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-amber-100/40 bg-gradient-to-b from-amber-50/60 to-white flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-500 flex-shrink-0 shadow-sm shadow-amber-200/50">
                        <i class="fa-solid fa-star text-sm sm:text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-gray-900 text-left leading-tight">Ürünü Değerlendir</h3>
                        <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5">Sipariş #{{ $order->order_number }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-y-auto flex-1 px-4 sm:px-8 py-4 sm:py-6">
                @foreach($order->items as $item)
                    @if($item->product)
                        <div
                            x-data="{ hoverStar: 0 }"
                            class="{{ !$loop->last ? 'pb-5 sm:pb-6 mb-5 sm:mb-6 border-b border-gray-100' : '' }}"
                        >
                            {{-- Ürün bilgisi kartı --}}
                            <div class="flex items-center text-left gap-3 sm:gap-4 mb-4 sm:mb-5 p-2.5 sm:p-3 rounded-xl bg-gray-50/80 border border-gray-100/80">
                                @if($item->product->images->count() > 0)
                                    <img
                                        src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                        alt="{{ $item->product_name }}"
                                        class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg sm:rounded-xl object-cover border border-gray-200/80 flex-shrink-0 shadow-sm"
                                    >
                                @else
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg sm:rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 flex-shrink-0">
                                        <i class="fa-solid fa-shoe-prints text-lg"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm sm:text-base font-bold text-gray-900 leading-snug line-clamp-2">{{ $item->product_name }}</h4>
                                    @if($item->variant_info)
                                        <p class="text-[11px] sm:text-xs text-gray-400 mt-0.5 truncate">{{ $item->variant_info }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Puan bölümü başlığı --}}
                            <p class="text-xs sm:text-sm text-gray-500 mb-2 text-center font-medium">Deneyiminizi puanlayın</p>

                            {{-- ⭐ YILDIZLAR --}}
                            <div
                                class="flex items-center justify-center gap-1.5 sm:gap-2 py-2 sm:py-3 mb-1 sm:mb-1.5"
                                @mouseleave="hoverStar = 0"
                            >
                                @for($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="setRating({{ $item->product_id }}, {{ $i }})"
                                        @mouseenter="hoverStar = {{ $i }}"
                                        aria-label="{{ $i }} yıldız"
                                        class="focus:outline-none transition-all duration-200 p-0.5 cursor-pointer active:scale-90 group"
                                    >
                                        <svg
                                            class="w-9 h-9 sm:w-11 sm:h-11 transition-all duration-200"
                                            :class="(hoverStar >= {{ $i }} || (hoverStar === 0 && {{ ($ratings[$item->product_id] ?? 5) }} >= {{ $i }})) ? 'scale-110 drop-shadow-[0_3px_6px_rgba(245,158,11,0.35)]' : 'scale-100 opacity-40'"
                                            viewBox="0 0 24 24"
                                            :fill="(hoverStar >= {{ $i }} || (hoverStar === 0 && {{ ($ratings[$item->product_id] ?? 5) }} >= {{ $i }})) ? '#f59e0b' : '#d1d5db'"
                                            style="display: block;"
                                        >
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>

                            {{-- Puan etiketi --}}
                            @php $currentRating = $ratings[$item->product_id] ?? 5; @endphp
                            <p class="text-center text-xs font-semibold mb-4 sm:mb-5 transition-all duration-200
                                {{ $currentRating >= 5 ? 'text-amber-500' : ($currentRating >= 4 ? 'text-amber-500' : ($currentRating >= 3 ? 'text-yellow-600' : ($currentRating >= 2 ? 'text-orange-500' : 'text-red-400'))) }}
                            "
                                x-text="
                                    (hoverStar || {{ $currentRating }}) >= 5 ? 'Mükemmel ✨' :
                                    (hoverStar || {{ $currentRating }}) >= 4 ? 'Çok İyi 👍' :
                                    (hoverStar || {{ $currentRating }}) >= 3 ? 'İyi 🙂' :
                                    (hoverStar || {{ $currentRating }}) >= 2 ? 'Kötü 😕' : 'Çok Kötü 😞'
                                "
                            >
                                {{ $currentRating >= 5 ? 'Mükemmel ✨' : ($currentRating >= 4 ? 'Çok İyi 👍' : ($currentRating >= 3 ? 'İyi 🙂' : ($currentRating >= 2 ? 'Kötü 😕' : 'Çok Kötü 😞'))) }}
                            </p>

                            {{-- Yorum alanı --}}
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="text-[11px] sm:text-sm font-bold text-gray-600">Yorumunuz</label>
                                    <span class="text-[10px] sm:text-xs text-gray-400 italic">(İsteğe bağlı)</span>
                                </div>
                                <textarea
                                    wire:model="comments.{{ $item->product_id }}"
                                    rows="2"
                                    maxlength="2000"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50/80 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 text-sm px-3.5 py-2.5 placeholder:text-gray-300 transition-all resize-none"
                                    placeholder="Bu ürün hakkında ne düşünüyorsunuz?"
                                ></textarea>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Gönder butonu - Amber/Gold --}}
            <div class="px-4 sm:px-8 py-3.5 sm:py-5 border-t border-gray-100 bg-gradient-to-t from-amber-50/40 to-white flex-shrink-0">
                <button
                    type="button"
                    wire:click="submitRatings"
                    wire:loading.attr="disabled"
                    x-on:click="setTimeout(() => { if ($wire.ratingsSubmitted) { open = false; $dispatch('show-toast', { message: 'Puanlamanız kaydedildi. Teşekkür ederiz! ⭐' }); } }, 600)"
                    class="w-full py-3 sm:py-3.5 rounded-xl sm:rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-xs sm:text-sm font-extrabold shadow-lg shadow-amber-300/30 transition-all hover:shadow-amber-400/40 active:scale-[0.97] disabled:opacity-50 tracking-wide cursor-pointer flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove wire:target="submitRatings" class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane text-xs sm:text-sm"></i>
                        <span>Değerlendirmeyi Gönder</span>
                    </span>
                    <span wire:loading wire:target="submitRatings" class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs sm:text-sm"></i>
                        <span>Gönderiliyor...</span>
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

<!-- Google Ads Conversion Data -->
<script>
    window.googleAdsConversionData = {
        transaction_id: "{{ $order->order_number }}",
        value: {{ number_format($order->total_amount ?? 0, 2, '.', '') }},
        currency: "TRY"
    };
</script>
</div>

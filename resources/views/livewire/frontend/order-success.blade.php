<div x-data="{ showToast: false, toastMessage: '', showConfetti: true }"
     x-init="setTimeout(() => showConfetti = false, 4000)">

{{-- 🎊 KONFETİ ANİMASYONU --}}
<div x-show="showConfetti" x-transition:leave="ease-in duration-1000" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 pointer-events-none overflow-hidden">
    @for($c = 0; $c < 40; $c++)
        @php
            $colors = ['#FF7A1A', '#ffb800', '#22C55E', '#3b82f6', '#a855f7', '#ec4899'];
            $color = $colors[$c % count($colors)];
            $left = rand(0, 100);
            $delay = rand(0, 20) / 10;
            $size = rand(6, 12);
            $duration = rand(25, 45) / 10;
        @endphp
        <div
            style="position:absolute; left:{{ $left }}%; top:-20px; width:{{ $size }}px; height:{{ $size }}px; background:{{ $color }}; border-radius:{{ rand(0,1) ? '50%' : '2px' }}; animation: confetti-fall {{ $duration }}s ease-in {{ $delay }}s forwards; opacity:0;"
        ></div>
    @endfor
</div>
<style>
    @keyframes confetti-fall {
        0% { opacity: 1; transform: translateY(0) rotate(0deg) scale(1); }
        100% { opacity: 0; transform: translateY(100vh) rotate({{ rand(180,720) }}deg) scale(0.5); }
    }
    @keyframes success-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
        50% { box-shadow: 0 0 0 16px rgba(34, 197, 94, 0); }
    }
    @keyframes check-draw {
        0% { stroke-dashoffset: 50; opacity: 0; }
        50% { opacity: 1; }
        100% { stroke-dashoffset: 0; opacity: 1; }
    }
    @keyframes slide-up {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 space-y-3.5 sm:space-y-5">

        {{-- ÜST: Sipariş Özeti Bar --}}
        <div class="flex items-center justify-between px-1" style="animation: slide-up 0.5s ease-out;">
            <span class="text-xs sm:text-sm font-semibold text-gray-500">Sipariş özeti</span>
            <span class="text-base sm:text-lg font-black text-gray-900">{{ number_format($order->grand_total, 2, ',', '.') }} ₺</span>
        </div>

        {{-- 1. KART: ONAY BAŞLIĞI + HARİTA --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs sm:shadow-sm" style="animation: slide-up 0.6s ease-out;">

            {{-- Gradient başlık alanı --}}
            <div class="relative overflow-hidden px-4 sm:px-6 md:px-8 pt-6 sm:pt-8 pb-5 sm:pb-7" style="background: linear-gradient(135deg, #FFF7ED 0%, #FEF3C7 50%, #ECFDF5 100%);">
                {{-- Dekoratif daireler --}}
                <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full opacity-20" style="background: #FF7A1A;"></div>
                <div class="absolute -bottom-8 -left-8 w-24 h-24 rounded-full opacity-10" style="background: #22C55E;"></div>

                <div class="relative flex flex-col items-center text-center gap-3 sm:gap-4">
                    {{-- Animasyonlu onay ikonu --}}
                    <div class="relative">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full flex items-center justify-center"
                             style="background: linear-gradient(135deg, #22C55E, #16a34a); animation: success-pulse 2s ease-in-out infinite;">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="stroke-dasharray: 50; animation: check-draw 0.8s ease-out 0.3s forwards; opacity: 0;">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        {{-- Parlama efekti --}}
                        <div class="absolute -inset-1 rounded-full opacity-30" style="background: radial-gradient(circle, rgba(34,197,94,0.3) 0%, transparent 70%);"></div>
                    </div>

                    <div>
                        <p class="text-[11px] sm:text-xs text-gray-500 font-medium tracking-wide mb-1">
                            <span class="inline-flex items-center gap-1 bg-white/70 backdrop-blur-sm rounded-full px-2.5 py-0.5 border border-gray-200/60">
                                <i class="fa-solid fa-hashtag text-[8px] sm:text-[9px]" style="color: #FF7A1A;"></i>
                                {{ $order->order_number }}
                            </span>
                        </p>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-900 mt-2 leading-tight">
                            Teşekkür ederiz {{ explode(' ', $order->customer_name)[0] }}! 🎉
                        </h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1.5 max-w-sm mx-auto">
                            Siparişiniz başarıyla oluşturuldu ve hazırlanıyor
                        </p>
                    </div>
                </div>
            </div>

            {{-- Harita --}}
            @php
                $cleanAddress = $order->shipping_address;
                $cleanAddress = preg_replace('/\b(no|no\.|no:)\s*\d+\w*/iu', '', $cleanAddress);
                $cleanAddress = preg_replace('/\b(blok|daire|kat|apt|apartman|rezidans|site)\s*\w*/iu', '', $cleanAddress);
                $cleanAddress = preg_replace('/\bK\d+\b/i', '', $cleanAddress);
                $cleanAddress = preg_replace('/\s+/', ' ', trim($cleanAddress));
                $cleanAddress = rtrim($cleanAddress, ', .');
                $mapQuery = urlencode($cleanAddress . ', ' . $order->shipping_district . ', ' . $order->shipping_city);
            @endphp
            <div class="px-3.5 sm:px-5 md:px-6 pb-2 sm:pb-3 -mt-1">
                <div class="rounded-xl overflow-hidden border border-gray-200 relative">
                    <iframe
                        src="https://www.google.com/maps?q={{ $mapQuery }}&z=16&output=embed&hl=tr"
                        width="100%"
                        style="border: 0; width: 100%;"
                        class="w-full order-success-map"
                        allowfullscreen=""
                        loading="lazy"
                    ></iframe>
                    <div class="absolute top-2.5 sm:top-3 left-1/2 -translate-x-1/2 bg-white/95 backdrop-blur-sm rounded-lg px-3 py-1.5 sm:px-4 sm:py-2 shadow-sm sm:shadow-md border border-gray-100 text-center max-w-[90%] truncate">
                        <p class="text-[9px] sm:text-[10px] uppercase tracking-wider font-semibold" style="color: #FF7A1A;">Kargo adresi</p>
                        <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $order->shipping_district }}, {{ $order->shipping_city }}</p>
                    </div>
                </div>
            </div>
            <style>
                .order-success-map { height: 140px !important; }
                @media (min-width: 640px) { .order-success-map { height: 200px !important; } }
                @media (min-width: 1024px) { .order-success-map { height: 280px !important; } }
            </style>

            {{-- Sipariş durumu timeline (mini) --}}
            <div class="px-3.5 sm:px-5 md:px-6 py-4 sm:py-5 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    {{-- Onaylandı --}}
                    <div class="flex flex-col items-center gap-1.5 flex-1">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center" style="background: #22C55E;">
                            <i class="fa-solid fa-check text-white text-[10px] sm:text-xs"></i>
                        </div>
                        <span class="text-[10px] sm:text-xs font-semibold text-gray-700">Onaylandı</span>
                    </div>
                    {{-- Çizgi --}}
                    <div class="flex-1 h-0.5 bg-gray-200 rounded-full mx-1 sm:mx-2 -mt-4 sm:-mt-5"></div>
                    {{-- Hazırlanıyor --}}
                    <div class="flex flex-col items-center gap-1.5 flex-1">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center bg-gray-200">
                            <i class="fa-solid fa-box text-gray-400 text-[10px] sm:text-xs"></i>
                        </div>
                        <span class="text-[10px] sm:text-xs font-medium text-gray-400">Hazırlanıyor</span>
                    </div>
                    {{-- Çizgi --}}
                    <div class="flex-1 h-0.5 bg-gray-200 rounded-full mx-1 sm:mx-2 -mt-4 sm:-mt-5"></div>
                    {{-- Kargoda --}}
                    <div class="flex flex-col items-center gap-1.5 flex-1">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center bg-gray-200">
                            <i class="fa-solid fa-truck text-gray-400 text-[10px] sm:text-xs"></i>
                        </div>
                        <span class="text-[10px] sm:text-xs font-medium text-gray-400">Kargoda</span>
                    </div>
                    {{-- Çizgi --}}
                    <div class="flex-1 h-0.5 bg-gray-200 rounded-full mx-1 sm:mx-2 -mt-4 sm:-mt-5"></div>
                    {{-- Teslim Edildi --}}
                    <div class="flex flex-col items-center gap-1.5 flex-1">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center bg-gray-200">
                            <i class="fa-solid fa-house text-gray-400 text-[10px] sm:text-xs"></i>
                        </div>
                        <span class="text-[10px] sm:text-xs font-medium text-gray-400">Teslim Edildi</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. KART: SİPARİŞ BİLGİLERİ --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-3.5 sm:p-5 md:p-6 shadow-xs sm:shadow-sm" style="animation: slide-up 0.7s ease-out;">
            <div class="flex items-center gap-2 mb-3 sm:mb-4">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background: #FFF7ED;">
                    <i class="fa-solid fa-file-lines text-xs sm:text-sm" style="color: #FF7A1A;"></i>
                </div>
                <h3 class="text-base sm:text-lg font-black text-gray-900">Sipariş Bilgileri</h3>
            </div>

            {{-- İletişim + Kargo yöntemi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 pb-3 sm:pb-4 border-b border-gray-100">
                <div class="min-w-0">
                    <h4 class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 sm:mb-1.5">İletişim bilgileri</h4>
                    <p class="text-xs sm:text-sm text-gray-700 break-all font-medium">{{ $order->customer_email }}</p>
                    @if($order->customer_phone)
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">{{ $order->customer_phone }}</p>
                    @endif
                </div>
                <div>
                    <h4 class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 sm:mb-1.5">Kargo yöntemi</h4>
                    <p class="text-xs sm:text-sm text-gray-700 font-medium">📦 Standart Teslimat</p>
                </div>
            </div>

            {{-- Ödeme yöntemi + Kargo firması --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 py-3 sm:py-4 border-b border-gray-100">
                <div class="min-w-0">
                    <h4 class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 sm:mb-1.5">Ödeme yöntemi</h4>
                    <div class="flex items-center gap-2">
                        @if($order->payment_method === 'credit_card')
                            <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-700 font-medium">
                                <i class="fa-solid fa-credit-card text-[11px]" style="color: #3b82f6;"></i>
                                Kredi Kartı · {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                            </span>
                        @elseif($order->payment_method === 'cash_on_delivery')
                            <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-700 font-medium">
                                <i class="fa-solid fa-money-bill-wave text-[11px]" style="color: #22C55E;"></i>
                                Kapıda Ödeme · {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                            </span>
                        @elseif($order->payment_method === 'wire_transfer')
                            <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-700 font-medium">
                                <i class="fa-solid fa-building-columns text-[11px]" style="color: #6366f1;"></i>
                                Havale / EFT · {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                            </span>
                        @else
                            <span class="text-xs sm:text-sm text-gray-700 font-medium">
                                {{ $order->payment_method }} · {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                            </span>
                        @endif
                    </div>
                    @if($order->coupon_code)
                        <div class="flex items-center gap-1.5 mt-2 bg-green-50 rounded-lg px-2.5 py-1.5">
                            <i class="fa-solid fa-ticket text-xs" style="color: #16a34a;"></i>
                            <span class="text-xs text-green-700 font-medium truncate">
                                Kupon: <span class="font-bold">{{ $order->coupon_code }}</span>
                                @if($order->discount_total > 0)
                                    <span class="text-green-600">(-{{ number_format($order->discount_total, 2, ',', '.') }} ₺)</span>
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 sm:mb-1.5">Kargo firması</h4>
                    <p class="text-xs sm:text-sm text-gray-700 font-medium">
                        @php
                            $cargoName = $order->cargo_company;
                            if (empty($cargoName) || stripos($cargoName, 'porego') !== false) {
                                $cargoName = 'DHL eCommerce';
                            }
                        @endphp
                        🚚 {{ $cargoName }}
                    </p>
                </div>
            </div>

            {{-- Kargo adresi + Fatura adresi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4 pt-3 sm:pt-4">
                <div>
                    <h4 class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 sm:mb-1.5">Kargo adresi</h4>
                    <div class="text-xs sm:text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="font-bold text-gray-800">{{ $order->customer_name }}</p>
                        <p class="break-words mt-0.5">{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_district }} / {{ $order->shipping_city }}</p>
                        <p>Türkiye</p>
                        @if($order->customer_phone)
                            <p class="mt-1 text-gray-500">{{ $order->customer_phone }}</p>
                        @endif
                    </div>
                </div>
                @if($order->billing_address)
                    <div>
                        <h4 class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 sm:mb-1.5">Fatura adresi</h4>
                        <div class="text-xs sm:text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="font-bold text-gray-800">{{ $order->customer_name }}</p>
                            <p class="break-words mt-0.5">{{ $order->billing_address }}</p>
                            <p>{{ $order->billing_district }} / {{ $order->billing_city }}</p>
                            <p>Türkiye</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- 3. KART: SİPARİŞ DETAYLARI --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-3.5 sm:p-5 md:p-6 shadow-xs sm:shadow-sm" style="animation: slide-up 0.8s ease-out;">
            <div class="flex items-center gap-2 mb-3 sm:mb-4">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background: #FFF7ED;">
                    <i class="fa-solid fa-bag-shopping text-xs sm:text-sm" style="color: #FF7A1A;"></i>
                </div>
                <h3 class="text-base sm:text-lg font-black text-gray-900">Sipariş Detayları</h3>
            </div>

            <div class="space-y-3">
                @foreach($order->items as $item)
                    <div class="flex items-center gap-2.5 sm:gap-3.5 p-2 sm:p-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                        @if($item->product && $item->product->images->count() > 0)
                            <div class="relative flex-shrink-0">
                                <img src="{{ Storage::url($item->product->images->first()->image_path) }}" class="rounded-xl object-cover border border-gray-200 w-16 h-16 sm:w-20 sm:h-20 shadow-xs">
                                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 sm:w-5.5 sm:h-5.5 text-white text-[9px] sm:text-[10px] font-bold rounded-full flex items-center justify-center shadow-sm" style="background: #FF7A1A;">{{ $item->quantity }}</span>
                            </div>
                        @else
                            <div class="relative flex-shrink-0">
                                <div class="rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 w-16 h-16 sm:w-20 sm:h-20">
                                    <i class="fa-solid fa-shoe-prints text-sm"></i>
                                </div>
                                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 sm:w-5.5 sm:h-5.5 text-white text-[9px] sm:text-[10px] font-bold rounded-full flex items-center justify-center shadow-sm" style="background: #FF7A1A;">{{ $item->quantity }}</span>
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
                    <span class="text-gray-700 font-medium">{{ $order->shipping_price > 0 ? number_format($order->shipping_price, 2, ',', '.') . ' ₺' : '✅ Ücretsiz' }}</span>
                </div>
                @if($order->discount_total > 0)
                    <div class="flex justify-between text-xs sm:text-sm">
                        <span class="text-green-600 font-medium">🎁 İndirim</span>
                        <span class="text-green-600 font-semibold">-{{ number_format($order->discount_total, 2, ',', '.') }} ₺</span>
                    </div>
                @endif
                <div class="flex justify-between items-center text-sm sm:text-base font-black pt-3 mt-1 border-t border-gray-100">
                    <span class="text-gray-900">Toplam</span>
                    <span class="text-lg sm:text-xl font-black" style="color: #FF7A1A;">{{ number_format($order->grand_total, 2, ',', '.') }} ₺</span>
                </div>
            </div>
        </div>

        {{-- 4. GÜVEN SİMGELERİ --}}
        <div class="grid grid-cols-3 gap-2 sm:gap-3" style="animation: slide-up 0.9s ease-out;">
            <div class="bg-white rounded-xl border border-gray-100 p-2.5 sm:p-3.5 text-center shadow-xs">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full mx-auto flex items-center justify-center mb-1.5" style="background: #FFF7ED;">
                    <i class="fa-solid fa-shield-halved text-xs sm:text-sm" style="color: #FF7A1A;"></i>
                </div>
                <p class="text-[10px] sm:text-xs font-bold text-gray-700 leading-tight">%100 Güvenli<br>Alışveriş</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-2.5 sm:p-3.5 text-center shadow-xs">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full mx-auto flex items-center justify-center mb-1.5" style="background: #ECFDF5;">
                    <i class="fa-solid fa-rotate-left text-xs sm:text-sm" style="color: #22C55E;"></i>
                </div>
                <p class="text-[10px] sm:text-xs font-bold text-gray-700 leading-tight">%100 İade<br>Garantisi</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-2.5 sm:p-3.5 text-center shadow-xs">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full mx-auto flex items-center justify-center mb-1.5" style="background: #EFF6FF;">
                    <i class="fa-solid fa-truck-fast text-xs sm:text-sm" style="color: #3b82f6;"></i>
                </div>
                <p class="text-[10px] sm:text-xs font-bold text-gray-700 leading-tight">Hızlı &<br>Ücretsiz Kargo</p>
            </div>
        </div>

        {{-- 5. BUTONLAR --}}
        <div class="space-y-2.5 sm:space-y-3 pt-1" style="animation: slide-up 1s ease-out;">
            <a href="{{ route('home') }}" class="group block w-full font-bold py-3.5 sm:py-4 px-4 sm:px-6 rounded-xl shadow-lg transition-all active:scale-[0.98] text-center text-xs sm:text-sm text-white hover:shadow-xl" style="background: linear-gradient(135deg, #FF7A1A, #FF5500);">
                <span class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-bag-shopping text-xs sm:text-sm"></i>
                    Alışverişe Devam Et
                    <i class="fa-solid fa-arrow-right text-[10px] sm:text-xs transition-transform group-hover:translate-x-1"></i>
                </span>
            </a>
            @if(auth()->check())
                <a href="{{ route('account.orders') }}" class="block w-full bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 sm:py-3.5 px-4 sm:px-6 rounded-xl transition-all border border-gray-200 text-center text-xs sm:text-sm shadow-xs hover:shadow-sm">
                    <span class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-eye text-xs"></i>
                        Siparişimi Görüntüle
                    </span>
                </a>
            @else
                <a href="{{ route('order.tracking', ['order_number' => $order_number]) }}" class="block w-full bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 sm:py-3.5 px-4 sm:px-6 rounded-xl transition-all border border-gray-200 text-center text-xs sm:text-sm shadow-xs hover:shadow-sm">
                    <span class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-location-dot text-xs"></i>
                        Siparişimi Takip Et
                    </span>
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
            class="relative w-full max-w-md sm:max-w-lg bg-white rounded-2xl sm:rounded-3xl shadow-[0_25px_60px_rgba(0,0,0,0.25)] overflow-hidden flex flex-col max-h-[90vh] z-10"
        >
            <div class="h-1.5 flex-shrink-0" style="background: linear-gradient(90deg, #FF7A1A, #ffb800, #FF7A1A);"></div>

            <div class="px-4 sm:px-8 py-3.5 sm:py-5 border-b border-gray-100 flex-shrink-0">
                <h3 class="text-base sm:text-xl font-black text-gray-900 text-left flex items-center gap-2">
                    <span>⭐</span> Ürünü Değerlendir
                </h3>
            </div>

            <div class="overflow-y-auto flex-1 px-3.5 sm:px-8 py-3.5 sm:py-6">
                @foreach($order->items as $item)
                    @if($item->product)
                        <div
                            x-data="{ hoverStar: 0 }"
                            class="{{ !$loop->last ? 'pb-4 sm:pb-6 mb-4 sm:mb-6 border-b border-gray-100' : '' }}"
                        >
                            {{-- Ürün bilgisi --}}
                            <div class="flex items-start text-left gap-2.5 sm:gap-4 mb-3 sm:mb-4">
                                @if($item->product->images->count() > 0)
                                    <img
                                        src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                        alt="{{ $item->product_name }}"
                                        class="w-14 h-14 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl object-cover border border-gray-200 flex-shrink-0 shadow-xs"
                                    >
                                @else
                                    <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl bg-gray-100 flex items-center justify-center text-gray-300 flex-shrink-0 shadow-xs">
                                        <i class="fa-solid fa-shoe-prints text-xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0 pt-0.5">
                                    <h4 class="text-xs sm:text-base font-bold text-gray-900 leading-snug line-clamp-2">{{ $item->product_name }}</h4>
                                    @if($item->variant_info)
                                        <p class="text-[11px] sm:text-xs text-gray-400 mt-0.5 truncate">{{ $item->variant_info }}</p>
                                    @endif
                                </div>
                            </div>

                            <p class="text-[11px] sm:text-sm text-gray-400 mb-1.5 sm:mb-2 text-center">Ürünü puanlayabilir ve yorum yazabilirsiniz</p>

                            {{-- ⭐ YILDIZLAR --}}
                            <div
                                class="flex items-center justify-center gap-1 sm:gap-2.5 py-2 sm:py-3 mb-2 sm:mb-3"
                                @mouseleave="hoverStar = 0"
                            >
                                @for($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="setRating({{ $item->product_id }}, {{ $i }})"
                                        @mouseenter="hoverStar = {{ $i }}"
                                        aria-label="{{ $i }} yıldız"
                                        class="focus:outline-none transition-all duration-150 p-0.5 sm:p-1 cursor-pointer active:scale-90"
                                    >
                                        <svg
                                            class="w-8 h-8 sm:w-11 sm:h-11 md:w-12 md:h-12 transition-all duration-150"
                                            :class="(hoverStar >= {{ $i }} || (hoverStar === 0 && {{ ($ratings[$item->product_id] ?? 5) }} >= {{ $i }})) ? 'scale-110 drop-shadow-[0_4px_8px_rgba(255,184,0,0.4)]' : 'scale-100'"
                                            viewBox="0 0 24 24"
                                            :fill="(hoverStar >= {{ $i }} || (hoverStar === 0 && {{ ($ratings[$item->product_id] ?? 5) }} >= {{ $i }})) ? '#ffb800' : '#e5e7eb'"
                                            style="display: block;"
                                        >
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>

                            {{-- Yorum alanı --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-[11px] sm:text-sm font-bold text-gray-700">Yorumunu Yaz</label>
                                    <span class="text-[10px] sm:text-xs text-gray-400">(İsteğe bağlı)</span>
                                </div>
                                <textarea
                                    wire:model="comments.{{ $item->product_id }}"
                                    rows="2"
                                    maxlength="2000"
                                    class="w-full rounded-xl sm:rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-orange-300 focus:ring-2 focus:ring-orange-100 text-base sm:text-sm px-3 py-2 sm:px-3.5 sm:py-2.5 placeholder:text-gray-300 transition-all resize-none"
                                    placeholder="Ürün hakkındaki deneyiminizi paylaşın..."
                                ></textarea>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Gönder butonu --}}
            <div class="px-4 sm:px-8 py-3 sm:py-5 border-t border-gray-100 bg-white flex-shrink-0">
                <button
                    type="button"
                    wire:click="submitRatings"
                    wire:loading.attr="disabled"
                    x-on:click="setTimeout(() => { if ($wire.ratingsSubmitted) { open = false; $dispatch('show-toast', { message: 'Puanlamanız kaydedildi. Teşekkür ederiz! ⭐' }); } }, 600)"
                    class="w-full py-3 sm:py-3.5 rounded-xl sm:rounded-2xl text-white text-xs sm:text-sm font-extrabold shadow-lg transition-all hover:brightness-110 active:scale-[0.97] disabled:opacity-50 tracking-wide cursor-pointer flex items-center justify-center gap-2"
                    style="background: linear-gradient(135deg, #FF7A1A, #FF5500);"
                >
                    <span wire:loading.remove wire:target="submitRatings" class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane text-xs sm:text-sm"></i>
                        <span>Gönder</span>
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
    <div class="text-white rounded-2xl px-5 py-4 shadow-2xl shadow-black/20 flex items-center gap-3" style="background: linear-gradient(135deg, #1a1a1a, #333);">
        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background: #22C55E;">
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

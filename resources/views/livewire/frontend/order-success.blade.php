<div>
<div class="min-h-[70vh] bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden p-8 text-center relative">
        <div class="absolute inset-0 pointer-events-none opacity-20 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-green-300 via-transparent to-transparent"></div>
        
        <div class="relative z-10">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
                <svg class="h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h2 class="text-3xl font-black text-gray-900 mb-2">Siparişiniz Alındı!</h2>
            <p class="text-gray-500 mb-8 text-lg">Bizi tercih ettiğiniz için teşekkür ederiz.</p>
            
            <div class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Sipariş Numaranız</p>
                <p class="text-2xl font-black text-blue-900">{{ $order_number }}</p>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">Siparişinizin durumunu "Sipariş Takip" sayfasından veya e-posta adresinize gönderilen linkten takip edebilirsiniz.</p>
                </div>
            </div>

            @if($ratingsSubmitted)
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium flex items-center justify-center gap-2 mb-6">
                    <i class="fa-solid fa-circle-check"></i>
                    Puanlamanız kaydedildi. Teşekkür ederiz! ⭐
                </div>
            @endif
            
            <div class="flex flex-col gap-3">
                <a href="{{ route('home') }}" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all active:scale-[0.98] inline-block">
                    Alışverişe Devam Et
                </a>
                @if(auth()->check())
                    <a href="{{ route('account.orders') }}" class="w-full bg-transparent hover:bg-gray-50 text-gray-700 font-bold py-4 px-6 rounded-xl transition-all inline-block border border-gray-200">
                        Siparişimi Görüntüle
                    </a>
                @else
                    <a href="{{ route('order.tracking', ['order_number' => $order_number]) }}" class="w-full bg-transparent hover:bg-gray-50 text-gray-700 font-bold py-4 px-6 rounded-xl transition-all inline-block border border-gray-200">
                        Siparişimi Görüntüle
                    </a>
                @endif
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
    {{-- Tam ekran bulanık arka plan — tıklanınca KAPANMAZ --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-black/60 backdrop-blur-md"
    ></div>

    {{-- Modal container --}}
    <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-400"
            x-transition:enter-start="opacity-0 scale-90 translate-y-8"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-[0_25px_60px_rgba(0,0,0,0.3)] overflow-hidden"
        >
            {{-- Üst dekoratif gradient --}}
            <div class="h-2 bg-gradient-to-r from-yellow-400 via-amber-400 to-orange-500"></div>

            {{-- Header --}}
            <div class="pt-8 pb-2 px-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-amber-100 to-yellow-50 mb-4 shadow-inner">
                    <i class="fa-solid fa-star text-amber-400 text-4xl drop-shadow-sm"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900">Ürünlerimizi Puanlayın</h3>
                <p class="text-sm text-gray-400 mt-1.5">Yıldızlara dokunarak puanınızı verin</p>
            </div>

            {{-- Ürünler --}}
            <div class="px-6 sm:px-8 py-6 space-y-5 max-h-[50vh] overflow-y-auto">
                @foreach($order->items as $item)
                    @if($item->product)
                        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-5">
                            {{-- Ürün bilgisi --}}
                            <div class="flex items-center gap-4 mb-4">
                                @if($item->product->images->count() > 0)
                                    <img
                                        src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                        alt="{{ $item->product_name }}"
                                        class="w-20 h-20 rounded-2xl object-cover border-2 border-white shadow-md flex-shrink-0"
                                    >
                                @else
                                    <div class="w-20 h-20 rounded-2xl bg-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0 shadow-md">
                                        <i class="fa-solid fa-shoe-prints text-xl"></i>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base font-bold text-gray-900 leading-snug line-clamp-2">{{ $item->product_name }}</h4>
                                    @if($item->variant_info)
                                        <p class="text-xs text-gray-400 mt-1">{{ $item->variant_info }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Yıldızlar - BÜYÜK ve ORTALI --}}
                            <div class="flex items-center justify-center gap-2 py-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="setRating({{ $item->product_id }}, {{ $i }})"
                                        class="focus:outline-none transition-all duration-150 hover:scale-125 active:scale-90"
                                    >
                                        <i class="fa-solid fa-star text-4xl sm:text-[2.5rem] {{ ($ratings[$item->product_id] ?? 5) >= $i ? 'text-amber-400 drop-shadow-[0_2px_4px_rgba(251,191,36,0.4)]' : 'text-gray-200 hover:text-amber-200' }} transition-colors"></i>
                                    </button>
                                @endfor
                            </div>

                            {{-- Yorum alanı --}}
                            <textarea
                                wire:model="comments.{{ $item->product_id }}"
                                rows="2"
                                class="mt-2 w-full rounded-xl border-gray-200 bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 text-sm px-4 py-3 placeholder:text-gray-300 transition-all resize-none"
                                placeholder="Yorum bırakın (isteğe bağlı)"
                            ></textarea>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Gönder butonu --}}
            <div class="px-6 sm:px-8 pb-8 pt-2">
                <button
                    type="button"
                    wire:click="submitRatings"
                    wire:loading.attr="disabled"
                    x-on:click="setTimeout(() => { if ($wire.ratingsSubmitted) open = false }, 600)"
                    class="w-full py-4 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-500 to-orange-500 hover:from-amber-600 hover:via-amber-600 hover:to-orange-600 text-white text-base font-extrabold shadow-xl shadow-amber-400/30 transition-all active:scale-[0.97] disabled:opacity-50 tracking-wide"
                >
                    <span wire:loading.remove wire:target="submitRatings" class="flex items-center justify-center gap-2.5">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                        Puanlamayı Gönder
                    </span>
                    <span wire:loading wire:target="submitRatings" class="flex items-center justify-center gap-2.5">
                        <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
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
      window.gapi.surveyoptin.render(
        {
          "merchant_id": 5828544730,
          "order_id": "{{ $order->order_number }}",
          "email": "{{ $order->customer_email }}",
          "delivery_country": "TR",
          "estimated_delivery_date": "{{ \Carbon\Carbon::now()->addDays(3)->format('Y-m-d') }}",
          "opt_in_style": "CENTER_DIALOG"
        }
      );
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

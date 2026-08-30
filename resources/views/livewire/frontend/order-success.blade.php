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

{{-- ⭐ YILDIZ PUANLAMA POP-UP --}}
@if(!$ratingsSubmitted)
<div
    x-data="{ open: true }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[9999] overflow-y-auto"
    role="dialog"
    aria-modal="true"
>
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-[2px]"
    ></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden"
        >
            {{-- Üst dekoratif şerit --}}
            <div class="h-1.5 bg-gradient-to-r from-yellow-400 via-amber-400 to-orange-400"></div>

            {{-- İçerik --}}
            <div class="px-6 pt-6 pb-2">
                {{-- Başlık --}}
                <div class="text-center mb-5">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-50 mb-3">
                        <i class="fa-solid fa-star text-amber-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-black text-gray-900">Deneyiminizi Puanlayın</h3>
                    <p class="text-xs text-gray-400 mt-1">Yıldızlara dokunarak puanınızı seçin</p>
                </div>

                {{-- Ürünler --}}
                <div class="space-y-3 max-h-[45vh] overflow-y-auto pr-1 -mr-1">
                    @foreach($order->items as $item)
                        @if($item->product)
                            <div class="group rounded-2xl bg-gray-50 p-3 transition-colors hover:bg-gray-100/80">
                                <div class="flex items-center gap-3">
                                    @if($item->product->images->count() > 0)
                                        <img
                                            src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                            alt="{{ $item->product_name }}"
                                            class="w-11 h-11 rounded-xl object-cover border border-gray-200/80 flex-shrink-0"
                                        >
                                    @else
                                        <div class="w-11 h-11 rounded-xl bg-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0">
                                            <i class="fa-solid fa-shoe-prints text-xs"></i>
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] font-semibold text-gray-800 truncate leading-tight">{{ $item->product_name }}</p>
                                        @if($item->variant_info)
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $item->variant_info }}</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Yıldız satırı --}}
                                <div class="flex items-center justify-between mt-2.5">
                                    <div class="flex gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button
                                                type="button"
                                                wire:click="setRating({{ $item->product_id }}, {{ $i }})"
                                                class="focus:outline-none transition-all duration-100 hover:scale-110 active:scale-90"
                                            >
                                                <i class="fa-solid fa-star text-xl {{ ($ratings[$item->product_id] ?? 5) >= $i ? 'text-amber-400' : 'text-gray-200' }}"></i>
                                            </button>
                                        @endfor
                                    </div>
                                    <span class="text-[10px] font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded-full">
                                        {{ $ratings[$item->product_id] ?? 5 }}/5
                                    </span>
                                </div>

                                {{-- Yorum input --}}
                                <input
                                    type="text"
                                    wire:model="comments.{{ $item->product_id }}"
                                    class="mt-2 w-full rounded-xl border-0 bg-white ring-1 ring-gray-200 focus:ring-2 focus:ring-amber-300 text-xs px-3 py-2 placeholder:text-gray-300 transition-all"
                                    placeholder="Yorum ekleyin (isteğe bağlı)"
                                >
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Gönder butonu --}}
            <div class="px-6 pb-6 pt-4">
                <button
                    type="button"
                    wire:click="submitRatings"
                    wire:loading.attr="disabled"
                    x-on:click="setTimeout(() => { if ($wire.ratingsSubmitted) open = false }, 500)"
                    class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-sm font-bold shadow-lg shadow-amber-300/30 transition-all active:scale-[0.97] disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="submitRatings" class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        Gönder
                    </span>
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

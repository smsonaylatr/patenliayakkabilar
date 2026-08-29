<div>
<div class="min-h-[70vh] bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden p-8 text-center relative">
        <!-- Confetti Background (Optional) -->
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

{{-- ⭐ ZORUNLU YILDIZ PUANLAMA POP-UP MODAL --}}
@if(!$ratingsSubmitted)
<div
    x-data="{ open: true }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[9999] overflow-y-auto"
    aria-labelledby="rating-modal-title"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop (tıklanınca KAPANMAZ — zorunlu) --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm"
    ></div>

    <div class="flex min-h-full items-center justify-center p-4">
        {{-- Modal Panel --}}
        <div
            x-show="open"
            x-transition:enter="ease-out duration-400"
            x-transition:enter-start="opacity-0 translate-y-12 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 scale-95"
            class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl w-full max-w-md border border-gray-100"
        >
            {{-- Header --}}
            <div class="bg-gradient-to-r from-amber-50 via-yellow-50 to-amber-50 px-5 py-4 border-b border-amber-100/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-500 flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-star text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900" id="rating-modal-title">Ürünlerimizi Puanlayın ⭐</h3>
                        <p class="text-[11px] text-gray-500 mt-0.5">Yıldız vererek diğer müşterilere yardımcı olun</p>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-5 py-5 space-y-3 max-h-[55vh] overflow-y-auto">
                @foreach($order->items as $item)
                    @if($item->product)
                        <div class="p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                            <div class="flex items-start gap-3">
                                {{-- Ürün Görseli --}}
                                @if($item->product->images->count() > 0)
                                    <img
                                        src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                        alt="{{ $item->product_name }}"
                                        class="w-14 h-14 rounded-lg object-cover border border-gray-200 flex-shrink-0"
                                    >
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0">
                                        <i class="fa-solid fa-shoe-prints"></i>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-gray-900 truncate">{{ $item->product_name }}</h4>
                                    @if($item->variant_info)
                                        <p class="text-[10px] text-gray-500 mt-0.5">{{ $item->variant_info }}</p>
                                    @endif

                                    {{-- Yıldızlar --}}
                                    <div class="flex gap-1 mt-2 text-2xl">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button
                                                type="button"
                                                wire:click="setRating({{ $item->product_id }}, {{ $i }})"
                                                class="focus:outline-none transition-all duration-150 hover:scale-125 active:scale-90 {{ ($ratings[$item->product_id] ?? 5) >= $i ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300' }}"
                                            >
                                                <i class="fa-solid fa-star drop-shadow-sm"></i>
                                            </button>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            {{-- Yorum (Opsiyonel) --}}
                            <div class="mt-2.5">
                                <input
                                    type="text"
                                    wire:model="comments.{{ $item->product_id }}"
                                    class="w-full rounded-lg border-gray-200 bg-white focus:border-amber-400 focus:ring-1 focus:ring-amber-100 text-xs px-3 py-2 transition-all placeholder:text-gray-400"
                                    placeholder="Yorum bırakın (isteğe bağlı)"
                                >
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-5 py-4 border-t border-gray-100">
                <button
                    type="button"
                    wire:click="submitRatings"
                    wire:loading.attr="disabled"
                    x-on:click="setTimeout(() => { if ($wire.ratingsSubmitted) open = false }, 500)"
                    class="w-full py-3.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold shadow-lg shadow-amber-200/50 transition-all active:scale-[0.97] disabled:opacity-50 flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove wire:target="submitRatings">
                        <i class="fa-solid fa-paper-plane mr-1"></i>
                        Gönder
                    </span>
                    <span wire:loading wire:target="submitRatings">
                        <i class="fa-solid fa-circle-notch fa-spin mr-1"></i>
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

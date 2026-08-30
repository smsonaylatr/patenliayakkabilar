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
    {{-- Bulanık arka plan --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-400"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-black/50 backdrop-blur-lg"
    ></div>

    {{-- Modal --}}
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-6"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden"
        >
            {{-- Başlık satırı --}}
            <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100">
                <h3 class="text-xl font-black text-gray-900">Ürünü Değerlendir</h3>
            </div>

            {{-- İçerik --}}
            <div class="max-h-[65vh] overflow-y-auto">
                @foreach($order->items as $itemIndex => $item)
                    @if($item->product)
                        <div class="px-7 py-6 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">

                            {{-- Ürün bilgisi --}}
                            <div class="flex items-start gap-5 mb-5">
                                @if($item->product->images->count() > 0)
                                    <img
                                        src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                        alt="{{ $item->product_name }}"
                                        class="w-24 h-24 rounded-xl object-cover border border-gray-200 flex-shrink-0"
                                    >
                                @else
                                    <div class="w-24 h-24 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 flex-shrink-0">
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
                            <p class="text-sm text-gray-500 mb-4 text-center">Ürünü aşağıdan puanlayabilir ve yorum yazabilirsiniz</p>

                            {{-- Yıldız puanlama --}}
                            <div class="flex items-center justify-center gap-3 py-2 mb-5">
                                @for($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="setRating({{ $item->product_id }}, {{ $i }})"
                                        class="focus:outline-none transition-all duration-150 hover:scale-110 active:scale-90"
                                    >
                                        <i class="fa-solid fa-star text-[2.75rem] {{ ($ratings[$item->product_id] ?? 5) >= $i ? 'text-amber-400' : 'text-gray-200' }} transition-colors"></i>
                                    </button>
                                @endfor
                            </div>

                            {{-- Yorum yaz --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-bold text-gray-700">Yorumunu Yaz</label>
                                    <span class="text-xs text-gray-400">(İsteğe bağlı)</span>
                                </div>
                                <div class="relative">
                                    <textarea
                                        wire:model="comments.{{ $item->product_id }}"
                                        rows="3"
                                        maxlength="2000"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 text-sm px-4 py-3.5 placeholder:text-gray-300 transition-all resize-none"
                                        placeholder="Ürün hakkındaki deneyiminizi paylaşın..."
                                    ></textarea>
                                    <span class="absolute bottom-3 right-3 text-[11px] text-gray-300">{{ strlen($comments[$item->product_id] ?? '') }}/2000</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Gönder butonu --}}
            <div class="px-7 py-5 border-t border-gray-100 bg-gray-50/50">
                <button
                    type="button"
                    wire:click="submitRatings"
                    wire:loading.attr="disabled"
                    x-on:click="setTimeout(() => { if ($wire.ratingsSubmitted) open = false }, 600)"
                    class="w-full py-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-base font-extrabold shadow-lg shadow-orange-200/40 transition-all active:scale-[0.98] disabled:opacity-50"
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

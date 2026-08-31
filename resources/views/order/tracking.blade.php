<x-layouts.app>
    <div class="min-h-[70vh] bg-gray-50 py-6 sm:py-12 px-3 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white p-4 sm:p-6 md:p-8 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-slate-100 text-black mb-3 sm:mb-4 border border-slate-200 shadow-sm">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8h4.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                    </svg>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-900 mb-1.5 sm:mb-2">Sipariş & Kargo Takibi</h1>
                <p class="text-gray-500 mb-5 sm:mb-8 text-xs sm:text-sm">Siparişinizin durumunu ve kargo takip kodunuzu öğrenmek için sipariş numaranızı girin.</p>
                
                <form action="{{ route('order.tracking') }}" method="GET" class="space-y-3 sm:space-y-4 mb-6 sm:mb-8">
                    <div>
                        <input type="text" name="order_number" value="{{ request('order_number') }}" class="w-full rounded-xl border-gray-200 focus:ring-black focus:border-black text-center text-base sm:text-lg py-2.5 sm:py-3 uppercase tracking-wider font-semibold" placeholder="Örn: TR123456" required>
                    </div>
                    <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-3 sm:py-3.5 px-6 rounded-xl shadow-md transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Sipariş Sorgula</span>
                    </button>
                </form>

                @if(isset($error))
                    <div class="mb-4 sm:mb-6 bg-red-50 text-red-700 p-3 sm:p-4 rounded-xl border border-red-100 text-xs sm:text-sm">
                        {{ $error }}
                    </div>
                @endif

                @if(isset($order))
                    @php
                        $rawTrackingCode = trim((string)($trackingData['tracking_code'] ?? $order->cargo_tracking_code));
                        $hasRealTrackingCode = !empty($rawTrackingCode) 
                            && $rawTrackingCode !== (string)$order->order_number 
                            && $rawTrackingCode !== (string)$order->id 
                            && $rawTrackingCode !== '#' . (string)$order->order_number;

                        $trackingCode = $hasRealTrackingCode ? $rawTrackingCode : null;
                        $cargoName = $trackingData['cargo_name'] ?? ($order->cargo_company ?: 'DHL eCommerce');
                        
                        // Kargo linki direkt olarak backend'den (Porego API'sinden dönen carrierTrackingUrl) geliyor.
                        // Eğer backend'den gelmemişse veya hatalıysa, sipariş tablosundaki varsayılan linki (varsa) kullanırız.
                        $trackingUrl = $trackingData['tracking_url'] ?? null;
                        
                        // Ancak bazı durumlarda backend boş gönderebiliyor ve bizde manuel kargo kodu varsa,
                        // fallback olarak kendimiz sadece DHL için oluşturabiliriz (isteğe bağlı)
                        if (empty($trackingUrl) && $hasRealTrackingCode && stripos($cargoName, 'dhl') !== false) {
                            $trackingUrl = 'https://kargotakip.dhlecommerce.com.tr/?takipNo=' . urlencode($trackingCode);
                        }
                        
                        // Eğer hiçbir özel URL oluşturulamadıysa ve Porego URL'si yoksa, boş bırak.
                        // (Porego URL'si varsa onu kullanmaya devam eder)

                        $deliveryDate = $trackingData['delivery_date'] ?? null;
                        if ($deliveryDate && strtotime($deliveryDate)) {
                            try {
                                $deliveryDate = \Carbon\Carbon::parse($deliveryDate)->translatedFormat('d F Y H:i');
                            } catch(\Exception $e) {}
                        }
                        $deliveryLocation = $trackingData['delivery_location'] ?? null;
                        $cargoMessage = $trackingData['cargo_message'] ?? null;
                    @endphp
                    <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-100 text-left">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4">Sipariş Detayları</h2>
                        
                        <div class="bg-gray-50 rounded-xl sm:rounded-2xl p-3.5 sm:p-5 space-y-3 sm:space-y-4 border border-gray-100">
                            {{-- Sipariş No --}}
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 sm:gap-0">
                                <span class="text-gray-500 text-xs sm:text-sm">Sipariş No:</span>
                                <div x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ e($order->order_number) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer inline-flex items-center gap-1.5 bg-white px-3 py-1 rounded-lg border border-gray-200 text-xs sm:text-sm font-semibold text-gray-900 hover:border-black transition-colors self-start sm:self-auto" title="Tıklayarak Kopyala">
                                    <span>#{{ $order->order_number }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px; height:14px; min-width:14px; display:inline-block; vertical-align:middle;" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                    <span x-show="copied" x-cloak class="text-xs text-emerald-600 font-bold ml-0.5">Kopyalandı!</span>
                                </div>
                            </div>

                            {{-- Sipariş Tarihi --}}
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-0.5 sm:gap-0">
                                <span class="text-gray-500 text-xs sm:text-sm">Sipariş Tarihi:</span>
                                <span class="font-medium text-gray-900 text-xs sm:text-sm">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                            </div>

                            {{-- Ödeme Yöntemi --}}
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-0.5 sm:gap-0">
                                <span class="text-gray-500 text-xs sm:text-sm">Ödeme Yöntemi:</span>
                                <span class="font-medium text-gray-900 text-xs sm:text-sm">
                                    {{ $order->payment_method === 'cash_on_delivery' ? 'Kapıda Ödeme' : ($order->payment_method === 'credit_card' ? 'Kredi Kartı' : 'Havale / EFT') }}
                                </span>
                            </div>

                            {{-- Sipariş Durumu --}}
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1.5 sm:gap-0 border-t border-gray-200 pt-3">
                                <span class="text-gray-500 text-xs sm:text-sm">Sipariş Durumu:</span>
                                <div>
                                    @if($order->status === 'pending')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Bekliyor</span>
                                    @elseif($order->status === 'processing')
                                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Hazırlanıyor</span>
                                    @elseif($order->status === 'shipped')
                                        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Kargoya Verildi</span>
                                    @elseif($order->status === 'completed' || $order->status === 'delivered')
                                        <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Teslim Edildi</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">İptal Edildi</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">{{ $order->status }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- KARGO TAKİP KODU VE FİRMA BİLGİSİ ALANI -->
                            @if($hasRealTrackingCode)
                                <div class="bg-white p-3 sm:p-4 rounded-xl border border-gray-200 space-y-3 mt-2 shadow-xs">
                                    {{-- Başlık satırı --}}
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 sm:gap-0">
                                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">KARGO TAKİP KODU</span>
                                        <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-bold">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Kargo Kodu Aktif
                                        </span>
                                    </div>

                                    {{-- Kargo kodu ve kopyala --}}
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-2 pt-1 border-b border-gray-100 pb-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="font-mono font-black text-black text-lg sm:text-xl tracking-wider break-all">{{ $trackingCode }}</div>
                                            
                                            @php
                                                $cargoLogo = null;
                                                if (stripos($cargoName, 'dhl') !== false) {
                                                    $cargoLogo = 'https://upload.wikimedia.org/wikipedia/commons/a/ac/DHL_Logo.svg';
                                                } elseif (stripos($cargoName, 'aras') !== false) {
                                                    $cargoLogo = 'https://upload.wikimedia.org/wikipedia/commons/a/ae/Aras_Kargo_logo.svg';
                                                } elseif (stripos($cargoName, 'yurtiçi') !== false || stripos($cargoName, 'yurtici') !== false) {
                                                    $cargoLogo = 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Yurti%C3%A7i_Kargo_logo.svg';
                                                } elseif (stripos($cargoName, 'mng') !== false) {
                                                    $cargoLogo = 'https://upload.wikimedia.org/wikipedia/commons/9/90/MNG_Kargo_logo.svg';
                                                } elseif (stripos($cargoName, 'ptt') !== false) {
                                                    $cargoLogo = 'https://upload.wikimedia.org/wikipedia/commons/6/67/PTT_logo.svg';
                                                }
                                            @endphp
                                            
                                            <div class="flex items-center gap-2 mt-1.5">
                                                <div class="text-xs font-medium text-gray-500">Kargo Firması:</div>
                                                <div class="flex items-center gap-1.5">
                                                    @if($cargoLogo)
                                                        <img src="{{ $cargoLogo }}" alt="{{ $cargoName }}" class="h-4 object-contain">
                                                    @endif
                                                    <strong class="text-gray-900 font-semibold text-xs">{{ $cargoName }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ e($trackingCode) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer bg-gray-100 hover:bg-black hover:text-white text-gray-800 text-xs font-bold px-3.5 py-2 rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-xs shrink-0 w-full sm:w-auto">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width:14px; height:14px; min-width:14px; display:inline-block; vertical-align:middle;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                            </svg>
                                            <span x-show="!copied">Kodu Kopyala</span>
                                            <span x-show="copied" x-cloak class="text-emerald-600 font-bold">Kopyalandı!</span>
                                        </div>
                                    </div>

                                    @if($trackingUrl)
                                    <div class="pt-1 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                                        <span class="text-xs text-gray-500 font-medium">Kargo Durum Sorgulama:</span>
                                        <a href="{{ $trackingUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline bg-blue-50 px-3 py-2 sm:py-1.5 rounded-lg transition-colors w-full sm:w-auto">
                                            <span>Kargo Takip Linki</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                    @endif
                                </div>

                                @if($deliveryDate || $deliveryLocation || $cargoMessage)
                                <div class="bg-emerald-50/70 p-3 sm:p-4 rounded-xl border border-emerald-200/80 mt-3 space-y-3 shadow-xs">
                                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-800">TESLİM BİLGİLERİ</div>
                                    <div class="space-y-2">
                                        @if($deliveryDate)
                                            <div>
                                                <div class="text-xs text-emerald-700/80 font-semibold uppercase">TESLİM TARİHİ</div>
                                                <div class="text-emerald-900 font-bold text-xs sm:text-sm">{{ $deliveryDate }}</div>
                                            </div>
                                        @endif
                                        @if($deliveryLocation)
                                            <div>
                                                <div class="text-xs text-emerald-700/80 font-semibold uppercase">TESLİM LOKASYONU</div>
                                                <div class="text-emerald-900 font-bold text-xs sm:text-sm uppercase">{{ $deliveryLocation }}</div>
                                            </div>
                                        @endif
                                        @if($cargoMessage)
                                            <div>
                                                <div class="text-xs text-emerald-700/80 font-semibold uppercase">KARGO MESAJI</div>
                                                <div class="text-emerald-900 font-bold text-xs sm:text-sm">{{ $cargoMessage }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            @else
                                {{-- Kargo takip kodu henüz oluşturulmamış --}}
                                <div class="bg-amber-50/70 p-3 sm:p-4 rounded-xl border border-amber-200/80 space-y-2.5 mt-2">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1.5 sm:gap-0">
                                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800">KARGO TAKİP DURUMU</span>
                                        <span class="inline-flex items-center gap-1.5 text-xs text-amber-700 font-bold">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> 
                                            @if(in_array($order->status, ['shipped', 'delivered', 'completed']))
                                                Kargo Kodu Bekleniyor
                                            @else
                                                Henüz Oluşturulmadı
                                            @endif
                                        </span>
                                    </div>

                                    {{-- Bilgilendirme ikonu ve mesaj --}}
                                    <div class="flex items-start gap-2.5 pt-1">
                                        <div class="shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-xs text-amber-900 leading-relaxed font-medium">
                                            @if(in_array($order->status, ['shipped', 'delivered', 'completed']))
                                                Siparişiniz kargo firmasına teslim edilmiştir. Kargo takip numaranız kargo firmasından sistemimize ulaştığında bu alanda otomatik olarak görünecektir.
                                            @elseif($order->status === 'processing')
                                                Siparişiniz hazırlanıyor. Kargo takip kodunuz, ürününüz paketlenip kargo firmasına teslim edildikten sonra bu alanda otomatik olarak görünecektir.
                                            @else
                                                Siparişiniz onay aşamasındadır. Kargo takip kodunuz, siparişiniz kargoya verildikten sonra bu alanda ve SMS ile telefonunuza otomatik olarak iletilecektir.
                                            @endif
                                        </p>
                                    </div>

                                    {{-- İlerleme göstergesi --}}
                                    <div class="pt-1">
                                        <div class="flex items-center gap-1.5">
                                            @php
                                                $steps = [
                                                    ['label' => 'Sipariş', 'done' => true],
                                                    ['label' => 'Hazırlık', 'done' => in_array($order->status, ['processing', 'shipped', 'delivered', 'completed'])],
                                                    ['label' => 'Kargoda', 'done' => in_array($order->status, ['shipped', 'delivered', 'completed'])],
                                                    ['label' => 'Teslim', 'done' => in_array($order->status, ['delivered', 'completed'])],
                                                ];
                                            @endphp
                                            @foreach($steps as $i => $step)
                                                <div class="flex-1 flex flex-col items-center">
                                                    <div class="w-full bg-gray-200 rounded-full h-1.5 {{ $i > 0 ? '' : '' }}">
                                                        <div class="h-1.5 rounded-full transition-all duration-500 {{ $step['done'] ? 'bg-amber-500' : 'bg-gray-200' }}" style="width: 100%"></div>
                                                    </div>
                                                    <span class="text-[10px] mt-1 font-semibold {{ $step['done'] ? 'text-amber-800' : 'text-gray-400' }}">{{ $step['label'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            </div>

                            {{-- Toplam Tutar --}}
                            <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                                <span class="text-gray-500 text-xs sm:text-sm">Toplam Tutar:</span>
                                <span class="font-black text-gray-900 text-sm sm:text-base">₺{{ number_format($order->grand_total, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Ürünler Listesi -->
                        @if($order->items->isNotEmpty())
                            <div class="mt-4 sm:mt-6">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2 sm:mb-3">Sipariş Edilen Ürünler</h3>
                                <div class="space-y-2">
                                    @foreach($order->items as $item)
                                        <div class="flex items-center p-2.5 sm:p-3.5 bg-gray-50 rounded-xl border border-gray-100 text-sm gap-3 sm:gap-4">
                                            @php
                                                $imageUrl = $item->product?->images->first()?->image_url;
                                            @endphp
                                            <div class="bg-white rounded-lg overflow-hidden border border-gray-200 shrink-0" style="width: 72px; height: 72px; min-width: 72px;">
                                                @if($imageUrl)
                                                    <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                                                @else
                                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0 flex flex-col justify-between gap-1" style="min-height: 72px;">
                                                <div>
                                                    <div class="font-bold text-gray-900 text-xs sm:text-sm leading-tight line-clamp-2">{{ $item->product_name }}</div>
                                                    @if($item->variant_info)
                                                        <div class="text-[11px] sm:text-xs text-gray-500 mt-0.5 truncate">{{ $item->variant_info }}</div>
                                                    @endif
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-gray-900 text-xs sm:text-sm">{{ $item->quantity }} adet</span>
                                                    <div class="text-xs sm:text-sm font-bold text-gray-900">₺{{ number_format($item->unit_price ?: 0, 2, ',', '.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                @else
                    <div class="mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-gray-100 text-xs sm:text-sm text-gray-500">
                        <p>Sipariş numaranız e-posta adresinize ve SMS ile telefonunuza gönderilmiştir.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>

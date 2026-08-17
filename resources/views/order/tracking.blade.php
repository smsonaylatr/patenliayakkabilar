<x-layouts.app>
    <div class="min-h-[70vh] bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-black text-white rounded-2xl mb-4 shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mb-2">Sipariş & Kargo Takibi</h1>
                <p class="text-gray-500 mb-8 text-sm">Siparişinizin durumunu ve kargo takip kodunuzu öğrenmek için sipariş numaranızı girin.</p>
                
                <form action="{{ route('order.tracking') }}" method="GET" class="space-y-4 mb-8">
                    <div>
                        <input type="text" name="order_number" value="{{ request('order_number') }}" class="w-full rounded-xl border-gray-200 focus:ring-black focus:border-black text-center text-lg py-3 uppercase tracking-wider font-mono" placeholder="Sipariş No (Örn: TR505322)" required>
                    </div>
                    <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-3.5 px-6 rounded-xl shadow-md transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Sipariş Sorgula</span>
                    </button>
                </form>

                @if(isset($error))
                    <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-100 text-sm">
                        {{ $error }}
                    </div>
                @endif

                @if(isset($order))
                    @php
                        $rawTrackingCode = trim((string)$order->cargo_tracking_code);
                        $hasRealTrackingCode = !empty($rawTrackingCode) 
                            && $rawTrackingCode !== (string)$order->order_number 
                            && $rawTrackingCode !== (string)$order->id 
                            && $rawTrackingCode !== '#' . (string)$order->order_number;

                        $trackingCode = $hasRealTrackingCode ? $rawTrackingCode : null;
                        $cargoName = $order->cargo_name ?: 'DHL eCommerce';
                        
                        if ($hasRealTrackingCode) {
                            if (stripos($cargoName, 'aras') !== false) {
                                $trackingUrl = 'https://kargotakip.araskargo.com.tr/mainpage.aspx?code=' . urlencode($trackingCode);
                            } elseif (stripos($cargoName, 'yurtiçi') !== false || stripos($cargoName, 'yurtici') !== false) {
                                $trackingUrl = 'https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code=' . urlencode($trackingCode);
                            } else {
                                $trackingUrl = 'https://kargotakip.dhlecommerce.com.tr/?takipNo=' . urlencode($trackingCode);
                            }
                        } else {
                            $trackingUrl = null;
                        }
                    @endphp
                    <div class="mt-6 pt-6 border-t border-gray-100 text-left">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Sipariş Detayları</h2>
                        
                        <div class="bg-gray-50 rounded-2xl p-5 space-y-4 border border-gray-100">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Sipariş No:</span>
                                <div x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ e($order->order_number) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer inline-flex items-center gap-1.5 bg-white px-3 py-1 rounded-lg border border-gray-200 text-sm font-mono font-bold text-gray-900 hover:border-black transition-colors" title="Tıklayarak Kopyala">
                                    <span>#{{ $order->order_number }}</span>
                                    <svg x-show="!copied" class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span x-show="copied" x-cloak class="text-xs text-emerald-600 font-bold">Kopyalandı!</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Sipariş Tarihi:</span>
                                <span class="font-medium text-gray-900 text-sm">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Ödeme Yöntemi:</span>
                                <span class="font-medium text-gray-900 text-sm">
                                    {{ $order->payment_method === 'cash_on_delivery' ? 'Kapıda Ödeme' : ($order->payment_method === 'credit_card' ? 'Kredi Kartı' : 'Havale / EFT') }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                                <span class="text-gray-500 text-sm">Sipariş Durumu:</span>
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
                                <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3 mt-2 shadow-xs">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">KARGO TAKİP KODU</span>
                                        <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-bold">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Kargo Kodu Aktif
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between gap-2 pt-1 border-b border-gray-100 pb-3">
                                        <div>
                                            <div class="font-mono font-black text-black text-xl tracking-wider">{{ $trackingCode }}</div>
                                            <div class="text-xs font-medium text-gray-500 mt-1">Kargo Firması: <strong class="text-gray-900 font-semibold">{{ $cargoName }}</strong></div>
                                        </div>
                                        <div x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ e($trackingCode) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer bg-gray-100 hover:bg-black hover:text-white text-gray-800 text-xs font-bold px-3.5 py-2 rounded-lg transition-colors flex items-center gap-1.5 shadow-xs shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            <span x-show="!copied">Kodu Kopyala</span>
                                            <span x-show="copied" x-cloak class="text-emerald-600 font-bold">Kopyalandı!</span>
                                        </div>
                                    </div>

                                    @if($trackingUrl)
                                    <div class="pt-1 flex items-center justify-between">
                                        <span class="text-xs text-gray-500 font-medium">Kargo Durum Sorgulama:</span>
                                        <a href="{{ $trackingUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                                            <span>Kargo Takip Linki</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <div class="bg-amber-50/70 p-4 rounded-xl border border-amber-200/80 space-y-2 mt-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800">KARGO TAKİP DURUMU</span>
                                        <span class="inline-flex items-center gap-1.5 text-xs text-amber-700 font-bold">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Kargo Kodu Hazırlanıyor
                                        </span>
                                    </div>
                                    <p class="text-xs text-amber-900 leading-relaxed font-medium pt-1">
                                        Siparişiniz Porego / Paketfy depomuza iletilmiştir. Kargo barkodunuz basılıp kuryeye teslim edildiğinde kargo takip numaranız bu alanda ve SMS ile otomatik görünecektir.
                                    </p>
                                </div>
                            @endif
                            </div>

                            <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                                <span class="text-gray-500 text-sm">Toplam Tutar:</span>
                                <span class="font-black text-gray-900 text-base">₺{{ number_format($order->grand_total, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Ürünler Listesi -->
                        @if($order->items->isNotEmpty())
                            <div class="mt-6">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Sipariş Edilen Ürünler</h3>
                                <div class="space-y-2">
                                    @foreach($order->items as $item)
                                        @php
                                            $sku = $item->variant?->sku ?: ($item->product?->sku ?: '-');
                                        @endphp
                                        <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100 text-sm">
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $item->product_name }}</div>
                                                @if($item->variant_info)
                                                    <div class="text-xs text-gray-500">{{ $item->variant_info }}</div>
                                                @endif
                                                @if($sku && $sku !== '-')
                                                    <div class="text-[11px] font-mono text-gray-400 mt-0.5">SKU: {{ $sku }}</div>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <span class="font-bold text-gray-900">{{ $item->quantity }} adet</span>
                                                <div class="text-xs text-gray-500">₺{{ number_format($item->unit_price ?: 0, 2, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                @else
                    <div class="mt-8 pt-8 border-t border-gray-100 text-sm text-gray-500">
                        <p>Sipariş numaranız e-posta adresinize ve SMS ile telefonunuza gönderilmiştir.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>

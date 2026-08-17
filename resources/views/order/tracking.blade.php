<x-layouts.app>
    <div class="min-h-[75vh] bg-slate-50/60 py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto">
            <!-- Header Card -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 mb-6 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-black text-white rounded-2xl mb-3 shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-1.5">Sipariş & Kargo Takibi</h1>
                <p class="text-slate-500 text-xs sm:text-sm max-w-md mx-auto mb-6">Siparişinizin anlık kargo durumunu ve takip numaranızı öğrenmek için sipariş numaranızı girin.</p>
                
                <form action="{{ route('order.tracking') }}" method="GET" class="space-y-3">
                    <div class="relative">
                        <input type="text" name="order_number" value="{{ request('order_number') }}" class="w-full rounded-2xl border-slate-200 focus:ring-black focus:border-black text-center text-lg font-mono font-bold py-3.5 uppercase tracking-wider shadow-inner" placeholder="TR505322" required>
                    </div>
                    <button type="submit" class="w-full bg-black hover:bg-slate-800 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-sm sm:text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Sipariş Sorgula</span>
                    </button>
                </form>

                @if(isset($error))
                    <div class="mt-4 bg-rose-50 text-rose-700 p-4 rounded-2xl border border-rose-100 text-xs sm:text-sm font-medium">
                        {{ $error }}
                    </div>
                @endif
            </div>

            @if(isset($order))
                @php
                    $trackingCode = $order->cargo_tracking_code ?: $order->order_number;
                    $cargoName = $order->cargo_name ?: 'DHL eCommerce';
                    $dhlTrackingUrl = "https://kargotakip.dhlecommerce.com.tr/?takipNo=" . urlencode($trackingCode);
                @endphp

                <!-- Order Details Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="text-lg font-black text-slate-900">Sipariş Bilgileri</h2>
                        <div x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ e($order->order_number) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl text-xs font-mono font-bold text-slate-800 transition-colors" title="Kopyala">
                            <span>#{{ $order->order_number }}</span>
                            <svg x-show="!copied" class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span x-show="copied" x-cloak class="text-emerald-600 font-bold">Kopyalandı</span>
                        </div>
                    </div>

                    <!-- Key-Value Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                        <div class="bg-slate-50/80 p-3.5 rounded-2xl border border-slate-100">
                            <span class="text-slate-400 block text-[11px] uppercase tracking-wider font-bold mb-0.5">Sipariş Tarihi</span>
                            <span class="font-semibold text-slate-800">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="bg-slate-50/80 p-3.5 rounded-2xl border border-slate-100">
                            <span class="text-slate-400 block text-[11px] uppercase tracking-wider font-bold mb-0.5">Ödeme Yöntemi</span>
                            <span class="font-semibold text-slate-800">
                                {{ $order->payment_method === 'cash_on_delivery' ? 'Kapıda Ödeme' : ($order->payment_method === 'credit_card' ? 'Kredi Kartı' : 'Havale / EFT') }}
                            </span>
                        </div>
                    </div>

                    <!-- Status Bar -->
                    <div class="flex items-center justify-between bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                        <span class="text-slate-500 text-xs sm:text-sm font-medium">Sipariş Durumu:</span>
                        <div>
                            @if($order->status === 'pending')
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Bekliyor</span>
                            @elseif($order->status === 'processing')
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Hazırlanıyor</span>
                            @elseif($order->status === 'shipped')
                                <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Kargoya Verildi</span>
                            @elseif($order->status === 'completed' || $order->status === 'delivered')
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Teslim Edildi</span>
                            @elseif($order->status === 'cancelled')
                                <span class="bg-rose-100 text-rose-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">İptal Edildi</span>
                            @else
                                <span class="bg-slate-200 text-slate-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">{{ $order->status }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Modern Cargo Tracking Card -->
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-5 rounded-2xl shadow-md space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-700/60 pb-2.5">
                            <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ $cargoName }} Takip Kodu</span>
                            <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 font-bold bg-emerald-950/80 px-2.5 py-0.5 rounded-full border border-emerald-800/50">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Canlı Takip
                            </span>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                            <span class="font-mono font-black text-2xl tracking-wider text-amber-400">{{ $trackingCode }}</span>
                            
                            <div class="flex items-center gap-2">
                                <div x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ e($trackingCode) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold px-3 py-2 rounded-xl transition-colors flex items-center gap-1.5 border border-slate-700">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span x-show="!copied">Kopyala</span>
                                    <span x-show="copied" x-cloak class="text-emerald-400 font-bold">Kopyalandı!</span>
                                </div>

                                <a href="{{ $dhlTrackingUrl }}" target="_blank" rel="noopener noreferrer" class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black px-4 py-2 rounded-xl transition-all shadow-md hover:shadow-amber-500/20 flex items-center gap-1.5">
                                    <span>DHL'de Sorgula</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Products List -->
                    @if($order->items->isNotEmpty())
                        <div class="pt-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Sipariş Edilen Ürünler</h3>
                            <div class="space-y-2">
                                @foreach($order->items as $item)
                                    @php
                                        $sku = $item->variant?->sku ?: ($item->product?->sku ?: '-');
                                    @endphp
                                    <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-100 text-xs sm:text-sm">
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $item->product_name }}</div>
                                            @if($item->variant_info)
                                                <div class="text-xs text-slate-500 mt-0.5">{{ $item->variant_info }}</div>
                                            @endif
                                            @if($sku && $sku !== '-')
                                                <div class="text-[11px] font-mono text-slate-400 mt-0.5">SKU: {{ $sku }}</div>
                                            @endif
                                        </div>
                                        <div class="text-right shrink-0 ml-3">
                                            <span class="font-bold text-slate-900 block">{{ $item->quantity }} Adet</span>
                                            <div class="text-xs text-slate-500">₺{{ number_format($item->unit_price ?: 0, 2, ',', '.') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Grand Total -->
                    <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                        <span class="text-slate-500 font-medium text-xs sm:text-sm">Toplam Tutar:</span>
                        <span class="font-black text-slate-900 text-lg">₺{{ number_format($order->grand_total, 2, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

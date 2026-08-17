<x-layouts.app>
    <div class="min-h-[70vh] bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-black text-white rounded-2xl mb-4 shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mb-2">Canlı Kargo & Sipariş Takibi</h1>
                <p class="text-gray-500 mb-8 text-sm">Porego kargo takip altyapısı ile siparişinizin anlık durumunu aşağıdan sorgulayabilirsiniz.</p>
                
                <form action="{{ route('order.tracking') }}" method="GET" class="space-y-4 mb-8">
                    <div>
                        <input type="text" name="order_number" value="{{ request('order_number') }}" class="w-full rounded-xl border-gray-200 focus:ring-black focus:border-black text-center text-lg py-3 uppercase tracking-wider font-mono" placeholder="Sipariş No (Örn: TR123456)" required>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-3.5 px-6 rounded-xl shadow-md transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>Siteden Sorgula</span>
                        </button>
                        <a href="https://porego.com/takip/patenliayakkabilar.com" target="_blank" rel="noopener noreferrer" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3.5 px-6 rounded-xl shadow-md transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            <span>Porego Paneli Açarak Takip Et</span>
                        </a>
                    </div>
                </form>

                @if(isset($error))
                    <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-100 text-sm">
                        {{ $error }}
                    </div>
                @endif

                @if(isset($order))
                    <div class="mt-6 pt-6 border-t border-gray-100 text-left">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-gray-900">Sipariş Özeti</h2>
                            <a href="https://porego.com/takip/patenliayakkabilar.com" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 px-3 py-1.5 rounded-lg border border-orange-100 transition-colors">
                                <span>Porego Takip Ekranında Aç</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                        
                        <div class="bg-gray-50 rounded-2xl p-5 space-y-3 border border-gray-100 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Sipariş No:</span>
                                <span class="font-bold text-gray-900 text-sm font-mono">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Tarih:</span>
                                <span class="font-medium text-gray-900 text-sm">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Ödeme Yöntemi:</span>
                                <span class="font-medium text-gray-900 text-sm">
                                    {{ $order->payment_method === 'cash_on_delivery' ? 'Kapıda Ödeme' : ($order->payment_method === 'credit_card' ? 'Kredi Kartı' : 'Havale / EFT') }}
                                </span>
                            </div>
                            @if($order->cargo_tracking_code)
                                <div class="flex justify-between items-center bg-white p-3 rounded-xl border border-gray-200">
                                    <span class="text-gray-700 text-sm font-medium">Kargo Takip Kodu:</span>
                                    <span class="font-mono font-bold text-black text-base">{{ $order->cargo_tracking_code }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Toplam Tutar:</span>
                                <span class="font-black text-gray-900 text-sm">{{ number_format($order->grand_total, 2) }} ₺</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-gray-200 pt-3 mt-3">
                                <span class="text-gray-500 text-sm">Sipariş Durumu:</span>
                                <div>
                                    @if($order->status === 'pending')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Bekliyor</span>
                                    @elseif($order->status === 'processing')
                                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Hazırlanıyor</span>
                                    @elseif($order->status === 'shipped')
                                        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Kargoya Verildi</span>
                                    @elseif($order->status === 'completed')
                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Tamamlandı</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">İptal Edildi</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">{{ $order->status }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Canlı Porego Kargo Takip Modülü (Iframe) -->
                <div class="mt-8 pt-8 border-t border-gray-100 text-left">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span>📦 Porego Canlı Kargo Takip Modülü</span>
                    </h2>
                    <div class="w-full h-[650px] rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm">
                        <iframe src="https://porego.com/takip/patenliayakkabilar.com" class="w-full h-full border-0" title="Porego Kargo Takip"></iframe>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>

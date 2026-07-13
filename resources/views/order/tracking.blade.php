<x-layouts.app>
    <div class="min-h-[70vh] bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 text-center">
                <h1 class="text-3xl font-black text-gray-900 mb-4">Sipariş Takip</h1>
                <p class="text-gray-500 mb-8">Sipariş durumunuzu öğrenmek için sipariş numaranızı girin.</p>
                
                <form action="{{ route('order.tracking') }}" method="GET" class="space-y-4">
                    <div>
                        <input type="text" name="order_number" value="{{ request('order_number') }}" class="w-full rounded-xl border-gray-200 focus:ring-black focus:border-black text-center text-lg py-3 uppercase tracking-wider" placeholder="Örn: TR123456" required>
                    </div>
                    <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all active:scale-[0.98]">
                        Sorgula
                    </button>
                </form>

                @if(isset($error))
                    <div class="mt-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-100 text-sm">
                        {{ $error }}
                    </div>
                @endif

                @if(isset($order))
                    <div class="mt-8 pt-8 border-t border-gray-100 text-left">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Sipariş Detayları</h2>
                        
                        <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Tarih:</span>
                                <span class="font-medium text-gray-900 text-sm">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Ödeme Yöntemi:</span>
                                <span class="font-medium text-gray-900 text-sm">
                                    {{ $order->payment_method === 'cash_on_delivery' ? 'Kapıda Ödeme' : 'Havale / EFT' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Toplam Tutar:</span>
                                <span class="font-bold text-brand-orange text-sm">{{ number_format($order->grand_total, 2) }} ₺</span>
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
                @else
                    <div class="mt-8 pt-8 border-t border-gray-100 text-sm text-gray-500">
                        <p>Sipariş numaranız e-posta adresinize ve SMS ile telefonunuza gönderilmiştir.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>

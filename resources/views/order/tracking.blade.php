<x-layouts.app>
    <div class="min-h-[70vh] bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 text-center">
                <h1 class="text-3xl font-black text-gray-900 mb-4">Sipariş Takip</h1>
                <p class="text-gray-500 mb-8">Sipariş durumunuzu öğrenmek için sipariş numaranızı girin.</p>
                
                <form action="#" method="GET" class="space-y-4">
                    <div>
                        <input type="text" name="order_number" class="w-full rounded-xl border-gray-200 focus:ring-black focus:border-black text-center text-lg py-3 uppercase tracking-wider" placeholder="Örn: PATEN-123456" required>
                    </div>
                    <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all active:scale-[0.98]">
                        Sorgula
                    </button>
                </form>

                <div class="mt-8 pt-8 border-t border-gray-100 text-sm text-gray-500">
                    <p>Sipariş numaranız e-posta adresinize ve SMS ile telefonunuza gönderilmiştir.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

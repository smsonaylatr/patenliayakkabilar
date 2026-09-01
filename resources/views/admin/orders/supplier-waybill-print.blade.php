<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tedarikçi Sipariş İrsaliyesi - Patenli Ayakkabılar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; }
            .print-container { max-width: 100% !important; margin: 0 !important; box-shadow: none !important; border: none !important; }
            .page-break { page-break-before: always; }
            tr, .order-card { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased p-4 md:p-8">

    {{-- Üst Kontrol Çubuğu (Yazdırmada Gizlenir) --}}
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white p-4 rounded-xl shadow-md border border-slate-200 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-3xl">📦</span>
            <div>
                <h1 class="font-bold text-slate-900 text-lg">Tedarikçi Sipariş İrsaliyesi</h1>
                <p class="text-xs text-slate-500">Seçilen {{ $totalOrders }} sipariş için hazırlık ve paketleme listesi</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/orders" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
                ⬅️ Siparişlere Dön
            </a>
            <a href="{{ route('admin.orders.supplier-waybill.download', ['ids' => request('ids')]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                📥 PDF Olarak İndir
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                🖨️ Yazdır / PDF Kaydet
            </button>
        </div>
    </div>

    {{-- Ana Belge --}}
    <div class="print-container max-w-4xl mx-auto bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-slate-900 text-white p-6 md:p-8 flex justify-between items-center border-b border-slate-800">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">📦 Tedarikçi Sipariş İrsaliyesi</h1>
                <p class="text-sm text-slate-400 mt-1">Patenli Ayakkabılar · patenliayakkabilar.com</p>
            </div>
            <div class="text-right">
                <div class="text-xs uppercase tracking-wider text-slate-400">Tarih</div>
                <div class="text-base font-semibold mt-0.5">{{ $date }}</div>
            </div>
        </div>

        {{-- Özet Kartları --}}
        <div class="grid grid-cols-3 bg-slate-50 border-b border-slate-200 divide-x divide-slate-200 text-center p-4">
            <div>
                <div class="text-2xl font-black text-indigo-600">{{ $totalOrders }}</div>
                <div class="text-xs uppercase font-semibold text-slate-500 mt-1">Sipariş Sayısı</div>
            </div>
            <div>
                <div class="text-2xl font-black text-indigo-600">{{ $totalProducts }}</div>
                <div class="text-xs uppercase font-semibold text-slate-500 mt-1">Ürün / Varyant</div>
            </div>
            <div>
                <div class="text-2xl font-black text-amber-600">{{ $totalQuantity }}</div>
                <div class="text-xs uppercase font-semibold text-slate-500 mt-1">Toplam Adet</div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            
            {{-- Konsolide Özet Tablosu --}}
            <div>
                <div class="flex items-center justify-between mb-4 border-b-2 border-indigo-600 pb-2">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span>📋</span> Konsolide Ürün Özeti (Tedarikçi Hazırlık Listesi)
                    </h2>
                    <span class="text-xs text-slate-500">Tüm seçili siparişlerin toplamı</span>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-indigo-600 text-white text-xs uppercase font-semibold tracking-wider">
                                <th class="p-3.5 text-center w-36">Görsel</th>
                                <th class="p-3.5 text-center">Numara / Beden</th>
                                <th class="p-3.5 text-center w-36">Toplam Adet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($consolidated as $item)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3 text-center">
                                        <img src="{{ $item['image'] }}" alt="Ürün" class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-xl border border-slate-200 shadow-sm mx-auto">
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="inline-block bg-indigo-50 text-indigo-800 border border-indigo-200 font-black text-sm md:text-base px-4 py-2 rounded-xl">
                                            {{ $item['variant'] ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="inline-block bg-amber-500 text-white font-black text-base md:text-lg px-4 py-2 rounded-xl shadow-sm">
                                            x{{ $item['quantity'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-100 font-bold text-slate-900">
                                <td colspan="2" class="p-4 text-right text-base">GENEL TOPLAM:</td>
                                <td class="p-4 text-center">
                                    <span class="inline-block bg-slate-900 text-white font-black text-lg px-5 py-2 rounded-xl">
                                        x{{ $totalQuantity }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Sipariş Detayları --}}
            <div>
                <div class="flex items-center justify-between mb-4 border-b-2 border-indigo-600 pb-2">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span>🧾</span> Sipariş Detayları
                    </h2>
                    <span class="text-xs text-slate-500">Sipariş bazında ayrılmış paket listesi</span>
                </div>

                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="order-card border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-slate-50 p-3.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-indigo-700 text-base">#{{ $order['order_number'] }}</span>
                                    <span class="font-bold text-slate-800 text-sm">{{ $order['customer_name'] }}</span>
                                    @if(!empty($order['customer_phone']))
                                        <span class="text-xs text-slate-500 font-mono">({{ $order['customer_phone'] }})</span>
                                    @endif
                                </div>
                                <div class="text-xs font-semibold text-slate-600">
                                    📍 {{ $order['city'] }} {{ $order['district'] ? '/ ' . $order['district'] : '' }} · 🕒 {{ $order['date'] }}
                                </div>
                            </div>

                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-600 text-xs uppercase font-semibold">
                                        <th class="p-2.5 text-center w-28">Görsel</th>
                                        <th class="p-2.5 text-center">Numara / Beden</th>
                                        <th class="p-2.5 text-center w-28">Adet</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($order['items'] as $item)
                                        <tr>
                                            <td class="p-2.5 text-center">
                                                <img src="{{ $item['image'] }}" alt="Ürün" class="w-16 h-16 object-cover rounded-lg border border-slate-200 shadow-sm mx-auto">
                                            </td>
                                            <td class="p-2.5 text-center">
                                                <span class="inline-block bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold text-sm px-3 py-1 rounded-lg">
                                                    {{ $item['variant'] ?: '-' }}
                                                </span>
                                            </td>
                                            <td class="p-2.5 text-center">
                                                <span class="inline-block bg-amber-500 text-white font-bold text-sm px-3.5 py-1 rounded-lg shadow-sm">
                                                    x{{ $item['quantity'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="bg-slate-50 border-t border-slate-200 p-4 text-center text-xs text-slate-500">
            Patenli Ayakkabılar · www.patenliayakkabilar.com · Bu belge tedarikçi sipariş hazırlığı için sistem tarafından otomatik oluşturulmuştur.
        </div>

    </div>

</body>
</html>

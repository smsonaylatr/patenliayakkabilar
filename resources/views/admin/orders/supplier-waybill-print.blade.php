<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tedarikçi Sipariş Sevk İrsaliyesi - Patenli Ayakkabılar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            500: '#0284c7',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; padding: 0 !important; }
            .print-wrapper { max-width: 100% !important; margin: 0 !important; box-shadow: none !important; border: none !important; }
            .page-break { page-break-before: always; }
            .avoid-break { page-break-inside: avoid !important; }
            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 antialiased p-4 md:p-8 selection:bg-brand-500 selection:text-white">

    {{-- Üst Kontrol Çubuğu (Yazdırmada Gizlenir) --}}
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-sm border border-slate-200/80 flex flex-wrap items-center justify-between gap-4 sticky top-4 z-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-lg shadow-sm">
                PA
            </div>
            <div>
                <h1 class="font-bold text-slate-900 text-sm tracking-tight">Tedarikçi Sevk İrsaliyesi</h1>
                <p class="text-xs text-slate-500">{{ $totalOrders }} sipariş · {{ $totalQuantity }} çift ürün</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="/admin/orders" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition duration-150">
                Geri Dön
            </a>
            <button onclick="window.print()" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Yazdır / PDF Olarak Kaydet
            </button>
        </div>
    </div>

    {{-- Ana Belge --}}
    <div class="print-wrapper max-w-4xl mx-auto bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        
        {{-- Kurumsal Header --}}
        <div class="p-6 md:p-8 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white border-b border-slate-800">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-extrabold tracking-wider uppercase">
                            ✓ Sevkiyata Hazır
                        </span>
                        <span class="text-xs text-slate-400 font-medium">Ref: IRS-{{ date('Ymd-His') }}</span>
                    </div>
                    <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-white flex items-center gap-2">
                        PATENLİ AYAKKABILAR
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium tracking-wide">
                        Tedarikçi Mal Kabul & Hazırlık İrsaliyesi
                    </p>
                </div>
                <div class="text-left md:text-right bg-white/5 border border-white/10 p-3 rounded-xl backdrop-blur-sm">
                    <div class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Tarih / Saat</div>
                    <div class="text-sm font-bold text-white mt-0.5">{{ $date }}</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">patenliayakkabilar.com</div>
                </div>
            </div>
        </div>

        {{-- KPI İstatistik Çubuğu --}}
        <div class="grid grid-cols-3 bg-slate-50/80 border-b border-slate-200 divide-x divide-slate-200 text-center">
            <div class="p-4">
                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Sipariş Sayısı</div>
                <div class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalOrders }}</div>
            </div>
            <div class="p-4">
                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Model / Çeşit</div>
                <div class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalProducts }}</div>
            </div>
            <div class="p-4 bg-amber-500/5">
                <div class="text-[10px] uppercase font-bold tracking-wider text-amber-700">Toplam Çift</div>
                <div class="text-2xl font-black text-amber-600 mt-0.5">{{ $totalQuantity }}</div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            
            {{-- Konsolide Ürün Özeti --}}
            <div>
                <div class="flex items-center justify-between mb-3.5 pb-2 border-b-2 border-slate-900">
                    <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-900"></span>
                        Konsolide Hazırlık Listesi
                    </h2>
                    <span class="text-[11px] font-semibold text-slate-500">Aynı model ve numaralar birleştirilmiştir</span>
                </div>

                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white text-[11px] uppercase font-bold tracking-wider">
                                <th class="py-3 px-4 w-32 text-center">Ürün Görseli</th>
                                <th class="py-3 px-4 text-left">Beden / Numara Detayı</th>
                                <th class="py-3 px-4 w-32 text-center">Miktar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80 bg-white">
                            @foreach($consolidated as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- Görsel --}}
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="w-20 h-20 md:w-24 md:h-24 bg-slate-50 rounded-xl border border-slate-200/90 p-1 flex items-center justify-center mx-auto shadow-sm">
                                            <img src="{{ $item['image'] }}" alt="Ürün" class="w-full h-full object-cover rounded-lg">
                                        </div>
                                    </td>
                                    
                                    {{-- Beden / Numara --}}
                                    <td class="py-3.5 px-4">
                                        @php
                                            $vText = (string)($item['variant'] ?: '-');
                                            $parts = explode('/', $vText);
                                        @endphp
                                        <div class="space-y-1">
                                            @if(count($parts) > 1)
                                                <div class="text-xs text-slate-500 font-medium tracking-wide">
                                                    {{ trim($parts[0]) }}
                                                </div>
                                                <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 border border-slate-300 rounded-lg text-slate-900 font-extrabold text-sm md:text-base">
                                                    <span>{{ trim($parts[1]) }}</span>
                                                </div>
                                            @else
                                                <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 border border-slate-300 rounded-lg text-slate-900 font-extrabold text-sm md:text-base">
                                                    <span>{{ $vText }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Miktar --}}
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 text-white font-black text-base md:text-lg rounded-xl shadow-sm tracking-tight">
                                            {{ $item['quantity'] }} Adet
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-950 text-white font-bold">
                                <td colspan="2" class="py-4 px-6 text-right text-xs uppercase tracking-wider font-extrabold">
                                    GENEL TOPLAM HAZIRLANACAK:
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="inline-flex items-center justify-center px-4 py-1.5 bg-white text-slate-950 font-black text-lg rounded-lg">
                                        {{ $totalQuantity }} Çift
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Sipariş Bazında Paket Dağılımı --}}
            <div class="pt-4">
                <div class="flex items-center justify-between mb-3.5 pb-2 border-b-2 border-slate-900">
                    <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-900"></span>
                        Sipariş Bazında Paketleme Detayı
                    </h2>
                    <span class="text-[11px] font-semibold text-slate-500">Müşteri kargo paketleri</span>
                </div>

                <div class="space-y-3.5">
                    @foreach($orders as $order)
                        <div class="avoid-break border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-sm">
                            {{-- Sipariş Başlığı --}}
                            <div class="bg-slate-100/80 px-4 py-2.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="font-black text-slate-950 bg-white border border-slate-300 px-2 py-0.5 rounded-md font-mono text-[11px]">
                                        #{{ $order['order_number'] }}
                                    </span>
                                    <span class="font-bold text-slate-900">{{ $order['customer_name'] }}</span>
                                    @if(!empty($order['customer_phone']))
                                        <span class="text-slate-500 font-mono text-[11px]">· {{ $order['customer_phone'] }}</span>
                                    @endif
                                </div>
                                <div class="text-slate-500 font-semibold text-[11px]">
                                    📍 {{ $order['city'] }} {{ $order['district'] ? '/ ' . $order['district'] : '' }}
                                </div>
                            </div>

                            {{-- Sipariş Kalemleri --}}
                            <div class="p-3 grid grid-cols-1 sm:grid-cols-2 gap-2.5 bg-slate-50/40">
                                @foreach($order['items'] as $item)
                                    <div class="flex items-center gap-3 p-2 bg-white rounded-xl border border-slate-200/80 shadow-xs">
                                        <div class="w-14 h-14 bg-slate-50 rounded-lg border border-slate-200 p-0.5 shrink-0 flex items-center justify-center">
                                            <img src="{{ $item['image'] }}" alt="Ürün" class="w-full h-full object-cover rounded-md">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-bold text-slate-900 truncate">
                                                {{ $item['variant'] ?: 'Standart' }}
                                            </div>
                                            <div class="text-[10px] text-slate-500 mt-0.5">
                                                Hazırlanacak
                                            </div>
                                        </div>
                                        <div class="px-2.5 py-1 bg-slate-900 text-white font-extrabold text-xs rounded-lg shrink-0">
                                            ×{{ $item['quantity'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Kurumsal Alt Bilgi --}}
        <div class="bg-slate-50 border-t border-slate-200 px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-2 text-[11px] text-slate-500">
            <div>
                <strong class="text-slate-700">Patenli Ayakkabılar</strong> · Resmi Tedarikçi Sevk Belgesidir.
            </div>
            <div class="font-mono text-[10px] text-slate-400">
                Sistem Çıktısı: {{ $date }}
            </div>
        </div>

    </div>

    @if(request('auto_print'))
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 400);
            });
        </script>
    @endif
</body>
</html>

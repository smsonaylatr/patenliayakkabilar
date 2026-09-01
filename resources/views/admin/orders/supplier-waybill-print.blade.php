<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tedarikçi Sipariş Sevk İrsaliyesi - Patenli Ayakkabılar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                            950: '#431407',
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
                margin: 8mm 10mm;
            }
        }
    </style>
</head>
<body class="bg-orange-50/40 text-slate-900 antialiased p-3 md:p-8 selection:bg-brand-500 selection:text-white">

    {{-- Üst Kontrol Çubuğu (Yazdırmada Gizlenir) --}}
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white p-4 rounded-2xl shadow-md border border-orange-200/80 flex flex-wrap items-center justify-between gap-4 sticky top-4 z-50">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white flex items-center justify-center font-black text-xl shadow-md shadow-brand-500/20">
                PA
            </div>
            <div>
                <h1 class="font-extrabold text-slate-900 text-sm tracking-tight flex items-center gap-2">
                    Tedarikçi Sevk İrsaliyesi
                    <span class="px-2 py-0.5 bg-brand-100 text-brand-700 text-[10px] font-bold rounded-full">Sipariş Hazırlık</span>
                </h1>
                <p class="text-xs text-slate-500 font-medium">{{ $totalOrders }} sipariş · {{ $totalQuantity }} çift ürün</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="/admin/orders" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition duration-150">
                ⬅ Geri Dön
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white text-xs font-black rounded-xl shadow-lg shadow-brand-500/30 transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Yazdır / PDF Olarak Kaydet
            </button>
        </div>
    </div>

    {{-- Ana Belge --}}
    <div class="print-wrapper max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl shadow-orange-950/5 border-2 border-orange-200 overflow-hidden">
        
        {{-- Turuncu Kurumsal Header --}}
        <div class="p-6 md:p-8 bg-gradient-to-r from-brand-600 via-brand-500 to-amber-500 text-white relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative z-10">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="inline-block px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white border border-white/30 text-[11px] font-black tracking-wider uppercase">
                            📦 Tedarikçi Sevk Emri
                        </span>
                        <span class="text-xs text-orange-100 font-semibold font-mono">#IRS-{{ date('Ymd-His') }}</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white drop-shadow-sm">
                        PATENLİ AYAKKABILAR
                    </h1>
                    <p class="text-xs text-orange-100 mt-1 font-medium tracking-wide">
                        Ürün Hazırlık, Paketleme ve Sevkiyat İrsaliyesi
                    </p>
                </div>
                <div class="text-left md:text-right bg-white/15 border border-white/25 p-3.5 rounded-2xl backdrop-blur-md shadow-sm">
                    <div class="text-[10px] uppercase font-black tracking-widest text-orange-100">Oluşturulma Tarihi</div>
                    <div class="text-sm font-black text-white mt-0.5">{{ $date }}</div>
                    <div class="text-[11px] text-orange-100 font-mono mt-0.5">patenliayakkabilar.com</div>
                </div>
            </div>
        </div>

        {{-- KPI İstatistik Çubuğu --}}
        <div class="grid grid-cols-3 bg-brand-50/60 border-b-2 border-orange-200 divide-x-2 divide-orange-200 text-center">
            <div class="p-4 md:p-5">
                <div class="text-[11px] uppercase font-black tracking-wider text-slate-500">Sipariş Sayısı</div>
                <div class="text-2xl md:text-3xl font-black text-slate-900 mt-0.5">{{ $totalOrders }}</div>
            </div>
            <div class="p-4 md:p-5">
                <div class="text-[11px] uppercase font-black tracking-wider text-slate-500">Model / Çeşit</div>
                <div class="text-2xl md:text-3xl font-black text-slate-900 mt-0.5">{{ $totalProducts }}</div>
            </div>
            <div class="p-4 md:p-5 bg-gradient-to-br from-brand-500/10 to-amber-500/10">
                <div class="text-[11px] uppercase font-black tracking-wider text-brand-700">Toplam Çift</div>
                <div class="text-2xl md:text-3xl font-black text-brand-600 mt-0.5">{{ $totalQuantity }}</div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            
            {{-- Konsolide Ürün Özeti --}}
            <div>
                <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-brand-500">
                    <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-brand-500 shadow-sm shadow-brand-500/50"></span>
                        Konsolide Hazırlık Listesi (Büyük Görsel)
                    </h2>
                    <span class="text-xs font-bold text-brand-700 bg-brand-50 border border-brand-200 px-3 py-1 rounded-full">
                        Toplam {{ $totalQuantity }} Çift Hazırlanacak
                    </span>
                </div>

                <div class="border-2 border-orange-200 rounded-2xl overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-900 to-slate-800 text-white text-xs uppercase font-black tracking-wider">
                                <th class="py-3.5 px-5 w-44 text-center">Büyük Ürün Görseli</th>
                                <th class="py-3.5 px-6 text-left">Beden / Numara & Renk</th>
                                <th class="py-3.5 px-6 w-40 text-center">Hazırlanacak Adet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-orange-100 bg-white">
                            @foreach($consolidated as $item)
                                <tr class="hover:bg-orange-50/30 transition-colors">
                                    {{-- Büyük Görsel & Belirgin Çerçeve --}}
                                    <td class="py-4 px-5 text-center">
                                        <div class="w-32 h-32 md:w-36 md:h-36 bg-white rounded-2xl border-2 border-orange-300 p-1.5 flex items-center justify-center mx-auto shadow-md shadow-orange-500/10">
                                            <img src="{{ $item['image'] }}" alt="Ürün" class="w-full h-full object-cover rounded-xl">
                                        </div>
                                    </td>
                                    
                                    {{-- Beden / Numara Detayı --}}
                                    <td class="py-4 px-6">
                                        @php
                                            $vText = (string)($item['variant'] ?: '-');
                                            $parts = explode('/', $vText);
                                        @endphp
                                        <div class="space-y-2">
                                            @if(count($parts) > 1)
                                                <div class="inline-block text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 px-3 py-1 rounded-lg">
                                                    🎨 {{ trim($parts[0]) }}
                                                </div>
                                                <div>
                                                    <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-brand-400 rounded-2xl text-slate-950 font-black text-lg md:text-xl shadow-xs">
                                                        <span class="text-brand-600 font-extrabold">NUMARA:</span>
                                                        <span>{{ trim($parts[1]) }}</span>
                                                    </span>
                                                </div>
                                            @else
                                                <div>
                                                    <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-brand-400 rounded-2xl text-slate-950 font-black text-lg md:text-xl shadow-xs">
                                                        <span>{{ $vText }}</span>
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Miktar Rozeti --}}
                                    <td class="py-4 px-6 text-center">
                                        <div class="inline-flex items-center justify-center px-5 py-3 bg-gradient-to-r from-brand-500 to-amber-500 text-white font-black text-lg md:text-xl rounded-2xl shadow-md shadow-brand-500/25 tracking-tight border-2 border-brand-600/30">
                                            {{ $item['quantity'] }} Adet
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-950 text-white font-black border-t-2 border-slate-900">
                                <td colspan="2" class="py-5 px-6 text-right text-xs uppercase tracking-wider">
                                    TOPLAM SEVK EDİLECEK ÜRÜN:
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <div class="inline-flex items-center justify-center px-5 py-2.5 bg-brand-500 text-white font-black text-xl rounded-xl shadow-md">
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
                <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-slate-900">
                    <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-slate-900"></span>
                        Sipariş Bazında Ayrılmış Paket Listesi
                    </h2>
                    <span class="text-xs font-semibold text-slate-500">Müşteri koli dağılımı</span>
                </div>

                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="avoid-break border-2 border-orange-200/90 rounded-2xl overflow-hidden bg-white shadow-sm">
                            {{-- Sipariş Başlığı --}}
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-4 py-3 border-b-2 border-orange-200 flex flex-wrap items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-brand-800 bg-white border border-brand-300 px-3 py-1 rounded-lg font-mono text-xs shadow-2xs">
                                        #{{ $order['order_number'] }}
                                    </span>
                                    <span class="font-black text-slate-900 text-sm">{{ $order['customer_name'] }}</span>
                                    @if(!empty($order['customer_phone']))
                                        <span class="text-slate-500 font-mono">({{ $order['customer_phone'] }})</span>
                                    @endif
                                </div>
                                <div class="text-brand-800 font-bold bg-white/80 px-2.5 py-1 rounded-md border border-brand-200">
                                    📍 {{ $order['city'] }} {{ $order['district'] ? '/ ' . $order['district'] : '' }}
                                </div>
                            </div>

                            {{-- Sipariş Kalemleri --}}
                            <div class="p-3.5 grid grid-cols-1 sm:grid-cols-2 gap-3 bg-white">
                                @foreach($order['items'] as $item)
                                    <div class="flex items-center gap-3.5 p-2.5 bg-orange-50/40 rounded-xl border-2 border-orange-200/80 shadow-2xs">
                                        <div class="w-18 h-18 bg-white rounded-xl border-2 border-orange-300 p-1 shrink-0 flex items-center justify-center shadow-xs">
                                            <img src="{{ $item['image'] }}" alt="Ürün" class="w-16 h-16 object-cover rounded-lg">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-black text-slate-900 truncate">
                                                {{ $item['variant'] ?: 'Standart' }}
                                            </div>
                                            <div class="text-[11px] text-brand-700 font-bold mt-0.5">
                                                Koliye Eklenecek
                                            </div>
                                        </div>
                                        <div class="px-3 py-1.5 bg-brand-500 text-white font-black text-sm rounded-xl shrink-0 shadow-xs">
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
        <div class="bg-gradient-to-r from-slate-950 to-slate-900 text-slate-300 px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-2 text-xs border-t-2 border-slate-900">
            <div>
                <strong class="text-white font-bold">Patenli Ayakkabılar</strong> · Resmi Tedarikçi Sevk ve Paketleme Belgesidir.
            </div>
            <div class="font-mono text-[11px] text-orange-300 font-bold">
                Sistem Çıktı Tarihi: {{ $date }}
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

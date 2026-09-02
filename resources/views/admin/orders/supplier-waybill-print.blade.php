<!DOCTYPE html>
<html lang="tr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tedarikçi Sipariş Sevk İrsaliyesi | Patenli Ayakkabılar®</title>
    
    <!-- Web Sitesinin Orijinal Fontları: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Outfit"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"Inter"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            black: '#111111',
                            white: '#FFFFFF',
                            light: '#F6F6F6',
                            dark: '#333333',
                            orange: '#FF7A1A',
                            orangeHover: '#e6690f',
                            blue: '#2F80ED',
                            green: '#22C55E',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #F6F6F6;
            color: #111111;
            -webkit-font-smoothing: antialiased;
        }
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
<body class="p-3 md:p-8 antialiased selection:bg-[#FF7A1A] selection:text-white">

    {{-- Üst Kontrol Çubuğu (Yazdırmada Gizlenir) --}}
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-wrap items-center justify-between gap-4 sticky top-4 z-50">
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" target="_blank" class="text-xl font-black text-black tracking-tighter inline-block">
                PATENLİ<span class="font-light text-gray-700">AYAKKABILAR&reg;</span>
            </a>
            <span class="px-2.5 py-0.5 bg-[#FF7A1A]/10 text-[#FF7A1A] font-bold text-xs rounded-full border border-[#FF7A1A]/20">
                Tedarikçi Paneli
            </span>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/orders" class="px-4 py-2 bg-[#F6F6F6] hover:bg-gray-200 text-black text-xs font-bold rounded-xl transition duration-150">
                Siparişlere Dön
            </a>
            <button onclick="window.print()" class="px-5 py-2 bg-[#FF7A1A] hover:bg-[#e6690f] text-white text-xs font-black rounded-xl shadow-md shadow-[#FF7A1A]/20 transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Yazdır / PDF Kaydet
            </button>
        </div>
    </div>

    {{-- Ana İrsaliye Belgesi --}}
    <div class="print-wrapper max-w-4xl mx-auto bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden">
        
        {{-- Header: Sitenin Marka Siyahı (#111111) ve Turuncu (#FF7A1A) Detayları --}}
        <div class="bg-[#111111] text-white p-6 md:p-8 relative overflow-hidden">
            {{-- Arka plan turuncu ışıltısı --}}
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-[#FF7A1A]/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative z-10">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#22C55E]/20 text-[#22C55E] border border-[#22C55E]/30 text-[10px] font-black tracking-wider uppercase">
                            SEVKİYATA HAZIR
                        </span>
                        <span class="text-xs text-gray-400 font-mono">Ref: IRS-{{ date('Ymd-His') }}</span>
                    </div>
                    
                    {{-- Web Sitesi Orijinal Logo Formatı --}}
                    <div class="text-2xl md:text-3xl font-black text-white tracking-tighter">
                        PATENLİ<span class="font-light text-gray-300">AYAKKABILAR&reg;</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1 font-medium tracking-wide">
                        Tedarikçi Mal Kabul, Hazırlık & Sevkiyat İrsaliyesi
                    </p>
                </div>
                
                <div class="text-left md:text-right bg-white/5 border border-white/10 p-3.5 rounded-2xl backdrop-blur-sm">
                    <div class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Belge Tarihi</div>
                    <div class="text-sm font-black text-[#FF7A1A] mt-0.5">{{ $date }}</div>
                    <div class="text-[11px] text-gray-400 font-mono mt-0.5">patenliayakkabilar.com</div>
                </div>
            </div>
        </div>

        {{-- Sitenin Şerit Sloganı Marquee Stili --}}
        <div class="bg-[#FF7A1A] text-white py-1.5 px-6 text-[11px] font-black tracking-[0.15em] uppercase flex items-center justify-between">
            <span>HER YERDE KAY</span>
            <span>TEDARİKÇİ SİPARİŞ PAKETLEME LİSTESİ</span>
            <span>ORİJİNAL ÜRÜN</span>
        </div>

        {{-- KPI İstatistik Çubuğu --}}
        <div class="grid grid-cols-3 bg-[#F6F6F6] border-b border-gray-200 divide-x divide-gray-200 text-center">
            <div class="p-4 md:p-5">
                <div class="text-[11px] uppercase font-bold tracking-wider text-gray-500">Sipariş Sayısı</div>
                <div class="text-2xl md:text-3xl font-black text-[#111111] mt-0.5">{{ $totalOrders }}</div>
            </div>
            <div class="p-4 md:p-5">
                <div class="text-[11px] uppercase font-bold tracking-wider text-gray-500">Model / Çeşit</div>
                <div class="text-2xl md:text-3xl font-black text-[#111111] mt-0.5">{{ $totalProducts }}</div>
            </div>
            <div class="p-4 md:p-5 bg-[#FF7A1A]/5">
                <div class="text-[11px] uppercase font-bold tracking-wider text-[#FF7A1A]">Toplam Çift</div>
                <div class="text-2xl md:text-3xl font-black text-[#FF7A1A] mt-0.5">{{ $totalQuantity }}</div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            
            {{-- Konsolide Ürün Özeti Tablosu --}}
            <div>
                <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-[#111111]">
                    <h2 class="text-sm font-black uppercase tracking-wider text-[#111111] flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#FF7A1A]"></span>
                        Konsolide Hazırlık Listesi
                    </h2>
                    <span class="text-xs font-bold text-[#FF7A1A] bg-[#FF7A1A]/10 border border-[#FF7A1A]/20 px-3 py-1 rounded-full">
                        Toplam {{ $totalQuantity }} Çift Hazırlanacak
                    </span>
                </div>

                <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#111111] text-white text-xs uppercase font-bold tracking-wider">
                                <th class="py-3.5 px-5 w-44 text-center">Büyük Görsel</th>
                                <th class="py-3.5 px-6 text-left">Beden / Numara & Renk</th>
                                <th class="py-3.5 px-6 w-40 text-center">Hazırlanacak Adet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($consolidated as $item)
                                <tr class="hover:bg-[#F6F6F6] transition-colors">
                                    {{-- Büyük Görsel & Belirgin Sitenin Turuncu Çerçevesi --}}
                                    <td class="py-4 px-5 text-center">
                                        <div class="w-28 h-28 md:w-32 md:h-32 bg-[#F6F6F6] rounded-2xl border-2 border-[#FF7A1A]/40 p-1.5 flex items-center justify-center mx-auto shadow-sm">
                                            <img src="{{ $item['image'] }}" alt="Ürün" class="w-full h-full object-cover rounded-xl">
                                        </div>
                                    </td>
                                    
                                    {{-- Beden / Numara & Renk Detayı --}}
                                    <td class="py-4 px-6">
                                        @php
                                            $vText = (string)($item['variant'] ?: '-');
                                            $parts = explode('/', $vText);
                                        @endphp
                                        <div class="space-y-2">
                                            @if(count($parts) > 1)
                                                <div class="inline-block text-xs font-bold text-gray-500 bg-[#F6F6F6] border border-gray-200 px-3 py-1 rounded-lg">
                                                    {{ trim($parts[0]) }}
                                                </div>
                                                <div>
                                                    <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-[#111111] rounded-2xl text-[#111111] font-black text-lg md:text-xl shadow-sm">
                                                        <span class="text-[#FF7A1A] font-black">NUMARA:</span>
                                                        <span>{{ trim($parts[1]) }}</span>
                                                    </span>
                                                </div>
                                            @else
                                                <div>
                                                    <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-[#111111] rounded-2xl text-[#111111] font-black text-lg md:text-xl shadow-sm">
                                                        <span>{{ $vText }}</span>
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Miktar Rozeti: Sitenin Turuncusu (#FF7A1A) --}}
                                    <td class="py-4 px-6 text-center">
                                        <div class="inline-flex items-center justify-center px-5 py-3 bg-[#FF7A1A] text-white font-black text-lg md:text-xl rounded-2xl shadow-md shadow-[#FF7A1A]/25 tracking-tight">
                                            {{ $item['quantity'] }} Adet
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-[#111111] text-white font-black">
                                <td colspan="2" class="py-4 px-6 text-right text-xs uppercase tracking-wider">
                                    TOPLAM HAZIRLANACAK:
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="inline-flex items-center justify-center px-5 py-2 bg-[#FF7A1A] text-white font-black text-xl rounded-xl">
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
                <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-[#111111]">
                    <h2 class="text-sm font-black uppercase tracking-wider text-[#111111] flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#111111]"></span>
                        Sipariş Bazında Koli Dağılımı
                    </h2>
                    <span class="text-xs font-semibold text-gray-500">Müşteri koli etiketleri</span>
                </div>

                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="avoid-break border border-gray-200 rounded-2xl overflow-hidden bg-white shadow-xs">
                            {{-- Sipariş Başlığı --}}
                            <div class="bg-[#F6F6F6] px-4 py-3 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-black bg-white border border-gray-300 px-3 py-1 rounded-lg font-mono text-xs">
                                        #{{ $order['order_number'] }}
                                    </span>
                                    <span class="font-black text-black text-sm">{{ $order['customer_name'] }}</span>
                                    @if(!empty($order['customer_phone']))
                                        <span class="text-gray-500 font-mono">({{ $order['customer_phone'] }})</span>
                                    @endif
                                </div>
                                <div class="text-[#FF7A1A] font-black bg-white px-3 py-1 rounded-md border border-gray-200">
                                    📍 {{ $order['city'] }} {{ $order['district'] ? '/ ' . $order['district'] : '' }}
                                </div>
                            </div>

                            {{-- Sipariş Kalemleri --}}
                            <div class="p-3.5 grid grid-cols-1 sm:grid-cols-2 gap-3 bg-white">
                                @foreach($order['items'] as $item)
                                    <div class="flex items-center gap-3.5 p-2.5 bg-[#F6F6F6] rounded-xl border border-gray-200">
                                        <div class="w-16 h-16 bg-white rounded-xl border border-gray-200 p-1 shrink-0 flex items-center justify-center">
                                            <img src="{{ $item['image'] }}" alt="Ürün" class="w-14 h-14 object-cover rounded-lg">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-black text-black truncate">
                                                {{ $item['variant'] ?: 'Standart' }}
                                            </div>
                                            <div class="text-[11px] text-[#FF7A1A] font-bold mt-0.5">
                                                Koliye Eklenecek
                                            </div>
                                        </div>
                                        <div class="px-3 py-1.5 bg-[#111111] text-white font-black text-sm rounded-xl shrink-0">
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

        {{-- Kurumsal Footer: Sitenin Footer Tasarımıyla Birebir --}}
        <div class="bg-[#111111] text-gray-400 px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-2 text-xs border-t border-gray-800">
            <div>
                <a href="{{ url('/') }}" target="_blank" class="font-black text-white tracking-tighter">
                    PATENLİ<span class="font-light text-gray-300">AYAKKABILAR&reg;</span>
                </a>
                <span class="text-gray-500 ml-2">· Resmi Tedarikçi Sevk ve Paketleme Belgesi</span>
            </div>
            <div class="font-mono text-[11px] text-[#FF7A1A] font-bold">
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

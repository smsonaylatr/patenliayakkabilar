<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patenli Ayakkabılar — Yapay Zeka Ürün Kataloğu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; font-size: 11px !important; }
            .print-container { max-width: 100% !important; margin: 0 !important; box-shadow: none !important; border: none !important; }
            .page-break { page-break-before: always; }
            tr, .product-card { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased p-3 md:p-6">

    {{-- ÜST KONTROL ÇUBUĞU (Yazdırmada Gizlenir) --}}
    <div class="no-print max-w-5xl mx-auto mb-6 bg-white p-4 rounded-xl shadow-md border border-slate-200 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-3xl">🤖</span>
            <div>
                <h1 class="font-bold text-slate-900 text-lg">Yapay Zeka Ürün Kataloğu</h1>
                <p class="text-xs text-slate-500">Seçilen {{ $summary['total_products'] }} ürün · {{ $summary['total_variants'] }} varyant · {{ $summary['total_stock'] }} toplam stok</p>
            </div>
        </div>
        <div class="flex items-center flex-wrap gap-2">
            <a href="/admin/products" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                ⬅️ Ürünlere Dön
            </a>
            <a href="{{ route('admin.products.ai-catalog.excel', ['ids' => request('ids')]) }}" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                📊 Excel (.xlsx) İndir
            </a>
            <a href="{{ route('admin.products.ai-catalog.csv', ['ids' => request('ids')]) }}" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                📄 CSV İndir
            </a>
            <button onclick="window.print()" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                🖨️ PDF Olarak Kaydet / Yazdır
            </button>
        </div>
    </div>

    {{-- ANA BELGE ALANI --}}
    <div class="print-container max-w-5xl mx-auto bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-slate-900 text-white p-6 md:p-8 flex justify-between items-center border-b border-slate-800">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-sky-400">PATENLİ AYAKKABILAR</h1>
                <p class="text-xs text-slate-400 mt-1">E-Ticaret Ürün Kataloğu · Yapay Zeka LLM Analiz Dokümanı</p>
            </div>
            <div class="text-right">
                <div class="text-xs uppercase tracking-wider text-slate-400">Rapor Tarihi</div>
                <div class="text-sm font-semibold mt-0.5 text-white">{{ $summary['exported_at'] }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $summary['store_name'] }}</div>
            </div>
        </div>

        {{-- AI Prompt Rehberi Banner --}}
        <div class="bg-emerald-50 border-b border-emerald-200 p-4">
            <div class="flex items-start gap-2.5">
                <span class="text-xl">💡</span>
                <div>
                    <h2 class="text-xs font-bold text-emerald-900 uppercase tracking-wide">Yapay Zeka Modelleri İçin Kullanım Bilgisi:</h2>
                    <p class="text-xs text-emerald-800 mt-0.5 leading-relaxed">
                        Bu dokümandaki verileri ChatGPT, Claude, Gemini veya diğer yapay zeka modellerine ekleyerek 
                        <b>müşteri destek yanıtları</b>, <b>beden önerisi</b>, <b>SEO meta optimizasyonu</b> ve <b>satış kurguları</b> 
                        için doğrudan bağlam (grounding context) olarak kullanabilirsiniz.
                    </p>
                </div>
            </div>
        </div>

        {{-- KPI Özet Kartları --}}
        <div class="grid grid-cols-2 md:grid-cols-4 bg-slate-50 border-b border-slate-200 divide-x divide-y md:divide-y-0 divide-slate-200 text-center p-3">
            <div class="p-2">
                <div class="text-xl font-black text-indigo-600">{{ $summary['total_products'] }}</div>
                <div class="text-[10px] uppercase font-semibold text-slate-500 mt-0.5">Toplam Ürün</div>
            </div>
            <div class="p-2">
                <div class="text-xl font-black text-blue-600">{{ $summary['total_variants'] }}</div>
                <div class="text-[10px] uppercase font-semibold text-slate-500 mt-0.5">Beden Varyantı</div>
            </div>
            <div class="p-2">
                <div class="text-xl font-black text-emerald-600">{{ $summary['total_stock'] }} Adet</div>
                <div class="text-[10px] uppercase font-semibold text-slate-500 mt-0.5">Toplam Stok</div>
            </div>
            <div class="p-2">
                <div class="text-xl font-black text-amber-600">₺ (TL)</div>
                <div class="text-[10px] uppercase font-semibold text-slate-500 mt-0.5">Para Birimi</div>
            </div>
        </div>

        {{-- Ürün Kartları Listesi --}}
        <div class="p-4 md:p-6 space-y-6">
            @foreach ($products as $p)
                <div class="product-card border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-white">
                    {{-- Ürün Başlık Çubuğu --}}
                    <div class="bg-slate-800 text-white px-4 py-3 flex flex-wrap justify-between items-center gap-2">
                        <div>
                            <span class="text-xs font-mono text-sky-400 font-bold mr-1.5">#{{ $p['id'] }}</span>
                            <span class="font-bold text-sm text-white">{{ $p['name'] }}</span>
                            <span class="text-xs text-slate-300 ml-2 font-mono">({{ $p['sku'] }})</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ $p['stock'] > 0 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                {{ $p['stock_status'] }} ({{ $p['stock'] }} adet)
                            </span>
                            @if ($p['discount_price'])
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    İndirimli Model
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Ürün Bilgileri Tablosu --}}
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs bg-slate-50 p-3 rounded-lg border border-slate-200">
                            <div><b class="text-slate-600">Satış Fiyatı:</b> <span class="font-bold text-indigo-700 text-sm">{{ $p['effective_price_formatted'] }}</span> @if($p['discount_price']) <span class="line-through text-slate-400 text-xs">{{ $p['price_formatted'] }}</span> @endif</div>
                            <div><b class="text-slate-600">Kategoriler:</b> <span class="text-slate-800">{{ $p['categories'] }}</span></div>
                            <div><b class="text-slate-600">Hedef Kitle:</b> <span class="text-slate-800">{{ $p['gender'] }} ({{ $p['age_group'] }})</span></div>
                            <div><b class="text-slate-600">Kargo & Teslimat:</b> <span class="text-slate-800">{{ $p['delivery_time'] }} (Ücretsiz Kargo)</span></div>
                            <div class="md:col-span-2"><b class="text-slate-600">Özellikler:</b> <span class="text-slate-800">{{ $p['features_summary'] }}</span></div>
                            <div class="md:col-span-2"><b class="text-slate-600">Ürün Canlı URL:</b> <a href="{{ $p['url'] }}" target="_blank" class="text-sky-600 underline font-mono text-[11px]">{{ $p['url'] }}</a></div>
                        </div>

                        {{-- Beden ve Varyant Tablosu --}}
                        @if (!empty($p['variants_list']))
                            <div>
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Beden & Stok Kırılımı:</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs border border-slate-200 rounded-lg overflow-hidden">
                                        <thead class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                                            <tr>
                                                <th class="p-2">Beden / Numara</th>
                                                <th class="p-2">Renk</th>
                                                <th class="p-2">Fiyat</th>
                                                <th class="p-2">Stok</th>
                                                <th class="p-2">Varyant SKU</th>
                                                <th class="p-2">Durum</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach ($p['variants_list'] as $v)
                                                <tr class="hover:bg-slate-50">
                                                    <td class="p-2 font-bold">{{ $v['size'] }}</td>
                                                    <td class="p-2">{{ $v['color'] }}</td>
                                                    <td class="p-2">{{ $v['price_formatted'] }}</td>
                                                    <td class="p-2 font-bold">{{ $v['stock'] }} adet</td>
                                                    <td class="p-2 font-mono text-slate-500">{{ $v['sku'] }}</td>
                                                    <td class="p-2">
                                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $v['stock'] > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                            {{ $v['status'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Açıklamalar --}}
                        @if (!empty($p['short_description']))
                            <div>
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kısa Açıklama:</h3>
                                <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded border border-slate-200 leading-relaxed">{{ $p['short_description'] }}</p>
                            </div>
                        @endif

                        @if (!empty($p['description_clean']))
                            <div>
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Detaylı Ürün Açıklaması (AI Temiz Metin):</h3>
                                <div class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded border border-slate-200 leading-relaxed whitespace-pre-line">{{ $p['description_clean'] }}</div>
                            </div>
                        @endif

                        {{-- SEO Bilgileri --}}
                        <div class="text-xs text-slate-600 bg-amber-50/50 p-2.5 rounded border border-amber-200">
                            <div><b class="text-amber-900">SEO Başlık:</b> {{ $p['meta_title'] }}</div>
                            <div class="mt-1"><b class="text-amber-900">SEO Açıklama:</b> {{ $p['meta_description'] }}</div>
                        </div>

                        {{-- AI Hızlı Bağlam Kartı --}}
                        <div class="bg-indigo-50 border-l-4 border-indigo-600 p-2.5 rounded-r text-[11px] text-indigo-950 font-mono leading-relaxed">
                            <b class="text-indigo-900">⚡ AI Quick Context:</b> {{ $p['ai_context'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="bg-slate-50 p-4 border-t border-slate-200 text-center text-xs text-slate-500">
            Patenli Ayakkabılar &copy; {{ date('Y') }} · <a href="https://patenliayakkabilar.com" target="_blank" class="text-sky-600 hover:underline">patenliayakkabilar.com</a> · Yapay Zeka E-Ticaret Ürün Kataloğu
        </div>
    </div>

    @if(request('auto_print'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
    @endif

</body>
</html>

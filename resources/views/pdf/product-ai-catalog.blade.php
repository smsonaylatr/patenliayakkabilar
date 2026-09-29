<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Patenli Ayakkabılar — Ürün Kataloğu (Yapay Zeka Raporu)</title>
    <style>
        @page {
            margin: 12mm 12mm 15mm 12mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DejaVu Sans', sans-serif;
        }
        body {
            font-size: 9.5px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.35;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            background: #0f172a;
            color: #ffffff;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-radius: 6px;
        }
        .header-table td {
            padding: 12px 16px;
            vertical-align: middle;
        }
        .brand-title {
            font-size: 15px;
            font-weight: bold;
            color: #38bdf8;
            letter-spacing: 0.5px;
        }
        .brand-sub {
            font-size: 8.5px;
            color: #94a3b8;
            margin-top: 3px;
        }
        .report-meta {
            text-align: right;
            font-size: 8.5px;
            color: #cbd5e1;
            line-height: 1.4;
        }

        /* AI PROMPT INSTRUCTION BANNER */
        .ai-banner {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-left: 4px solid #16a34a;
            padding: 8px 12px;
            margin-bottom: 12px;
            border-radius: 4px;
        }
        .ai-banner-title {
            font-size: 10px;
            font-weight: bold;
            color: #166534;
            margin-bottom: 3px;
        }
        .ai-banner-desc {
            font-size: 8.5px;
            color: #14532d;
            line-height: 1.3;
        }

        /* KPI SUMMARY */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 14px;
            border-radius: 4px;
        }
        .summary-table td {
            width: 25%;
            text-align: center;
            padding: 8px;
            border-right: 1px solid #e2e8f0;
        }
        .summary-table td:last-child {
            border-right: none;
        }
        .summary-num {
            font-size: 14px;
            font-weight: bold;
            color: #4338ca;
        }
        .summary-lbl {
            font-size: 7.5px;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 2px;
        }

        /* PRODUCT CARDS */
        .product-card {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            border-radius: 5px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .card-header {
            background: #1e293b;
            color: #ffffff;
            padding: 8px 12px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .product-name {
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
        }
        .product-sku {
            font-size: 8.5px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .card-body {
            padding: 10px 12px;
        }

        /* SPECS GRID */
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .specs-table td {
            padding: 5px 8px;
            font-size: 8.5px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .specs-label {
            font-weight: bold;
            color: #475569;
            width: 22%;
        }
        .specs-val {
            color: #0f172a;
            width: 28%;
        }

        /* VARIANTS TABLE */
        .variants-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8px;
        }
        .variants-table th {
            background: #e2e8f0;
            color: #334155;
            padding: 4px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #cbd5e1;
        }
        .variants-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        .variants-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        /* BADGES */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-warning { background: #fef3c7; color: #b45309; }

        /* TEXT SECTIONS */
        .text-section-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #334155;
            margin-top: 6px;
            margin-bottom: 2px;
            text-transform: uppercase;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 1px;
        }
        .text-desc {
            font-size: 8px;
            color: #334155;
            line-height: 1.35;
            white-space: pre-line;
            margin-bottom: 6px;
        }

        /* AI PROMPT CARD */
        .ai-context-box {
            background: #f1f5f9;
            border-left: 3px solid #6366f1;
            padding: 6px 8px;
            font-size: 7.5px;
            color: #312e81;
            line-height: 1.3;
            margin-top: 4px;
            border-radius: 2px;
        }

        .page-footer {
            position: fixed;
            bottom: -8mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
        }
    </style>
</head>
<body>

    <div class="page-footer">
        Patenli Ayakkabılar &copy; {{ date('Y') }} — patenliayakkabilar.com | Yapay Zeka Ürün Kataloğu & Veri Dökümü
    </div>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td>
                <div class="brand-title">PATENLİ AYAKKABILAR</div>
                <div class="brand-sub">E-Ticaret Ürün Kataloğu — Yapay Zeka & Sistem Veri Raporu</div>
            </td>
            <td class="report-meta">
                <div><b>Tarih:</b> {{ $summary['exported_at'] }}</div>
                <div><b>Mağaza:</b> {{ $summary['store_name'] }}</div>
                <div><b>Format:</b> Yapay Zeka LLM Analiz Dokümanı</div>
            </td>
        </tr>
    </table>

    <!-- AI PROMPT BANNER -->
    <div class="ai-banner">
        <div class="ai-banner-title">🤖 YAPAY ZEKA MODEL TALİMATI & SİSTEM BAĞLAMI:</div>
        <div class="ai-banner-desc">
            Bu belge Patenli Ayakkabılar mağazasının canlı ürün kataloğunu, varyant bazlı beden/stok seviyelerini, fiyat politikalarını, ürün teknik özelliklerini ve SEO metinlerini içerir. Yapay zeka modelleri (ChatGPT, Claude, Gemini, DeepSeek vb.) bu verileri <b>Müşteri Hizmetleri Desteği</b>, <b>Beden Önerisi</b>, <b>Satış & Kampanya Analizi</b> ve <b>İçerik Optimizasyonu</b> için zemin bilgi (grounding context) olarak kullanabilir.
        </div>
    </div>

    <!-- KPI SUMMARY -->
    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-num">{{ $summary['total_products'] }}</div>
                <div class="summary-lbl">Toplam Ürün Modeli</div>
            </td>
            <td>
                <div class="summary-num">{{ $summary['total_variants'] }}</div>
                <div class="summary-lbl">Toplam Beden Varyantı</div>
            </td>
            <td>
                <div class="summary-num">{{ $summary['total_stock'] }} Adet</div>
                <div class="summary-lbl">Katalog Toplam Stoğu</div>
            </td>
            <td>
                <div class="summary-num">₺ (TL)</div>
                <div class="summary-lbl">Para Birimi & Ücretsiz Kargo</div>
            </td>
        </tr>
    </table>

    <!-- PRODUCT CARDS -->
    @foreach ($products as $p)
        <div class="product-card">
            <!-- Header -->
            <div class="card-header">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle;">
                            <div class="product-name">#{{ $p['id'] }} — {{ $p['name'] }}</div>
                            <div class="product-sku">SKU: {{ $p['sku'] }} | Marka: {{ $p['brand'] }}</div>
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <span class="badge {{ $p['stock'] > 0 ? 'badge-success' : 'badge-danger' }}">
                                {{ $p['stock_status'] }} ({{ $p['stock'] }} Adet)
                            </span>
                            @if ($p['discount_price'])
                                <span class="badge badge-warning">İndirimli Model</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Body -->
            <div class="card-body">
                <!-- Specs Table -->
                <table class="specs-table">
                    <tr>
                        <td class="specs-label">Satış Fiyatı:</td>
                        <td class="specs-val">
                            <b style="color: #4338ca; font-size: 10px;">{{ $p['effective_price_formatted'] }}</b>
                            @if ($p['discount_price'])
                                <span style="text-decoration: line-through; color: #94a3b8; font-size: 8px;">{{ $p['price_formatted'] }}</span>
                            @endif
                        </td>
                        <td class="specs-label">Kategoriler:</td>
                        <td class="specs-val">{{ $p['categories'] }}</td>
                    </tr>
                    <tr>
                        <td class="specs-label">Hedef Kitle / Cinsiyet:</td>
                        <td class="specs-val">{{ $p['gender'] }} ({{ $p['age_group'] }})</td>
                        <td class="specs-label">Kargo / Teslimat:</td>
                        <td class="specs-val">{{ $p['delivery_time'] }} (Ücretsiz Kargo)</td>
                    </tr>
                    <tr>
                        <td class="specs-label">Özellikler:</td>
                        <td class="specs-val" colspan="3">{{ $p['features_summary'] }}</td>
                    </tr>
                    <tr>
                        <td class="specs-label">Ürün Canlı URL:</td>
                        <td class="specs-val" colspan="3" style="color: #0284c7; word-break: break-all;">
                            {{ $p['url'] }}
                        </td>
                    </tr>
                </table>

                <!-- Variants Table -->
                @if (!empty($p['variants_list']))
                    <div class="text-section-title">BEDEN & VARYANT STOK TABLOSU:</div>
                    <table class="variants-table">
                        <thead>
                            <tr>
                                <th>Beden / Numara</th>
                                <th>Renk</th>
                                <th>Varyant Fiyatı</th>
                                <th>Stok Miktarı</th>
                                <th>Varyant SKU</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($p['variants_list'] as $v)
                                <tr>
                                    <td><b>{{ $v['size'] }}</b></td>
                                    <td>{{ $v['color'] }}</td>
                                    <td>{{ $v['price_formatted'] }}</td>
                                    <td><b>{{ $v['stock'] }} adet</b></td>
                                    <td style="color: #64748b;">{{ $v['sku'] }}</td>
                                    <td>
                                        <span class="badge {{ $v['stock'] > 0 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $v['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <!-- Descriptions -->
                @if (!empty($p['short_description']))
                    <div class="text-section-title">Kısa Açıklama (Özet):</div>
                    <div class="text-desc">{{ $p['short_description'] }}</div>
                @endif

                @if (!empty($p['description_clean']))
                    <div class="text-section-title">Detaylı Açıklama & Ürün Bilgisi:</div>
                    <div class="text-desc">{{ $p['description_clean'] }}</div>
                @endif

                <!-- SEO & Metadata -->
                <div class="text-section-title">SEO & Arama Motoru Optimizasyonu:</div>
                <div style="font-size: 7.5px; color: #475569; margin-bottom: 4px;">
                    <div><b>Meta Title:</b> {{ $p['meta_title'] }}</div>
                    <div><b>Meta Description:</b> {{ $p['meta_description'] }}</div>
                </div>

                <!-- AI Quick Context Card -->
                <div class="ai-context-box">
                    <b>⚡ AI Quick Context:</b> {{ $p['ai_context'] }}
                </div>
            </div>
        </div>
    @endforeach

</body>
</html>

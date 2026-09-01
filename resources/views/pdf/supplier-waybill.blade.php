<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tedarikçi Sipariş Sevk İrsaliyesi</title>
    <style>
        @page {
            margin: 8mm 8mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DejaVu Sans', sans-serif;
        }
        body {
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.3;
        }

        .header-table {
            width: 100%;
            background: #ea580c;
            color: #ffffff;
            border-collapse: collapse;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        .header-table td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff7ed;
            border: 2px solid #fdba74;
            margin-bottom: 14px;
            border-radius: 6px;
        }
        .summary-table td {
            width: 33.33%;
            text-align: center;
            padding: 10px;
            border-right: 1px solid #fed7aa;
        }
        .summary-table td:last-child {
            border-right: none;
        }
        .summary-num {
            font-size: 18px;
            font-weight: bold;
            color: #ea580c;
        }
        .summary-lbl {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
            color: #9a3412;
            margin-top: 2px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            padding-bottom: 4px;
            margin-bottom: 8px;
            border-bottom: 2px solid #ea580c;
        }

        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 2px solid #fdba74;
        }
        .table-custom th {
            background: #0f172a;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: center;
            font-weight: bold;
        }
        .table-custom td {
            padding: 8px 10px;
            border-bottom: 1px solid #fed7aa;
            vertical-align: middle;
            font-size: 10px;
            text-align: center;
        }
        .table-custom tr:nth-child(even) td {
            background: #fffaf5;
        }

        .order-box {
            border: 1px solid #fdba74;
            margin-bottom: 10px;
            page-break-inside: avoid;
            border-radius: 4px;
        }
        .order-head {
            background: #ffedd5;
            padding: 6px 10px;
            border-bottom: 1px solid #fdba74;
            font-size: 9px;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-table th {
            background: #fed7aa;
            color: #7c2d12;
            font-size: 8px;
            text-transform: uppercase;
            padding: 5px 8px;
            text-align: center;
        }
        .order-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ffedd5;
            font-size: 10px;
            vertical-align: middle;
            text-align: center;
        }

        .badge-variant {
            background: #ffedd5;
            border: 1px solid #fb923c;
            color: #9a3412;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }
        .badge-qty {
            background: #ea580c;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }

        .img-thumb-large {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #fb923c;
            display: block;
            margin: 0 auto;
            background: #ffffff;
            padding: 2px;
        }

        .img-thumb-small {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #fb923c;
            display: block;
            margin: 0 auto;
            background: #ffffff;
        }

        .footer-text {
            text-align: center;
            font-size: 8px;
            color: #9a3412;
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #fed7aa;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td>
                <div style="font-size: 16px; font-weight: bold;">📦 PATENLİ AYAKKABILAR</div>
                <div style="font-size: 9px; color: #ffedd5; margin-top: 2px;">Tedarikçi Mal Kabul ve Sevkiyat İrsaliyesi</div>
            </td>
            <td style="text-align: right;">
                <div style="font-size: 8px; color: #ffedd5; text-transform: uppercase;">Belge Tarihi</div>
                <div style="font-size: 11px; font-weight: bold; margin-top: 2px;">{{ $date }}</div>
            </td>
        </tr>
    </table>

    {{-- SUMMARY --}}
    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-num">{{ $totalOrders }}</div>
                <div class="summary-lbl">Sipariş Sayısı</div>
            </td>
            <td>
                <div class="summary-num">{{ $totalProducts }}</div>
                <div class="summary-lbl">Model / Çeşit</div>
            </td>
            <td>
                <div class="summary-num" style="color: #ea580c;">{{ $totalQuantity }}</div>
                <div class="summary-lbl">Toplam Çift</div>
            </td>
        </tr>
    </table>

    {{-- KONSOLİDE TABLO --}}
    <div class="section-title">📋 Konsolide Hazırlık Listesi (Büyük Görsel)</div>
    <table class="table-custom">
        <thead>
            <tr>
                <th style="width: 100px;">Büyük Görsel</th>
                <th>Beden / Numara & Renk</th>
                <th style="width: 110px;">Hazırlanacak Adet</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consolidated as $item)
                <tr>
                    <td>
                        <img src="{{ $item['image'] }}" class="img-thumb-large">
                    </td>
                    <td>
                        <span class="badge-variant">{{ $item['variant'] ?: '-' }}</span>
                    </td>
                    <td>
                        <span class="badge-qty">{{ $item['quantity'] }} Adet</span>
                    </td>
                </tr>
            @endforeach
            <tr style="background: #0f172a; color: #ffffff; font-weight: bold;">
                <td colspan="2" style="text-align: right; padding-right: 16px; font-size: 11px; color: #ffffff;">
                    TOPLAM SEVK EDİLECEK ÜRÜN:
                </td>
                <td>
                    <span style="background: #ea580c; color: #fff; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 12px;">
                        {{ $totalQuantity }} Çift
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- SİPARİŞ DETAYLARI --}}
    <div class="section-title" style="margin-top: 14px;">🧾 Sipariş Bazında Paket Dağılımı</div>

    @foreach($orders as $order)
        <div class="order-box">
            <div class="order-head">
                <table width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left;">
                            <strong style="color: #9a3412; font-size: 10px;">#{{ $order['order_number'] }}</strong>
                            <span style="margin-left: 8px; font-weight: bold; color: #0f172a;">{{ $order['customer_name'] }}</span>
                            @if(!empty($order['customer_phone']))
                                <span style="color: #64748b; margin-left: 6px;">({{ $order['customer_phone'] }})</span>
                            @endif
                        </td>
                        <td style="text-align: right; color: #7c2d12; font-weight: bold;">
                            📍 {{ $order['city'] }} · 🕒 {{ $order['date'] }}
                        </td>
                    </tr>
                </table>
            </div>

            <table class="order-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Görsel</th>
                        <th>Numara / Beden</th>
                        <th style="width: 80px;">Adet</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order['items'] as $item)
                        <tr>
                            <td>
                                <img src="{{ $item['image'] }}" class="img-thumb-small">
                            </td>
                            <td>
                                <span class="badge-variant">{{ $item['variant'] ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="badge-qty">×{{ $item['quantity'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer-text">
        Patenli Ayakkabılar · www.patenliayakkabilar.com · Bu irsaliye sipariş hazırlığı ve koli kontrolü için sistem tarafından otomatik üretilmiştir.
    </div>

</body>
</html>

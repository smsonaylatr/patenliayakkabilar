<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tedarikçi Sipariş İrsaliyesi</title>
    <style>
        @page {
            margin: 10mm 10mm;
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
            background: #0f172a;
            color: #ffffff;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-radius: 6px;
        }
        .header-table td {
            padding: 14px 18px;
            vertical-align: middle;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 16px;
            border-radius: 6px;
        }
        .summary-table td {
            width: 33.33%;
            text-align: center;
            padding: 10px;
            border-right: 1px solid #e2e8f0;
        }
        .summary-table td:last-child {
            border-right: none;
        }
        .summary-num {
            font-size: 16px;
            font-weight: bold;
            color: #4338ca;
        }
        .summary-lbl {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 2px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            padding-bottom: 4px;
            margin-bottom: 8px;
            border-bottom: 2px solid #4338ca;
        }

        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #cbd5e1;
        }
        .table-custom th {
            background: #4338ca;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: center;
            font-weight: bold;
        }
        .table-custom td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            font-size: 10px;
            text-align: center;
        }
        .table-custom tr:nth-child(even) td {
            background: #f8fafc;
        }

        .order-box {
            border: 1px solid #cbd5e1;
            margin-bottom: 12px;
            page-break-inside: avoid;
            border-radius: 4px;
        }
        .order-head {
            background: #f1f5f9;
            padding: 6px 10px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 9px;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-table th {
            background: #e2e8f0;
            color: #475569;
            font-size: 8px;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: center;
        }
        .order-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10px;
            vertical-align: middle;
            text-align: center;
        }

        .badge-variant {
            background: #e0e7ff;
            color: #3730a3;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            display: inline-block;
        }
        .badge-qty {
            background: #f59e0b;
            color: #ffffff;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            display: inline-block;
        }

        .img-thumb-large {
            width: 58px;
            height: 58px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            display: block;
            margin: 0 auto;
        }

        .img-thumb-small {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            display: block;
            margin: 0 auto;
        }

        .footer-text {
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td>
                <div style="font-size: 16px; font-weight: bold;">📦 Tedarikçi Sipariş İrsaliyesi</div>
                <div style="font-size: 9px; color: #94a3b8; margin-top: 2px;">Patenli Ayakkabılar · patenliayakkabilar.com</div>
            </td>
            <td style="text-align: right;">
                <div style="font-size: 8px; color: #94a3b8; text-transform: uppercase;">Belge Tarihi</div>
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
                <div class="summary-lbl">Ürün / Varyant</div>
            </td>
            <td>
                <div class="summary-num" style="color: #d97706;">{{ $totalQuantity }}</div>
                <div class="summary-lbl">Toplam Adet</div>
            </td>
        </tr>
    </table>

    {{-- KONSOLİDE TABLO --}}
    <div class="section-title">📋 Konsolide Ürün Özeti (Tedarikçi Hazırlık Listesi)</div>
    <table class="table-custom">
        <thead>
            <tr>
                <th style="width: 80px;">Görsel</th>
                <th>Numara / Beden</th>
                <th style="width: 100px;">Toplam Adet</th>
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
                        <span class="badge-qty">x{{ $item['quantity'] }}</span>
                    </td>
                </tr>
            @endforeach
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="2" style="text-align: right; padding-right: 16px; font-size: 11px;">GENEL TOPLAM:</td>
                <td>
                    <span style="background: #0f172a; color: #fff; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 12px;">
                        x{{ $totalQuantity }}
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- SİPARİŞ DETAYLARI --}}
    <div class="section-title" style="margin-top: 14px;">🧾 Sipariş Detayları</div>

    @foreach($orders as $order)
        <div class="order-box">
            <div class="order-head">
                <table width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left;">
                            <strong style="color: #1e1b4b; font-size: 10px;">#{{ $order['order_number'] }}</strong>
                            <span style="margin-left: 8px; font-weight: bold;">{{ $order['customer_name'] }}</span>
                            @if(!empty($order['customer_phone']))
                                <span style="color: #64748b; margin-left: 6px;">({{ $order['customer_phone'] }})</span>
                            @endif
                        </td>
                        <td style="text-align: right; color: #64748b;">
                            {{ $order['city'] }} · {{ $order['date'] }}
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
                                <span class="badge-qty">x{{ $item['quantity'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer-text">
        Patenli Ayakkabılar · www.patenliayakkabilar.com · Bu irsaliye sipariş hazırlığı için sistem tarafından otomatik oluşturulmuştur.
    </div>

</body>
</html>

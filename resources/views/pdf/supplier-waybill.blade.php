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
            color: #111111;
            background: #ffffff;
            line-height: 1.3;
        }

        .header-table {
            width: 100%;
            background: #111111;
            color: #ffffff;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-radius: 6px;
        }
        .header-table td {
            padding: 14px 16px;
            vertical-align: middle;
        }

        .stripe-banner {
            background: #FF7A1A;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            padding: 4px 10px;
            text-align: center;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            background: #F6F6F6;
            border: 1px solid #e5e7eb;
            margin-bottom: 14px;
            border-radius: 6px;
        }
        .summary-table td {
            width: 33.33%;
            text-align: center;
            padding: 10px;
            border-right: 1px solid #e5e7eb;
        }
        .summary-table td:last-child {
            border-right: none;
        }
        .summary-num {
            font-size: 18px;
            font-weight: bold;
            color: #111111;
        }
        .summary-lbl {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
            color: #6b7280;
            margin-top: 2px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #111111;
            padding-bottom: 4px;
            margin-bottom: 8px;
            border-bottom: 2px solid #111111;
        }

        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #e5e7eb;
        }
        .table-custom th {
            background: #111111;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: center;
            font-weight: bold;
        }
        .table-custom td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
            font-size: 10px;
            text-align: center;
        }
        .table-custom tr:nth-child(even) td {
            background: #fafafa;
        }

        .order-box {
            border: 1px solid #e5e7eb;
            margin-bottom: 10px;
            page-break-inside: avoid;
            border-radius: 4px;
        }
        .order-head {
            background: #F6F6F6;
            padding: 6px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-table th {
            background: #f3f4f6;
            color: #374151;
            font-size: 8px;
            text-transform: uppercase;
            padding: 5px 8px;
            text-align: center;
        }
        .order-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 10px;
            vertical-align: middle;
            text-align: center;
        }

        .badge-variant {
            background: #ffffff;
            border: 2px solid #111111;
            color: #111111;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            display: inline-block;
        }
        .badge-qty {
            background: #FF7A1A;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            display: inline-block;
        }

        .img-thumb-large {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #FF7A1A;
            display: block;
            margin: 0 auto;
            background: #ffffff;
            padding: 2px;
        }

        .img-thumb-small {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
            display: block;
            margin: 0 auto;
            background: #ffffff;
        }

        .footer-text {
            text-align: center;
            font-size: 8px;
            color: #6b7280;
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td>
                <div style="font-size: 16px; font-weight: bold; color: #ffffff;">
                    PATENLİ AYAKKABILAR®
                </div>
                <div style="font-size: 9px; color: #9ca3af; margin-top: 2px;">
                    Tedarikçi Mal Kabul, Hazırlık & Sevkiyat İrsaliyesi
                </div>
            </td>
            <td style="text-align: right;">
                <div style="font-size: 8px; color: #9ca3af; text-transform: uppercase;">Belge Tarihi</div>
                <div style="font-size: 11px; font-weight: bold; color: #FF7A1A; margin-top: 2px;">{{ $date }}</div>
            </td>
        </tr>
    </table>

    <div class="stripe-banner">
        HER YERDE KAY · TEDARİKÇİ SİPARİŞ PAKETLEME LİSTESİ
    </div>

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
                <div class="summary-num" style="color: #FF7A1A;">{{ $totalQuantity }}</div>
                <div class="summary-lbl" style="color: #FF7A1A;">Toplam Çift</div>
            </td>
        </tr>
    </table>

    {{-- KONSOLİDE TABLO --}}
    <div class="section-title">📋 Konsolide Hazırlık Listesi</div>
    <table class="table-custom">
        <thead>
            <tr>
                <th style="width: 100px;">Büyük Görsel</th>
                <th>Beden / Numara Detayı</th>
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
            <tr style="background: #111111; color: #ffffff; font-weight: bold;">
                <td colspan="2" style="text-align: right; padding-right: 16px; font-size: 11px; color: #ffffff;">
                    TOPLAM HAZIRLANACAK:
                </td>
                <td>
                    <span style="background: #FF7A1A; color: #fff; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 12px;">
                        {{ $totalQuantity }} Çift
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- SİPARİŞ DETAYLARI --}}
    <div class="section-title" style="margin-top: 14px;">🧾 Sipariş Bazında Koli Dağılımı</div>

    @foreach($orders as $order)
        <div class="order-box">
            <div class="order-head">
                <table width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left;">
                            <strong style="color: #111111; font-size: 10px;">#{{ $order['order_number'] }}</strong>
                            <span style="margin-left: 8px; font-weight: bold; color: #111111;">{{ $order['customer_name'] }}</span>
                            @if(!empty($order['customer_phone']))
                                <span style="color: #6b7280; margin-left: 6px;">({{ $order['customer_phone'] }})</span>
                            @endif
                        </td>
                        <td style="text-align: right; color: #FF7A1A; font-weight: bold;">
                            📍 {{ $order['city'] }} · 🕒 {{ $order['date'] }}
                        </td>
                    </tr>
                </table>
            </div>

            <table class="order-table">
                <thead>
                    <tr>
                        <th style="width: 65px;">Görsel</th>
                        <th>Numara / Beden</th>
                        <th style="width: 75px;">Adet</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order['items'] as $item)
                        <tr>
                            <td>
                                <img src="{{ $item['image'] }}" class="img-thumb-small">
                            </td>
                            <td>
                                <span class="badge-variant" style="font-size: 10px; padding: 2px 6px;">{{ $item['variant'] ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="badge-qty" style="font-size: 10px; padding: 2px 8px;">×{{ $item['quantity'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer-text">
        Patenli Ayakkabılar® · www.patenliayakkabilar.com · Bu irsaliye sipariş hazırlığı ve koli kontrolü için sistem tarafından otomatik üretilmiştir.
    </div>

</body>
</html>

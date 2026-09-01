<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tedarikçi Sipariş İrsaliyesi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }

        .header {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: #fff;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-left h1 { font-size: 20px; font-weight: 700; }
        .header-left p { font-size: 11px; color: #94a3b8; margin-top: 4px; }
        .header-right { text-align: right; font-size: 11px; color: #cbd5e1; }
        .header-right .date-label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }

        .summary-bar {
            background: #f1f5f9;
            padding: 12px 30px;
            display: flex;
            gap: 30px;
            border-bottom: 2px solid #e2e8f0;
        }
        .summary-item { text-align: center; }
        .summary-item .num { font-size: 18px; font-weight: 700; color: #6366f1; }
        .summary-item .lbl { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }

        .content { padding: 20px 30px; }

        .order-block {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 16px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .order-header {
            background: #f8fafc;
            padding: 10px 16px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-header .order-num { font-weight: 700; font-size: 13px; color: #1e293b; }
        .order-header .order-meta { font-size: 10px; color: #64748b; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background: #f1f5f9;
            padding: 8px 12px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .items-table tr:last-child td { border-bottom: none; }

        .product-cell { display: flex; align-items: center; gap: 10px; }
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .product-name { font-weight: 600; font-size: 11px; color: #1e293b; }

        .variant-badge {
            display: inline-block;
            background: #6366f1;
            color: #fff;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
        }
        .qty-badge {
            display: inline-block;
            background: #f59e0b;
            color: #fff;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
        }

        .footer {
            margin-top: 20px;
            padding: 16px 30px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }

        /* Consolidated summary table */
        .consolidated {
            margin-bottom: 24px;
        }
        .consolidated h2 {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #6366f1;
        }
        .consolidated-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        .consolidated-table th {
            background: #6366f1;
            color: #fff;
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .consolidated-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .consolidated-table tr:nth-child(even) { background: #f8fafc; }
        .consolidated-table tr:last-child td { border-bottom: none; }
        .consolidated-table .total-row {
            background: #f1f5f9;
            font-weight: 700;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <table width="100%" style="background: linear-gradient(135deg, #1e293b, #334155); color: #fff; padding: 0;">
        <tr>
            <td style="padding: 20px 30px;">
                <div style="font-size: 20px; font-weight: 700;">📦 Tedarikçi Sipariş İrsaliyesi</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Patenli Ayakkabılar</div>
            </td>
            <td style="padding: 20px 30px; text-align: right;">
                <div style="font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Oluşturulma Tarihi</div>
                <div style="font-size: 13px; font-weight: 600; margin-top: 2px;">{{ $date }}</div>
            </td>
        </tr>
    </table>

    {{-- SUMMARY BAR --}}
    <table width="100%" style="background: #f1f5f9; border-bottom: 2px solid #e2e8f0;">
        <tr>
            <td style="padding: 12px 30px; text-align: center;">
                <div style="font-size: 18px; font-weight: 700; color: #6366f1;">{{ $totalOrders }}</div>
                <div style="font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Sipariş</div>
            </td>
            <td style="padding: 12px 30px; text-align: center;">
                <div style="font-size: 18px; font-weight: 700; color: #6366f1;">{{ $totalProducts }}</div>
                <div style="font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Ürün Çeşidi</div>
            </td>
            <td style="padding: 12px 30px; text-align: center;">
                <div style="font-size: 18px; font-weight: 700; color: #6366f1;">{{ $totalQuantity }}</div>
                <div style="font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Toplam Adet</div>
            </td>
        </tr>
    </table>

    <div style="padding: 20px 30px;">
        {{-- CONSOLIDATED SUMMARY --}}
        <div style="margin-bottom: 24px;">
            <h2 style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 2px solid #6366f1;">
                📋 Konsolide Ürün Özeti (Tedarikçi İçin)
            </h2>
            <table class="consolidated-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Görsel</th>
                        <th>Ürün Adı</th>
                        <th style="width: 80px;">Numara</th>
                        <th style="width: 80px;">Toplam Adet</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consolidated as $item)
                        <tr>
                            <td>
                                <img src="{{ $item['image'] }}" class="product-img" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                            </td>
                            <td style="font-weight: 600; font-size: 11px;">{{ $item['name'] }}</td>
                            <td>
                                <span class="variant-badge" style="display: inline-block; background: #6366f1; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">
                                    {{ $item['variant'] ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="qty-badge" style="display: inline-block; background: #f59e0b; color: #fff; padding: 2px 10px; border-radius: 4px; font-size: 12px; font-weight: 700;">
                                    x{{ $item['quantity'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="total-row" style="background: #f1f5f9; font-weight: 700;">
                        <td colspan="3" style="text-align: right; padding-right: 20px;">TOPLAM:</td>
                        <td>
                            <span style="display: inline-block; background: #1e293b; color: #fff; padding: 3px 12px; border-radius: 4px; font-size: 13px; font-weight: 700;">
                                x{{ $totalQuantity }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ORDER DETAILS --}}
        <h2 style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 2px solid #6366f1;">
            🧾 Sipariş Detayları
        </h2>

        @foreach($orders as $order)
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 16px; overflow: hidden; page-break-inside: avoid;">
                {{-- Order Header --}}
                <table width="100%" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <td style="padding: 10px 16px;">
                            <span style="font-weight: 700; font-size: 13px; color: #1e293b;">#{{ $order['order_number'] }}</span>
                            <span style="font-size: 10px; color: #64748b; margin-left: 10px;">{{ $order['customer_name'] }}</span>
                        </td>
                        <td style="padding: 10px 16px; text-align: right;">
                            <span style="font-size: 10px; color: #64748b;">{{ $order['city'] }} · {{ $order['date'] }}</span>
                        </td>
                    </tr>
                </table>

                {{-- Items Table --}}
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 60px; background: #f1f5f9; padding: 8px 12px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Görsel</th>
                            <th style="background: #f1f5f9; padding: 8px 12px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Ürün</th>
                            <th style="width: 80px; background: #f1f5f9; padding: 8px 12px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Numara</th>
                            <th style="width: 80px; background: #f1f5f9; padding: 8px 12px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Adet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order['items'] as $item)
                            <tr>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle;">
                                    <img src="{{ $item['image'] }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-weight: 600; font-size: 11px; color: #1e293b;">
                                    {{ $item['name'] }}
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle;">
                                    <span style="display: inline-block; background: #6366f1; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">
                                        {{ $item['variant'] ?: '-' }}
                                    </span>
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle;">
                                    <span style="display: inline-block; background: #f59e0b; color: #fff; padding: 2px 10px; border-radius: 4px; font-size: 12px; font-weight: 700;">
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

    {{-- FOOTER --}}
    <div style="padding: 16px 30px; border-top: 2px solid #e2e8f0; text-align: center; font-size: 9px; color: #94a3b8;">
        Patenli Ayakkabılar · patenliayakkabilar.com · {{ $date }} · Bu belge tedarikçi siparişi amaçlı oluşturulmuştur.
    </div>
</body>
</html>

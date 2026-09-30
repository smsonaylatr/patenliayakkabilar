<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>E-Arşiv Fatura - #{{ $order->gib_invoice_number ?: $order->order_number }}</title>
    <style>
        @page {
            margin: 10mm 12mm 12mm 12mm;
            size: A4 portrait;
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

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
        }

        .title-badge {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 14px;
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 4px;
            display: inline-block;
            letter-spacing: 0.5px;
        }

        .meta-table {
            border-collapse: collapse;
            margin-top: 4px;
        }
        .meta-table td {
            font-size: 9px;
            padding: 2px 4px;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
        }
        .meta-val {
            color: #0f172a;
            font-weight: 600;
        }

        .parties-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .party-box {
            width: 49%;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
            vertical-align: top;
            background: #fafafa;
        }
        .party-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .party-field {
            font-size: 9px;
            margin-bottom: 3px;
            color: #334155;
        }
        .party-field strong {
            color: #0f172a;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            border: 1px solid #cbd5e1;
        }
        .items-table th {
            background: #0f172a;
            color: #ffffff;
            font-size: 8.5px;
            text-transform: uppercase;
            padding: 7px 6px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #0f172a;
        }
        .items-table td {
            padding: 6px 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .totals-table td {
            vertical-align: top;
        }
        .summary-box {
            width: 50%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            margin-left: auto;
        }
        .summary-box td {
            padding: 5px 8px;
            font-size: 9px;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-box tr:last-child td {
            border-bottom: none;
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
        }

        .footer-note {
            font-size: 8.5px;
            color: #64748b;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
            margin-top: 14px;
            line-height: 1.4;
        }

        .signature-box {
            width: 180px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
            background: #ffffff;
            margin-top: 10px;
        }
    </style>
</head>
<body>
@php
    $invoiceNo = $order->gib_invoice_number ?: ('PA-' . $order->order_number);
    $invoiceDate = $order->gib_invoice_date ? $order->gib_invoice_date->format('d.m.Y H:i') : ($order->created_at ? $order->created_at->format('d.m.Y H:i') : date('d.m.Y H:i'));
    $uuid = $order->gib_invoice_uuid ?: strtoupper(\Illuminate\Support\Str::uuid()->toString());

    $rawName = trim($order->customer_name ?? '');
    $customerName = (empty($rawName) || strtolower($rawName) === 'admin') ? 'Değerli Müşterimiz' : ucwords(strtolower($rawName));

    $subtotal = (float)($order->subtotal ?? 0);
    $shipping = (float)($order->shipping_price ?? 0);
    $discount = (float)($order->discount_total ?? 0);
    $grandTotal = (float)($order->grand_total ?? ($subtotal + $shipping - $discount));

    // KDV Hesaplama (%20 dahil kabul edilir)
    $taxRate = 0.20;
    $taxBase = $grandTotal / (1 + $taxRate);
    $taxAmount = $grandTotal - $taxBase;
@endphp

    <!-- ÜST BİLGİ & BAŞLIK -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div style="font-size: 18px; font-weight: bold; color: #0f172a; letter-spacing: -0.5px;">
                    PATENLİ AYAKKABILAR
                </div>
                <div style="font-size: 9px; color: #64748b; margin-top: 2px;">
                    www.patenliayakkabilar.com | siparis@patenliayakkabilar.com
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="title-badge">E-ARŞİV FATURA</div>
                <table class="meta-table" align="right">
                    <tr>
                        <td class="meta-label">Fatura No:</td>
                        <td class="meta-val">{{ $invoiceNo }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Fatura Tarihi:</td>
                        <td class="meta-val">{{ $invoiceDate }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">ETTN:</td>
                        <td class="meta-val" style="font-size: 7.5px;">{{ $uuid }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- TARAFLAR BİLGİSİ (SATICI & ALICI) -->
    <table class="parties-table">
        <tr>
            <!-- SATICI -->
            <td class="party-box">
                <div class="party-title">SATICI (DÜZENLEYEN)</div>
                <div class="party-field"><strong>Ünvan:</strong> Patenli Ayakkabılar E-Ticaret</div>
                <div class="party-field"><strong>Web Sitesi:</strong> www.patenliayakkabilar.com</div>
                <div class="party-field"><strong>E-Posta:</strong> siparis@patenliayakkabilar.com</div>
                <div class="party-field"><strong>Müşteri Hizmetleri:</strong> info@patenliayakkabilar.com</div>
                <div class="party-field"><strong>Ödeme Yöntemi:</strong> {{ ucfirst($order->payment_method ?? 'Kredi Kartı / Online') }}</div>
            </td>
            <td style="width: 2%;"></td>
            <!-- ALICI -->
            <td class="party-box">
                <div class="party-title">ALICI (MÜŞTERİ)</div>
                <div class="party-field"><strong>Sayın:</strong> {{ $customerName }}</div>
                <div class="party-field"><strong>TCKN / VKN:</strong> 11111111111</div>
                @if(!empty($order->customer_email))
                <div class="party-field"><strong>E-Posta:</strong> {{ $order->customer_email }}</div>
                @endif
                @if(!empty($order->customer_phone))
                <div class="party-field"><strong>Telefon:</strong> {{ $order->customer_phone }}</div>
                @endif
                <div class="party-field">
                    <strong>Fatura / Teslimat Adresi:</strong> 
                    {{ $order->billing_address ?: $order->shipping_address ?: 'Adres Belirtilmemiş' }} 
                    {{ $order->billing_district ?: $order->shipping_district }} / {{ $order->billing_city ?: $order->shipping_city }}
                </div>
            </td>
        </tr>
    </table>

    <!-- ÜRÜN KALEMLERİ TABLOSU -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">Sıra</th>
                <th style="width: 45%; text-align: left;">Mal / Hizmet Açıklaması</th>
                <th style="width: 10%;">Miktar</th>
                <th style="width: 13%;">Birim Fiyat</th>
                <th style="width: 10%;">KDV (%20)</th>
                <th style="width: 17%;">Toplam Tutar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->items as $index => $item)
            @php
                $itemPrice = (float)($item->unit_price ?? 0);
                $itemTotal = (float)($item->total_price ?? ($itemPrice * ($item->quantity ?? 1)));
                $itemKdv = $itemTotal - ($itemTotal / 1.20);
            @endphp
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight: bold; color: #0f172a;">{{ $item->product_name }}</div>
                    @if(!empty($item->variant_info))
                    <div style="font-size: 8px; color: #4338ca; margin-top: 1px;">Varyant: {{ $item->variant_info }}</div>
                    @endif
                </td>
                <td style="text-align: center; font-weight: bold;">{{ $item->quantity ?? 1 }} Adet</td>
                <td style="text-align: right;">{{ number_format($itemPrice, 2, ',', '.') }} ₺</td>
                <td style="text-align: right;">{{ number_format($itemKdv, 2, ',', '.') }} ₺</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($itemTotal, 2, ',', '.') }} ₺</td>
            </tr>
            @empty
            <tr>
                <td style="text-align: center;">1</td>
                <td>
                    <div style="font-weight: bold; color: #0f172a;">Sipariş Bedeli (#{{ $order->order_number }})</div>
                </td>
                <td style="text-align: center; font-weight: bold;">1 Adet</td>
                <td style="text-align: right;">{{ number_format($grandTotal, 2, ',', '.') }} ₺</td>
                <td style="text-align: right;">{{ number_format($taxAmount, 2, ',', '.') }} ₺</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($grandTotal, 2, ',', '.') }} ₺</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- HESAP VE TOPLAMLAR -->
    <table class="totals-table">
        <tr>
            <td style="width: 50%;">
                <div class="signature-box">
                    <div style="font-weight: bold; color: #0f172a; margin-bottom: 25px;">Patenli Ayakkabılar</div>
                    <div style="font-size: 8px; color: #64748b;">Elektronik İmzalıdır</div>
                </div>
            </td>
            <td style="width: 50%;">
                <table class="summary-box">
                    <tr>
                        <td style="color: #475569;">Mal / Hizmet Toplam Tutarı:</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($taxBase, 2, ',', '.') }} ₺</td>
                    </tr>
                    @if($discount > 0)
                    <tr>
                        <td style="color: #e11d48;">Toplam İskonto / Kupon:</td>
                        <td style="text-align: right; font-weight: 600; color: #e11d48;">-{{ number_format($discount, 2, ',', '.') }} ₺</td>
                    </tr>
                    @endif
                    @if($shipping > 0)
                    <tr>
                        <td style="color: #475569;">Kargo Bedeli:</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($shipping, 2, ',', '.') }} ₺</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="color: #475569;">Hesaplanan KDV (%20):</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($taxAmount, 2, ',', '.') }} ₺</td>
                    </tr>
                    <tr>
                        <td>ÖDENECEK GENEL TOPLAM:</td>
                        <td style="text-align: right;">{{ number_format($grandTotal, 2, ',', '.') }} ₺</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- YASAL BİLGİLENDİRME VE NOT -->
    <div class="footer-note">
        <strong>Yasal Bilgilendirme:</strong> İşbu fatura 213 sayılı Vergi Usul Kanunu uyarınca elektronik ortamda düzenlenmiş ve 
        GİB E-Arşiv Fatura standartlarına uygundur. Elektronik imzalı belge kağıt fatura ile aynı yasal geçerliliğe sahiptir. 
        Sipariş No: <strong>#{{ $order->order_number }}</strong>
    </div>

</body>
</html>

@extends('emails.layouts.base')

@section('title', 'Siparişiniz ' . $statusText . ' — #' . $order->order_number . ' — Patenli Ayakkabılar®')

@section('content')
@php
    $rawName = trim($order->customer_name ?? '');
    $displayName = (empty($rawName) || strtolower($rawName) === 'admin') ? 'Değerli Müşterimiz' : ucwords(strtolower($rawName));
    
    $cargoName = $order->shipping_company ?: $order->cargo_company;
    if (empty($cargoName) || stripos($cargoName, 'porego') !== false) {
        $cargoName = 'Yurtiçi Kargo';
    }

    $trackingCode = $order->cargo_tracking_code ?: ($order->tracking_number ?? null);
    $trackingUrl = $order->cargo_tracking_url ?: ($order->tracking_url ?? null);
    if (empty($trackingUrl) && !empty($trackingCode)) {
        $trackingUrl = 'https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code=' . $trackingCode;
    }

    $currentStatus = $order->status;
    $isDelivered = ($currentStatus === 'delivered');
    $isShipped = in_array($currentStatus, ['shipped', 'delivered']);
    $isPreparing = in_array($currentStatus, ['processing', 'preparing', 'shipped', 'delivered']);
@endphp

<div style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #1e293b;">

    <!-- ÜST: GÖNDERİ ÖZETİ BARI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td align="left" style="font-size: 13px; font-weight: 500; color: #64748b;">
                Sipariş No: #{{ $order->order_number }}
            </td>
            <td align="right" style="font-size: 13px; font-weight: 600; color: #2563eb; white-space: nowrap;">
                ✓ {{ $statusText }}
            </td>
        </tr>
    </table>

    <!-- 1. KART: KARGO DURUMU KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="42" valign="middle" style="padding-right: 14px;">
                            <div style="width: 36px; height: 36px; border: 1.5px solid #2563eb; border-radius: 50%; text-align: center; line-height: 34px; font-size: 16px; color: #2563eb; font-weight: 700;">
                                ✓
                            </div>
                        </td>
                        <td valign="middle">
                            <p style="margin: 0 0 2px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8;">
                                Kargo Takip Bildirimi
                            </p>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: -0.3px;">
                                Siparişiniz {{ $statusText }}
                            </h1>
                        </td>
                    </tr>
                </table>

                <div style="border-top: 1px solid #f1f5f9; margin-top: 18px; padding-top: 14px;">
                    <h3 style="margin: 0 0 4px; font-size: 14px; font-weight: 600; color: #0f172a;">
                        Sayın {{ $displayName }},
                    </h3>
                    <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400; line-height: 1.6;">
                        #{{ $order->order_number }} numaralı siparişinizin güncel sevkiyat durumu aşağıda yer almaktadır. Kargonuzu anlık olarak takip edebilirsiniz.
                    </p>
                </div>

                <!-- İLERLEME ÇUBUĞU (AŞAMALAR) -->
                <div style="border-top: 1px solid #f1f5f9; margin-top: 18px; padding-top: 18px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align: center;">
                        <tr>
                            <td style="width: 25%;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background-color: #2563eb; color: #ffffff; font-size: 13px; line-height: 28px; margin: 0 auto; font-weight: 700;">✓</div>
                                <p style="margin: 6px 0 0; font-size: 11px; font-weight: 600; color: #0f172a;">Onaylandı</p>
                            </td>
                            <td style="width: 25%;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background-color: {{ $isPreparing ? '#2563eb' : '#e2e8f0' }}; color: {{ $isPreparing ? '#ffffff' : '#94a3b8' }}; font-size: 13px; line-height: 28px; margin: 0 auto; font-weight: 700;">
                                    {{ $isShipped ? '✓' : '2' }}
                                </div>
                                <p style="margin: 6px 0 0; font-size: 11px; font-weight: 600; color: {{ $isPreparing ? '#0f172a' : '#94a3b8' }};">Hazırlanıyor</p>
                            </td>
                            <td style="width: 25%;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background-color: {{ $isShipped ? '#2563eb' : '#e2e8f0' }}; color: {{ $isShipped ? '#ffffff' : '#94a3b8' }}; font-size: 13px; line-height: 28px; margin: 0 auto; font-weight: 700;">
                                    {{ $isDelivered ? '✓' : '3' }}
                                </div>
                                <p style="margin: 6px 0 0; font-size: 11px; font-weight: 600; color: {{ $isShipped ? '#0f172a' : '#94a3b8' }};">Kargoda</p>
                            </td>
                            <td style="width: 25%;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background-color: {{ $isDelivered ? '#16a34a' : '#e2e8f0' }}; color: {{ $isDelivered ? '#ffffff' : '#94a3b8' }}; font-size: 13px; line-height: 28px; margin: 0 auto; font-weight: 700;">
                                    {{ $isDelivered ? '✓' : '4' }}
                                </div>
                                <p style="margin: 6px 0 0; font-size: 11px; font-weight: 600; color: {{ $isDelivered ? '#16a34a' : '#94a3b8' }};">Teslim Edildi</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. KART: GÖNDERİ & KARGO BİLGİLERİ -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">
                    Kargo ve Teslimat Detayları
                </h3>

                <!-- Satır 1: Kargo Firması & Takip Numarası -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 14px;">
                    <tr>
                        <td width="50%" valign="top" style="padding-right: 12px;">
                            <p style="margin: 0 0 3px; font-size: 12px; font-weight: 600; color: #334155;">Kargo Firması</p>
                            <p style="margin: 0; font-size: 13px; color: #0f172a; font-weight: 600;">{{ $cargoName }}</p>
                        </td>
                        <td width="50%" valign="top">
                            <p style="margin: 0 0 3px; font-size: 12px; font-weight: 600; color: #334155;">Kargo Takip Kodu</p>
                            <p style="margin: 0; font-size: 13px; color: #2563eb; font-weight: 700; font-family: monospace;">
                                {{ $trackingCode ?: 'Atanıyor' }}
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Satır 2: Teslimat Adresi -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td valign="top">
                            <p style="margin: 0 0 3px; font-size: 12px; font-weight: 600; color: #334155;">Teslimat Adresi</p>
                            <p style="margin: 0 0 2px; font-size: 13px; color: #0f172a; font-weight: 500;">{{ $order->customer_name }}</p>
                            <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400; line-height: 1.4;">
                                {{ $order->shipping_address ?? $order->address }}
                                @if($order->shipping_district || $order->shipping_city)
                                    <br>{{ $order->shipping_district }} / {{ $order->shipping_city }}
                                @endif
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- 3. KART: SİPARİŞ DETAYLARI -->
    @if($order->items && $order->items->isNotEmpty())
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">
                    Gönderideki Ürünler
                </h3>

                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    @foreach($order->items as $item)
                    @php
                        $itemTotal = $item->total_price ?? ($item->unit_price ? ($item->unit_price * $item->quantity) : ($item->price ?? 0));
                        $itemName = $item->product_name ?? $item->name ?? 'Patenli Ayakkabı';
                        $variantInfo = $item->variant_info ?? $item->variant_name ?? null;

                        $imageUrl = null;
                        if ($item->product && method_exists($item->product, 'images') && $item->product->images && $item->product->images->isNotEmpty()) {
                            $imageUrl = Storage::url($item->product->images->first()->image_path ?? '');
                            if (!str_starts_with($imageUrl, 'http')) {
                                $imageUrl = url($imageUrl);
                            }
                        }
                    @endphp
                    <tr>
                        <td width="64" valign="middle" style="padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                            <div style="position: relative; width: 54px; height: 54px; display: inline-block;">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $itemName }}" width="54" height="54" style="width: 54px; height: 54px; object-fit: cover; border-radius: 10px; border: 1px solid #e2e8f0; display: block;">
                                @else
                                    <div style="width: 54px; height: 54px; background-color: #f1f5f9; border-radius: 10px; border: 1px solid #e2e8f0; text-align: center; line-height: 54px; font-size: 18px; color: #94a3b8;">
                                        👟
                                    </div>
                                @endif
                                <div style="position: absolute; top: -5px; right: -5px; background-color: #475569; color: #ffffff; font-size: 10px; font-weight: 700; width: 17px; height: 17px; border-radius: 50%; text-align: center; line-height: 17px; border: 2px solid #ffffff;">
                                    {{ $item->quantity }}
                                </div>
                            </div>
                        </td>
                        <td valign="middle" style="padding: 12px 14px; border-bottom: 1px solid #f1f5f9;">
                            <p style="margin: 0; font-size: 13px; font-weight: 600; color: #1e293b; line-height: 1.4;">
                                {{ $itemName }}
                            </p>
                            @if($variantInfo)
                                <p style="margin: 3px 0 0; font-size: 12px; color: #94a3b8; font-weight: 400;">
                                    {{ $variantInfo }}
                                </p>
                            @endif
                        </td>
                        <td valign="middle" align="right" style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; white-space: nowrap;">
                            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">
                                {{ number_format($itemTotal, 2, ',', '.') }} ₺
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
    @endif

    <!-- 4. BUTONLAR: ZARİF KURUMSAL BUTONLAR -->
    <div style="margin-top: 24px; text-align: center;">
        @if($trackingUrl)
        <a href="{{ $trackingUrl }}" target="_blank" style="display: block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 14px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; margin-bottom: 10px;">
            Kargomu Takip Et
        </a>
        @endif

        <a href="https://patenliayakkabilar.com/siparis-takip?order={{ $order->order_number }}" target="_blank" style="display: block; background-color: #ffffff; color: #334155; text-decoration: none; padding: 13px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; border: 1px solid #cbd5e1;">
            Siparişimi Görüntüle
        </a>
    </div>

</div>
@endsection

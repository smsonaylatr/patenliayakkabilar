@extends('emails.layouts.base')

@section('title', 'Siparişiniz Alındı! #' . $order->order_number . ' — Patenli Ayakkabılar®')

@section('content')
@php
    $firstName = explode(' ', trim($order->customer_name))[0];
    
    $cargoName = $order->cargo_company;
    if (empty($cargoName) || stripos($cargoName, 'porego') !== false) {
        $cargoName = 'DHL eCommerce';
    }

    $paymentMethodText = match($order->payment_method) {
        'credit_card' => 'Kredi Kartı',
        'cash_on_delivery' => 'Kapıda Ödeme',
        'wire_transfer', 'bank_transfer' => 'Havale / EFT',
        default => $order->payment_method ?? 'Kredi Kartı',
    };
@endphp

<div style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #1e293b;">

    <!-- ÜST: SİPARİŞ ÖZETİ BARI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td align="left" style="font-size: 13px; font-weight: 500; color: #64748b;">
                Sipariş özeti
            </td>
            <td align="right" style="font-size: 16px; font-weight: 700; color: #0f172a;">
                {{ number_format($order->grand_total, 2, ',', '.') }} ₺
            </td>
        </tr>
    </table>

    <!-- 1. KART: ONAY BAŞLIĞI KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="42" valign="middle" style="padding-right: 14px;">
                            <!-- Mavi Onay Dairesi (Zarif İnce Çizgili) -->
                            <div style="width: 36px; height: 36px; border: 1.5px solid #2563eb; border-radius: 50%; text-align: center; line-height: 34px; font-size: 17px; color: #2563eb; font-weight: 700;">
                                ✓
                            </div>
                        </td>
                        <td valign="middle">
                            <p style="margin: 0 0 2px; font-size: 12px; color: #94a3b8; font-weight: 400;">
                                {{ $order->order_number }} numaralı onaylama
                            </p>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: -0.3px;">
                                Teşekkür ederiz {{ $firstName }}
                            </h1>
                        </td>
                    </tr>
                </table>

                <div style="border-top: 1px solid #f1f5f9; margin-top: 18px; padding-top: 14px;">
                    <h3 style="margin: 0 0 3px; font-size: 14px; font-weight: 600; color: #0f172a;">
                        Siparişiniz doğrulandı
                    </h3>
                    <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400; line-height: 1.5;">
                        Siparişiniz hazırlanma aşamasına alınmıştır. Kargonuz çıktığında takip bilgisi iletilecektir.
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. KART: SİPARİŞ BİLGİLERİ KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">
                    Sipariş Bilgileri
                </h3>

                <!-- Satır 1: İletişim Bilgileri & Kargo Yöntemi -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 14px;">
                    <tr>
                        <td width="50%" valign="top" style="padding-right: 12px;">
                            <p style="margin: 0 0 3px; font-size: 12px; font-weight: 600; color: #334155;">İletişim bilgileri</p>
                            <p style="margin: 0 0 2px; font-size: 13px; color: #64748b; font-weight: 400;">{{ $order->customer_email }}</p>
                            @if($order->customer_phone)
                                <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400;">{{ $order->customer_phone }}</p>
                            @endif
                        </td>
                        <td width="50%" valign="top">
                            <p style="margin: 0 0 3px; font-size: 12px; font-weight: 600; color: #334155;">Kargo yöntemi</p>
                            <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400;">Standart Teslimat</p>
                        </td>
                    </tr>
                </table>

                <!-- Satır 2: Ödeme Yöntemi & Kargo Firması -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 14px;">
                    <tr>
                        <td width="50%" valign="top" style="padding-right: 12px;">
                            <p style="margin: 0 0 3px; font-size: 12px; font-weight: 600; color: #334155;">Ödeme yöntemi</p>
                            <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400;">
                                {{ $paymentMethodText }} · {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                            </p>
                            @if($order->coupon_code)
                                <p style="margin: 3px 0 0; font-size: 12px; color: #16a34a; font-weight: 500;">
                                    Kupon: {{ $order->coupon_code }} (-{{ number_format($order->discount_total, 2, ',', '.') }} ₺)
                                </p>
                            @endif
                        </td>
                        <td width="50%" valign="top">
                            <p style="margin: 0 0 3px; font-size: 12px; font-weight: 600; color: #334155;">Kargo firması</p>
                            <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400;">{{ $cargoName }}</p>
                        </td>
                    </tr>
                </table>

                <!-- Satır 3: Kargo Adresi & Fatura Adresi -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="50%" valign="top" style="padding-right: 12px;">
                            <p style="margin: 0 0 5px; font-size: 12px; font-weight: 600; color: #334155;">Kargo adresi</p>
                            <div style="font-size: 13px; color: #64748b; line-height: 1.5; font-weight: 400;">
                                <p style="margin: 0; font-weight: 600; color: #1e293b;">{{ $order->customer_name }}</p>
                                <p style="margin: 2px 0;">{{ $order->shipping_address }}</p>
                                <p style="margin: 2px 0;">{{ $order->shipping_district }} / {{ $order->shipping_city }}</p>
                                <p style="margin: 2px 0;">Türkiye</p>
                                @if($order->customer_phone)
                                    <p style="margin: 2px 0;">{{ $order->customer_phone }}</p>
                                @endif
                            </div>
                        </td>
                        <td width="50%" valign="top">
                            <p style="margin: 0 0 5px; font-size: 12px; font-weight: 600; color: #334155;">Fatura adresi</p>
                            <div style="font-size: 13px; color: #64748b; line-height: 1.5; font-weight: 400;">
                                <p style="margin: 0; font-weight: 600; color: #1e293b;">{{ $order->customer_name }}</p>
                                <p style="margin: 2px 0;">{{ $order->billing_address ?: $order->shipping_address }}</p>
                                <p style="margin: 2px 0;">{{ $order->billing_district ?: $order->shipping_district }} / {{ $order->billing_city ?: $order->shipping_city }}</p>
                                <p style="margin: 2px 0;">Türkiye</p>
                            </div>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- 3. KART: SİPARİŞ DETAYLARI KARTI (ÜRÜNLER & ARA TOPLAMLAR) -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 22px;">
                <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">
                    Sipariş Detayları
                </h3>

                <!-- Ürünler Listesi -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    @foreach($order->items as $item)
                    @php
                        $itemTotal = $item->total_price ?? ($item->unit_price ? ($item->unit_price * $item->quantity) : ($item->price ?? 0));
                        $itemName = $item->product_name ?? $item->name ?? 'Ürün';
                        $variantInfo = $item->variant_info ?? $item->variant_name ?? null;

                        $imageUrl = null;
                        if ($item->product && $item->product->images && $item->product->images->count() > 0) {
                            $imageUrl = Storage::url($item->product->images->first()->image_path);
                            if (!str_starts_with($imageUrl, 'http')) {
                                $imageUrl = url($imageUrl);
                            }
                        }
                    @endphp
                    <tr>
                        <!-- Ürün Görseli & Adet Rozeti -->
                        <td width="64" valign="middle" style="padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                            <div style="position: relative; width: 54px; height: 54px; display: inline-block;">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $itemName }}" width="54" height="54" style="width: 54px; height: 54px; object-fit: cover; border-radius: 10px; border: 1px solid #e2e8f0; display: block;">
                                @else
                                    <div style="width: 54px; height: 54px; background-color: #f1f5f9; border-radius: 10px; border: 1px solid #e2e8f0; text-align: center; line-height: 54px; font-size: 18px; color: #94a3b8;">
                                        👟
                                    </div>
                                @endif
                                <!-- Adet Rozeti -->
                                <div style="position: absolute; top: -5px; right: -5px; background-color: #475569; color: #ffffff; font-size: 10px; font-weight: 700; width: 17px; height: 17px; border-radius: 50%; text-align: center; line-height: 17px; border: 2px solid #ffffff;">
                                    {{ $item->quantity }}
                                </div>
                            </div>
                        </td>

                        <!-- Ürün Adı & Varyant -->
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

                        <!-- Fiyat -->
                        <td valign="middle" align="right" style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; white-space: nowrap;">
                            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">
                                {{ number_format($itemTotal, 2, ',', '.') }} ₺
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </table>

                <!-- Alt Toplamlar -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 14px;">
                    <tr>
                        <td style="padding: 4px 0; font-size: 13px; color: #64748b; font-weight: 400;">Ara toplam</td>
                        <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 500; color: #334155;">
                            {{ number_format($order->subtotal, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; font-size: 13px; color: #64748b; font-weight: 400;">Kargo</td>
                        <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 500; color: #334155;">
                            {{ $order->shipping_price > 0 ? number_format($order->shipping_price, 2, ',', '.') . ' ₺' : 'Ücretsiz' }}
                        </td>
                    </tr>
                    @if($order->discount_total > 0)
                    <tr>
                        <td style="padding: 4px 0; font-size: 13px; color: #16a34a; font-weight: 500;">İndirim</td>
                        <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 600; color: #16a34a;">
                            -{{ number_format($order->discount_total, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 12px 0 0; font-size: 15px; font-weight: 700; color: #0f172a; border-top: 1px solid #f1f5f9;">Toplam</td>
                        <td align="right" style="padding: 12px 0 0; font-size: 16px; font-weight: 700; color: #0f172a; border-top: 1px solid #f1f5f9;">
                            {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- 4. BUTONLAR: ZARİF KURUMSAL BUTONLAR -->
    <div style="margin-top: 24px; text-align: center;">
        <a href="https://patenliayakkabilar.com" target="_blank" style="display: block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 14px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; margin-bottom: 10px;">
            Alışverişe Devam Et
        </a>

        <a href="https://patenliayakkabilar.com/siparis-takip?order={{ $order->order_number }}" target="_blank" style="display: block; background-color: #ffffff; color: #334155; text-decoration: none; padding: 13px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; border: 1px solid #cbd5e1;">
            Siparişimi Takip Et
        </a>
    </div>

</div>
@endsection

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

<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #0f172a;">

    <!-- ÜST: SİPARİŞ ÖZETİ BARI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td align="left" style="font-size: 13px; font-weight: 600; color: #64748b;">
                Sipariş özeti
            </td>
            <td align="right" style="font-size: 18px; font-weight: 900; color: #0f172a;">
                {{ number_format($order->grand_total, 2, ',', '.') }} ₺
            </td>
        </tr>
    </table>

    <!-- 1. KART: ONAY BAŞLIĞI KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <tr>
            <td style="padding: 24px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="48" valign="top" style="padding-right: 16px;">
                            <!-- Mavi Onay Dairesi -->
                            <div style="width: 44px; height: 44px; border: 2px solid #3b82f6; border-radius: 50%; text-align: center; line-height: 42px; font-size: 20px; color: #3b82f6; font-weight: 900;">
                                ✓
                            </div>
                        </td>
                        <td valign="middle">
                            <p style="margin: 0 0 4px; font-size: 13px; color: #94a3b8; font-weight: 500;">
                                {{ $order->order_number }} numaralı onaylama
                            </p>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 900; color: #0f172a; letter-spacing: -0.5px;">
                                Teşekkür ederiz {{ $firstName }}
                            </h1>
                        </td>
                    </tr>
                </table>

                <div style="border-top: 1px solid #f1f5f9; margin-top: 20px; padding-top: 16px;">
                    <h3 style="margin: 0 0 4px; font-size: 15px; font-weight: 800; color: #0f172a;">
                        Siparişiniz doğrulandı
                    </h3>
                    <p style="margin: 0; font-size: 13px; color: #64748b;">
                        Siparişiniz hazırlanma aşamasına alınmıştır. Kargonuz çıktığında takip bilgisi iletilecektir.
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. KART: SİPARİŞ BİLGİLERİ KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <tr>
            <td style="padding: 24px;">
                <h3 style="margin: 0 0 18px; font-size: 17px; font-weight: 900; color: #0f172a; letter-spacing: -0.3px;">
                    Sipariş Bilgileri
                </h3>

                <!-- Satır 1: İletişim Bilgileri & Kargo Yöntemi -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 16px;">
                    <tr>
                        <td width="50%" valign="top" style="padding-right: 12px;">
                            <p style="margin: 0 0 4px; font-size: 13px; font-weight: 700; color: #334155;">İletişim bilgileri</p>
                            <p style="margin: 0 0 2px; font-size: 13px; color: #64748b;">{{ $order->customer_email }}</p>
                            @if($order->customer_phone)
                                <p style="margin: 0; font-size: 13px; color: #64748b;">{{ $order->customer_phone }}</p>
                            @endif
                        </td>
                        <td width="50%" valign="top">
                            <p style="margin: 0 0 4px; font-size: 13px; font-weight: 700; color: #334155;">Kargo yöntemi</p>
                            <p style="margin: 0; font-size: 13px; color: #64748b;">Standart Teslimat</p>
                        </td>
                    </tr>
                </table>

                <!-- Satır 2: Ödeme Yöntemi & Kargo Firması -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 16px;">
                    <tr>
                        <td width="50%" valign="top" style="padding-right: 12px;">
                            <p style="margin: 0 0 4px; font-size: 13px; font-weight: 700; color: #334155;">Ödeme yöntemi</p>
                            <p style="margin: 0; font-size: 13px; color: #64748b;">
                                {{ $paymentMethodText }} · {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                            </p>
                            @if($order->coupon_code)
                                <p style="margin: 4px 0 0; font-size: 12px; color: #16a34a; font-weight: 600;">
                                    🎟️ Kupon: {{ $order->coupon_code }} (-{{ number_format($order->discount_total, 2, ',', '.') }} ₺)
                                </p>
                            @endif
                        </td>
                        <td width="50%" valign="top">
                            <p style="margin: 0 0 4px; font-size: 13px; font-weight: 700; color: #334155;">Kargo firması</p>
                            <p style="margin: 0; font-size: 13px; color: #64748b;">{{ $cargoName }}</p>
                        </td>
                    </tr>
                </table>

                <!-- Satır 3: Kargo Adresi & Fatura Adresi -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="50%" valign="top" style="padding-right: 12px;">
                            <p style="margin: 0 0 6px; font-size: 13px; font-weight: 700; color: #334155;">Kargo adresi</p>
                            <div style="font-size: 13px; color: #64748b; line-height: 1.55;">
                                <p style="margin: 0; font-weight: 700; color: #1e293b;">{{ $order->customer_name }}</p>
                                <p style="margin: 2px 0;">{{ $order->shipping_address }}</p>
                                <p style="margin: 2px 0;">{{ $order->shipping_district }} / {{ $order->shipping_city }}</p>
                                <p style="margin: 2px 0;">Türkiye</p>
                                @if($order->customer_phone)
                                    <p style="margin: 2px 0;">{{ $order->customer_phone }}</p>
                                @endif
                            </div>
                        </td>
                        <td width="50%" valign="top">
                            <p style="margin: 0 0 6px; font-size: 13px; font-weight: 700; color: #334155;">Fatura adresi</p>
                            <div style="font-size: 13px; color: #64748b; line-height: 1.55;">
                                <p style="margin: 0; font-weight: 700; color: #1e293b;">{{ $order->customer_name }}</p>
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
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <tr>
            <td style="padding: 24px;">
                <h3 style="margin: 0 0 20px; font-size: 17px; font-weight: 900; color: #0f172a; letter-spacing: -0.3px;">
                    Sipariş Detayları
                </h3>

                <!-- Ürünler Listesi -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    @foreach($order->items as $item)
                    @php
                        // Fiyat hesabı: total_price veya unit_price * quantity
                        $itemTotal = $item->total_price ?? ($item->unit_price ? ($item->unit_price * $item->quantity) : ($item->price ?? 0));
                        $itemName = $item->product_name ?? $item->name ?? 'Ürün';
                        $variantInfo = $item->variant_info ?? $item->variant_name ?? null;

                        // Görsel URL
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
                        <td width="72" valign="middle" style="padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                            <div style="position: relative; width: 60px; height: 60px; display: inline-block;">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $itemName }}" width="60" height="60" style="width: 60px; height: 60px; object-fit: cover; border-radius: 12px; border: 1px solid #e2e8f0; display: block;">
                                @else
                                    <div style="width: 60px; height: 60px; background-color: #f1f5f9; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; line-height: 60px; font-size: 20px; color: #94a3b8;">
                                        👟
                                    </div>
                                @endif
                                <!-- Adet Rozeti (Sağ Üst Köşede) -->
                                <div style="position: absolute; top: -6px; right: -6px; background-color: #475569; color: #ffffff; font-size: 10px; font-weight: 800; width: 18px; height: 18px; border-radius: 50%; text-align: center; line-height: 18px; border: 2px solid #ffffff;">
                                    {{ $item->quantity }}
                                </div>
                            </div>
                        </td>

                        <!-- Ürün Adı & Varyant -->
                        <td valign="middle" style="padding: 12px 16px; border-bottom: 1px solid #f1f5f9;">
                            <p style="margin: 0; font-size: 14px; font-weight: 800; color: #0f172a; line-height: 1.4;">
                                {{ $itemName }}
                            </p>
                            @if($variantInfo)
                                <p style="margin: 4px 0 0; font-size: 12px; color: #94a3b8; font-weight: 500;">
                                    {{ $variantInfo }}
                                </p>
                            @endif
                        </td>

                        <!-- Fiyat (Hatasız Hesaplanan Tutar) -->
                        <td valign="middle" align="right" style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; white-space: nowrap;">
                            <span style="font-size: 15px; font-weight: 900; color: #0f172a;">
                                {{ number_format($itemTotal, 2, ',', '.') }} ₺
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </table>

                <!-- Alt Toplamlar (Ara Toplam, Kargo, İndirim, Toplam) -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 18px;">
                    <tr>
                        <td style="padding: 5px 0; font-size: 13px; color: #64748b;">Ara toplam</td>
                        <td align="right" style="padding: 5px 0; font-size: 13px; font-weight: 600; color: #334155;">
                            {{ number_format($order->subtotal, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; font-size: 13px; color: #64748b;">Kargo</td>
                        <td align="right" style="padding: 5px 0; font-size: 13px; font-weight: 600; color: #334155;">
                            {{ $order->shipping_price > 0 ? number_format($order->shipping_price, 2, ',', '.') . ' ₺' : 'Ücretsiz' }}
                        </td>
                    </tr>
                    @if($order->discount_total > 0)
                    <tr>
                        <td style="padding: 5px 0; font-size: 13px; color: #16a34a; font-weight: 600;">İndirim</td>
                        <td align="right" style="padding: 5px 0; font-size: 13px; font-weight: 700; color: #16a34a;">
                            -{{ number_format($order->discount_total, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 14px 0 0; font-size: 16px; font-weight: 900; color: #0f172a; border-top: 1px solid #f1f5f9;">Toplam</td>
                        <td align="right" style="padding: 14px 0 0; font-size: 17px; font-weight: 900; color: #0f172a; border-top: 1px solid #f1f5f9;">
                            {{ number_format($order->grand_total, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- 4. BUTONLAR: SİTEDEKİ ALISVERISE DEVAM ET & SIPARISIMI TAKIP ET -->
    <div style="margin-top: 28px; text-align: center;">
        <!-- Siyah Buton: Alışverişe Devam Et -->
        <a href="https://patenliayakkabilar.com" target="_blank" style="display: block; background-color: #000000; color: #ffffff; text-decoration: none; padding: 15px 24px; border-radius: 12px; font-weight: 800; font-size: 14px; text-align: center; margin-bottom: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            Alışverişe Devam Et
        </a>

        <!-- Beyaz Buton: Siparişimi Takip Et -->
        <a href="https://patenliayakkabilar.com/siparis-takip?order={{ $order->order_number }}" target="_blank" style="display: block; background-color: #ffffff; color: #374151; text-decoration: none; padding: 14px 24px; border-radius: 12px; font-weight: 800; font-size: 14px; text-align: center; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            Siparişimi Takip Et
        </a>
    </div>

</div>
@endsection

@extends('emails.layouts.base')

@section('title', 'Sana Özel %10 İndirim! — Patenli Ayakkabılar®')

@section('content')
@php
    $rawName = trim($customerName ?? '');
    $displayName = (empty($rawName) || strtolower($rawName) === 'admin') ? 'Değerli Müşterimiz' : ucwords(strtolower($rawName));

    $totalAmount = 0;
    foreach ($cart->items as $it) {
        $price = $it->product?->discount_price ?? $it->product?->price ?? 0;
        $totalAmount += $price * ($it->quantity ?? 1);
    }
    $discountAmount = $totalAmount * 0.10;
    $finalAmount = $totalAmount - $discountAmount;
@endphp

<div style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #1e293b;">

    <!-- ÜST: SEPET ÖZETİ BARI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td align="left" style="font-size: 13px; font-weight: 500; color: #64748b;">
                Sepet özeti
            </td>
            <td align="right" style="font-size: 16px; font-weight: 700; color: #0f172a; white-space: nowrap;">
                <span style="font-size: 13px; text-decoration: line-through; color: #94a3b8; font-weight: 400; margin-right: 6px;">{{ number_format($totalAmount, 2, ',', '.') }} ₺</span>
                {{ number_format($finalAmount, 2, ',', '.') }} ₺
            </td>
        </tr>
    </table>

    <!-- 1. KART: BİLDİRİM & KUPON KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="42" valign="middle" style="padding-right: 14px;">
                            <div style="width: 36px; height: 36px; border: 1.5px solid #2563eb; border-radius: 50%; text-align: center; line-height: 34px; font-size: 16px; color: #2563eb; font-weight: 700;">
                                %
                            </div>
                        </td>
                        <td valign="middle">
                            <p style="margin: 0 0 2px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8;">
                                Patenli Ayakkabılar® Özel Teklif
                            </p>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: -0.3px;">
                                Size Özel %10 İndirim Fırsatı
                            </h1>
                        </td>
                    </tr>
                </table>

                <div style="border-top: 1px solid #f1f5f9; margin-top: 18px; padding-top: 14px;">
                    <h3 style="margin: 0 0 4px; font-size: 14px; font-weight: 600; color: #0f172a;">
                        Sayın {{ $displayName }},
                    </h3>
                    <p style="margin: 0 0 16px; font-size: 13px; color: #64748b; font-weight: 400; line-height: 1.6;">
                        Sepetinize eklediğiniz modelleri tamamlamanız için hesabınıza özel tek kullanımlık <strong>%10 indirim kuponu</strong> tanımlanmıştır.
                    </p>

                    <!-- KUPON KUTUSU -->
                    <div style="background-color: #fff7ed; border: 1.5px dashed #ff4e00; border-radius: 10px; padding: 16px; text-align: center;">
                        <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #c2410c; display: block; margin-bottom: 4px;">Kupon Kodunuz</span>
                        <div style="font-size: 22px; font-weight: 800; letter-spacing: 2px; color: #ea580c; font-family: monospace; padding: 6px 14px; background-color: #ffffff; border-radius: 6px; display: inline-block; border: 1px solid #fed7aa;">
                            {{ $couponCode }}
                        </div>
                        <div style="font-size: 12px; color: #9a3412; margin-top: 8px;">
                            ⏰ Son Geçerlilik: <strong>{{ $expiresAt }}</strong>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. KART: SEPETTEKİ ÜRÜNLER KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">
                    Sepetinizdeki Ürünler
                </h3>

                <!-- Ürünler Listesi -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    @foreach($cart->items as $item)
                    @php
                        $product = $item->product;
                        $price = $product?->discount_price ?? $product?->price ?? 0;
                        $itemTotal = $price * ($item->quantity ?? 1);
                        $itemName = $product?->name ?? 'Patenli Ayakkabı';
                        
                        $variantInfo = null;
                        if ($item->variant) {
                            $color = is_array($item->variant->color) ? implode(', ', $item->variant->color) : $item->variant->color;
                            $variantInfo = trim(($color ? "Renk: {$color} " : '') . ($item->variant->size ? "Beden: {$item->variant->size}" : ''));
                        }

                        $imageUrl = null;
                        if (!empty($product?->images) && count($product->images) > 0) {
                            $firstImg = is_iterable($product->images) ? $product->images->first() : null;
                            if ($firstImg) {
                                $imgPath = $firstImg->image_path ?? $firstImg->image_url ?? '';
                                if ($imgPath) {
                                    $imageUrl = Storage::url($imgPath);
                                    if (!str_starts_with($imageUrl, 'http')) {
                                        $imageUrl = url($imageUrl);
                                    }
                                }
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
                            {{ number_format($totalAmount, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; font-size: 13px; color: #16a34a; font-weight: 500;">%10 Kupon İndirimi</td>
                        <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 600; color: #16a34a;">
                            -{{ number_format($discountAmount, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; font-size: 13px; color: #64748b; font-weight: 400;">Kargo</td>
                        <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 600; color: #16a34a;">
                            Ücretsiz Kargo
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 0 0; font-size: 15px; font-weight: 700; color: #0f172a; border-top: 1px solid #f1f5f9;">İndirimli Toplam</td>
                        <td align="right" style="padding: 12px 0 0; font-size: 16px; font-weight: 700; color: #0f172a; border-top: 1px solid #f1f5f9;">
                            {{ number_format($finalAmount, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- 3. BUTONLAR: ZARİF KURUMSAL BUTONLAR -->
    <div style="margin-top: 24px; text-align: center;">
        <a href="{{ url('/checkout') }}" target="_blank" style="display: block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 14px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; margin-bottom: 10px;">
            Kuponu Kullan ve Siparişi Tamamla
        </a>

        <a href="https://patenliayakkabilar.com" target="_blank" style="display: block; background-color: #ffffff; color: #334155; text-decoration: none; padding: 13px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; border: 1px solid #cbd5e1;">
            Alışverişe Devam Et
        </a>
    </div>

</div>
@endsection

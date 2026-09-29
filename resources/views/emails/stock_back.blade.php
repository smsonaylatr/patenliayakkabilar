@extends('emails.layouts.base')

@section('title', 'Beklediğiniz Ürün Tekrar Stokta! — Patenli Ayakkabılar®')

@section('content')
@php
    $productPrice = $product->discount_price ?? $product->price ?? 0;
    
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

<div style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #1e293b;">

    <!-- ÜST: BİLDİRİM BARI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td align="left" style="font-size: 13px; font-weight: 500; color: #64748b;">
                Stok Bildirimi
            </td>
            <td align="right" style="font-size: 13px; font-weight: 600; color: #16a34a; white-space: nowrap;">
                ✓ Stoklar Güncellendi
            </td>
        </tr>
    </table>

    <!-- 1. KART: STOK BİLDİRİMİ KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="42" valign="middle" style="padding-right: 14px;">
                            <div style="width: 36px; height: 36px; border: 1.5px solid #16a34a; border-radius: 50%; text-align: center; line-height: 34px; font-size: 16px; color: #16a34a; font-weight: 700;">
                                ✓
                            </div>
                        </td>
                        <td valign="middle">
                            <p style="margin: 0 0 2px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8;">
                                Patenli Ayakkabılar® Stok Uyarısı
                            </p>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: -0.3px;">
                                Beklediğiniz Ürün Tekrar Stokta
                            </h1>
                        </td>
                    </tr>
                </table>

                <div style="border-top: 1px solid #f1f5f9; margin-top: 18px; padding-top: 14px;">
                    <h3 style="margin: 0 0 3px; font-size: 14px; font-weight: 600; color: #0f172a;">
                        Müjde! Talep ettiğiniz modelin stokları güncellendi
                    </h3>
                    <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400; line-height: 1.5;">
                        Daha önce stok alarmı oluşturduğunuz model mağazamızda tekrar satışa açılmıştır. Stoklar sınırlı sayıda olduğu için tükenmeden sepetinize ekleyebilirsiniz.
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. KART: ÜRÜN DETAYI KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">
                    Stoktaki Ürün Bilgisi
                </h3>

                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    <tr>
                        <td width="64" valign="middle" style="padding: 12px 0;">
                            <div style="width: 54px; height: 54px; display: inline-block;">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" width="54" height="54" style="width: 54px; height: 54px; object-fit: cover; border-radius: 10px; border: 1px solid #e2e8f0; display: block;">
                                @else
                                    <div style="width: 54px; height: 54px; background-color: #f1f5f9; border-radius: 10px; border: 1px solid #e2e8f0; text-align: center; line-line: 54px; font-size: 18px; color: #94a3b8; line-height: 54px;">
                                        👟
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td valign="middle" style="padding: 12px 14px;">
                            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #1e293b; line-height: 1.4;">
                                {{ $product->name }}
                            </p>
                            @if($variant)
                                <p style="margin: 3px 0 0; font-size: 12px; color: #94a3b8; font-weight: 400;">
                                    Beden: {{ $variant->size }}
                                </p>
                            @endif
                        </td>
                        <td valign="middle" align="right" style="padding: 12px 0; white-space: nowrap;">
                            <span style="font-size: 15px; font-weight: 700; color: #ff4e00;">
                                {{ number_format($productPrice, 2, ',', '.') }} ₺
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 3. BUTONLAR: ZARİF KURUMSAL BUTONLAR -->
    <div style="margin-top: 24px; text-align: center;">
        <a href="{{ url('/urun/' . $product->slug) }}" target="_blank" style="display: block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 14px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; margin-bottom: 10px;">
            Ürünü Hemen İncele ve Satın Al
        </a>

        <a href="https://patenliayakkabilar.com" target="_blank" style="display: block; background-color: #ffffff; color: #334155; text-decoration: none; padding: 13px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; border: 1px solid #cbd5e1;">
            Tüm Modelleri Gör
        </a>
    </div>

</div>
@endsection

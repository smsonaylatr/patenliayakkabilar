@extends('emails.layouts.base')

@section('title', 'Siparişiniz Başarıyla Alındı! — #' . $order->order_number)

@section('content')
<div style="text-align: left; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    
    <!-- Başarı Rozeti -->
    <div style="display: inline-block; background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 11px; font-weight: 800; padding: 5px 14px; border-radius: 9999px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.8px;">
        ✓ Siparişiniz Alındı
    </div>

    <!-- Başlık -->
    <h1 style="margin: 0 0 16px; font-size: 26px; font-weight: 900; color: #0f172a; letter-spacing: -0.8px; line-height: 1.3;">
        Teşekkür Ederiz, {{ $order->customer_name }}! 🎉
    </h1>

    <p style="font-size: 15px; color: #475569; line-height: 1.7; margin-bottom: 26px;">
        Siparişiniz başarıyla sistemimize ulaştı ve hazırlanma sürecine alındı. Aşağıda siparişinizin özet bilgilerini inceleyebilirsiniz:
    </p>

    <!-- Sipariş Bilgi Tablosu -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; margin-bottom: 30px; font-size: 14px;">
        <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Sipariş Numarası:</td>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 800; text-align: right;">#{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Sipariş Tarihi:</td>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 600; text-align: right;">{{ $order->created_at->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</td>
        </tr>
        <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Ödeme Yöntemi:</td>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 600; text-align: right;">
                @if($order->payment_method == 'credit_card') Kredi Kartı (PayTR)
                @elseif($order->payment_method == 'bank_transfer') Havale / EFT
                @elseif($order->payment_method == 'cash_on_delivery') Kapıda Ödeme
                @else {{ $order->payment_method }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding: 16px 20px; color: #0f172a; font-weight: 900; font-size: 16px;">Toplam Tutar:</td>
            <td style="padding: 16px 20px; color: #0f172a; font-weight: 900; font-size: 18px; text-align: right;">{{ number_format($order->grand_total, 2) }} ₺</td>
        </tr>
    </table>

    <!-- Sipariş Detayları Başlığı -->
    <h2 style="font-size: 18px; font-weight: 900; color: #0f172a; margin: 30px 0 16px; letter-spacing: -0.5px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
        Sipariş Detayı
    </h2>

    <!-- Ürünler Tablosu -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 30px; border-collapse: collapse;">
        @foreach($order->items as $item)
        <tr>
            <td width="64" style="padding: 14px 0; border-bottom: 1px solid #f1f5f9;">
                @if($item->product && $item->product->images->first())
                    <img src="{{ url(Storage::url($item->product->images->first()->image_path)) }}" alt="{{ $item->name }}" style="width: 54px; height: 54px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                @else
                    <div style="width: 54px; height: 54px; background-color: #f1f5f9; border-radius: 8px; border: 1px solid #e2e8f0;"></div>
                @endif
            </td>
            <td style="padding: 14px 16px; border-bottom: 1px solid #f1f5f9;">
                <div style="font-weight: 800; font-size: 14px; color: #0f172a;">{{ $item->name }}</div>
                @if($item->variant_name)
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">{{ $item->variant_name }}</div>
                @endif
            </td>
            <td style="padding: 14px; border-bottom: 1px solid #f1f5f9; text-align: center; font-size: 14px; color: #64748b; font-weight: 600;">{{ $item->quantity }} adet</td>
            <td style="padding: 14px 0; border-bottom: 1px solid #f1f5f9; text-align: right; font-weight: 800; font-size: 15px; color: #0f172a;">{{ number_format($item->price, 2) }} ₺</td>
        </tr>
        @endforeach
    </table>

    <!-- Teslimat Adresi -->
    <h2 style="font-size: 18px; font-weight: 900; color: #0f172a; margin: 30px 0 14px; letter-spacing: -0.5px;">
        Teslimat Adresi
    </h2>
    <div style="background-color: #f8fafc; padding: 18px 22px; border-radius: 12px; font-size: 14px; line-height: 1.6; border: 1px solid #e2e8f0; color: #334155; margin-bottom: 36px;">
        {{ $order->shipping_address }}
    </div>

    <!-- Sitedeki Lüks Siyah Buton -->
    <div style="text-align: center; margin: 36px 0 16px;">
        <a href="https://patenliayakkabilar.com/siparis-takip?order={{ $order->order_number }}" target="_blank" style="display: inline-block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 16px 42px; border-radius: 9999px; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 1.2px; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);">
            SİPARİŞİMİ TAKİP ET &rarr;
        </a>
    </div>

    <p style="margin-top: 32px; text-align: center; font-size: 13px; color: #64748b; line-height: 1.6;">
        Bizi tercih ettiğiniz için teşekkür ederiz. Siparişiniz kargoya teslim edildiğinde takip numaranız SMS ve e-posta ile iletilecektir.
    </p>

</div>
@endsection

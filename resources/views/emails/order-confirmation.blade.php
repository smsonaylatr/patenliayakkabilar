@extends('emails.layouts.base')

@section('title', 'Siparişiniz Başarıyla Alındı!')

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h2 style="color: #ff4e00; margin-top: 0;">Siparişiniz Başarıyla Alındı! 🎉</h2>
</div>

<p style="font-size: 16px; margin-bottom: 20px;">Merhaba {{ $order->customer_name }},</p>

<table width="100%" cellpadding="10" cellspacing="0" style="background-color: #f9fafb; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e5e7eb;">
    <tr>
        <td style="font-size: 14px; border-bottom: 1px solid #e5e7eb;"><strong>Sipariş No:</strong></td>
        <td style="font-size: 14px; border-bottom: 1px solid #e5e7eb; text-align: right;">{{ $order->order_number }}</td>
    </tr>
    <tr>
        <td style="font-size: 14px; border-bottom: 1px solid #e5e7eb;"><strong>Tarih:</strong></td>
        <td style="font-size: 14px; border-bottom: 1px solid #e5e7eb; text-align: right;">{{ $order->created_at->format('d.m.Y H:i') }}</td>
    </tr>
    <tr>
        <td style="font-size: 14px; border-bottom: 1px solid #e5e7eb;"><strong>Ödeme Yöntemi:</strong></td>
        <td style="font-size: 14px; border-bottom: 1px solid #e5e7eb; text-align: right;">
            @if($order->payment_method == 'credit_card') Kredi Kartı
            @elseif($order->payment_method == 'bank_transfer') Havale/EFT
            @elseif($order->payment_method == 'cash_on_delivery') Kapıda Ödeme
            @else {{ $order->payment_method }}
            @endif
        </td>
    </tr>
    <tr>
        <td style="font-size: 16px; font-weight: bold; color: #ff4e00;"><strong>Toplam:</strong></td>
        <td style="font-size: 16px; font-weight: bold; color: #ff4e00; text-align: right;">{{ number_format($order->grand_total, 2) }} ₺</td>
    </tr>
</table>

<h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid #f3f4f6; padding-bottom: 5px;">Sipariş Detayı</h3>

<table width="100%" cellpadding="10" cellspacing="0" style="margin-bottom: 30px; border-collapse: collapse;">
    @foreach($order->items as $item)
    <tr>
        <td width="60" style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            @if($item->product && $item->product->images->first())
                <img src="{{ url(Storage::url($item->product->images->first()->image_path)) }}" alt="{{ $item->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
            @else
                <div style="width: 50px; height: 50px; background-color: #f3f4f6; border-radius: 4px; display: inline-block;"></div>
            @endif
        </td>
        <td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">
            <div style="font-weight: bold; font-size: 14px;">{{ $item->name }}</div>
            @if($item->variant_name)
                <div style="font-size: 12px; color: #6b7280;">{{ $item->variant_name }}</div>
            @endif
        </td>
        <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: center; font-size: 14px;">{{ $item->quantity }}x</td>
        <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: bold; font-size: 14px;">{{ number_format($item->price, 2) }} ₺</td>
    </tr>
    @endforeach
</table>

<h3 style="margin-top: 20px; margin-bottom: 10px;">Teslimat Adresi</h3>
<div style="background-color: #f9fafb; padding: 15px; border-radius: 8px; font-size: 14px; line-height: 1.5; border: 1px solid #e5e7eb; margin-bottom: 30px;">
    {{ $order->shipping_address }}
</div>

<div style="text-align: center; margin-top: 30px;">
    <a href="{{ url('/hesabim/siparislerim') }}" style="display: inline-block; background-color: #ff4e00; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: bold; font-size: 16px;">Siparişimi Takip Et</a>
</div>

<p style="margin-top: 30px; text-align: center; font-size: 14px; color: #6b7280;">Bizi tercih ettiğiniz için teşekkür ederiz. Siparişiniz kargoya verildiğinde sizi tekrar bilgilendireceğiz.</p>
@endsection

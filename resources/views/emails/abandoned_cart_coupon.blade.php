@extends('emails.layouts.base')

@section('title', 'Size Özel %10 İndirim!')

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h2 style="color: #ff4e00; margin-top: 0;">Size Özel %10 İndirim Fırsatı! 🎁</h2>
</div>

<p style="font-size: 16px;">Merhaba {{ $customerName }},</p>
<p style="font-size: 15px; line-height: 1.6;">Sepetinize eklediğiniz harika ürünler hâlâ sizi bekliyor! Alışverişinizi tamamlamanız için size özel bir <strong>%10 indirim kuponu</strong> hazırladık.</p>

<div style="background: linear-gradient(135deg, #ff4e00, #ff7b3d); border-radius: 12px; padding: 25px; text-align: center; margin: 30px 0; color: #ffffff;">
    <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Kupon Kodunuz</div>
    <div style="font-size: 28px; font-weight: bold; letter-spacing: 3px; background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 8px; display: inline-block; margin: 10px 0;">{{ $couponCode }}</div>
    <div style="font-size: 13px; opacity: 0.9; margin-top: 8px;">⏰ {{ $expiresAt }} tarihine kadar geçerlidir</div>
</div>

<div style="margin-top: 30px; border-top: 1px solid #e5e7eb;">
    @foreach($cart->items as $item)
        <div style="display: flex; padding: 20px 0; border-bottom: 1px solid #e5e7eb;">
            @if($item->product && $item->product->images->first())
                <img src="{{ url(Storage::url($item->product->images->first()->image_path)) }}" alt="{{ $item->product->name }}" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; margin-right: 20px; background-color: #f3f4f6;">
            @else
                <div style="width: 80px; height: 80px; border-radius: 8px; margin-right: 20px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #9ca3af; float: left;">Görsel Yok</div>
            @endif
            <div style="flex-grow: 1;">
                <h3 style="font-size: 16px; font-weight: bold; margin: 0 0 5px 0;">{{ $item->product->name ?? 'Ürün' }}</h3>
                @if($item->variant)
                    <p style="font-size: 14px; color: #6b7280; margin: 0 0 5px 0;">Renk: {{ is_array($item->variant->color) ? implode(', ', $item->variant->color) : $item->variant->color }} | Beden: {{ $item->variant->size }}</p>
                @endif
                <p style="font-size: 14px; color: #6b7280; margin: 0 0 5px 0;">Adet: {{ $item->quantity }}</p>
                <p style="font-size: 16px; font-weight: bold; color: #ff4e00; margin: 0;">{{ number_format($item->product->discount_price ?? $item->product->price ?? 0, 2) }} ₺</p>
            </div>
        </div>
    @endforeach
</div>

<div style="text-align: center; margin-top: 40px;">
    <a href="{{ url('/checkout') }}" style="display: inline-block; background-color: #ff4e00; color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: bold; font-size: 16px;">Kuponu Kullan ve Satın Al</a>
</div>

<p style="text-align: center; color: #6b7280; font-size: 13px; margin-top: 20px;">Checkout sayfasında kupon kodunuzu girerek indirimi uygulayabilirsiniz.</p>
@endsection

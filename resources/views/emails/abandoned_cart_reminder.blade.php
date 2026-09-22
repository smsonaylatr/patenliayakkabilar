@extends('emails.layouts.base')

@section('title', 'Sepetinizde Ürünler Unuttunuz!')

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h2 style="color: #ff4e00; margin-top: 0;">Sepetinizde Ürünler Sizi Bekliyor! 🎁</h2>
</div>

<p style="font-size: 16px;">Merhaba {{ $cart->user->name ?? 'Değerli Müşterimiz' }},</p>
<p style="font-size: 15px; line-height: 1.6;">Patenli Ayakkabılar'da gezinirken sepetinize harika ürünler eklemiştiniz ama siparişinizi henüz tamamlamadınız. Stoklarımız tükenmeden sepetinizdeki ürünleri almak için hala bir şansınız var!</p>

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
    <a href="{{ url('/checkout') }}" style="display: inline-block; background-color: #ff4e00; color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: bold; font-size: 16px;">Sepetime Git ve Satın Al</a>
</div>
@endsection

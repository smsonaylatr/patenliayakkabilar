@extends('emails.layouts.base')

@section('title', 'Beklediğiniz Ürün Stokta!')

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h2 style="color: #ff4e00; margin-top: 0;">🎉 Beklediğiniz Ürün Stokta!</h2>
</div>

<p style="font-size: 15px; text-align: center; margin-bottom: 25px;">Harika haber! Daha önce stok bildirimi istediğiniz ürünün stokları yenilendi.</p>

<div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 25px; margin: 20px 0; text-align: center;">
    @if($product->images->first())
        <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}" style="max-width: 220px; height: auto; border-radius: 8px; margin-bottom: 15px;">
    @endif
    
    <div style="font-size: 18px; font-weight: bold; margin-bottom: 10px;">{{ $product->name }}</div>
    
    @if($variant)
        <div style="display: inline-block; background-color: #ffe4d6; color: #ff4e00; font-weight: bold; font-size: 13px; padding: 6px 14px; border-radius: 20px; margin-bottom: 15px;">{{ $variant->size }} Beden</div>
    @endif
    
    <div style="font-size: 24px; font-weight: bold; color: #ff4e00; margin-bottom: 25px;">{{ number_format($product->discount_price ?? $product->price, 2) }} ₺</div>
    
    <a href="{{ url('/urun/' . $product->slug) }}" style="display: inline-block; background-color: #ff4e00; color: #ffffff; font-weight: bold; font-size: 15px; padding: 14px 32px; border-radius: 8px; text-decoration: none;">Hemen İncele & Satın Al</a>
</div>

<p style="font-size: 14px; color: #6b7280; text-align: center;">Stoklar tükenmeden siparişinizi vermek için acele edin!</p>
@endsection

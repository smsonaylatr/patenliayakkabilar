@extends('emails.layouts.base')

@section('title', 'E-Faturanız Oluşturuldu')

@section('content')
<div style="text-align: center; margin-bottom: 30px;">
    <h2 style="margin-top: 0;">Merhaba {{ $order->customer_name }},</h2>
</div>

<p style="font-size: 15px; line-height: 1.6;"><strong>{{ $order->order_number }}</strong> numaralı siparişinize ait e-faturanız/e-arşiv belgeniz başarıyla oluşturulmuştur.</p>

<p style="font-size: 15px; line-height: 1.6;">Faturanızı bu e-postanın ekinde PDF formatında bulabilirsiniz.</p>

@if(!empty($pdfUrl))
    <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
        <a href="{{ $pdfUrl }}" style="display: inline-block; padding: 14px 28px; background-color: #ff4e00; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px;">Faturayı Tarayıcıda Görüntüle</a>
    </div>
@endif

<p style="font-size: 15px; line-height: 1.6;">Bizi tercih ettiğiniz için teşekkür ederiz.</p>
@endsection

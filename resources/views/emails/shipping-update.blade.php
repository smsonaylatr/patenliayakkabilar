@extends('emails.layouts.base')

@section('title', 'Siparişiniz ' . $statusText)

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h2 style="color: #ff4e00; margin-top: 0;">Siparişiniz {{ $statusText }}!</h2>
</div>

<p style="font-size: 16px; margin-bottom: 30px;">Merhaba {{ $order->customer_name }},</p>

<!-- Progress Bar -->
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px; text-align: center;">
    <tr>
        <td style="width: 25%; font-size: 24px; color: {{ in_array($order->status, ['processing', 'preparing', 'shipped', 'delivered']) ? '#ff4e00' : '#e5e7eb' }};">📝</td>
        <td style="width: 25%; font-size: 24px; color: {{ in_array($order->status, ['preparing', 'shipped', 'delivered']) ? '#ff4e00' : '#e5e7eb' }};">📦</td>
        <td style="width: 25%; font-size: 24px; color: {{ in_array($order->status, ['shipped', 'delivered']) ? '#ff4e00' : '#e5e7eb' }};">🚚</td>
        <td style="width: 25%; font-size: 24px; color: {{ $order->status == 'delivered' ? '#ff4e00' : '#e5e7eb' }};">✅</td>
    </tr>
    <tr>
        <td style="width: 25%; font-size: 12px; font-weight: bold; color: {{ in_array($order->status, ['processing', 'preparing', 'shipped', 'delivered']) ? '#ff4e00' : '#9ca3af' }}; padding-top: 5px;">Onaylandı</td>
        <td style="width: 25%; font-size: 12px; font-weight: bold; color: {{ in_array($order->status, ['preparing', 'shipped', 'delivered']) ? '#ff4e00' : '#9ca3af' }}; padding-top: 5px;">Hazırlanıyor</td>
        <td style="width: 25%; font-size: 12px; font-weight: bold; color: {{ in_array($order->status, ['shipped', 'delivered']) ? '#ff4e00' : '#9ca3af' }}; padding-top: 5px;">Kargoda</td>
        <td style="width: 25%; font-size: 12px; font-weight: bold; color: {{ $order->status == 'delivered' ? '#ff4e00' : '#9ca3af' }}; padding-top: 5px;">Teslim Edildi</td>
    </tr>
</table>

<div style="background-color: #f9fafb; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 30px;">
    <p style="margin: 0 0 10px 0; font-size: 14px;"><strong>Sipariş No:</strong> {{ $order->order_number }}</p>
    
    @if($order->shipping_company)
        <p style="margin: 0 0 10px 0; font-size: 14px;"><strong>Kargo Firması:</strong> {{ $order->shipping_company }}</p>
    @endif
    
    @if($order->cargo_tracking_code)
        <p style="margin: 0; font-size: 14px;"><strong>Takip Kodu:</strong> {{ $order->cargo_tracking_code }}</p>
    @endif
</div>

@if($order->cargo_tracking_url || $order->cargo_tracking_code)
    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ $order->cargo_tracking_url ?? url('/hesabim/siparislerim') }}" style="display: inline-block; background-color: #ff4e00; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: bold; font-size: 16px;">Kargo Takip</a>
    </div>
@endif

<p style="margin-top: 30px; text-align: center; font-size: 14px; color: #6b7280;">Bizi tercih ettiğiniz için teşekkür ederiz.</p>
@endsection

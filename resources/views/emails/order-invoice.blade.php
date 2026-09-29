@extends('emails.layouts.base')

@section('title', 'Siparişinizin Faturası — #' . $order->order_number . ' — Patenli Ayakkabılar®')

@section('content')
@php
    $rawName = trim($order->customer_name ?? '');
    $displayName = (empty($rawName) || strtolower($rawName) === 'admin') ? 'Değerli Müşterimiz' : ucwords(strtolower($rawName));
    $total = $order->grand_total ?? $order->total_amount ?? 0;
@endphp

<div style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #1e293b;">

    <!-- ÜST: FATURA BARI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td align="left" style="font-size: 13px; font-weight: 500; color: #64748b;">
                Fatura Bildirimi
            </td>
            <td align="right" style="font-size: 16px; font-weight: 700; color: #0f172a; white-space: nowrap;">
                {{ number_format($total, 2, ',', '.') }} ₺
            </td>
        </tr>
    </table>

    <!-- 1. KART: FATURA BAŞLIĞI KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="42" valign="middle" style="padding-right: 14px;">
                            <div style="width: 36px; height: 36px; border: 1.5px solid #2563eb; border-radius: 50%; text-align: center; line-height: 34px; font-size: 16px; color: #2563eb; font-weight: 700;">
                                ✓
                            </div>
                        </td>
                        <td valign="middle">
                            <p style="margin: 0 0 2px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8;">
                                #{{ $order->order_number }} Numaralı Sipariş
                            </p>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: -0.3px;">
                                Faturanız Hazırlandı
                            </h1>
                        </td>
                    </tr>
                </table>

                <div style="border-top: 1px solid #f1f5f9; margin-top: 18px; padding-top: 14px;">
                    <h3 style="margin: 0 0 4px; font-size: 14px; font-weight: 600; color: #0f172a;">
                        Sayın {{ $displayName }},
                    </h3>
                    <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400; line-height: 1.6;">
                        Patenli Ayakkabılar mağazamızdan vermiş olduğunuz siparişinize ait e-fatura belgeniz hazırlanmıştır. Faturanızı bu e-postanın ekinde bulabilir veya aşağıdaki bağlantı üzerinden görüntüleyebilirsiniz.
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. BUTONLAR: ZARİF KURUMSAL BUTONLAR -->
    <div style="margin-top: 24px; text-align: center;">
        @if(!empty($pdfUrl))
        <a href="{{ $pdfUrl }}" target="_blank" style="display: block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 14px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; margin-bottom: 10px;">
            📄 Faturayı Görüntüle ve İndir
        </a>
        @endif

        <a href="https://patenliayakkabilar.com/siparis-takip?order={{ $order->order_number }}" target="_blank" style="display: block; background-color: #ffffff; color: #334155; text-decoration: none; padding: 13px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; border: 1px solid #cbd5e1;">
            Siparişimi Takip Et
        </a>
    </div>

</div>
@endsection

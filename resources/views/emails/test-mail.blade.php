@extends('emails.layouts.base')

@section('title', 'E-Posta Sistemi Doğrulama Bildirimi — Patenli Ayakkabılar®')

@section('content')
<div style="text-align: left;">
    
    <div style="display: inline-block; background-color: #f4f4f5; border: 1px solid #e4e4e7; color: #18181b; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 9999px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 1px;">
        ✓ Sistem Doğrulandı
    </div>

    <h1 style="margin: 0 0 16px; font-size: 24px; font-weight: 900; color: #111827; letter-spacing: -0.8px; line-height: 1.25;">
        E-Posta Sunucusu Başarıyla Çalışıyor
    </h1>

    <p style="margin: 0 0 16px; font-size: 15px; color: #52525b; line-height: 1.6;">
        Merhaba,
    </p>

    <p style="margin: 0 0 20px; font-size: 15px; color: #52525b; line-height: 1.6;">
        Bu e-posta, <strong>patenliayakkabilar.com</strong> yönetim paneli üzerinden e-posta altyapısını, güvenlik sertifikalarını (SPF, DKIM, DMARC) ve şablon uyumunu doğrulamak amacıyla gönderilmiştir.
    </p>

    <!-- Bilgi Kartı -->
    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px 20px; margin-bottom: 24px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 13px;">
            <tr>
                <td style="padding: 6px 0; color: #6b7280; width: 140px; font-weight: 600;">Gönderici:</td>
                <td style="padding: 6px 0; color: #111827; font-weight: 700;">{{ $fromAddress ?? config('mail.from.address') }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b7280; font-weight: 600;">Durum:</td>
                <td style="padding: 6px 0; color: #16a34a; font-weight: 700;">✓ Aktif ve Güvenli (IPv4 Dedicated Postfix)</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b7280; font-weight: 600;">Gönderim Zamanı:</td>
                <td style="padding: 6px 0; color: #111827;">{{ now()->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <p style="margin: 0 0 24px; font-size: 14px; color: #71717a; line-height: 1.6;">
        Sipariş onayları, kargo bildirimleri ve e-arşiv faturaları alıcılarınıza bu kurumsal şablonla iletilecektir.
    </p>

    <!-- Ana Sayfadaki Birebir Siyah Oval Buton -->
    <div style="text-align: center; margin: 30px 0 10px;">
        <a href="https://patenliayakkabilar.com" target="_blank" style="display: inline-block; background-color: #111827; color: #ffffff; text-decoration: none; padding: 15px 36px; border-radius: 9999px; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);">
            TÜM KOLEKSİYONU KEŞFET &rarr;
        </a>
    </div>

</div>
@endsection

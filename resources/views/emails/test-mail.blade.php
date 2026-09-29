@extends('emails.layouts.base')

@section('title', 'E-Posta Sistemi Doğrulama Bildirimi — Patenli Ayakkabılar®')

@section('content')
<div style="text-align: left; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    
    <!-- Üst Mini Rozet -->
    <div style="display: inline-block; background-color: #f1f5f9; border: 1px solid #cbd5e1; color: #1e293b; font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 9999px; margin-bottom: 18px; text-transform: uppercase; letter-spacing: 0.5px;">
        ✓ Sistem Doğrulandı
    </div>

    <!-- Başlık -->
    <h1 style="margin: 0 0 14px; font-size: 21px; font-weight: 700; color: #0f172a; letter-spacing: -0.3px; line-height: 1.35;">
        E-Posta Sunucusu Başarıyla Çalışıyor
    </h1>

    <p style="margin: 0 0 14px; font-size: 14px; color: #475569; line-height: 1.65; font-weight: 400;">
        Merhaba,
    </p>

    <p style="margin: 0 0 22px; font-size: 14px; color: #475569; line-height: 1.65; font-weight: 400;">
        Bu e-posta, <strong>patenliayakkabilar.com</strong> yönetim paneli üzerinden e-posta altyapısını, güvenlik sertifikalarını (SPF, DKIM, DMARC) ve şablon uyumunu doğrulamak amacıyla gönderilmiştir.
    </p>

    <!-- Bilgi Kartı -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 18px 22px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; width: 140px; font-weight: 500; font-size: 13px;">Gönderici:</td>
                        <td style="padding: 6px 0; color: #0f172a; font-weight: 600; font-size: 13px;">{{ $fromAddress ?? config('mail.from.address') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 500; font-size: 13px;">Durum:</td>
                        <td style="padding: 6px 0; color: #059669; font-weight: 600; font-size: 13px;">✓ Aktif ve Güvenli (IPv4 Dedicated Postfix)</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 500; font-size: 13px;">Gönderim Zamanı:</td>
                        <td style="padding: 6px 0; color: #0f172a; font-weight: 600; font-size: 13px;">{{ now()->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 26px; font-size: 13px; color: #64748b; line-height: 1.6; font-weight: 400;">
        Sipariş onayları, kargo bildirimleri ve e-arşiv faturaları alıcılarınıza bu kurumsal şablonla iletilecektir.
    </p>

    <!-- Zarif Buton -->
    <div style="text-align: center; margin: 28px 0 8px;">
        <a href="https://patenliayakkabilar.com" target="_blank" style="display: inline-block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 14px 34px; border-radius: 9999px; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.8px;">
            TÜM KOLEKSİYONU KEŞFET &rarr;
        </a>
    </div>

</div>
@endsection

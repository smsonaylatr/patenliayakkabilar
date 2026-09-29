@extends('emails.layouts.base')

@section('title', 'E-Posta Sistemi Doğrulama Bildirimi — Patenli Ayakkabılar®')

@section('content')
<div style="text-align: left; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    
    <!-- Üst Mini Rozet -->
    <div style="display: inline-block; background-color: #f1f5f9; border: 1px solid #cbd5e1; color: #0f172a; font-size: 11px; font-weight: 800; padding: 5px 14px; border-radius: 9999px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.8px;">
        ✓ Sistem Doğrulandı
    </div>

    <!-- Başlık (Sitenin Başlık Karakteri) -->
    <h1 style="margin: 0 0 18px; font-size: 26px; font-weight: 900; color: #0f172a; letter-spacing: -0.8px; line-height: 1.3; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        E-Posta Sunucusu Başarıyla Çalışıyor
    </h1>

    <p style="margin: 0 0 16px; font-size: 15px; color: #334155; line-height: 1.7; font-weight: 400;">
        Merhaba,
    </p>

    <p style="margin: 0 0 26px; font-size: 15px; color: #334155; line-height: 1.7; font-weight: 400;">
        Bu e-posta, <strong>patenliayakkabilar.com</strong> yönetim paneli üzerinden e-posta altyapısını, güvenlik sertifikalarını (SPF, DKIM, DMARC) ve şablon uyumunu doğrulamak amacıyla gönderilmiştir.
    </p>

    <!-- Bilgi Kartı (Yüksek Kontrastlı, Net Fontlu Tablo) -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; margin-bottom: 28px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        <tr>
            <td style="padding: 22px 26px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; width: 140px; font-weight: 600; font-size: 14px;">Gönderici:</td>
                        <td style="padding: 6px 0; color: #0f172a; font-weight: 700; font-size: 14px;">{{ $fromAddress ?? config('mail.from.address') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 600; font-size: 14px;">Durum:</td>
                        <td style="padding: 6px 0; color: #059669; font-weight: 700; font-size: 14px;">✓ Aktif ve Güvenli (IPv4 Dedicated Postfix)</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 600; font-size: 14px;">Gönderim Zamanı:</td>
                        <td style="padding: 6px 0; color: #0f172a; font-weight: 700; font-size: 14px;">{{ now()->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 32px; font-size: 14px; color: #64748b; line-height: 1.65;">
        Sipariş onayları, kargo bildirimleri ve e-arşiv faturaları alıcılarınıza bu kurumsal şablonla iletilecektir.
    </p>

    <!-- Sitedeki Lüks Siyah Oval Buton -->
    <div style="text-align: center; margin: 34px 0 10px;">
        <a href="https://patenliayakkabilar.com" target="_blank" style="display: inline-block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 16px 42px; border-radius: 9999px; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 1.2px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);">
            TÜM KOLEKSİYONU KEŞFET &rarr;
        </a>
    </div>

</div>
@endsection

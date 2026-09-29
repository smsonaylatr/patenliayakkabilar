@extends('emails.layouts.base')

@section('title', 'E-Posta Sistemi Doğrulama Bildirimi — Patenli Ayakkabılar®')

@section('content')
<div style="text-align: left;">
    <div style="display: inline-block; background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 6px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
        ✓ Sistem Doğrulaması Başarılı
    </div>

    <h2 style="margin: 0 0 16px; font-size: 20px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
        E-Posta Sunucusu Başarıyla Bağlandı! 🎉
    </h2>

    <p style="margin: 0 0 16px; font-size: 15px; color: #475569; line-height: 1.6;">
        Merhaba,
    </p>

    <p style="margin: 0 0 20px; font-size: 15px; color: #475569; line-height: 1.6;">
        Bu e-posta, <strong>patenliayakkabilar.com</strong> yönetim paneli üzerinden e-posta gönderim protokollerini, güvenlik anahtarlarını (SPF, DKIM, DMARC) ve şablon uyumluluğunu test etmek amacıyla otomatik olarak oluşturulmuştur.
    </p>

    <!-- Bilgi Kartı -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 13px;">
            <tr>
                <td style="padding: 6px 0; color: #64748b; width: 140px; font-weight: 600;">Gönderici:</td>
                <td style="padding: 6px 0; color: #0f172a; font-weight: 700;">{{ $fromAddress ?? config('mail.from.address') }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748b; font-weight: 600;">SMTP Durumu:</td>
                <td style="padding: 6px 0; color: #16a34a; font-weight: 700;">✓ Aktif ve Doğrulandı (Port 465 SSL)</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748b; font-weight: 600;">Gönderim Zamanı:</td>
                <td style="padding: 6px 0; color: #0f172a;">{{ now()->timezone('Europe/Istanbul')->format('d.m.Y H:i:s') }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748b; font-weight: 600;">Protokol:</td>
                <td style="padding: 6px 0; color: #0f172a;">IPv4 Dedicated Postfix (Datalix VDS)</td>
            </tr>
        </table>
    </div>

    <p style="margin: 0 0 24px; font-size: 14px; color: #64748b; line-height: 1.6;">
        Artık mağazanızdaki yeni sipariş bildirimleri, e-arşiv faturaları, kargo takip mesajları ve müşteri hoşgeldin e-postaları bu şablon yapısıyla alıcıların gelen kutusuna sorunsuz teslim edilecektir.
    </p>

    <!-- Aksiyon Butonu -->
    <div style="text-align: center; margin: 30px 0 10px;">
        <a href="https://patenliayakkabilar.com" target="_blank" style="display: inline-block; background-color: #ff4e00; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 700; font-size: 14px; letter-spacing: 0.3px; box-shadow: 0 4px 12px rgba(255, 78, 0, 0.25);">
            Mağazayı Ziyaret Et →
        </a>
    </div>
</div>
@endsection

@extends('emails.layouts.base')

@section('title', 'E-Posta Sistemi Doğrulama Bildirimi — Patenli Ayakkabılar®')

@section('content')
<div style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #1e293b;">

    <!-- ÜST: SİSTEM BARI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td align="left" style="font-size: 13px; font-weight: 500; color: #64748b;">
                Sistem Doğrulama Bildirimi
            </td>
            <td align="right" style="font-size: 14px; font-weight: 700; color: #16a34a;">
                ✓ Sistem Aktif
            </td>
        </tr>
    </table>

    <!-- 1. KART: DOĞRULAMA KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="42" valign="middle" style="padding-right: 14px;">
                            <div style="width: 36px; height: 36px; border: 1.5px solid #16a34a; border-radius: 50%; text-align: center; line-height: 34px; font-size: 18px; color: #16a34a; font-weight: 700;">
                                ✓
                            </div>
                        </td>
                        <td valign="middle">
                            <p style="margin: 0 0 2px; font-size: 12px; color: #94a3b8; font-weight: 400;">
                                E-Posta Altyapı Kontrolü
                            </p>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: -0.3px;">
                                E-Posta Sunucusu Başarıyla Çalışıyor
                            </h1>
                        </td>
                    </tr>
                </table>

                <div style="border-top: 1px solid #f1f5f9; margin-top: 18px; padding-top: 14px;">
                    <h3 style="margin: 0 0 3px; font-size: 14px; font-weight: 600; color: #0f172a;">
                        Tüm güvenlik sertifikaları doğrulandı
                    </h3>
                    <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400; line-height: 1.5;">
                        Bu e-posta, <strong>patenliayakkabilar.com</strong> yönetim paneli üzerinden e-posta altyapısını, güvenlik protokollerini (SPF, DKIM, DMARC) ve şablon uyumunu doğrulamak amacıyla gönderilmiştir.
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. KART: SUNUCU BİLGİLERİ -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">
                    Altyapı Parametreleri
                </h3>

                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #64748b;">Gönderici:</td>
                        <td align="right" style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; font-weight: 600; color: #0f172a;">{{ $fromAddress ?? config('mail.from.address') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #64748b;">Güvenlik:</td>
                        <td align="right" style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; font-weight: 600; color: #16a34a;">✓ SPF, DKIM, DMARC & IPv4 Dedicated</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-size: 13px; color: #64748b;">Gönderim Zamanı:</td>
                        <td align="right" style="padding: 8px 0; font-size: 13px; font-weight: 600; color: #0f172a;">{{ now()->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 3. BUTONLAR: ZARİF KURUMSAL BUTONLAR -->
    <div style="margin-top: 24px; text-align: center;">
        <a href="https://patenliayakkabilar.com" target="_blank" style="display: block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 14px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; margin-bottom: 10px;">
            Yönetim Paneline Dön
        </a>
    </div>

</div>
@endsection

@extends('emails.layouts.base')

@section('title', 'Patenli Ayakkabılar® Ailesine Hoş Geldiniz! 🎉')

@section('content')
@php
    $firstName = explode(' ', trim($user->name ?? 'Değerli Üyemiz'))[0];
@endphp

<div style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #1e293b;">

    <!-- ÜST: BİLDİRİM BARI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td align="left" style="font-size: 13px; font-weight: 500; color: #64748b;">
                Üyelik Bildirimi
            </td>
            <td align="right" style="font-size: 14px; font-weight: 700; color: #ff4e00;">
                Hoş Geldiniz 🎉
            </td>
        </tr>
    </table>

    <!-- 1. KART: HOŞGELDİN BAŞLIĞI KARTI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="42" valign="middle" style="padding-right: 14px;">
                            <div style="width: 36px; height: 36px; border: 1.5px solid #ff4e00; border-radius: 50%; text-align: center; line-height: 34px; font-size: 18px; color: #ff4e00;">
                                👋
                            </div>
                        </td>
                        <td valign="middle">
                            <p style="margin: 0 0 2px; font-size: 12px; color: #94a3b8; font-weight: 400;">
                                Patenli Ayakkabılar® Üyelik
                            </p>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: -0.3px;">
                                Hoş Geldin {{ $firstName }}!
                            </h1>
                        </td>
                    </tr>
                </table>

                <div style="border-top: 1px solid #f1f5f9; margin-top: 18px; padding-top: 14px;">
                    <h3 style="margin: 0 0 3px; font-size: 14px; font-weight: 600; color: #0f172a;">
                        Hesabınız başarıyla oluşturuldu
                    </h3>
                    <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400; line-height: 1.5;">
                        Patenli Ayakkabılar ailesine katıldığınız için çok mutluyuz. Hesabınızla siparişlerinizi anlık olarak takip edebilir, favori modellerinizi kaydedebilir ve üyelere özel sürpriz indirimlerden anında haberdar olabilirsiniz.
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. KART: AYRICALIKLAR VE AVANTAJLAR -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">
                    Patenli Ayakkabılar Ayrıcalıkları
                </h3>

                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    <tr>
                        <td width="36" valign="middle" style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 20px;">
                            🚀
                        </td>
                        <td valign="middle" style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9;">
                            <p style="margin: 0; font-size: 13px; font-weight: 600; color: #1e293b;">Hızlı Teslimat & Güvenli Paketleme</p>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #64748b;">Siparişleriniz titizlikle kontrol edilip hızla kargolanır.</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="36" valign="middle" style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 20px;">
                            💯
                        </td>
                        <td valign="middle" style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9;">
                            <p style="margin: 0; font-size: 13px; font-weight: 600; color: #1e293b;">%100 Orijinal Ürün Garantisi</p>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #64748b;">Tüm ürünlerimiz test edilmiş ve tam güvence altındadır.</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="36" valign="middle" style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 20px;">
                            🔄
                        </td>
                        <td valign="middle" style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9;">
                            <p style="margin: 0; font-size: 13px; font-weight: 600; color: #1e293b;">Kolay Değişim ve Destek</p>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #64748b;">Beden uyumsuzluklarında hızlı değişim ve WhatsApp destek hattı.</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="36" valign="middle" style="padding: 10px 0; font-size: 20px;">
                            🛡️
                        </td>
                        <td valign="middle" style="padding: 10px 12px;">
                            <p style="margin: 0; font-size: 13px; font-weight: 600; color: #1e293b;">3D Güvenli Ödeme & Kapıda Ödeme</p>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #64748b;">İster kartla vadesiz 3 taksit, ister kapıda nakit/kart seçeneği.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 3. BUTONLAR: ZARİF KURUMSAL BUTONLAR -->
    <div style="margin-top: 24px; text-align: center;">
        <a href="https://patenliayakkabilar.com" target="_blank" style="display: block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 14px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; margin-bottom: 10px;">
            Alışverişe Başla
        </a>

        <a href="https://patenliayakkabilar.com/hesabim" target="_blank" style="display: block; background-color: #ffffff; color: #334155; text-decoration: none; padding: 13px 22px; border-radius: 10px; font-weight: 600; font-size: 13px; text-align: center; border: 1px solid #cbd5e1;">
            Hesabımı Görüntüle
        </a>
    </div>

</div>
@endsection

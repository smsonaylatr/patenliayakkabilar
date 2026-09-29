@extends('emails.layouts.base')

@section('title', 'Patenli Ayakkabılar® — Aramıza Hoş Geldiniz')

@section('content')
@php
    $rawName = trim($user->name ?? '');
    if (empty($rawName) || strtolower($rawName) === 'admin') {
        $displayName = 'Değerli Müşterimiz';
    } else {
        $displayName = ucwords(strtolower($rawName));
    }
@endphp

<div style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #1e293b;">

    <!-- ÜST: BİLDİRİM BARI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td align="left" style="font-size: 13px; font-weight: 500; color: #64748b;">
                Üyelik Bildirimi
            </td>
            <td align="right" style="font-size: 13px; font-weight: 600; color: #16a34a; white-space: nowrap;">
                ✓ Hesap Aktif
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
                            <!-- Hoş Geldin Karşılama Rozeti -->
                            <div style="width: 36px; height: 36px; border: 1.5px solid #ff4e00; background-color: #fff7ed; border-radius: 50%; text-align: center; line-height: 34px; font-size: 18px;">
                                👋
                            </div>
                        </td>
                        <td valign="middle">
                            <p style="margin: 0 0 2px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8;">
                                Patenli Ayakkabılar® Üyeliği
                            </p>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: -0.3px;">
                                Aramıza Hoş Geldiniz
                            </h1>
                        </td>
                    </tr>
                </table>

                <div style="border-top: 1px solid #f1f5f9; margin-top: 18px; padding-top: 14px;">
                    <h3 style="margin: 0 0 4px; font-size: 14px; font-weight: 600; color: #0f172a;">
                        Sayın {{ $displayName }},
                    </h3>
                    <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 400; line-height: 1.6;">
                        Patenli Ayakkabılar ailesine katıldığınız için teşekkür ederiz. Üyelik hesabınız başarıyla tanımlanmıştır. Siparişlerinizi ve kargo durumunuzu anlık olarak takip edebilir, yeni koleksiyonları ve üyelere özel avantajları inceleyebilirsiniz.
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. KART: HESAP BİLGİLERİ -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <h3 style="margin: 0 0 16px; font-size: 15px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">
                    Hesap Bilgileri
                </h3>

                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="50%" valign="top" style="padding-right: 12px;">
                            <p style="margin: 0 0 3px; font-size: 12px; font-weight: 600; color: #334155;">Kayıtlı E-Posta</p>
                            <p style="margin: 0; font-size: 13px; color: #0f172a; font-weight: 500;">{{ $user->email }}</p>
                        </td>
                        <td width="50%" valign="top">
                            <p style="margin: 0 0 3px; font-size: 12px; font-weight: 600; color: #334155;">Üyelik Durumu</p>
                            <p style="margin: 0; font-size: 13px; color: #16a34a; font-weight: 600;">Onaylandı & Aktif</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 3. KART: KURUMSAL AVANTAJLAR -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 22px;">
                <h3 style="margin: 0 0 16px; font-size: 15px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">
                    Patenli Ayakkabılar Güvencesi
                </h3>

                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    <tr>
                        <td width="32" valign="middle" style="padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                            <div style="width: 24px; height: 24px; border-radius: 6px; background-color: #f1f5f9; text-align: center; line-height: 24px; font-size: 11px; font-weight: 700; color: #0f172a;">
                                01
                            </div>
                        </td>
                        <td valign="middle" style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9;">
                            <p style="margin: 0; font-size: 13px; font-weight: 600; color: #1e293b;">Hızlı Teslimat & Özenli Paketleme</p>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #64748b;">Siparişleriniz kalite kontrolden geçerek hızlıca kargoya teslim edilir.</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="32" valign="middle" style="padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                            <div style="width: 24px; height: 24px; border-radius: 6px; background-color: #f1f5f9; text-align: center; line-height: 24px; font-size: 11px; font-weight: 700; color: #0f172a;">
                                02
                            </div>
                        </td>
                        <td valign="middle" style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9;">
                            <p style="margin: 0; font-size: 13px; font-weight: 600; color: #1e293b;">%100 Orijinal Ürün Garantisi</p>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #64748b;">Tüm modellerimiz test edilmiş, dayanıklı ve marka lisanslıdır.</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="32" valign="middle" style="padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                            <div style="width: 24px; height: 24px; border-radius: 6px; background-color: #f1f5f9; text-align: center; line-height: 24px; font-size: 11px; font-weight: 700; color: #0f172a;">
                                03
                            </div>
                        </td>
                        <td valign="middle" style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9;">
                            <p style="margin: 0; font-size: 13px; font-weight: 600; color: #1e293b;">Hızlı Değişim ve Uzman Desteği</p>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #64748b;">Beden uyumu ve tüm sorularınız için kesintisiz müşteri desteği.</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="32" valign="middle" style="padding: 10px 0;">
                            <div style="width: 24px; height: 24px; border-radius: 6px; background-color: #f1f5f9; text-align: center; line-height: 24px; font-size: 11px; font-weight: 700; color: #0f172a;">
                                04
                            </div>
                        </td>
                        <td valign="middle" style="padding: 10px 12px;">
                            <p style="margin: 0; font-size: 13px; font-weight: 600; color: #1e293b;">3D Güvenli Ödeme & Kapıda Ödeme</p>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #64748b;">Vadesiz 3 taksit seçeneği veya teslimatta güvenli ödeme fırsatı.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 4. BUTONLAR: ZARİF KURUMSAL BUTONLAR -->
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

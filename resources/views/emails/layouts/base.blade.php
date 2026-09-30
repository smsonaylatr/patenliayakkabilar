@php
    $footerSocials = [];
    try {
        if (class_exists(\App\Models\Setting::class)) {
            $footerSocials = \App\Models\Setting::whereIn('key', [
                'footer_whatsapp', 'footer_instagram', 'footer_tiktok',
                'footer_facebook', 'footer_twitter', 'footer_youtube'
            ])->pluck('value', 'key')->toArray();
        }
    } catch (\Throwable $e) {}

    $cleanWa = preg_replace('/[^0-9]/', '', $footerSocials['footer_whatsapp'] ?? '905441828800');
    $waUrl = !empty($cleanWa) ? "https://wa.me/{$cleanWa}" : "https://wa.me/905441828800";
    $igUrl = !empty($footerSocials['footer_instagram']) ? $footerSocials['footer_instagram'] : 'https://www.instagram.com/patenliayakkabilar';
    $tiktokUrl = !empty($footerSocials['footer_tiktok']) ? $footerSocials['footer_tiktok'] : 'https://www.tiktok.com/@patenliayakkabilar';
    $ytUrl = !empty($footerSocials['footer_youtube']) ? $footerSocials['footer_youtube'] : 'https://www.youtube.com/@patenliayakkabilar';
    $fbUrl = !empty($footerSocials['footer_facebook']) ? $footerSocials['footer_facebook'] : 'https://www.facebook.com/patenliayakkabilar';
@endphp
<!DOCTYPE html>
<html lang="tr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Patenli Ayakkabılar®')</title>
    <!--[if mso]>
    <style>
        * { font-family: 'Segoe UI', Arial, sans-serif !important; }
    </style>
    <![endif]-->
    <style>
        body, table, td, p, a, span { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; display: block; }
        @media only screen and (max-width: 820px) {
            .email-container { width: 100% !important; }
            .mobile-padding { padding-left: 18px !important; padding-right: 18px !important; }
            .footer-stack { display: block !important; width: 100% !important; margin-bottom: 24px !important; }
            .footer-links-wrap { width: 100% !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #1e293b;">

    <!-- ANA GÖVDE SARMALAYICI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8fafc; padding: 36px 12px;">
        <tr>
            <td align="center">
                <table class="email-container" width="800" cellpadding="0" cellspacing="0" border="0" style="max-width: 800px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    
                    <!-- İÇERİK ALANI -->
                    <tr>
                        <td class="mobile-padding" style="padding: 44px 48px 36px; background-color: #ffffff; color: #334155; font-size: 14px; line-height: 1.65;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- 3. FOOTER: MAT LÜKS SİYAH (#121316) -->
                    <tr bgcolor="#121316">
                        <td class="mobile-padding" bgcolor="#121316" style="background-color: #121316; padding: 24px 32px 18px; color: #94a3b8; font-size: 11px; line-height: 1.5;">
                            
                            <!-- 2 Sütunlu Grid: Sol (Logo, Açıklama, Sosyal İkonlar, ETBİS), Sağ (Hızlı Menü, Kurumsal) -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#121316" style="background-color: #121316;">
                                <tr>
                                    <!-- Sol Kolon -->
                                    <td class="footer-stack" width="55%" valign="top" style="padding-right: 20px;">
                                        <!-- Footer Logo (Beyaz) -->
                                        <a href="https://patenliayakkabilar.com" target="_blank" style="text-decoration: none; display: inline-block; margin-bottom: 6px;">
                                            <span style="font-size: 16px; font-weight: 700; color: #ffffff; letter-spacing: -0.3px; text-transform: uppercase;">PATENLİ</span><span style="font-size: 16px; font-weight: 300; color: #cbd5e1; letter-spacing: -0.3px; text-transform: uppercase;">AYAKKABILAR<sup style="font-size: 8px; font-weight: 400; vertical-align: super; color: #94a3b8;">®</sup></span>
                                        </a>
                                        
                                        <!-- Site Açıklaması -->
                                        <p style="margin: 0 0 10px; color: #94a3b8; font-size: 11px; line-height: 1.45; max-width: 320px; font-weight: 400;">
                                            Çocukların eğlenirken güvende olması için ürün seçimini, kargo sürecini ve satış sonrası desteği kolaylaştırıyoruz.
                                        </p>

                                        <!-- Sosyal Medya İkonları ve ETBİS Yan Yana (Aktif) -->
                                        <table cellpadding="0" cellspacing="0" border="0" bgcolor="#121316" style="background-color: #121316;">
                                            <tr>
                                                <td style="padding-right: 10px;">
                                                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" title="WhatsApp Destek Hattı" style="display: block; opacity: 0.9;">
                                                        <img src="https://img.icons8.com/material-rounded/48/9ca3af/whatsapp.png" alt="WhatsApp" width="18" height="18" style="display: block; width: 18px; height: 18px; border: 0;">
                                                    </a>
                                                </td>
                                                <td style="padding-right: 10px;">
                                                    <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer" title="Instagram: @patenliayakkabilar" style="display: block; opacity: 0.9;">
                                                        <img src="https://img.icons8.com/material-rounded/48/9ca3af/instagram-new.png" alt="Instagram" width="18" height="18" style="display: block; width: 18px; height: 18px; border: 0;">
                                                    </a>
                                                </td>
                                                <td style="padding-right: 10px;">
                                                    <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer" title="TikTok: @patenliayakkabilar" style="display: block; opacity: 0.9;">
                                                        <img src="https://img.icons8.com/material-rounded/48/9ca3af/tiktok.png" alt="TikTok" width="18" height="18" style="display: block; width: 18px; height: 18px; border: 0;">
                                                    </a>
                                                </td>
                                                <td style="padding-right: 10px;">
                                                    <a href="{{ $ytUrl }}" target="_blank" rel="noopener noreferrer" title="YouTube" style="display: block; opacity: 0.9;">
                                                        <img src="https://img.icons8.com/material-rounded/48/9ca3af/youtube-play.png" alt="YouTube" width="18" height="18" style="display: block; width: 18px; height: 18px; border: 0;">
                                                    </a>
                                                </td>
                                                <td style="padding-right: 12px;">
                                                    <a href="{{ $fbUrl }}" target="_blank" rel="noopener noreferrer" title="Facebook" style="display: block; opacity: 0.9;">
                                                        <img src="https://img.icons8.com/material-rounded/48/9ca3af/facebook-f.png" alt="Facebook" width="18" height="18" style="display: block; width: 18px; height: 18px; border: 0;">
                                                    </a>
                                                </td>
                                                <!-- ETBİS Rozeti (Aktif Link) -->
                                                <td>
                                                    <a href="https://etbis.ticaret.gov.tr/" target="_blank" rel="noopener noreferrer" title="ETBİS Kayıtlı E-Ticaret Sitesi" style="text-decoration: none; display: inline-block;">
                                                        <div style="background-color: rgba(255,255,255,0.06); border: 1px solid #27272a; border-radius: 4px; padding: 2px 6px; line-height: 1;">
                                                            <span style="color: #10b981; font-weight: 700; font-size: 9px;">ETBİS</span>
                                                            <span style="color: #6ee7b7; font-size: 8px; background: rgba(16,185,129,0.15); padding: 1px 2px; border-radius: 2px; margin-left: 2px; font-weight: 600;">Kayıtlı</span>
                                                        </div>
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <!-- Sağ Kolon: Hızlı Menü & Kurumsal -->
                                    <td class="footer-stack" width="45%" valign="top">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="footer-links-wrap">
                                            <tr>
                                                <td width="50%" valign="top" style="padding-right: 8px;">
                                                    <p style="margin: 0 0 6px; font-weight: 600; color: #f8fafc; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Hızlı Menü</p>
                                                    <p style="margin: 0 0 3px;"><a href="https://patenliayakkabilar.com" target="_blank" style="color: #94a3b8; text-decoration: none; font-size: 11px; font-weight: 400;">Ana Sayfa</a></p>
                                                    <p style="margin: 0 0 3px;"><a href="https://patenliayakkabilar.com/patenli-ayakkabilar" target="_blank" style="color: #94a3b8; text-decoration: none; font-size: 11px; font-weight: 400;">Tüm Ürünler</a></p>
                                                    <p style="margin: 0 0 3px;"><a href="https://patenliayakkabilar.com/beden-rehberi" target="_blank" style="color: #94a3b8; text-decoration: none; font-size: 11px; font-weight: 400;">Beden Rehberi</a></p>
                                                    <p style="margin: 0;"><a href="https://patenliayakkabilar.com/iletisim" target="_blank" style="color: #94a3b8; text-decoration: none; font-size: 11px; font-weight: 400;">İletişim</a></p>
                                                </td>
                                                <td width="50%" valign="top">
                                                    <p style="margin: 0 0 6px; font-weight: 600; color: #f8fafc; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Kurumsal</p>
                                                    <p style="margin: 0 0 3px;"><a href="https://patenliayakkabilar.com/hakkimizda" target="_blank" style="color: #94a3b8; text-decoration: none; font-size: 11px; font-weight: 400;">Hakkımızda</a></p>
                                                    <p style="margin: 0 0 3px;"><a href="https://patenliayakkabilar.com/sikca-sorulan-sorular" target="_blank" style="color: #94a3b8; text-decoration: none; font-size: 11px; font-weight: 400;">Sıkça Sorulanlar</a></p>
                                                    <p style="margin: 0 0 3px;"><a href="https://patenliayakkabilar.com/iade-ve-degisim" target="_blank" style="color: #94a3b8; text-decoration: none; font-size: 11px; font-weight: 400;">İade & Değişim</a></p>
                                                    <p style="margin: 0;"><a href="https://patenliayakkabilar.com/gizlilik-politikasi" target="_blank" style="color: #94a3b8; text-decoration: none; font-size: 11px; font-weight: 400;">Gizlilik</a></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Alt Kısım: Telif Hakkı -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#121316" style="background-color: #121316; border-top: 1px solid #1e2229; margin-top: 14px; padding-top: 10px;">
                                <tr>
                                    <td align="center" style="color: #64748b; font-size: 11px;">
                                        <p style="margin: 0; color: #64748b; font-size: 11px; font-weight: 400; line-height: 1.4;">
                                            &copy; {{ date('Y') }} Patenli Ayakkabılar®. Tüm hakları saklıdır.
                                        </p>
                                        <p style="margin: 3px 0 0; color: #475569; font-size: 10px; font-family: monospace;">
                                            İleti Ref: #{{ date('ymd-His') }}-{{ rand(100, 999) }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

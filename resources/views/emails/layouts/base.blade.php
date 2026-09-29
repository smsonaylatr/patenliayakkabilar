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
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; display: block; }
        @media only screen and (max-width: 620px) {
            .email-container { width: 100% !important; }
            .mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
            .footer-col { display: block !important; width: 100% !important; margin-bottom: 24px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #18181b;">

    <!-- 1. EN ÜST MARQUEE DUYURU BANDI (Sitedeki bg-[#6b6b6b] bandı) -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #6b6b6b;">
        <tr>
            <td align="center" style="padding: 11px 16px;">
                <p style="margin: 0; font-size: 11px; font-weight: 700; color: #ffffff; letter-spacing: 2px; text-transform: uppercase; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
                    HIZLI TESLİMAT &nbsp;&nbsp;&bull;&nbsp;&nbsp; KAPIDA ÖDEME FIRSATI &nbsp;&nbsp;&bull;&nbsp;&nbsp; %100 İADE GARANTİSİ &nbsp;&nbsp;&bull;&nbsp;&nbsp; VADESİZ 3 TAKSİT
                </p>
            </td>
        </tr>
    </table>

    <!-- ANA GÖVDE SARMALAYICI -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f5; padding: 24px 8px;">
        <tr>
            <td align="center">
                <table class="email-container" width="620" cellpadding="0" cellspacing="0" border="0" style="max-width: 620px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06); border: 1px solid #e4e4e7;">
                    
                    <!-- 2. HEADER: BEYAZ ZEMİN, SADECE LOGO (Menü kaldırıldı) -->
                    <tr>
                        <td align="center" style="background-color: #ffffff; padding: 28px 24px; border-bottom: 1px solid #e4e4e7;">
                            <a href="https://patenliayakkabilar.com" target="_blank" style="text-decoration: none; display: inline-block;">
                                <span style="font-size: 26px; font-weight: 900; color: #111827; letter-spacing: -1px; text-transform: uppercase; font-family: 'Arial Black', -apple-system, sans-serif;">PATENLİ</span><span style="font-size: 26px; font-weight: 300; color: #111827; letter-spacing: -1px; text-transform: uppercase; font-family: -apple-system, sans-serif;">AYAKKABILAR<sup style="font-size: 12px; font-weight: 400; vertical-align: super;">®</sup></span>
                            </a>
                        </td>
                    </tr>

                    <!-- 3. İÇERİK ALANI -->
                    <tr>
                        <td class="mobile-padding" style="padding: 36px 36px 32px; background-color: #ffffff; color: #18181b; font-size: 15px; line-height: 1.65;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- 4. FOOTER: SİTEDEKİ MAT SİYAH (#121212) KURUMSAL FOOTER -->
                    <tr>
                        <td class="mobile-padding" style="background-color: #121212; padding: 40px 36px 28px; color: #9ca3af; font-size: 12px; line-height: 1.6;">
                            
                            <!-- 2 Sütunlu Grid: Sol (Logo, Açıklama, Sosyal İkonlar, ETBİS), Sağ (Hızlı Menü, Kurumsal) -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <!-- Sol Kolon: Logo & Açıklama & Sosyal Medya İkonları -->
                                    <td class="footer-col" width="55%" valign="top" style="padding-right: 20px;">
                                        <!-- Footer Logo (Beyaz) -->
                                        <a href="https://patenliayakkabilar.com" target="_blank" style="text-decoration: none; display: inline-block; margin-bottom: 14px;">
                                            <span style="font-size: 20px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px; text-transform: uppercase;">PATENLİ</span><span style="font-size: 20px; font-weight: 300; color: #ffffff; letter-spacing: -0.5px; text-transform: uppercase;">AYAKKABILAR<sup style="font-size: 10px; font-weight: 400; vertical-align: super;">®</sup></span>
                                        </a>
                                        
                                        <!-- Site Açıklaması -->
                                        <p style="margin: 0 0 18px; color: #9ca3af; font-size: 12px; line-height: 1.6; max-width: 280px;">
                                            Çocukların eğlenirken güvende olması için ürün seçimini, kargo sürecini ve satış sonrası desteği kolaylaştırıyoruz.
                                        </p>

                                        <!-- Sosyal Medya İkonları (Sitedeki gibi tek tip kütüphane ikonları) -->
                                        <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 18px;">
                                            <tr>
                                                <!-- WhatsApp -->
                                                <td style="padding-right: 12px;">
                                                    <a href="https://wa.me/905441828800" target="_blank" style="display: block; opacity: 0.85;">
                                                        <img src="https://img.icons8.com/material-rounded/48/9ca3af/whatsapp.png" alt="WhatsApp" width="22" height="22" style="display: block; width: 22px; height: 22px;">
                                                    </a>
                                                </td>
                                                <!-- Instagram -->
                                                <td style="padding-right: 12px;">
                                                    <a href="https://www.instagram.com/patenliayakkabilar" target="_blank" style="display: block; opacity: 0.85;">
                                                        <img src="https://img.icons8.com/material-rounded/48/9ca3af/instagram-new.png" alt="Instagram" width="22" height="22" style="display: block; width: 22px; height: 22px;">
                                                    </a>
                                                </td>
                                                <!-- Facebook -->
                                                <td style="padding-right: 12px;">
                                                    <a href="https://patenliayakkabilar.com" target="_blank" style="display: block; opacity: 0.85;">
                                                        <img src="https://img.icons8.com/material-rounded/48/9ca3af/facebook-f.png" alt="Facebook" width="22" height="22" style="display: block; width: 22px; height: 22px;">
                                                    </a>
                                                </td>
                                                <!-- X / Twitter -->
                                                <td style="padding-right: 12px;">
                                                    <a href="https://patenliayakkabilar.com" target="_blank" style="display: block; opacity: 0.85;">
                                                        <img src="https://img.icons8.com/material-rounded/48/9ca3af/twitterx.png" alt="X" width="22" height="22" style="display: block; width: 22px; height: 22px;">
                                                    </a>
                                                </td>
                                                <!-- YouTube -->
                                                <td style="padding-right: 12px;">
                                                    <a href="https://patenliayakkabilar.com" target="_blank" style="display: block; opacity: 0.85;">
                                                        <img src="https://img.icons8.com/material-rounded/48/9ca3af/youtube-play.png" alt="YouTube" width="22" height="22" style="display: block; width: 22px; height: 22px;">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- ETBİS Güven Rozeti -->
                                        <div style="display: inline-block; background-color: rgba(255,255,255,0.05); border: 1px solid #27272a; border-radius: 8px; padding: 6px 14px;">
                                            <span style="color: #10b981; font-weight: 900; font-size: 11px; letter-spacing: 1px;">ETBİS</span>
                                            <span style="color: #6ee7b7; font-size: 9px; background: rgba(16,185,129,0.15); padding: 2px 5px; border-radius: 4px; margin-left: 4px; font-weight: 600;">Kayıtlı</span>
                                            <span style="color: #9ca3af; font-size: 10px; margin-left: 6px;">Güvenli E-Ticaret</span>
                                        </div>
                                    </td>

                                    <!-- Sağ Kolon: Hızlı Menü & Kurumsal Linkler -->
                                    <td class="footer-col" width="45%" valign="top">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="50%" valign="top" style="padding-right: 10px;">
                                                    <p style="margin: 0 0 12px; font-weight: 700; color: #ffffff; font-size: 13px;">Hızlı Menü</p>
                                                    <p style="margin: 0 0 8px;"><a href="https://patenliayakkabilar.com" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Ana Sayfa</a></p>
                                                    <p style="margin: 0 0 8px;"><a href="https://patenliayakkabilar.com/katalog" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Tüm Ürünler</a></p>
                                                    <p style="margin: 0 0 8px;"><a href="https://patenliayakkabilar.com/beden-rehberi" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Beden Rehberi</a></p>
                                                    <p style="margin: 0 0 8px;"><a href="https://patenliayakkabilar.com/iletisim" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">İletişim</a></p>
                                                </td>
                                                <td width="50%" valign="top">
                                                    <p style="margin: 0 0 12px; font-weight: 700; color: #ffffff; font-size: 13px;">Kurumsal</p>
                                                    <p style="margin: 0 0 8px;"><a href="https://patenliayakkabilar.com/hakkimizda" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Hakkımızda</a></p>
                                                    <p style="margin: 0 0 8px;"><a href="https://patenliayakkabilar.com/sikca-sorulan-sorular" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Sıkça Sorulanlar</a></p>
                                                    <p style="margin: 0 0 8px;"><a href="https://patenliayakkabilar.com/iade-ve-degisim" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">İade & Değişim</a></p>
                                                    <p style="margin: 0 0 8px;"><a href="https://patenliayakkabilar.com/gizlilik-politikasi" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Gizlilik</a></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Ödeme Yöntemleri Barı (PayTR, TROY, VISA, Master, Havale) -->
                            <div style="border-top: 1px solid #1f2937; margin-top: 28px; padding-top: 22px; text-align: center;">
                                <table align="center" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="padding: 0 4px;">
                                            <div style="background-color: #ffffff; border-radius: 4px; padding: 4px 8px; font-weight: 900; font-size: 11px; color: #0b2545;">
                                                Pay<span style="color: #00a8e1;">TR</span>
                                            </div>
                                        </td>
                                        <td style="padding: 0 4px;">
                                            <div style="background-color: #ffffff; border-radius: 4px; padding: 4px 8px; font-weight: 900; font-size: 11px; color: #00a8e1;">
                                                TROY
                                            </div>
                                        </td>
                                        <td style="padding: 0 4px;">
                                            <div style="background-color: #ffffff; border-radius: 4px; padding: 4px 8px; font-weight: 900; font-size: 11px; color: #1a1f71;">
                                                VISA
                                            </div>
                                        </td>
                                        <td style="padding: 0 4px;">
                                            <div style="background-color: #ffffff; border-radius: 4px; padding: 4px 8px; font-weight: 900; font-size: 11px; color: #eb001b;">
                                                Mastercard
                                            </div>
                                        </td>
                                        <td style="padding: 0 4px;">
                                            <div style="background-color: #ffffff; border-radius: 4px; padding: 4px 8px; font-weight: 700; font-size: 10px; color: #475569;">
                                                Havale / EFT
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Telif Hakkı (Sitedeki copyright) -->
                            <p style="margin: 20px 0 0; text-align: center; color: #64748b; font-size: 11px;">
                                &copy; {{ date('Y') }} Patenli Ayakkabılar. Tüm hakları saklıdır.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

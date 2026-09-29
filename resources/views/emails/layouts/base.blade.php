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
            .mobile-padding { padding-left: 18px !important; padding-right: 18px !important; }
            .stack-column { display: block !important; width: 100% !important; text-align: left !important; margin-bottom: 20px !important; }
            .mobile-hide { display: none !important; }
            .footer-grid-col { display: block !important; width: 100% !important; margin-bottom: 24px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #18181b;">

    <!-- 1. EN ÜST MARQUEE DUYURU BANDI (Sitedeki bg-[#6b6b6b] bandının birebiri) -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #6b6b6b;">
        <tr>
            <td align="center" style="padding: 10px 16px;">
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
                    
                    <!-- 2. HEADER: BEYAZ ZEMİN, SİTENİN BİREBİR LOGOSU VE MENÜSÜ -->
                    <tr>
                        <td style="background-color: #ffffff; padding: 24px 32px; border-bottom: 1px solid #e4e4e7;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <!-- Logo (PATENLİ - kalın siyah, AYAKKABILAR® - ince siyah) -->
                                    <td align="left" valign="middle">
                                        <a href="https://patenliayakkabilar.com" target="_blank" style="text-decoration: none; display: inline-block;">
                                            <span style="font-size: 24px; font-weight: 900; color: #111827; letter-spacing: -1px; text-transform: uppercase; font-family: 'Arial Black', -apple-system, sans-serif;">PATENLİ</span><span style="font-size: 24px; font-weight: 300; color: #111827; letter-spacing: -1px; text-transform: uppercase; font-family: -apple-system, sans-serif;">AYAKKABILAR<sup style="font-size: 11px; font-weight: 400; vertical-align: super;">®</sup></span>
                                        </a>
                                    </td>
                                    <!-- Header Menü Linkleri (Desktop) -->
                                    <td class="mobile-hide" align="right" valign="middle">
                                        <a href="https://patenliayakkabilar.com" target="_blank" style="color: #111827; text-decoration: none; font-size: 11px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; margin-left: 14px;">Ana Sayfa</a>
                                        <a href="https://patenliayakkabilar.com/katalog" target="_blank" style="color: #111827; text-decoration: none; font-size: 11px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; margin-left: 14px;">Katalog</a>
                                        <a href="https://patenliayakkabilar.com/iletisim" target="_blank" style="color: #111827; text-decoration: none; font-size: 11px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; margin-left: 14px;">İletişim</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 3. İÇERİK ALANI -->
                    <tr>
                        <td class="mobile-padding" style="padding: 36px 36px 32px; background-color: #ffffff; color: #18181b; font-size: 15px; line-height: 1.65;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- 4. "HER YERDE KAY" MARQUEE BANDI (Sitenin Footer Üstündeki İmzası) -->
                    <tr>
                        <td style="background-color: #ffffff; border-top: 1px solid #e4e4e7; border-bottom: 1px solid #e4e4e7; padding: 16px 20px; text-align: center;">
                            <p style="margin: 0; font-size: 15px; font-weight: 900; color: #000000; letter-spacing: 4px; text-transform: uppercase; font-family: 'Arial Black', -apple-system, sans-serif;">
                                HER YERDE KAY &nbsp;&nbsp;&bull;&nbsp;&nbsp; HER YERDE KAY &nbsp;&nbsp;&bull;&nbsp;&nbsp; HER YERDE KAY
                            </p>
                        </td>
                    </tr>

                    <!-- 5. FOOTER: SİTEDEKİ MAT SİYAH (#121212) KURUMSAL FOOTER'IN BİREBİRİ -->
                    <tr>
                        <td class="mobile-padding" style="background-color: #121212; padding: 36px 36px 24px; color: #9ca3af; font-size: 12px; line-height: 1.6;">
                            
                            <!-- 2 Sütunlu Grid: Sol (Logo, Açıklama, Sosyal), Sağ (Hızlı Menü, Kurumsal) -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <!-- Sol Kolon: Logo & Açıklama & Sosyal Medya -->
                                    <td class="footer-grid-col" width="55%" valign="top" style="padding-right: 20px;">
                                        <!-- Footer Logo (Beyaz) -->
                                        <a href="https://patenliayakkabilar.com" target="_blank" style="text-decoration: none; display: inline-block; margin-bottom: 12px;">
                                            <span style="font-size: 20px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px; text-transform: uppercase;">PATENLİ</span><span style="font-size: 20px; font-weight: 300; color: #ffffff; letter-spacing: -0.5px; text-transform: uppercase;">AYAKKABILAR<sup style="font-size: 10px; font-weight: 400; vertical-align: super;">®</sup></span>
                                        </a>
                                        
                                        <!-- Site Açıklaması -->
                                        <p style="margin: 0 0 16px; color: #9ca3af; font-size: 12px; line-height: 1.6; max-width: 280px;">
                                            Çocukların eğlenirken güvende olması için ürün seçimini, kargo sürecini ve satış sonrası desteği kolaylaştırıyoruz.
                                        </p>

                                        <!-- Sosyal Medya İkonları -->
                                        <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
                                            <tr>
                                                <td style="padding-right: 10px;">
                                                    <a href="https://wa.me/905441828800" target="_blank" style="display: inline-block; background-color: #27272a; color: #22c55e; text-decoration: none; width: 32px; height: 32px; line-height: 32px; text-align: center; border-radius: 50%; font-size: 14px; font-weight: bold;">💬</a>
                                                </td>
                                                <td style="padding-right: 10px;">
                                                    <a href="https://www.instagram.com/patenliayakkabilar" target="_blank" style="display: inline-block; background-color: #27272a; color: #ffffff; text-decoration: none; width: 32px; height: 32px; line-height: 32px; text-align: center; border-radius: 50%; font-size: 14px;">📷</a>
                                                </td>
                                                <td style="padding-right: 10px;">
                                                    <a href="https://patenliayakkabilar.com" target="_blank" style="display: inline-block; background-color: #27272a; color: #ffffff; text-decoration: none; width: 32px; height: 32px; line-height: 32px; text-align: center; border-radius: 50%; font-size: 14px;">🌐</a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- ETBİS Güven Rozeti -->
                                        <div style="display: inline-block; background-color: rgba(255,255,255,0.05); border: 1px solid #27272a; border-radius: 8px; padding: 6px 12px;">
                                            <span style="color: #10b981; font-weight: 900; font-size: 11px; letter-spacing: 1px;">ETBİS</span>
                                            <span style="color: #6ee7b7; font-size: 9px; background: rgba(16,185,129,0.15); padding: 2px 4px; border-radius: 4px; margin-left: 4px;">Kayıtlı</span>
                                            <span style="color: #9ca3af; font-size: 10px; margin-left: 6px;">Güvenli E-Ticaret</span>
                                        </div>
                                    </td>

                                    <!-- Sağ Kolon: Hızlı Menü & Kurumsal Linkler -->
                                    <td class="footer-grid-col" width="45%" valign="top">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="50%" valign="top" style="padding-right: 10px;">
                                                    <p style="margin: 0 0 10px; font-weight: 700; color: #ffffff; font-size: 13px;">Hızlı Menü</p>
                                                    <p style="margin: 0 0 6px;"><a href="https://patenliayakkabilar.com" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Ana Sayfa</a></p>
                                                    <p style="margin: 0 0 6px;"><a href="https://patenliayakkabilar.com/katalog" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Tüm Ürünler</a></p>
                                                    <p style="margin: 0 0 6px;"><a href="https://patenliayakkabilar.com/beden-rehberi" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Beden Rehberi</a></p>
                                                    <p style="margin: 0 0 6px;"><a href="https://patenliayakkabilar.com/iletisim" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">İletişim</a></p>
                                                </td>
                                                <td width="50%" valign="top">
                                                    <p style="margin: 0 0 10px; font-weight: 700; color: #ffffff; font-size: 13px;">Kurumsal</p>
                                                    <p style="margin: 0 0 6px;"><a href="https://patenliayakkabilar.com/hakkimizda" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Hakkımızda</a></p>
                                                    <p style="margin: 0 0 6px;"><a href="https://patenliayakkabilar.com/sikca-sorulan-sorular" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Sıkça Sorulanlar</a></p>
                                                    <p style="margin: 0 0 6px;"><a href="https://patenliayakkabilar.com/iade-ve-degisim" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">İade & Değişim</a></p>
                                                    <p style="margin: 0 0 6px;"><a href="https://patenliayakkabilar.com/gizlilik-politikasi" target="_blank" style="color: #9ca3af; text-decoration: none; font-size: 11px;">Gizlilik</a></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Ödeme Yöntemleri Barı (PayTR, TROY, VISA, Master, Havale) -->
                            <div style="border-top: 1px solid #1f2937; margin-top: 24px; padding-top: 20px; text-align: center;">
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

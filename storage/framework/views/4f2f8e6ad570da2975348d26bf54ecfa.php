<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Patenli Ayakkabılar'); ?></title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6; color: #000000;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f3f4f6; width: 100%; margin: 0; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #ff4e00; padding: 20px; text-align: center;">
                            <img src="https://patenliayakkabilar.com/favicon.png" alt="Patenli Ayakkabılar Logo" style="max-height: 50px; vertical-align: middle; margin-right: 10px;">
                            <span style="color: #ffffff; font-size: 24px; font-weight: bold; vertical-align: middle;">Patenli Ayakkabılar</span>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px; background-color: #ffffff;">
                            <?php echo $__env->yieldContent('content'); ?>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0;">İletişim: <a href="mailto:destek@patenliayakkabilar.com" style="color: #ff4e00; text-decoration: none;">destek@patenliayakkabilar.com</a> | 0850 XXX XX XX</p>
                            <p style="margin: 0 0 10px 0;">Adres: İstanbul, Türkiye</p>
                            <p style="margin: 0 0 10px 0;">
                                <a href="#" style="color: #9ca3af; text-decoration: none; margin: 0 5px;">Facebook</a> |
                                <a href="#" style="color: #9ca3af; text-decoration: none; margin: 0 5px;">Instagram</a> |
                                <a href="#" style="color: #9ca3af; text-decoration: none; margin: 0 5px;">Twitter</a>
                            </p>
                            <p style="margin: 0 0 10px 0;">&copy; 2026 Patenli Ayakkabılar. Tüm hakları saklıdır.</p>
                            <p style="margin: 0;"><a href="#" style="color: #9ca3af; text-decoration: underline;">Abonelikten Çık</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\emails\layouts\base.blade.php ENDPATH**/ ?>
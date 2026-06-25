<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/ayarlarui.php';

yonetim_require_login();
$pdo = yonetim_settings_require_pdo();
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $patch = [
            'store_phone' => trim((string) ($_POST['store_phone'] ?? '')),
            'store_whatsapp' => trim((string) ($_POST['store_whatsapp'] ?? '')),
            'store_email' => trim((string) ($_POST['store_email'] ?? '')),
            'store_address' => (string) ($_POST['store_address'] ?? ''),
        ];
        yonetim_settings_save($pdo, $patch);
        yonetim_flash('İletişim bilgileri kaydedildi.');
        yonetim_redirect('settings_contact');
        exit;
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$s = ded_shop_settings_get($pdo);

yonetim_layout_start('İletişim');
yonetim_settings_page_head('settings_contact', 'İletişim');
if ($err !== '') {
    yonetim_alert('danger', $err);
}
yonetim_settings_form_open();
?>
  <label>Mağaza telefonu</label>
  <input type="tel" name="store_phone" value="<?= ded_h((string) ($s['store_phone'] ?? '')) ?>" placeholder="0 (5xx) xxx xx xx">
  <label>WhatsApp</label>
  <input type="tel" name="store_whatsapp" value="<?= ded_h((string) ($s['store_whatsapp'] ?? '')) ?>" placeholder="905xxxxxxxxx">
  <label>İletişim e-postası</label>
  <input type="email" name="store_email" value="<?= ded_h((string) ($s['store_email'] ?? '')) ?>" placeholder="destek@magaza.com">
  <label>Adres / not</label>
  <textarea name="store_address" rows="3"><?= ded_h((string) ($s['store_address'] ?? '')) ?></textarea>
<?php yonetim_settings_form_close(); ?>
<?php yonetim_layout_end(); ?>

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
            'notify_order_email' => !empty($_POST['notify_order_email']),
            'notify_order_sms' => !empty($_POST['notify_order_sms']),
            'notify_ship_email' => !empty($_POST['notify_ship_email']),
            'notify_ship_sms' => !empty($_POST['notify_ship_sms']),
        ];
        yonetim_settings_save($pdo, $patch);
        yonetim_flash('Bildirim ayarları kaydedildi.');
        yonetim_redirect('settings_notifications');
        exit;
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$s = ded_shop_settings_get($pdo);

yonetim_layout_start('Bildirimler');
yonetim_settings_page_head('settings_notifications', 'Bildirimler');
if ($err !== '') {
    yonetim_alert('danger', $err);
}
yonetim_settings_form_open();
?>
  <div class="yun-check-grid">
    <label class="yun-check"><input type="checkbox" name="notify_order_email" value="1"<?= !empty($s['notify_order_email']) ? ' checked' : '' ?>> Sipariş oluşunca e-posta</label>
    <label class="yun-check"><input type="checkbox" name="notify_order_sms" value="1"<?= !empty($s['notify_order_sms']) ? ' checked' : '' ?>> Sipariş oluşunca SMS</label>
    <label class="yun-check"><input type="checkbox" name="notify_ship_email" value="1"<?= !empty($s['notify_ship_email']) ? ' checked' : '' ?>> Kargo takip no eklenince e-posta</label>
    <label class="yun-check"><input type="checkbox" name="notify_ship_sms" value="1"<?= !empty($s['notify_ship_sms']) ? ' checked' : '' ?>> Kargo takip no eklenince SMS</label>
  </div>
<?php yonetim_settings_form_close(); ?>
<?php yonetim_layout_end(); ?>

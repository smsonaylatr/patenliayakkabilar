<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/ayarlarui.php';
require_once dirname(__DIR__) . '/lib/smsnetgsm.php';

yonetim_require_login();
$pdo = yonetim_settings_require_pdo();
$err = '';
$testMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'save');
    try {
        $prev = ded_shop_settings_get($pdo);
        if ($action === 'test_sms') {
            $num = trim((string) ($_POST['test_phone'] ?? ''));
            if ($num === '') {
                $err = 'Test için telefon numarası girin.';
            } else {
                $msg = (string) ($_POST['test_message'] ?? 'Laykids SMS test');
                $res = ded_toplus_sms_send($prev, $num, $msg);
                ded_notification_log_insert(
                    $pdo,
                    'sms',
                    $num,
                    'test',
                    $msg,
                    $res['ok'] ? 'sent' : 'failed',
                    $res
                );
                $testMsg = $res['ok']
                    ? 'Test SMS gönderildi.'
                    : ('Gönderilemedi: ' . (string) ($res['detail'] ?? 'API hatası'));
            }
        } else {
            $patch = [
                'toplus_api_base' => trim((string) ($_POST['toplus_api_base'] ?? '')),
                'toplus_username' => (string) ($_POST['toplus_username'] ?? ''),
                'toplus_password' => (string) ($_POST['toplus_password'] ?? ''),
                'toplus_caption' => (string) ($_POST['toplus_caption'] ?? ''),
                'toplus_encoding' => (string) ($_POST['toplus_encoding'] ?? 'tr'),
            ];
            $patch = yonetim_settings_preserve_secrets($patch, $prev, ['toplus_password']);
            yonetim_settings_save($pdo, $patch);
            yonetim_flash('SMS ayarları kaydedildi.');
            yonetim_redirect('settings_sms');
            exit;
        }
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$s = ded_shop_settings_get($pdo);

yonetim_layout_start('SMS');
yonetim_settings_page_head('settings_sms', 'SMS');
if ($err !== '') {
    yonetim_alert('danger', $err);
}
if ($testMsg !== '') {
    yonetim_alert('success', $testMsg);
}
yonetim_settings_form_open();
?>
  <input type="hidden" name="action" value="save">
  <label>API base URL</label>
  <input type="url" name="toplus_api_base" value="<?= ded_h((string) ($s['toplus_api_base'] ?? 'https://panel.toplusms.tc/api/v1/')) ?>">
  <label>Kullanıcı adı</label>
  <input type="text" name="toplus_username" value="<?= ded_h((string) ($s['toplus_username'] ?? '')) ?>" autocomplete="off">
  <label>Şifre / MD5</label>
  <input type="password" name="toplus_password" value="" autocomplete="new-password">
  <label>Başlık (caption)</label>
  <input type="text" name="toplus_caption" value="<?= ded_h((string) ($s['toplus_caption'] ?? '')) ?>">
  <label>Encoding</label>
  <input type="text" name="toplus_encoding" value="<?= ded_h((string) ($s['toplus_encoding'] ?? 'tr')) ?>">
<?php yonetim_settings_form_close(); ?>

<?php yonetim_settings_test_form_open('Test SMS'); ?>
  <input type="hidden" name="action" value="test_sms">
  <label>Test telefon (5xxxxxxxxx)</label>
  <input type="tel" name="test_phone" placeholder="5XXXXXXXXX" required>
  <label>Mesaj</label>
  <input type="text" name="test_message" value="Laykids SMS test">
<?php yonetim_settings_test_form_close('Test SMS gönder'); ?>
<?php yonetim_layout_end(); ?>

<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/ayarlarui.php';
require_once dirname(__DIR__) . '/lib/posta.php';

yonetim_require_login();
$pdo = yonetim_settings_require_pdo();
$err = '';
$testMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'save');
    try {
        $prev = ded_shop_settings_get($pdo);
        if ($action === 'test_email') {
            $to = trim((string) ($_POST['test_to'] ?? ''));
            if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $err = 'Geçerli bir test e-posta adresi girin.';
            } else {
                $settings = $prev;
                $ok = ded_mail_send($settings, $to, 'Laykids test e-postası', '<p>SMTP bağlantı testi başarılı görünüyor.</p>', 'SMTP test');
                ded_notification_log_insert($pdo, 'email', $to, 'Laykids test', 'test', $ok ? 'sent' : 'failed', null);
                $testMsg = $ok ? 'Test e-postası gönderildi.' : 'Gönderilemedi — SMTP ayarlarını kontrol edin.';
            }
        } else {
            $patch = [
                'smtp_host' => (string) ($_POST['smtp_host'] ?? ''),
                'smtp_port' => (int) ($_POST['smtp_port'] ?? 587),
                'smtp_encryption' => (string) ($_POST['smtp_encryption'] ?? 'tls'),
                'smtp_user' => (string) ($_POST['smtp_user'] ?? ''),
                'smtp_pass' => (string) ($_POST['smtp_pass'] ?? ''),
                'mail_from' => trim((string) ($_POST['mail_from'] ?? '')),
                'mail_from_name' => (string) ($_POST['mail_from_name'] ?? ''),
            ];
            $patch = yonetim_settings_preserve_secrets($patch, $prev, ['smtp_pass']);
            yonetim_settings_save($pdo, $patch);
            yonetim_flash('SMTP ayarları kaydedildi.');
            yonetim_redirect('settings_smtp');
            exit;
        }
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$s = ded_shop_settings_get($pdo);

yonetim_layout_start('SMTP');
yonetim_settings_page_head('settings_smtp', 'E-posta');
if ($err !== '') {
    yonetim_alert('danger', $err);
}
if ($testMsg !== '') {
    yonetim_alert('success', $testMsg);
}
yonetim_settings_form_open();
?>
  <input type="hidden" name="action" value="save">
  <label>SMTP sunucu</label>
  <input type="text" name="smtp_host" value="<?= ded_h((string) ($s['smtp_host'] ?? '')) ?>" placeholder="smtp.ornek.com">
  <div class="yun-row2">
    <div>
      <label>Port</label>
      <input type="number" name="smtp_port" value="<?= (int) ($s['smtp_port'] ?? 587) ?>">
    </div>
    <div>
      <label>Şifreleme</label>
      <select name="smtp_encryption">
        <?php foreach (['tls', 'ssl', 'none'] as $enc) {
            $sel = ($s['smtp_encryption'] ?? 'tls') === $enc ? ' selected' : '';
            echo '<option value="' . ded_h($enc) . '"' . $sel . '>' . ded_h($enc) . '</option>';
        } ?>
      </select>
    </div>
  </div>
  <label>SMTP kullanıcı</label>
  <input type="text" name="smtp_user" value="<?= ded_h((string) ($s['smtp_user'] ?? '')) ?>" autocomplete="off">
  <label>SMTP şifre</label>
  <input type="password" name="smtp_pass" value="" autocomplete="new-password">
  <label>Gönderen e-posta</label>
  <input type="email" name="mail_from" value="<?= ded_h((string) ($s['mail_from'] ?? '')) ?>">
  <label>Gönderen adı</label>
  <input type="text" name="mail_from_name" value="<?= ded_h((string) ($s['mail_from_name'] ?? '')) ?>">
<?php yonetim_settings_form_close(); ?>

<?php yonetim_settings_test_form_open('Test e-postası'); ?>
  <input type="hidden" name="action" value="test_email">
  <label>Test alıcı e-posta</label>
  <input type="email" name="test_to" placeholder="ornek@mail.com" required>
<?php yonetim_settings_test_form_close('Test e-postası gönder'); ?>
<?php yonetim_layout_end(); ?>

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
            'checkout_mode' => (string) ($_POST['checkout_mode'] ?? 'manual_transfer'),
            'checkout_gateways' => trim((string) ($_POST['checkout_gateways'] ?? '')),
            'checkout_intro' => (string) ($_POST['checkout_intro'] ?? ''),
            'bank_instructions' => (string) ($_POST['bank_instructions'] ?? ''),
            'cod_instructions' => (string) ($_POST['cod_instructions'] ?? ''),
            'payment_demo_notice' => (string) ($_POST['payment_demo_notice'] ?? ''),
            'shopify_checkout_url' => trim((string) ($_POST['shopify_checkout_url'] ?? '')),
        ];
        yonetim_settings_save($pdo, $patch);
        yonetim_flash('Ödeme ayarları kaydedildi.');
        yonetim_redirect('settings_checkout');
        exit;
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$s = ded_shop_settings_get($pdo);
    $modes = [
        'manual_transfer' => 'Havale / EFT',
        'cod' => 'Kapıda ödeme',
        'paytr' => 'PayTR',
        'shopify_redirect' => 'Harici URL',
        'demo_completed' => 'Demo (otomatik ödendi)',
        'disabled' => 'Kapalı',
    ];

yonetim_layout_start('Ödeme ayarları');
yonetim_settings_page_head('settings_checkout', 'Ödeme');
if ($err !== '') {
    yonetim_alert('danger', $err);
}
yonetim_settings_form_open();
?>
  <label>Varsayılan mod</label>
  <select name="checkout_mode">
    <?php foreach ($modes as $val => $label) {
        $sel = ($s['checkout_mode'] ?? '') === $val ? ' selected' : '';
        echo '<option value="' . ded_h($val) . '"' . $sel . '>' . ded_h($label) . '</option>';
    } ?>
  </select>
  <label>Üst metin</label>
  <textarea name="checkout_intro" rows="2"><?= ded_h((string) ($s['checkout_intro'] ?? '')) ?></textarea>
  <label>Havale talimatı</label>
  <textarea name="bank_instructions" rows="4"><?= ded_h((string) ($s['bank_instructions'] ?? '')) ?></textarea>
  <label>Kapıda ödeme metni</label>
  <textarea name="cod_instructions" rows="3"><?= ded_h((string) ($s['cod_instructions'] ?? '')) ?></textarea>
  <label>Demo uyarısı</label>
  <textarea name="payment_demo_notice" rows="2"><?= ded_h((string) ($s['payment_demo_notice'] ?? '')) ?></textarea>
  <label>Çoklu ödeme (virgülle)</label>
  <input type="text" name="checkout_gateways" value="<?= ded_h((string) ($s['checkout_gateways'] ?? '')) ?>" placeholder="paytr,cod,manual_transfer">
  <label>Harici ödeme URL</label>
  <input type="url" name="shopify_checkout_url" value="<?= ded_h((string) ($s['shopify_checkout_url'] ?? '')) ?>" placeholder="https://magazam.myshopify.com/...">
<?php yonetim_settings_form_close(); ?>
<?php yonetim_layout_end(); ?>

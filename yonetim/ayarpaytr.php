<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/ayarlarui.php';

yonetim_require_login();
$pdo = yonetim_settings_require_pdo();
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $prev = ded_shop_settings_get($pdo);
        $patch = [
            'paytr_merchant_id' => trim((string) ($_POST['paytr_merchant_id'] ?? '')),
            'paytr_merchant_key' => trim((string) ($_POST['paytr_merchant_key'] ?? '')),
            'paytr_merchant_salt' => trim((string) ($_POST['paytr_merchant_salt'] ?? '')),
            'paytr_test_mode' => !empty($_POST['paytr_test_mode']),
            'paytr_debug_on' => !empty($_POST['paytr_debug_on']),
            'paytr_no_installment' => !empty($_POST['paytr_no_installment']),
            'paytr_max_installment' => (int) ($_POST['paytr_max_installment'] ?? 0),
            'paytr_checkout_note' => (string) ($_POST['paytr_checkout_note'] ?? ''),
        ];
        $patch = yonetim_settings_preserve_secrets($patch, $prev, ['paytr_merchant_key', 'paytr_merchant_salt']);
        yonetim_settings_save($pdo, $patch);
        yonetim_flash('PayTR ayarları kaydedildi.');
        yonetim_redirect('settings_paytr');
        exit;
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$s = ded_shop_settings_get($pdo);
$callback = yonetim_ded_public_url() . '/paytrag.php';

yonetim_layout_start('PayTR');
yonetim_settings_page_head('settings_paytr', 'PayTR');
if ($err !== '') {
    yonetim_alert('danger', $err);
}
?>
<div class="card border-0 shadow-sm mb-3">
  <div class="card-body py-3">
    <p class="text-muted mb-1 fs-13">PayTR callback</p>
    <code class="fs-12"><?= ded_h($callback) ?></code>
  </div>
</div>
<?php yonetim_settings_form_open(); ?>
  <label>Mağaza numarası (merchant_id)</label>
  <input type="text" name="paytr_merchant_id" value="<?= ded_h((string) ($s['paytr_merchant_id'] ?? '')) ?>" autocomplete="off">
  <label>Merchant key</label>
  <input type="password" name="paytr_merchant_key" value="" autocomplete="new-password" placeholder="Değiştirmek için yazın">
  <label>Merchant salt</label>
  <input type="password" name="paytr_merchant_salt" value="" autocomplete="new-password" placeholder="••••">
  <div class="yun-check-grid">
    <label class="yun-check"><input type="checkbox" name="paytr_test_mode" value="1"<?= !empty($s['paytr_test_mode']) ? ' checked' : '' ?>> Test modu</label>
    <label class="yun-check"><input type="checkbox" name="paytr_debug_on" value="1"<?= !empty($s['paytr_debug_on']) ? ' checked' : '' ?>> API hata detayı (iframe)</label>
    <label class="yun-check"><input type="checkbox" name="paytr_no_installment" value="1"<?= !empty($s['paytr_no_installment']) ? ' checked' : '' ?>> Taksit gösterme</label>
  </div>
  <label>Maksimum taksit (0 = tümü)</label>
  <input type="number" name="paytr_max_installment" min="0" max="12" value="<?= (int) ($s['paytr_max_installment'] ?? 0) ?>">
  <label>Ödeme sayfası açıklaması</label>
  <textarea name="paytr_checkout_note" rows="2"><?= ded_h((string) ($s['paytr_checkout_note'] ?? '')) ?></textarea>
<?php yonetim_settings_form_close(); ?>
<?php yonetim_layout_end(); ?>

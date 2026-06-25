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
            'default_shipping_fee' => (string) ($_POST['default_shipping_fee'] ?? '0'),
            'shipping_note' => (string) ($_POST['shipping_note'] ?? ''),
            'currency' => trim((string) ($_POST['currency'] ?? 'TRY')) ?: 'TRY',
        ];
        yonetim_settings_save($pdo, $patch);
        yonetim_flash('Kargo ayarları kaydedildi.');
        yonetim_redirect('settings_shipping');
        exit;
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$s = ded_shop_settings_get($pdo);
$feeDisplay = ded_format_price_input_tr((float) ($s['default_shipping_fee'] ?? 0));

yonetim_layout_start('Kargo');
yonetim_settings_page_head('settings_shipping', 'Kargo');
if ($err !== '') {
    yonetim_alert('danger', $err);
}
yonetim_settings_form_open();
?>
  <label>Sabit kargo ücreti</label>
  <input type="text" name="default_shipping_fee" class="yun-price-input" inputmode="decimal" value="<?= ded_h($feeDisplay) ?>" placeholder="0">
  <label>Kargo notu</label>
  <textarea name="shipping_note" rows="2"><?= ded_h((string) ($s['shipping_note'] ?? '')) ?></textarea>
  <label>Para birimi kodu</label>
  <input type="text" name="currency" value="<?= ded_h((string) ($s['currency'] ?? 'TRY')) ?>" maxlength="8">
<?php yonetim_settings_form_close(); ?>
<script src="<?= ded_h(yonetim_panel_asset('fiyatgiris.js?v=2')) ?>"></script>
<?php yonetim_layout_end(); ?>

<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/ayarlarui.php';

yonetim_require_login();
$pdo = yonetim_settings_require_pdo();
$s = ded_shop_settings_get($pdo);

$paytrOk = ded_paytr_configured($s);
$smtpOk = trim((string) ($s['smtp_host'] ?? '')) !== '' && trim((string) ($s['mail_from'] ?? '')) !== '';
$smsOk = trim((string) ($s['toplus_username'] ?? '')) !== '';

$icons = [
    'settings_checkout' => 'iconoir-credit-card',
    'settings_paytr' => 'iconoir-wallet',
    'settings_shipping' => 'iconoir-delivery-truck',
    'settings_smtp' => 'iconoir-mail',
    'settings_sms' => 'iconoir-phone',
    'settings_notifications' => 'iconoir-bell-notification',
    'settings_contact' => 'iconoir-phone-plus',
];

yonetim_layout_start('Ayarlar');
yonetim_settings_page_head('settings', 'Ayarlar');
?>
<div class="row g-3">
  <?php foreach (yonetim_settings_nav_items() as $item) {
      if ($item['id'] === 'settings') {
          continue;
      }
      $ok = match ($item['id']) {
          'settings_paytr' => $paytrOk,
          'settings_smtp' => $smtpOk,
          'settings_sms' => $smsOk,
          default => null,
      };
      $icon = $icons[$item['id']] ?? 'iconoir-settings';
      ?>
  <div class="col-md-6 col-xl-4">
    <a href="<?= ded_h(yonetim_resolve_href((string) $item['href'])) ?>" class="card text-decoration-none text-body h-100 yun-settings-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div class="thumb-md bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
            <i class="<?= ded_h($icon) ?> fs-4"></i>
          </div>
          <?php if ($ok !== null) { ?>
          <span class="badge <?= $ok ? 'bg-success-subtle text-success' : 'bg-light text-muted' ?>"><?= $ok ? 'OK' : '—' ?></span>
          <?php } ?>
        </div>
        <h5 class="mt-3 mb-0 fw-semibold"><?= ded_h($item['label']) ?></h5>
      </div>
    </a>
  </div>
  <?php } ?>
</div>
<div class="card border-0 shadow-sm mt-3">
  <div class="card-body py-3">
    <p class="text-muted mb-1 fs-13">PayTR callback</p>
    <code class="fs-12"><?= ded_h(yonetim_ded_public_url() . '/paytrag.php') ?></code>
  </div>
</div>
<?php yonetim_layout_end(); ?>

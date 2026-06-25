<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/ayarlarui.php';

yonetim_require_login();

$base = yonetim_ded_public_url();
$panel = yonetim_ded_public_url() . '/yonetim/';
$phpOk = version_compare(PHP_VERSION, '8.1.0', '>=');
$pdoOk = ded_pdo() !== null;
$dbOk = ded_db_ready();
$shopOk = $pdoOk && ded_shop_ready(ded_pdo());
$rewriteOk = function_exists('apache_get_modules')
    ? in_array('mod_rewrite', apache_get_modules(), true)
    : null;
$writable = is_writable(DED_ROOT . '/data') && is_writable(dirname(DED_CONFIG_FILE));

yonetim_layout_start('Sunucu');
yonetim_page_header('Sunucu', 'dashboard');
yonetim_panel_open('Durum');
?>
<table class="table table-sm mb-0">
  <tbody>
    <tr><td>PHP <?= ded_h(PHP_VERSION) ?></td><td><?= $phpOk ? '<span class="text-success">OK</span>' : '<span class="text-danger">8.1+</span>' ?></td></tr>
    <tr><td>MySQL</td><td><?= $pdoOk ? '<span class="text-success">Bağlı</span>' : '<span class="text-danger">Yok</span>' ?></td></tr>
    <tr><td>Katalog</td><td><?= $dbOk ? '<span class="text-success">OK</span>' : '<span class="text-warning">—</span>' ?></td></tr>
    <tr><td>Mağaza</td><td><?= $shopOk ? '<span class="text-success">OK</span>' : '<span class="text-warning">—</span>' ?></td></tr>
    <tr><td>data/</td><td><?= $writable ? '<span class="text-success">Yazılabilir</span>' : '<span class="text-danger">İzin yok</span>' ?></td></tr>
    <tr><td>Rewrite</td><td><?= $rewriteOk === null ? '<span class="text-muted">—</span>' : ($rewriteOk ? '<span class="text-success">Açık</span>' : '<span class="text-warning">Kapalı</span>') ?></td></tr>
  </tbody>
</table>
<?php yonetim_panel_close();
yonetim_panel_open('Adresler');
?>
<ul class="list-unstyled fs-13 mb-0">
  <li class="mb-2"><a href="<?= ded_h($base) ?>" target="_blank" rel="noopener"><?= ded_h($base) ?></a> <span class="text-muted">vitrin</span></li>
  <li class="mb-2"><a href="<?= ded_h($panel) ?>" target="_blank" rel="noopener"><?= ded_h($panel) ?></a> <span class="text-muted">panel</span></li>
  <li class="mb-2"><code class="fs-12"><?= ded_h($base) ?>paytrag.php</code> <span class="text-muted">PayTR</span></li>
  <li><code class="fs-12"><?= ded_h($base) ?>api.php?path=ping</code> <span class="text-muted">API</span></li>
</ul>
<?php
yonetim_panel_close();
yonetim_layout_end();

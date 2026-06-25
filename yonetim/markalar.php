<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/magaza.php';

yonetim_require_login();
$pdo = yonetim_magaza_pdo();
if (!$pdo) {
    yonetim_layout_start('Markalar');
    yonetim_alert('danger', 'Veritabanı yok.');
    yonetim_layout_end();
    exit;
}

$rows = ded_markalar_listesi($pdo);

yonetim_layout_start('Markalar');
yonetim_panel_open('Marka listesi');
?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light"><tr><th>Marka</th><th>Ürün</th></tr></thead>
<tbody>
<?php foreach ($rows as $r) { ?>
  <tr>
    <td class="fw-medium"><?= ded_h((string) ($r['brand'] ?? '')) ?></td>
    <td><?= (int) ($r['cnt'] ?? 0) ?></td>
  </tr>
<?php } ?>
</tbody>
<?php
yonetim_table_responsive_close();
yonetim_panel_close();
yonetim_layout_end();

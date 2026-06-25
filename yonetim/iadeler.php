<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/magaza.php';

yonetim_require_login();
$pdo = yonetim_magaza_pdo();
if (!$pdo) {
    yonetim_layout_start('İadeler');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

$rows = ded_iadeler($pdo, 150);

yonetim_layout_start('İadeler');
yonetim_panel_open('İptal / iade / iade talebi');
?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr><th>No</th><th>Müşteri</th><th>Tutar</th><th>Durum</th><th>Tarih</th><th></th></tr>
</thead>
<tbody>
<?php foreach ($rows as $o) {
    $id = (int) ($o['id'] ?? 0); ?>
  <tr>
    <td><code class="fs-12"><?= ded_h((string) ($o['order_number'] ?? '')) ?></code></td>
    <td><?= ded_h((string) ($o['customer_name'] ?? '')) ?></td>
    <td><?= ded_h(ded_format_price_try_like_theme((float) ($o['total'] ?? 0))) ?></td>
    <td><span class="badge bg-secondary-subtle text-secondary"><?= ded_h((string) ($o['status'] ?? '')) ?></span></td>
    <td class="text-muted fs-13"><?= ded_h((string) ($o['updated_at'] ?? '')) ?></td>
    <td class="text-end"><a class="btn btn-sm btn-soft-primary" href="<?= ded_h(yonetim_url('order', ['id' => $id])) ?>">Detay</a></td>
  </tr>
<?php } ?>
</tbody>
<?php
yonetim_table_responsive_close();
yonetim_panel_close();
yonetim_layout_end();

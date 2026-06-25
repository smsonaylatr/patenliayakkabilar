<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/magaza.php';

yonetim_require_login();
$pdo = yonetim_magaza_pdo();
if (!$pdo) {
    yonetim_layout_start('Stok');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

$esik = isset($_GET['esik']) ? max(0, (int) $_GET['esik']) : 5;
$rows = ded_stok_dusuk($pdo, $esik, 300);

yonetim_layout_start('Stok uyarıları');
yonetim_panel_open('Düşük stok', [
    ['href' => yonetim_url('stock', ['esik' => 0]), 'label' => 'Tükenen', 'class' => 'btn btn-sm btn-light border'],
    ['href' => yonetim_url('stock', ['esik' => 5]), 'label' => '≤5', 'class' => 'btn btn-sm btn-light border'],
    ['href' => yonetim_url('stock', ['esik' => 10]), 'label' => '≤10', 'class' => 'btn btn-sm btn-primary'],
]);
?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr><th>Ürün</th><th>Varyant</th><th>SKU</th><th>Stok</th><th></th></tr>
</thead>
<tbody>
<?php foreach ($rows as $r) {
    $slug = (string) ($r['slug'] ?? ''); ?>
  <tr>
    <td class="fw-medium"><?= ded_h((string) ($r['title'] ?? '')) ?></td>
    <td><?= ded_h((string) ($r['variant_name'] ?? '')) ?></td>
    <td><code class="fs-12"><?= ded_h((string) ($r['sku'] ?? '')) ?></code></td>
    <td><span class="badge <?= (int) ($r['stock_qty'] ?? 0) <= 0 ? 'bg-danger' : 'bg-warning-subtle text-warning' ?>"><?= (int) ($r['stock_qty'] ?? 0) ?></span></td>
    <td class="text-end"><a class="btn btn-sm btn-soft-primary" href="<?= ded_h(yonetim_url('product', ['slug' => $slug])) ?>">Düzenle</a></td>
  </tr>
<?php } ?>
</tbody>
<?php
yonetim_table_responsive_close();
yonetim_panel_close();
yonetim_layout_end();

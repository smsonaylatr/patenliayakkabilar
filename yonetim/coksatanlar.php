<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';

yonetim_require_login();
$pdo = yonetim_shop_pdo();
if (!$pdo) {
    yonetim_layout_start('Çok satanlar');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

$filter = isset($_GET['days']) ? trim((string) $_GET['days']) : '';
$days = null;
if ($filter !== '' && $filter !== 'all') {
    $days = max(1, (int) $filter);
}
$rows = ded_shop_bestsellers($pdo, 120, $days);

yonetim_layout_start('Çok satanlar');
yonetim_panel_open('Çok satanlar');
yonetim_filter_pills([
    ['href' => yonetim_url('bestsellers', ['days' => 7]), 'label' => '7 gün', 'active' => $days === 7],
    ['href' => yonetim_url('bestsellers', ['days' => 30]), 'label' => '30 gün', 'active' => $days === 30],
    ['href' => yonetim_url('bestsellers', ['days' => 90]), 'label' => '90 gün', 'active' => $days === 90],
    ['href' => yonetim_url('bestsellers', ['days' => 'all']), 'label' => 'Tümü', 'active' => $days === null],
]);
?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr>
    <th>#</th>
    <th>Slug</th>
    <th>Ürün</th>
    <th class="text-end">Adet</th>
    <th class="text-end">Ciro</th>
  </tr>
</thead>
<tbody>
  <?php foreach ($rows as $i => $r) { ?>
  <tr>
    <td class="text-muted"><?= (int) $i + 1 ?></td>
    <td><code class="fs-12"><?= ded_h((string) ($r['slug'] ?? '')) ?></code></td>
    <td class="fw-medium"><?= ded_h((string) ($r['title'] ?? '')) ?></td>
    <td class="text-end"><?= (int) ($r['sold'] ?? 0) ?></td>
    <td class="text-end fw-semibold"><?= ded_h(number_format((float) ($r['revenue'] ?? 0), 2, ',', '.')) ?> ₺</td>
  </tr>
  <?php } ?>
</tbody>
<?php yonetim_table_responsive_close(); ?>
<?php yonetim_panel_close(); ?>
<?php yonetim_layout_end(); ?>

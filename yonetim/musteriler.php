<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/magaza.php';

yonetim_require_login();
$pdo = yonetim_magaza_pdo();
if (!$pdo) {
    yonetim_layout_start('Müşteriler');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

$q = trim((string) ($_GET['q'] ?? ''));
$data = ded_musteri_listesi($pdo, 200, 0, $q);

yonetim_layout_start('Müşteriler');
yonetim_panel_open('Müşteriler');
yonetim_search_bar($q, 'Ad, e-posta veya telefon', [], ded_h(yonetim_url('customers')));
?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr>
    <th>Müşteri</th>
    <th>E-posta</th>
    <th>Telefon</th>
    <th>Sipariş</th>
    <th>Harcama</th>
    <th>Son sipariş</th>
    <th></th>
  </tr>
</thead>
<tbody>
<?php foreach ($data['items'] as $row) {
    $em = (string) ($row['email'] ?? '');
    ?>
  <tr>
    <td class="fw-medium"><?= ded_h((string) ($row['name'] ?? '')) ?></td>
    <td><code class="fs-12"><?= ded_h($em) ?></code></td>
    <td><?= ded_h((string) ($row['phone'] ?? '')) ?></td>
    <td><?= (int) ($row['order_count'] ?? 0) ?></td>
    <td><?= ded_h(ded_format_price_try_like_theme((float) ($row['spent'] ?? 0))) ?></td>
    <td class="text-muted fs-13"><?= ded_h((string) ($row['last_order'] ?? '')) ?></td>
    <td class="text-end"><a class="btn btn-sm btn-soft-primary" href="<?= ded_h(yonetim_url('customer', ['id' => $em])) ?>">Detay</a></td>
  </tr>
<?php } ?>
</tbody>
<?php yonetim_table_responsive_close();
yonetim_panel_close();
yonetim_layout_end();

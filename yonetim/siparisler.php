<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/magazadepo.php';

yonetim_require_login();

$pdo = ded_pdo();
if (!$pdo) {
    yonetim_layout_start('Siparişler');
    echo '<div class="alert alert-danger">Veritabanı yapılandırılmamış.</div>';
    yonetim_layout_end();
    exit;
}

try {
    $pdo->query('SELECT 1 FROM ded_orders LIMIT 1');
} catch (Throwable) {
    yonetim_layout_start('Siparişler');
    echo '<div class="alert alert-danger">Sipariş tablosu yok.</div>';
    yonetim_layout_end();
    exit;
}

$status = isset($_GET['status']) ? (string) $_GET['status'] : '';
$q = trim((string) ($_GET['q'] ?? ''));
$list = ded_orders_list($pdo, 200, 0, $status !== '' ? $status : null);
if ($q !== '') {
    $qLow = mb_strtolower($q, 'UTF-8');
    $list['items'] = array_values(array_filter($list['items'], static function ($o) use ($qLow) {
        $hay = mb_strtolower(
            (string) ($o['order_number'] ?? '') . ' '
            . (string) ($o['customer_name'] ?? '') . ' '
            . (string) ($o['customer_email'] ?? '') . ' '
            . (string) ($o['customer_phone'] ?? ''),
            'UTF-8'
        );
        return str_contains($hay, $qLow);
    }));
}

yonetim_layout_start('Siparişler');
yonetim_panel_open('Siparişler');
?>
<div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
  <a href="<?= ded_h(yonetim_url('orders')) ?>" class="btn btn-sm <?= $status === '' ? 'btn-primary' : 'btn-light border' ?>">Tümü</a>
  <a href="<?= ded_h(yonetim_url('orders', ['status' => 'pending'])) ?>" class="btn btn-sm <?= $status === 'pending' ? 'btn-primary' : 'btn-light border' ?>">Bekleyen</a>
  <a href="<?= ded_h(yonetim_url('order_export', $status !== '' ? ['status' => $status] : [])) ?>" class="btn btn-sm btn-light border">CSV</a>
</div>
<?php yonetim_search_bar(
    $q,
    'Sipariş no, müşteri, e-posta, telefon',
    $status !== '' ? ['status' => $status] : [],
    ded_h(yonetim_url('orders', $status !== '' ? ['status' => $status] : []))
); ?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr>
    <th>No</th>
    <th>Müşteri</th>
    <th>Tutar</th>
    <th>Durum</th>
    <th>Ödeme</th>
    <th>Tarih</th>
    <th class="text-end"></th>
  </tr>
</thead>
<tbody>
  <?php foreach ($list['items'] as $o) {
      $id = (int) ($o['id'] ?? 0);
      $st = (string) ($o['status'] ?? '');
      $pay = (string) ($o['payment_status'] ?? '');
      ?>
  <tr>
    <td class="fw-medium"><?= ded_h((string) ($o['order_number'] ?? '')) ?></td>
    <td><?= ded_h((string) ($o['customer_name'] ?? '')) ?></td>
    <td><?= ded_h((string) ($o['total'] ?? '')) ?> <?= ded_h((string) ($o['currency'] ?? '')) ?></td>
    <td><span class="badge bg-light text-dark"><?= ded_h($st) ?></span></td>
    <td><span class="badge bg-primary-subtle text-primary"><?= ded_h($pay) ?></span></td>
    <td class="text-muted fs-12"><?= ded_h((string) ($o['created_at'] ?? '')) ?></td>
    <td class="text-end"><a class="btn btn-sm btn-soft-primary" href="<?= ded_h(yonetim_url('order', ['id' => $id])) ?>">Detay</a></td>
  </tr>
  <?php } ?>
</tbody>
<?php yonetim_table_responsive_close(); ?>
<?php yonetim_panel_close(); ?>
<?php yonetim_layout_end(); ?>

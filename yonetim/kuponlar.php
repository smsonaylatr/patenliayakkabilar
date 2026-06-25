<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';

yonetim_require_login();
$pdo = yonetim_shop_pdo();
if (!$pdo) {
    yonetim_layout_start('Kuponlar');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $did = (int) ($_POST['delete_id'] ?? 0);
    if ($did > 0) {
        ded_coupon_delete($pdo, $did);
        yonetim_flash('Kupon silindi.');
    }
    yonetim_redirect('coupons');
    exit;
}

$items = ded_coupons_list($pdo);
$q = mb_strtolower(trim((string) ($_GET['q'] ?? '')), 'UTF-8');
if ($q !== '') {
    $items = array_values(array_filter($items, static function ($c) use ($q) {
        $hay = mb_strtolower((string) ($c['code'] ?? '') . ' ' . (string) ($c['discount_type'] ?? ''), 'UTF-8');
        return str_contains($hay, $q);
    }));
}

yonetim_layout_start('Kuponlar');
yonetim_panel_open('Kuponlar', [
    ['href' => 'coupon', 'label' => 'Yeni kupon', 'class' => 'btn btn-sm btn-primary'],
]);
yonetim_search_bar(trim((string) ($_GET['q'] ?? '')), 'Kupon kodu', [], ded_h(yonetim_url('coupons')));
?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr>
    <th>Kod</th>
    <th>Tip</th>
    <th class="text-end">Değer</th>
    <th class="text-end">Min. sepet</th>
    <th>Aktif</th>
    <th class="text-end">Kullanım</th>
    <th class="text-end"></th>
  </tr>
</thead>
<tbody>
  <?php foreach ($items as $c) {
      $id = (int) ($c['id'] ?? 0);
      $type = (string) ($c['discount_type'] ?? '');
      $val = (float) ($c['discount_value'] ?? 0);
      $valShow = $type === 'percent' ? '%' . $val : number_format($val, 2, ',', '.') . ' ₺';
      ?>
  <tr>
    <td><code class="fs-12 fw-semibold"><?= ded_h((string) ($c['code'] ?? '')) ?></code></td>
    <td><?= ded_h($type) ?></td>
    <td class="text-end"><?= ded_h($valShow) ?></td>
    <td class="text-end"><?= ded_h(number_format((float) ($c['min_subtotal'] ?? 0), 2, ',', '.')) ?> ₺</td>
    <td><?= !empty($c['active']) ? '<span class="badge bg-success-subtle text-success">Evet</span>' : '<span class="badge bg-light text-muted">Hayır</span>' ?></td>
    <td class="text-end"><?= (int) ($c['used_count'] ?? 0) ?><?php
        $mx = $c['max_uses'];
      echo $mx !== null && $mx !== '' ? ' / ' . (int) $mx : '';
      ?></td>
    <td class="text-end text-nowrap">
      <a class="btn btn-sm btn-soft-primary" href="<?= ded_h(yonetim_url('coupon', ['id' => $id])) ?>">Düzenle</a>
      <form method="post" class="d-inline" onsubmit="return confirm('Kupon silinsin mi?');">
        <input type="hidden" name="delete_id" value="<?= $id ?>">
        <button type="submit" class="btn btn-sm btn-soft-danger">Sil</button>
      </form>
    </td>
  </tr>
  <?php } ?>
</tbody>
<?php yonetim_table_responsive_close(); ?>
<?php yonetim_panel_close(); ?>
<?php yonetim_layout_end(); ?>

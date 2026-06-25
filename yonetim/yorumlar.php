<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/ekstra.php';

yonetim_require_login();
$pdo = yonetim_magaza_pdo();
if (!$pdo) {
    yonetim_layout_start('Yorumlar');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = (string) ($_POST['act'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        if ($act === 'approve') {
            ded_review_set_approved($pdo, $id, true);
            yonetim_flash('Yorum onaylandı.');
        }
        if ($act === 'reject') {
            ded_review_set_approved($pdo, $id, false);
            yonetim_flash('Yorum reddedildi.');
        }
        if ($act === 'delete') {
            ded_review_delete($pdo, $id);
            yonetim_flash('Yorum silindi.');
        }
    }
    yonetim_redirect('reviews');
    exit;
}

$filter = (string) ($_GET['filter'] ?? 'pending');
$approved = match ($filter) {
    'approved' => true,
    'all' => null,
    default => false,
};
$slug = trim((string) ($_GET['slug'] ?? ''));
$data = ded_reviews_list($pdo, 150, $approved, $slug);

yonetim_layout_start('Yorumlar');
yonetim_page_header('Ürün yorumları', 'dashboard');
yonetim_panel_open('Yorumlar (' . (int) $data['total'] . ')');
yonetim_filter_pills([
    ['href' => yonetim_url('reviews', ['filter' => 'pending']), 'label' => 'Bekleyen', 'active' => $filter === 'pending'],
    ['href' => yonetim_url('reviews', ['filter' => 'approved']), 'label' => 'Onaylı', 'active' => $filter === 'approved'],
    ['href' => yonetim_url('reviews', ['filter' => 'all']), 'label' => 'Tümü', 'active' => $filter === 'all'],
]);
yonetim_search_bar(
    $slug,
    'Ürün slug',
    ['filter' => $filter],
    ded_h(yonetim_url('reviews', ['filter' => $filter])),
    'slug'
);
?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr><th>Ürün</th><th>Yazar</th><th>Puan</th><th>Yorum</th><th>Durum</th><th></th></tr>
</thead>
<tbody>
<?php foreach ($data['items'] as $r) {
    $rid = (int) ($r['id'] ?? 0);
    $ok = (int) ($r['approved'] ?? 0) === 1;
    ?>
  <tr>
    <td><code class="fs-12"><?= ded_h((string) ($r['product_slug'] ?? '')) ?></code></td>
    <td><?= ded_h((string) ($r['author_name'] ?? '')) ?></td>
    <td><?= (int) ($r['rating'] ?? 0) ?>/5</td>
    <td class="fs-13"><?= ded_h(mb_strimwidth((string) ($r['body'] ?? ''), 0, 120, '…', 'UTF-8')) ?></td>
    <td><?= $ok ? '<span class="badge bg-success-subtle text-success">Onaylı</span>' : '<span class="badge bg-warning-subtle text-warning">Bekliyor</span>' ?></td>
    <td class="text-end text-nowrap">
      <?php if (!$ok) { ?>
      <form method="post" class="d-inline"><input type="hidden" name="act" value="approve"><input type="hidden" name="id" value="<?= $rid ?>"><button class="btn btn-sm btn-success">Onayla</button></form>
      <?php } else { ?>
      <form method="post" class="d-inline"><input type="hidden" name="act" value="reject"><input type="hidden" name="id" value="<?= $rid ?>"><button class="btn btn-sm btn-light border">Gizle</button></form>
      <?php } ?>
      <form method="post" class="d-inline" onsubmit="return confirm('Silinsin mi?');"><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $rid ?>"><button class="btn btn-sm btn-outline-danger">Sil</button></form>
    </td>
  </tr>
<?php } ?>
</tbody>
<?php
yonetim_table_responsive_close();
yonetim_panel_close();
yonetim_layout_end();

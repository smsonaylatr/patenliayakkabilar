<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/urunyukle.php';
require_once __DIR__ . '/inc/yerlesim.php';

yonetim_require_login();

if (isset($_GET['format']) && (string) $_GET['format'] === 'json') {
    $catJson = yonetim_catalog_get();
    header('Content-Type: application/json; charset=utf-8');
    if ($catJson === null) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'catalog'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $rows = [];
    foreach ($catJson['collections'] ?? [] as $c) {
        $cid = (string) ($c['id'] ?? '');
        if ($cid === '') {
            continue;
        }
        $rows[] = [
            'id' => $cid,
            'title' => (string) ($c['title'] ?? $cid),
        ];
    }
    echo json_encode(['ok' => true, 'collections' => $rows], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_collection_id'])) {
    $delId = trim((string) $_POST['delete_collection_id']);
    $catDel = yonetim_catalog_get();
    if ($catDel === null) {
        yonetim_flash('Katalog yüklenemedi.');
        yonetim_redirect('collections');
        exit;
    }
    if ($delId === '' || yonetim_catalog_find_collection_index($catDel, $delId) === null) {
        yonetim_flash('Koleksiyon bulunamadı.');
        yonetim_redirect('collections');
        exit;
    }
    yonetim_catalog_delete_collection_by_id($catDel, $delId);
    $pdoSeo = ded_pdo();
    if ($pdoSeo) {
        require_once dirname(__DIR__) . '/lib/seo.php';
        ded_seo_overrides_save($pdoSeo, 'collection', $delId, ded_seo_overrides_empty());
    }
    try {
        yonetim_catalog_save($catDel);
        yonetim_flash('Koleksiyon silindi.');
    } catch (Throwable $e) {
        yonetim_flash('Koleksiyon silinemedi: ' . $e->getMessage());
    }
    yonetim_redirect('collections');
    exit;
}

$cat = yonetim_catalog_get();
if ($cat === null) {
    yonetim_layout_start('Koleksiyonlar');
    yonetim_alert('danger', 'Katalog yüklenemedi.');
    yonetim_layout_end();
    exit;
}

$q = mb_strtolower(trim((string) ($_GET['q'] ?? '')), 'UTF-8');
$cols = $cat['collections'];
if ($q !== '') {
    $cols = array_values(array_filter($cols, static function ($c) use ($q) {
        $hay = mb_strtolower(
            (string) ($c['id'] ?? '') . ' ' . (string) ($c['title'] ?? ''),
            'UTF-8'
        );
        return str_contains($hay, $q);
    }));
}
$n = count($cols);
yonetim_layout_start('Koleksiyonlar');
yonetim_panel_open('Koleksiyonlar', [
    ['href' => 'collection?new=1', 'label' => 'Yeni', 'class' => 'btn btn-sm btn-primary'],
]);
yonetim_search_bar(trim((string) ($_GET['q'] ?? '')), 'ID veya başlık', [], ded_h(yonetim_url('collections')));
?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr>
    <th style="width:52px" class="text-muted small">Kapak</th>
    <th>ID</th>
    <th>Başlık</th>
    <th class="text-end">Ürün</th>
    <th class="text-end"></th>
  </tr>
</thead>
<tbody>
  <?php foreach ($cols as $c) {
      $cid = (string) ($c['id'] ?? '');
      $cnt = is_array($c['productSlugs'] ?? null) ? count($c['productSlugs']) : 0;
      $thumb = yonetim_product_image_admin_src((string) ($c['image'] ?? ''));
      ?>
  <tr>
    <td class="align-middle"><?php if ($thumb !== '') { ?>
      <img src="<?= ded_h($thumb) ?>" alt="" width="40" height="40" class="rounded border" style="object-fit:cover">
    <?php } else { ?>
      <span class="d-inline-flex align-items-center justify-content-center rounded border bg-light text-muted small" style="width:40px;height:40px">—</span>
    <?php } ?></td>
    <td><code class="fs-12"><?= ded_h($cid) ?></code></td>
    <td class="fw-medium"><?= ded_h((string) ($c['title'] ?? '')) ?></td>
    <td class="text-end"><?= (int) $cnt ?></td>
    <td class="text-end text-nowrap">
      <a class="btn btn-sm btn-soft-primary" href="<?= ded_h(yonetim_url('collection', ['id' => $cid])) ?>">Düzenle</a>
      <form method="post" class="d-inline" action="<?= ded_h(yonetim_url('collections')) ?>" onsubmit="return confirm('Bu koleksiyon silinsin mi? Ürünlerden koleksiyon bağlantısı kalkar; ürünler silinmez.');">
        <input type="hidden" name="delete_collection_id" value="<?= ded_attr($cid) ?>">
        <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
      </form>
    </td>
  </tr>
  <?php } ?>
</tbody>
<?php yonetim_table_responsive_close(); ?>
<?php if ($n === 0) { ?>
  <p class="text-muted mb-0 p-3">Koleksiyon bulunamadı.</p>
<?php } ?>
<?php yonetim_panel_close(); ?>
<?php yonetim_layout_end(); ?>

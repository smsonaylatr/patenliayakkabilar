<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';

yonetim_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_page') {
    $catDel = yonetim_catalog_get();
    $slug = trim((string) ($_POST['slug'] ?? ''));
    if ($catDel !== null && $slug !== '' && ($_POST['confirm_delete'] ?? '') === '1') {
        if (yonetim_catalog_delete_page_by_slug($catDel, $slug)) {
            try {
                yonetim_catalog_save($catDel);
                yonetim_flash('Sayfa silindi.');
            } catch (Throwable $e) {
                yonetim_flash('Silinemedi: ' . $e->getMessage());
            }
        } else {
            yonetim_flash('Sayfa bulunamadı.');
        }
    }
    yonetim_redirect('pages');
    exit;
}

$cat = yonetim_catalog_get();
if ($cat === null) {
    yonetim_layout_start('Sayfalar');
    yonetim_alert('danger', 'Katalog yüklenemedi.');
    yonetim_layout_end();
    exit;
}

$pages = array_values(array_filter($cat['pages'], static fn ($pg) => (string) ($pg['slug'] ?? '') !== ''));
$q = mb_strtolower(trim((string) ($_GET['q'] ?? '')), 'UTF-8');
if ($q !== '') {
    $pages = array_values(array_filter($pages, static function ($pg) use ($q) {
        $hay = mb_strtolower(
            (string) ($pg['slug'] ?? '') . ' ' . (string) ($pg['title'] ?? ''),
            'UTF-8'
        );
        return str_contains($hay, $q);
    }));
}
yonetim_layout_start('Sayfalar');
yonetim_panel_open('Sayfalar', [
    ['href' => 'page?new=1', 'label' => 'Yeni sayfa', 'class' => 'btn btn-sm btn-primary'],
]);
yonetim_search_bar(trim((string) ($_GET['q'] ?? '')), 'Slug veya başlık', [], ded_h(yonetim_url('pages')));
?>
<?php if ($pages === []) { ?>
<p class="text-muted mb-0">Sayfa bulunamadı.</p>
<?php } else { ?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr>
    <th>Slug</th>
    <th>Başlık</th>
    <th class="text-end">İşlem</th>
  </tr>
</thead>
<tbody>
  <?php foreach ($pages as $pg) {
      $slug = (string) ($pg['slug'] ?? '');
      ?>
  <tr>
    <td><code class="fs-12"><?= ded_h($slug) ?></code></td>
    <td class="fw-medium"><?= ded_h((string) ($pg['title'] ?? '')) ?></td>
    <td class="text-end text-nowrap">
      <a class="btn btn-sm btn-soft-primary" href="<?= ded_h(yonetim_url('page', ['slug' => $slug])) ?>">Düzenle</a>
      <form method="post" class="d-inline" onsubmit="return confirm('Bu sayfa kalıcı olarak silinsin mi?');">
        <input type="hidden" name="action" value="delete_page">
        <input type="hidden" name="slug" value="<?= ded_h($slug) ?>">
        <input type="hidden" name="confirm_delete" value="1">
        <button type="submit" class="btn btn-sm btn-soft-danger">Sil</button>
      </form>
    </td>
  </tr>
  <?php } ?>
</tbody>
<?php yonetim_table_responsive_close(); ?>
<?php } ?>
<?php yonetim_panel_close(); ?>
<?php yonetim_layout_end(); ?>

<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/urunyukle.php';

yonetim_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_product') {
    $catDel = yonetim_catalog_get();
    $slug = trim((string) ($_POST['slug'] ?? ''));
    if ($catDel !== null && $slug !== '' && ($_POST['confirm_delete'] ?? '') === '1') {
        if (yonetim_catalog_delete_product_by_slug($catDel, $slug)) {
            try {
                yonetim_catalog_save($catDel);
                yonetim_flash('Ürün silindi.');
            } catch (Throwable $e) {
                yonetim_flash('Silinemedi: ' . $e->getMessage());
            }
        } else {
            yonetim_flash('Ürün bulunamadı.');
        }
    }
    yonetim_redirect('products');
    exit;
}

$cat = yonetim_catalog_get();
if ($cat === null) {
    yonetim_layout_start('Ürünler');
    echo '<div class="alert alert-danger">Katalog yüklenemedi.</div>';
    yonetim_layout_end();
    exit;
}

$q = mb_strtolower(trim((string) ($_GET['q'] ?? '')), 'UTF-8');
$urunler = $cat['products'];
if ($q !== '') {
    $urunler = array_values(array_filter($urunler, static function ($p) use ($q) {
        $hay = mb_strtolower(
            (string) ($p['slug'] ?? '') . ' ' . (string) ($p['title'] ?? '') . ' ' . (string) ($p['brand'] ?? ''),
            'UTF-8'
        );
        return str_contains($hay, $q);
    }));
}
$n = count($urunler);
$qRaw = trim((string) ($_GET['q'] ?? ''));
yonetim_layout_start('Ürünler');
yonetim_panel_open('Ürünler', [
    ['href' => 'product?new=1', 'label' => 'Yeni ürün', 'class' => 'btn btn-sm btn-primary'],
]);
yonetim_search_bar($qRaw, 'Slug, başlık veya marka', [], ded_h(yonetim_url('products')));
?>
<div class="table-responsive d-none d-md-block"><table class="table mb-0 table-hover align-middle yun-products-table">
<thead class="table-light">
  <tr>
    <th style="width:64px"></th>
    <th>Ürün</th>
    <th>Kategori</th>
    <th>Fiyat</th>
    <th class="text-end">İşlem</th>
  </tr>
</thead>
<tbody>
  <?php foreach ($urunler as $p) {
      $slug = (string) ($p['slug'] ?? '');
      if ($slug === '') {
          continue;
      }
      $price = $p['price'] ?? '';
      $cur = (string) ($p['currency'] ?? '');
      $audLabel = ded_product_audience_label(ded_product_audience_normalize((string) ($p['audience'] ?? '')));
      $imgs = (array) ($p['images'] ?? []);
      $firstImg = '';
      foreach ($imgs as $im) {
          $im = trim((string) $im);
          if ($im !== '') {
              $firstImg = yonetim_product_image_admin_src($im);
              break;
          }
      }
      $editHref = ded_h(yonetim_url('product', ['slug' => $slug]));
      ?>
  <tr>
    <td>
      <a href="<?= $editHref ?>" class="yun-prod-thumb">
        <?php if ($firstImg !== '') { ?>
          <img src="<?= ded_h($firstImg) ?>" alt="" loading="lazy">
        <?php } else { ?>
          <span class="yun-prod-thumb__placeholder"><i class="iconoir-media-image"></i></span>
        <?php } ?>
      </a>
    </td>
    <td>
      <a href="<?= $editHref ?>" class="fw-medium text-body text-decoration-none d-block"><?= ded_h((string) ($p['title'] ?? '')) ?></a>
      <code class="fs-12 text-muted d-block text-truncate" style="max-width:340px"><?= ded_h($slug) ?></code>
    </td>
    <td><?php if ($audLabel !== '') { ?><span class="badge bg-primary-subtle text-primary"><?= ded_h($audLabel) ?></span><?php } else { ?>—<?php } ?></td>
    <td class="text-nowrap"><?= ded_h(ded_format_price_try_like_theme((float) $price)) ?><?= $cur !== '' && $cur !== 'TRY' ? ' ' . ded_h($cur) : '' ?></td>
    <td class="text-end text-nowrap">
      <a class="btn btn-sm btn-soft-primary" href="<?= $editHref ?>">Düzenle</a>
      <form method="post" class="d-inline" onsubmit="return confirm('Silinsin mi?');">
        <input type="hidden" name="action" value="delete_product">
        <input type="hidden" name="slug" value="<?= ded_h($slug) ?>">
        <input type="hidden" name="confirm_delete" value="1">
        <button type="submit" class="btn btn-sm btn-soft-danger">Sil</button>
      </form>
    </td>
  </tr>
  <?php } ?>
</tbody>
</table></div>

<ul class="list-unstyled mb-0 d-md-none yun-products-list">
  <?php foreach ($urunler as $p) {
      $slug = (string) ($p['slug'] ?? '');
      if ($slug === '') {
          continue;
      }
      $price = $p['price'] ?? '';
      $cur = (string) ($p['currency'] ?? '');
      $audLabel = ded_product_audience_label(ded_product_audience_normalize((string) ($p['audience'] ?? '')));
      $imgs = (array) ($p['images'] ?? []);
      $firstImg = '';
      foreach ($imgs as $im) {
          $im = trim((string) $im);
          if ($im !== '') {
              $firstImg = yonetim_product_image_admin_src($im);
              break;
          }
      }
      $editHref = ded_h(yonetim_url('product', ['slug' => $slug]));
      ?>
  <li class="yun-prod-card">
    <a href="<?= $editHref ?>" class="yun-prod-card__thumb">
      <?php if ($firstImg !== '') { ?>
        <img src="<?= ded_h($firstImg) ?>" alt="" loading="lazy">
      <?php } else { ?>
        <span class="yun-prod-thumb__placeholder"><i class="iconoir-media-image"></i></span>
      <?php } ?>
    </a>
    <div class="yun-prod-card__body">
      <a href="<?= $editHref ?>" class="yun-prod-card__title"><?= ded_h((string) ($p['title'] ?? '')) ?></a>
      <code class="yun-prod-card__slug"><?= ded_h($slug) ?></code>
      <div class="yun-prod-card__meta">
        <?php if ($audLabel !== '') { ?><span class="badge bg-primary-subtle text-primary"><?= ded_h($audLabel) ?></span><?php } ?>
        <span class="yun-prod-card__price"><?= ded_h(ded_format_price_try_like_theme((float) $price)) ?><?= $cur !== '' && $cur !== 'TRY' ? ' ' . ded_h($cur) : '' ?></span>
      </div>
      <div class="yun-prod-card__actions">
        <a class="btn btn-sm btn-soft-primary flex-grow-1" href="<?= $editHref ?>">Düzenle</a>
        <form method="post" class="flex-grow-1 d-flex" onsubmit="return confirm('Silinsin mi?');">
          <input type="hidden" name="action" value="delete_product">
          <input type="hidden" name="slug" value="<?= ded_h($slug) ?>">
          <input type="hidden" name="confirm_delete" value="1">
          <button type="submit" class="btn btn-sm btn-soft-danger w-100">Sil</button>
        </form>
      </div>
    </div>
  </li>
  <?php } ?>
</ul>
<?php if ($n === 0) { ?>
  <p class="text-muted mb-0 p-3">Ürün bulunamadı.</p>
<?php } ?>
<?php yonetim_panel_close(); ?>
<?php yonetim_layout_end(); ?>

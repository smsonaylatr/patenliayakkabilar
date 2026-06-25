<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/urunyukle.php';
require_once __DIR__ . '/inc/medyakutuphane.php';
require_once dirname(__DIR__) . '/lib/seo.php';

yonetim_require_login();

$slugParam = trim((string) ($_GET['slug'] ?? ''));
$isNew = isset($_GET['new']) && (string) $_GET['new'] === '1' && $slugParam === '';

if (!$isNew && $slugParam === '') {
    yonetim_redirect('products');
}

$cat = yonetim_catalog_get();
if ($cat === null) {
    yonetim_layout_start('Ürün');
    yonetim_alert('danger', 'Katalog yüklenemedi.');
    yonetim_layout_end();
    exit;
}

$idx = null;
if (!$isNew) {
    $idx = yonetim_catalog_find_product_index($cat, $slugParam);
    if ($idx === null) {
        yonetim_layout_start('Ürün');
        yonetim_alert('danger', 'Ürün bulunamadı.');
        echo '<p class="mt-2"><a href="' . ded_h(yonetim_url('products')) . '" class="btn btn-sm btn-light border">Ürün listesi</a></p>';
        yonetim_layout_end();
        exit;
    }
}

$saveErr = '';
$originalSlug = $isNew ? '' : (string) ($cat['products'][$idx]['slug'] ?? '');

$collectionIdsFromPost = static function (): array {
    $raw = $_POST['collection_ids'] ?? [];
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $id) {
        $id = trim((string) $id);
        if ($id !== '') {
            $out[] = $id;
        }
    }

    return array_values(array_unique($out));
};

$parsePost = static function (array $images) use ($collectionIdsFromPost): array {
    $newSlug = trim((string) ($_POST['slug'] ?? ''));
    $title = (string) ($_POST['title'] ?? '');
    $description = (string) ($_POST['description'] ?? '');
    $brand = (string) ($_POST['brand'] ?? '');
    $audience = ded_product_audience_normalize((string) ($_POST['audience'] ?? ''));
    $price = ded_parse_price_input($_POST['price'] ?? 0);
    $currency = (string) ($_POST['currency'] ?? 'TRY');
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);
    $idKeep = (string) ($_POST['external_id'] ?? '');

    $compareAtPrice = null;
    $compareRaw = trim((string) ($_POST['compare_at_price'] ?? ''));
    if ($compareRaw !== '') {
        $cp = ded_parse_price_input($_POST['compare_at_price'] ?? '');
        $compareAtPrice = $cp > 0 ? $cp : null;
    }

    $variantsIn = $_POST['variants'] ?? null;
    if ($variantsIn !== null && !is_array($variantsIn)) {
        return ['ok' => false, 'err' => 'Varyant verisi geçersiz.'];
    }
    $variants = [];
    if (is_array($variantsIn)) {
        foreach ($variantsIn as $v) {
            if (!is_array($v)) {
                continue;
            }
            $name = trim((string) ($v['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $rowCur = trim((string) ($v['currency'] ?? ''));
            $skuRaw = $v['sku'] ?? null;
            $stockQty = max(0, (int) ($v['stockQty'] ?? 0));
            $vPrice = ded_parse_price_input($v['price'] ?? 0);
            if ($vPrice <= 0) {
                $vPrice = $price;
            }
            $variants[] = [
                'name' => $name,
                'price' => $vPrice,
                'currency' => $rowCur !== '' ? $rowCur : $currency,
                'sku' => $skuRaw !== '' && $skuRaw !== null ? (string) $skuRaw : null,
                'inStock' => !empty($v['inStock']) || $stockQty > 0,
                'stockQty' => $stockQty,
            ];
        }
    }
    if ($variants === []) {
        return ['ok' => false, 'err' => 'En az bir varyant için ad (ör. beden) girin.'];
    }

    if ($newSlug === '') {
        return ['ok' => false, 'err' => 'Slug boş olamaz.'];
    }

    return [
        'ok' => true,
        'variants' => $variants,
        'newSlug' => $newSlug,
        'title' => $title,
        'description' => $description,
        'brand' => $brand,
        'audience' => $audience,
        'price' => $price,
        'currency' => $currency,
        'sortOrder' => $sortOrder,
        'idKeep' => $idKeep,
        'images' => $images,
        'collectionIds' => $collectionIdsFromPost(),
        'compareAtPrice' => $compareAtPrice,
    ];
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadErr = '';
    $uploadedNew = [];
    try {
        $uploadedNew = yonetim_product_save_uploaded_images();
    } catch (RuntimeException $e) {
        $uploadErr = $e->getMessage();
    }
    $existingImgs = yonetim_product_existing_images_from_post();
    $picked = [];
    $pickRaw = $_POST['media_pick'] ?? [];
    if (is_array($pickRaw)) {
        foreach ($pickRaw as $path) {
            $path = trim((string) $path);
            if ($path !== '' && yonetim_safe_storage_rel_path($path)) {
                $picked[] = $path;
            }
        }
    }
    $postImages = $uploadErr === '' ? array_merge($existingImgs, $picked, $uploadedNew) : array_merge($existingImgs, $picked);
    $postImages = array_values(array_unique($postImages));

    if ($uploadErr !== '') {
        $saveErr = $uploadErr;
    } else {
        $isNewPost = !empty($_POST['is_new']);

        if ($isNewPost) {
            if (!$isNew) {
                $saveErr = 'Geçersiz istek.';
            } else {
                $parsed = $parsePost($postImages);
                if (!$parsed['ok']) {
                    $saveErr = (string) ($parsed['err'] ?? 'Hata');
                } elseif (yonetim_catalog_find_product_index($cat, $parsed['newSlug']) !== null) {
                    $saveErr = 'Bu slug başka bir üründe kullanılıyor.';
                } else {
                    $np = [
                        'id' => $parsed['idKeep'],
                        'slug' => $parsed['newSlug'],
                        'title' => $parsed['title'],
                        'description' => $parsed['description'],
                        'brand' => $parsed['brand'],
                        'audience' => $parsed['audience'] ?? '',
                        'price' => $parsed['price'],
                        'currency' => $parsed['currency'],
                        'sortOrder' => $parsed['sortOrder'] ?? 0,
                        'images' => $parsed['images'],
                        'variants' => $parsed['variants'],
                    ];
                    if (($parsed['compareAtPrice'] ?? null) !== null) {
                        $np['compareAtPrice'] = (float) $parsed['compareAtPrice'];
                    }
                    $cat['products'][] = $np;
                    $colIds = $parsed['collectionIds'] ?? [];
                    if ($colIds === []) {
                        yonetim_catalog_append_product_to_all_collections($cat, $parsed['newSlug']);
                    } else {
                        yonetim_catalog_sync_product_collections($cat, $parsed['newSlug'], $colIds);
                    }
                    try {
                        yonetim_catalog_save($cat);
                        $pdoSeo = ded_pdo();
                        if ($pdoSeo) {
                            ded_seo_overrides_save($pdoSeo, 'product', $parsed['newSlug'], [
                                'title' => trim((string) ($_POST['seo_title'] ?? '')),
                                'description' => trim((string) ($_POST['seo_description'] ?? '')),
                                'image' => trim((string) ($_POST['seo_image'] ?? '')),
                                'noindex' => isset($_POST['seo_noindex']),
                            ]);
                        }
                        yonetim_flash('Ürün oluşturuldu.');
                        yonetim_redirect('product', ['slug' => $parsed['newSlug']]);
                        exit;
                    } catch (Throwable $e) {
                        $saveErr = $e->getMessage();
                        array_pop($cat['products']);
                        yonetim_catalog_remove_product_slug_from_collections($cat, $parsed['newSlug']);
                    }
                }
            }
        } else {
            $orig = (string) ($_POST['original_slug'] ?? '');
            if ($orig !== $originalSlug) {
                $saveErr = 'Oturum / ürün uyuşmazlığı. Sayfayı yenileyin.';
            } else {
                $parsed = $parsePost($postImages);
                if (!$parsed['ok']) {
                    $saveErr = (string) ($parsed['err'] ?? 'Hata');
                } elseif ($parsed['newSlug'] !== $originalSlug && yonetim_catalog_find_product_index($cat, $parsed['newSlug']) !== null) {
                    $saveErr = 'Bu slug başka bir üründe kullanılıyor.';
                } else {
                    if ($parsed['newSlug'] !== $originalSlug) {
                        yonetim_catalog_replace_product_slug($cat, $originalSlug, $parsed['newSlug']);
                    }
                    $cat['products'][$idx] = array_merge($cat['products'][$idx], [
                        'id' => $parsed['idKeep'] !== '' ? $parsed['idKeep'] : ($cat['products'][$idx]['id'] ?? ''),
                        'slug' => $parsed['newSlug'],
                        'title' => $parsed['title'],
                        'description' => $parsed['description'],
                        'brand' => $parsed['brand'],
                        'audience' => $parsed['audience'] ?? '',
                        'price' => $parsed['price'],
                        'currency' => $parsed['currency'],
                        'sortOrder' => $parsed['sortOrder'] ?? 0,
                        'images' => $parsed['images'],
                        'variants' => $parsed['variants'],
                    ]);
                    if (($parsed['compareAtPrice'] ?? null) !== null) {
                        $cat['products'][$idx]['compareAtPrice'] = (float) $parsed['compareAtPrice'];
                    } else {
                        unset($cat['products'][$idx]['compareAtPrice'], $cat['products'][$idx]['compare_at_price']);
                    }
                    $colIds = $parsed['collectionIds'] ?? [];
                    if ($colIds !== []) {
                        yonetim_catalog_sync_product_collections($cat, $parsed['newSlug'], $colIds);
                    }
                    try {
                        yonetim_catalog_save($cat);
                        $pdoSeo = ded_pdo();
                        if ($pdoSeo) {
                            if ($parsed['newSlug'] !== $originalSlug) {
                                ded_seo_overrides_rename($pdoSeo, 'product', $originalSlug, $parsed['newSlug']);
                            }
                            ded_seo_overrides_save($pdoSeo, 'product', $parsed['newSlug'], [
                                'title' => trim((string) ($_POST['seo_title'] ?? '')),
                                'description' => trim((string) ($_POST['seo_description'] ?? '')),
                                'image' => trim((string) ($_POST['seo_image'] ?? '')),
                                'noindex' => isset($_POST['seo_noindex']),
                            ]);
                        }
                        yonetim_flash('Ürün kaydedildi.');
                        yonetim_redirect('product', ['slug' => $parsed['newSlug']]);
                        exit;
                    } catch (Throwable $e) {
                        $saveErr = $e->getMessage();
                    }
                }
            }
        }
    }
}

$p = $isNew ? yonetim_catalog_default_product() : $cat['products'][$idx];
$productImages = $p['images'] ?? [];
if (!is_array($productImages)) {
    $productImages = [];
}
$variantRows = $p['variants'] ?? [];
if (!is_array($variantRows) || $variantRows === []) {
    $variantRows = [
        [
            'name' => 'Varsayılan',
            'price' => (float) ($p['price'] ?? 0),
            'currency' => (string) ($p['currency'] ?? 'TRY'),
            'sku' => null,
            'inStock' => true,
            'stockQty' => 10,
        ],
    ];
}
$variantRows = array_values($variantRows);

$allCollections = $cat['collections'] ?? [];
if (!is_array($allCollections)) {
    $allCollections = [];
}
if ($isNew) {
    $selectedCollectionIds = array_values(array_filter(array_map(
        static fn ($c) => (string) ($c['id'] ?? ''),
        $allCollections
    )));
} else {
    $selectedCollectionIds = yonetim_catalog_collections_for_product_slug($cat, $originalSlug);
}
$pageTitle = $isNew ? 'Yeni ürün' : ('Ürün: ' . ($p['title'] ?? ''));
$productSortOrder = (int) ($p['sortOrder'] ?? 0);
$imgCount = count($productImages);
$varCount = count($variantRows);
$colCount = count($selectedCollectionIds);
$mediaLibrary = yonetim_media_library_list(300);
$headerTitle = $isNew ? 'Yeni ürün' : (string) ($p['title'] ?? 'Ürün');
yonetim_layout_start($pageTitle);
yonetim_layout_script('<script src="' . ded_h(yonetim_panel_asset('uruntablar.js?v=2')) . '"></script>');
?>
<?php
yonetim_page_header($headerTitle, 'products', [
    ['href' => 'products', 'label' => 'İptal', 'class' => 'btn btn-sm btn-light border'],
]);
?>
<div class="d-flex justify-content-end mb-3">
  <button type="submit" form="yun-product-form" class="btn btn-primary btn-sm"><?= $isNew ? 'Ürünü oluştur' : 'Kaydet' ?></button>
</div>
<?php
?>
<?php if ($saveErr !== '') {
    yonetim_alert('danger', $saveErr);
} ?>
<form id="yun-product-form" method="post" class="card border-0 shadow-sm yun-form yun-product-form" enctype="multipart/form-data" data-yun-is-new="<?= $isNew ? '1' : '0' ?>">
  <div class="card-header border-bottom bg-transparent px-3 py-3">
    <nav id="yun-product-tabs" class="yun-product-tabs" role="tablist" aria-label="Ürün bölümleri">
      <button type="button" class="yun-tab-btn is-active" data-panel="sec-temel">Temel bilgiler</button>
      <button type="button" class="yun-tab-btn" data-panel="sec-fiyat">Fiyat</button>
      <button type="button" class="yun-tab-btn" data-panel="sec-koleksiyon">Koleksiyonlar<?php if ($colCount > 0) { ?> <span class="badge bg-primary-subtle text-primary ms-1"><?= (int) $colCount ?></span><?php } ?></button>
      <button type="button" class="yun-tab-btn" data-panel="sec-gorsel">Görseller <span class="badge bg-light text-body border ms-1"><?= (int) $imgCount ?></span></button>
      <button type="button" class="yun-tab-btn" data-panel="sec-varyant">Varyantlar <span class="badge bg-light text-body border ms-1"><?= (int) $varCount ?></span></button>
      <button type="button" class="yun-tab-btn" data-panel="sec-seo">SEO</button>
    </nav>
  </div>
  <div class="card-body">
  <?php if ($isNew) { ?>
  <input type="hidden" name="is_new" value="1">
  <?php } else { ?>
  <input type="hidden" name="original_slug" value="<?= ded_h($originalSlug) ?>">
  <?php } ?>

    <div id="sec-temel" class="yun-product-panel">
    <h2 class="yun-panel-title">Temel bilgiler</h2>
    <div class="yun-form-grid yun-form-grid--2">
      <div>
        <label>Dış ID</label>
        <input type="text" name="external_id" value="<?= ded_h((string) ($p['id'] ?? '')) ?>">
      </div>
      <div>
        <label>Slug (URL) <span class="yun-req">*</span></label>
        <input type="text" name="slug" value="<?= ded_h((string) ($p['slug'] ?? '')) ?>" required class="yun-mono">
      </div>
    </div>
    <label>Başlık</label>
    <input type="text" name="title" value="<?= ded_h((string) ($p['title'] ?? '')) ?>">
    <label>Marka</label>
    <input type="text" name="brand" value="<?= ded_h((string) ($p['brand'] ?? '')) ?>">
    <label>Açıklama</label>
    <textarea name="description" rows="5"><?= ded_h((string) ($p['description'] ?? '')) ?></textarea>
    <div class="yun-form-grid yun-form-grid--2">
      <div>
        <label>Hedef kategori</label>
        <select name="audience" class="yun-select-audience">
          <?php
            $curAud = ded_product_audience_normalize((string) ($p['audience'] ?? ''));
            foreach (ded_product_audience_form_options() as $opt) {
                $sel = $curAud === $opt['value'] ? ' selected' : '';
                ?>
          <option value="<?= ded_attr($opt['value']) ?>"<?= $sel ?>><?= ded_h($opt['label']) ?></option>
          <?php } ?>
        </select>
      </div>
      <div>
        <label>Sıra (liste önceliği)</label>
        <input type="number" name="sort_order" min="0" step="1" value="<?= (int) $productSortOrder ?>">
      </div>
    </div>

    </div>
    <div id="sec-fiyat" class="yun-product-panel" hidden>
    <h2 class="yun-panel-title">Fiyat ve para birimi</h2>
    <div class="yun-form-grid yun-form-grid--2 yun-form-grid--price">
      <div>
        <label>Satış fiyatı</label>
        <input type="text" name="price" inputmode="decimal" autocomplete="off" value="<?= ded_h(ded_format_price_input_tr((float) ($p['price'] ?? 0))) ?>" class="yun-input-price" data-yun-price>
        <p class="yun-muted yun-price-preview" data-yun-price-preview="main"></p>
      </div>
      <div>
        <?php
          $compareAtExisting = isset($p['compareAtPrice']) ? (float) $p['compareAtPrice'] : (isset($p['compare_at_price']) ? (float) $p['compare_at_price'] : 0.0);
          $compareAtInput = $compareAtExisting > 0 ? ded_format_price_input_tr($compareAtExisting) : '';
          ?>
        <label>Çizili (liste) fiyat <span class="yun-muted fw-normal">— isteğe bağlı</span></label>
        <input type="text" name="compare_at_price" inputmode="decimal" autocomplete="off" value="<?= ded_h($compareAtInput) ?>" class="yun-input-price" data-yun-price placeholder="Boş: indirim rozeti gösterme">
        <p class="yun-muted small mb-0">Doldurulursa vitrin ve ürün sayfasında kırmızı satış fiyatı ve üstü çizili liste fiyatı gösterilir. Boş bırakılırsa bu indirim satırı ve tema kalıbı temizlenir.</p>
      </div>
    </div>
    <div class="yun-form-grid yun-form-grid--2 mt-3">
      <div>
        <label>Para birimi</label>
        <input type="text" name="currency" value="<?= ded_h((string) ($p['currency'] ?? 'TRY')) ?>" placeholder="TRY">
      </div>
    </div>

    </div>
    <div id="sec-koleksiyon" class="yun-product-panel" hidden>
    <h2 class="yun-panel-title">Koleksiyonlar</h2>
    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
      <p class="small text-muted mb-0 flex-grow-1">Başka sekmede yeni koleksiyon eklediyseniz listeyi yenileyin.</p>
      <button type="button" class="btn btn-sm btn-light border" id="yun-collections-refresh">Koleksiyonları yenile</button>
    </div>
    <label class="yun-sr-only">Koleksiyon</label>
  <div class="yun-collection-picks">
    <?php foreach ($allCollections as $col) {
        $cid = (string) ($col['id'] ?? '');
        if ($cid === '') {
            continue;
        }
        $checked = in_array($cid, $selectedCollectionIds, true);
        ?>
    <label class="yun-check-row">
      <input type="checkbox" name="collection_ids[]" value="<?= ded_attr($cid) ?>"<?= $checked ? ' checked' : '' ?>>
      <span><?= ded_h((string) ($col['title'] ?? $cid)) ?></span>
      <span class="yun-mono yun-muted"><?= ded_h($cid) ?></span>
    </label>
    <?php } ?>
  </div>
  <?php if ($allCollections === []) { ?>
  <p class="yun-empty" id="yun-koleksiyon-empty">Koleksiyon yok.</p>
  <?php } ?>

    </div>
    <div id="sec-gorsel" class="yun-product-panel" hidden>
    <h2 class="yun-panel-title">Ürün görselleri</h2>
    <div class="yun-img-toolbar d-flex flex-wrap gap-2 mb-3">
      <label class="btn btn-sm btn-light border mb-0 yun-file-label">
        <i class="iconoir-upload me-1"></i>Yükle
        <input type="file" name="product_images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple class="yun-file-hidden" id="yun-product-file-input">
      </label>
      <button type="button" class="btn btn-sm btn-light border" id="yun-open-media"><i class="iconoir-media-image me-1"></i>Galeriden seç</button>
    </div>
    <p class="small text-muted mb-2" id="yun-upload-file-summary" hidden aria-live="polite"></p>
    <div id="yun-picked-media" class="yun-sr-only" aria-hidden="true"></div>
    <div class="yun-product-images-grid" id="yun-existing-images">
    <?php foreach ($productImages as $imgPath) {
        $imgPath = trim((string) $imgPath);
        if ($imgPath === '') {
            continue;
        }
        $src = yonetim_product_image_admin_src($imgPath);
        ?>
      <div class="yun-product-image-card">
        <img src="<?= ded_h($src) ?>" alt="">
        <input type="hidden" name="existing_images[]" value="<?= ded_h($imgPath) ?>">
        <div class="yun-product-image-card__actions btn-group btn-group-sm w-100">
          <button type="button" class="btn btn-light border yun-img-up" title="Yukarı"><i class="iconoir-arrow-up"></i></button>
          <button type="button" class="btn btn-light border yun-img-down" title="Aşağı"><i class="iconoir-arrow-down"></i></button>
          <button type="button" class="btn btn-light border text-danger yun-image-remove" title="Kaldır"><i class="iconoir-trash"></i></button>
        </div>
      </div>
    <?php } ?>
    </div>
    <?php if ($productImages === []) { ?>
    <p class="yun-empty" id="yun-img-empty">Görsel yok</p>
    <?php } ?>

    </div>
    <div id="sec-varyant" class="yun-product-panel" hidden>
    <h2 class="yun-panel-title">Varyantlar</h2>
  <div class="yun-variant-block">
    <div id="yun-variant-rows" data-next-index="<?= count($variantRows) ?>">
      <?php foreach ($variantRows as $vi => $vv) {
          $vName = (string) ($vv['name'] ?? '');
          $vPrice = $vv['price'] ?? 0;
          $vCur = (string) ($vv['currency'] ?? '');
          $vSku = $vv['sku'] ?? null;
          $vStock = array_key_exists('inStock', $vv)
              ? (bool) $vv['inStock']
              : (bool) ($vv['in_stock'] ?? true);
          $vStockQty = (int) ($vv['stockQty'] ?? $vv['stock_qty'] ?? ($vStock ? 10 : 0));
          ?>
      <div class="yun-variant-row" data-variant-index="<?= (int) $vi ?>">
        <div class="yun-variant-row__head">
          <span class="yun-variant-row__title">Varyant</span>
          <button type="button" class="yun-btn-sm yun-btn-ghost yun-variant-remove" aria-label="Varyantı kaldır">Kaldır</button>
        </div>
        <div class="yun-variant-row__fields">
          <div>
            <label>Ad (beden vb.)</label>
            <input type="text" name="variants[<?= (int) $vi ?>][name]" value="<?= ded_h($vName) ?>" autocomplete="off">
          </div>
          <div>
            <label>Fiyat</label>
            <input type="text" name="variants[<?= (int) $vi ?>][price]" inputmode="decimal" autocomplete="off" value="<?= ded_h($vPrice > 0 ? ded_format_price_input_tr((float) $vPrice) : '') ?>" class="yun-input-price" data-yun-price>
          </div>
          <div>
            <label>Para birimi (boş: ürün birimi)</label>
            <input type="text" name="variants[<?= (int) $vi ?>][currency]" value="<?= ded_h($vCur) ?>" placeholder="TRY">
          </div>
          <div>
            <label>SKU</label>
            <input type="text" name="variants[<?= (int) $vi ?>][sku]" value="<?= ded_h($vSku !== null && $vSku !== '' ? (string) $vSku : '') ?>" autocomplete="off">
          </div>
          <div>
            <label>Stok adedi</label>
            <input type="number" name="variants[<?= (int) $vi ?>][stockQty]" min="0" step="1" value="<?= (int) $vStockQty ?>">
          </div>
          <div>
            <label class="yun-variant-instock">
              <input type="checkbox" name="variants[<?= (int) $vi ?>][inStock]" value="1" <?= $vStock ? ' checked' : '' ?>>
              Stokta (satışa açık)
            </label>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
    <div class="yun-variant-add-wrap">
      <button type="button" class="btn btn-sm btn-light border" id="yun-variant-add"><i class="iconoir-plus me-1"></i>Varyant ekle</button>
    </div>
  </div>
    </div>

    <?php
      $seoCur = ded_seo_overrides_empty();
      if (!$isNew) {
          $pdoSeoPanel = ded_pdo();
          if ($pdoSeoPanel) {
              $seoCur = ded_seo_product_overrides($pdoSeoPanel, $originalSlug);
          }
      }
      foreach (['seo_title', 'seo_description', 'seo_image'] as $k) {
          if (isset($_POST[$k])) {
              $seoCur[str_replace('seo_', '', $k)] = (string) $_POST[$k];
          }
      }
      if (isset($_POST['seo_noindex'])) {
          $seoCur['noindex'] = true;
      }
    ?>
    <div id="sec-seo" class="yun-product-panel" hidden>
      <h2 class="yun-panel-title">SEO</h2>
      <label>SEO başlık</label>
      <input type="text" name="seo_title" maxlength="120" value="<?= ded_h((string) $seoCur['title']) ?>">
      <label>Meta açıklama</label>
      <textarea name="seo_description" rows="3" maxlength="320"><?= ded_h((string) $seoCur['description']) ?></textarea>
      <label>OG/Twitter görseli</label>
      <input type="text" name="seo_image" value="<?= ded_h((string) $seoCur['image']) ?>">
      <label class="d-flex align-items-center gap-2 mt-2">
        <input type="checkbox" name="seo_noindex" value="1"<?= !empty($seoCur['noindex']) ? ' checked' : '' ?>>
        Arama motorlarına gösterme
      </label>
    </div>

  </div>

  <div class="card-footer bg-light border-top d-flex flex-wrap gap-2 justify-content-end py-3">
    <a class="btn btn-light border" href="<?= ded_h(yonetim_url('products')) ?>">İptal</a>
    <button type="submit" class="btn btn-primary"><?= $isNew ? 'Ürünü oluştur' : 'Kaydet' ?></button>
  </div>

  <template id="yun-variant-tpl">
    <div class="yun-variant-row" data-variant-index="__N__">
      <div class="yun-variant-row__head">
        <span class="yun-variant-row__title">Varyant</span>
        <button type="button" class="yun-btn-sm yun-btn-ghost yun-variant-remove" aria-label="Varyantı kaldır">Kaldır</button>
      </div>
      <div class="yun-variant-row__fields">
        <div>
          <label>Ad (beden vb.)</label>
          <input type="text" name="variants[__N__][name]" value="" autocomplete="off">
        </div>
        <div>
          <label>Fiyat</label>
          <input type="text" name="variants[__N__][price]" inputmode="decimal" autocomplete="off" value="" class="yun-input-price" data-yun-price placeholder="Boş: liste fiyatı">
        </div>
        <div>
          <label>Para birimi (boş: ürün birimi)</label>
          <input type="text" name="variants[__N__][currency]" value="" placeholder="TRY">
        </div>
        <div>
          <label>SKU</label>
          <input type="text" name="variants[__N__][sku]" value="" autocomplete="off">
        </div>
        <div>
          <label>Stok adedi</label>
          <input type="number" name="variants[__N__][stockQty]" min="0" step="1" value="10">
        </div>
        <div>
          <label class="yun-variant-instock">
            <input type="checkbox" name="variants[__N__][inStock]" value="1" checked>
            Stokta (satışa açık)
          </label>
        </div>
      </div>
    </div>
  </template>
  <script>
  (function () {
    var ex = document.getElementById('yun-existing-images');
    if (ex) {
      ex.addEventListener('click', function (e) {
        var b = e.target && e.target.closest && e.target.closest('.yun-image-remove');
        if (!b) return;
        var card = b.closest('.yun-product-image-card');
        if (card) card.remove();
        var emptyHint = document.getElementById('yun-img-empty');
        if (emptyHint && ex) {
          emptyHint.style.display = ex.querySelector('.yun-product-image-card') ? 'none' : '';
        }
      });
    }
  })();
  </script>
  <script>
  (function () {
    var input = document.getElementById('yun-product-file-input');
    var el = document.getElementById('yun-upload-file-summary');
    if (!input || !el) return;
    function syncUploadSummary() {
      var files = input.files;
      if (!files || files.length === 0) {
        el.hidden = true;
        el.textContent = '';
        return;
      }
      var n = files.length;
      var parts = [];
      for (var i = 0; i < n && i < 8; i++) {
        parts.push(files[i].name);
      }
      var more = n > 8 ? ' … (+' + (n - 8) + ')' : '';
      el.hidden = false;
      el.textContent =
        (n === 1 ? '1 görsel seçildi: ' : n + ' görsel seçildi: ') +
        parts.join(', ') +
        more +
        ' — Kaydet’e basınca yüklenecek.';
    }
    input.addEventListener('change', syncUploadSummary);
  })();
  </script>
  <script>
  (function () {
    var box = document.getElementById('yun-variant-rows');
    var tpl = document.getElementById('yun-variant-tpl');
    var addBtn = document.getElementById('yun-variant-add');
    if (!box || !tpl || !addBtn) return;
    function rowCount() { return box.querySelectorAll('.yun-variant-row').length; }
    addBtn.addEventListener('click', function () {
      var next = parseInt(box.getAttribute('data-next-index'), 10);
      if (isNaN(next)) next = rowCount();
      var html = tpl.innerHTML.replace(/__N__/g, String(next));
      var wrap = document.createElement('div');
      wrap.innerHTML = html.trim();
      var row = wrap.firstElementChild;
      if (row) {
        box.appendChild(row);
        if (window.yunBindPriceInput) {
          row.querySelectorAll('[data-yun-price]').forEach(window.yunBindPriceInput);
        }
      }
      box.setAttribute('data-next-index', String(next + 1));
    });
    box.addEventListener('click', function (e) {
      var btn = e.target && e.target.closest && e.target.closest('.yun-variant-remove');
      if (!btn) return;
      if (rowCount() <= 1) return;
      var row = btn.closest('.yun-variant-row');
      if (row) row.remove();
    });
  })();
  </script>
  <script>
  (function () {
    var API = <?= json_encode(yonetim_url('collections', ['format' => 'json']), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) ?>;
    var panel = document.getElementById('sec-koleksiyon');
    var btn = document.getElementById('yun-collections-refresh');
    var form = document.getElementById('yun-product-form');
    if (!panel || !btn || !form) return;

    function updateKoleksiyonBadge() {
      var n = panel.querySelectorAll('input[name="collection_ids[]"]:checked').length;
      var tabBtn = document.querySelector('#yun-product-tabs .yun-tab-btn[data-panel="sec-koleksiyon"]');
      if (!tabBtn) return;
      var badge = tabBtn.querySelector('.badge');
      if (n > 0) {
        if (!badge) {
          badge = document.createElement('span');
          badge.className = 'badge bg-primary-subtle text-primary ms-1';
          tabBtn.appendChild(badge);
        }
        badge.textContent = String(n);
      } else if (badge) {
        badge.remove();
      }
    }

    function syncEmptyKoleksiyon(rows) {
      var picks = panel.querySelector('.yun-collection-picks');
      if (!picks) return;
      var emptyEl = document.getElementById('yun-koleksiyon-empty');
      if (!rows || rows.length === 0) {
        if (!emptyEl) {
          emptyEl = document.createElement('p');
          emptyEl.className = 'yun-empty';
          emptyEl.id = 'yun-koleksiyon-empty';
          emptyEl.textContent = 'Koleksiyon yok.';
          picks.after(emptyEl);
        }
        emptyEl.style.display = '';
      } else if (emptyEl) {
        emptyEl.remove();
      }
    }

    function rebuild(rows) {
      var picks = panel.querySelector('.yun-collection-picks');
      if (!picks) return;
      var oldMap = {};
      picks.querySelectorAll('input[name="collection_ids[]"]').forEach(function (inp) {
        oldMap[inp.value] = inp.checked;
      });
      var isNew = form.getAttribute('data-yun-is-new') === '1';
      picks.textContent = '';
      (rows || []).forEach(function (c) {
        var id = c.id;
        var title = c.title || id;
        var label = document.createElement('label');
        label.className = 'yun-check-row';
        var inp = document.createElement('input');
        inp.type = 'checkbox';
        inp.name = 'collection_ids[]';
        inp.value = id;
        if (Object.prototype.hasOwnProperty.call(oldMap, id)) {
          inp.checked = oldMap[id];
        } else {
          inp.checked = !!isNew;
        }
        var sp1 = document.createElement('span');
        sp1.textContent = title;
        var sp2 = document.createElement('span');
        sp2.className = 'yun-mono yun-muted';
        sp2.textContent = id;
        label.appendChild(inp);
        label.appendChild(sp1);
        label.appendChild(sp2);
        picks.appendChild(label);
      });
      syncEmptyKoleksiyon(rows);
      updateKoleksiyonBadge();
    }

    function refresh() {
      btn.disabled = true;
      fetch(API, { credentials: 'same-origin' })
        .then(function (r) {
          return r.json();
        })
        .then(function (data) {
          if (!data || !data.ok || !Array.isArray(data.collections)) {
            throw new Error('bad');
          }
          rebuild(data.collections);
        })
        .catch(function () {
          window.alert('Koleksiyon listesi alınamadı. Oturum açık olduğundan emin olun.');
        })
        .finally(function () {
          btn.disabled = false;
        });
    }

    btn.addEventListener('click', refresh);
    panel.addEventListener('change', function (e) {
      if (e.target && e.target.name === 'collection_ids[]') {
        updateKoleksiyonBadge();
      }
    });
    var visTimer;
    document.addEventListener('visibilitychange', function () {
      if (document.visibilityState !== 'visible') return;
      clearTimeout(visTimer);
      visTimer = setTimeout(refresh, 500);
    });
  })();
  </script>
  <script>
  (function () {
    var modal = document.getElementById('yun-media-modal');
    var openBtn = document.getElementById('yun-open-media');
    var grid = document.getElementById('yun-media-grid');
    var ex = document.getElementById('yun-existing-images');
    if (!modal || !openBtn || !grid || !ex) return;

    function hasPath(path) {
      var inputs = ex.querySelectorAll('input[type="hidden"]');
      for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].value === path) return true;
      }
      return false;
    }

    function escAttr(s) {
      return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
    }
    function syncEmptyHint() {
      var emptyHint = document.getElementById('yun-img-empty');
      if (emptyHint) {
        emptyHint.style.display = ex.querySelector('.yun-product-image-card') ? 'none' : '';
      }
    }
    function appendImage(path, src, useMediaPick) {
      if (hasPath(path)) return;
      var card = document.createElement('div');
      card.className = 'yun-product-image-card';
      var name = useMediaPick ? 'media_pick[]' : 'existing_images[]';
      card.innerHTML =
        '<img src="' + escAttr(src) + '" alt="">' +
        '<input type="hidden" name="' + name + '" value="' + escAttr(path) + '">' +
        '<div class="yun-product-image-card__actions btn-group btn-group-sm w-100">' +
        '<button type="button" class="btn btn-light border yun-img-up" title="Yukarı"><i class="iconoir-arrow-up"></i></button>' +
        '<button type="button" class="btn btn-light border yun-img-down" title="Aşağı"><i class="iconoir-arrow-down"></i></button>' +
        '<button type="button" class="btn btn-light border text-danger yun-image-remove" title="Kaldır"><i class="iconoir-trash"></i></button>' +
        '</div>';
      ex.appendChild(card);
      syncEmptyHint();
    }
    function moveCard(card, dir) {
      if (!card) return;
      var sib = dir < 0 ? card.previousElementSibling : card.nextElementSibling;
      if (!sib || !sib.classList.contains('yun-product-image-card')) return;
      if (dir < 0) ex.insertBefore(card, sib);
      else ex.insertBefore(sib, card);
    }
    ex.addEventListener('click', function (e) {
      var t = e.target;
      if (!t || !t.closest) return;
      if (t.closest('.yun-img-up')) {
        moveCard(t.closest('.yun-product-image-card'), -1);
        return;
      }
      if (t.closest('.yun-img-down')) {
        moveCard(t.closest('.yun-product-image-card'), 1);
      }
    });

    var addPickedBtn = document.getElementById('yun-media-add-picked');
    openBtn.addEventListener('click', function () {
      grid.querySelectorAll('.yun-media-thumb.is-picked').forEach(function (el) {
        el.classList.remove('is-picked');
      });
      modal.hidden = false;
    });
    modal.querySelectorAll('[data-yun-media-close]').forEach(function (el) {
      el.addEventListener('click', function () {
        modal.hidden = true;
      });
    });
    grid.addEventListener('click', function (e) {
      var btn = e.target && e.target.closest && e.target.closest('[data-media-path]');
      if (!btn) return;
      btn.classList.toggle('is-picked');
    });
    if (addPickedBtn) {
      addPickedBtn.addEventListener('click', function () {
        grid.querySelectorAll('.yun-media-thumb.is-picked').forEach(function (btn) {
          appendImage(btn.getAttribute('data-media-path'), btn.getAttribute('data-media-src'), true);
          btn.classList.remove('is-picked');
        });
        modal.hidden = true;
      });
    }
  })();
  </script>
</form>

<div id="yun-media-modal" class="yun-media-modal" hidden>
  <div class="yun-media-modal__backdrop" data-yun-media-close></div>
  <div class="yun-media-modal__panel">
    <header class="yun-media-modal__head">
      <h2>Galeriden görsel seç</h2>
      <button type="button" class="btn btn-sm btn-light border" data-yun-media-close>Kapat</button>
    </header>
    <div class="yun-media-grid" id="yun-media-grid">
      <?php foreach ($mediaLibrary as $m) {
          $path = (string) ($m['path'] ?? '');
          if ($path === '') {
              continue;
          }
          $src = yonetim_product_image_admin_src($path);
          ?>
      <button type="button" class="yun-media-thumb" data-media-path="<?= ded_attr($path) ?>" data-media-src="<?= ded_attr($src) ?>">
        <img src="<?= ded_h($src) ?>" alt="">
        <code><?= ded_h($path) ?></code>
      </button>
      <?php } ?>
    </div>
    <footer class="yun-media-modal__foot">
      <button type="button" class="btn btn-primary" id="yun-media-add-picked">Seçilenleri ekle</button>
      <button type="button" class="btn btn-light border" data-yun-media-close>İptal</button>
    </footer>
  </div>
</div>

<script src="<?= ded_h(yonetim_panel_asset('fiyatgiris.js?v=1')) ?>"></script>
<script>
(function () {
  var NS = '[yun-product]';
  window.addEventListener('error', function (e) {
    console.error(NS, 'JS hata:', e.message, e.filename, e.lineno, e.colno, e.error);
  });
  window.addEventListener('unhandledrejection', function (e) {
    console.error(NS, 'Promise hata:', e.reason);
  });
  function diag() {
    var form = document.getElementById('yun-product-form');
    if (!form) {
      console.error(NS, 'HATA: #yun-product-form bulunamadı — PHP sayfa ortasında kesilmiş olabilir (F12 → Ağ → urun.php yanıtına bakın).');
      return;
    }
    var titles = form.querySelectorAll('h2.yun-form-section__title');
    var inputs = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
    console.log(NS, 'Form OK — bölüm:', titles.length, 'alan:', inputs.length);
    titles.forEach(function (h2, i) {
      var r = h2.getBoundingClientRect();
      var st = window.getComputedStyle(h2);
      console.log(NS, 'Bölüm ' + (i + 1) + ':', h2.textContent.trim(),
        '| görünür:', r.height > 0 && st.display !== 'none' && st.visibility !== 'hidden',
        '| display:', st.display);
    });
    if (inputs.length < 5) {
      console.warn(NS, 'Az alan sayısı — sayfa eksik render olmuş olabilir.');
    }
    var missing = [];
    ['Temel bilgiler', 'Fiyat ve para birimi', 'Koleksiyonlar', 'Ürün görselleri', 'Varyantlar'].forEach(function (t) {
      var ok = false;
      titles.forEach(function (h2) {
        if (h2.textContent.indexOf(t) !== -1) ok = true;
      });
      if (!ok) missing.push(t);
    });
    if (missing.length) {
      console.error(NS, 'Eksik bölümler (PHP hata / kesik HTML):', missing.join(', '));
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', diag);
  } else {
    diag();
  }
})();
</script>
<?php yonetim_layout_end();

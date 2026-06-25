<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/urunyukle.php';
require_once dirname(__DIR__) . '/lib/seo.php';

yonetim_require_login();

$idParam = (string) ($_GET['id'] ?? '');
$isNewGet = isset($_GET['new']) && (string) $_GET['new'] === '1';

$cat = yonetim_catalog_get();
if ($cat === null) {
    yonetim_layout_start('Koleksiyon');
    yonetim_alert('danger', 'Katalog yüklenemedi.');
    yonetim_layout_end();
    exit;
}

$isNewPost = ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['is_new']));
$isNewMode = $isNewGet || $isNewPost;

$idx = null;
if (!$isNewMode) {
    if ($idParam === '') {
        yonetim_layout_start('Koleksiyon');
        yonetim_alert('danger', 'Koleksiyon seçilmedi.');
        echo '<p class="mt-2"><a href="' . ded_h(yonetim_url('collections')) . '" class="btn btn-sm btn-light border">Listeye dön</a></p>';
        yonetim_layout_end();
        exit;
    }
    $idx = yonetim_catalog_find_collection_index($cat, $idParam);
    if ($idx === null) {
        yonetim_layout_start('Koleksiyon');
        yonetim_alert('danger', 'Koleksiyon bulunamadı.');
        echo '<p class="mt-2"><a href="' . ded_h(yonetim_url('collections')) . '" class="btn btn-sm btn-light border">Listeye dön</a></p>';
        yonetim_layout_end();
        exit;
    }
}

$saveErr = '';
$collectionId = $isNewMode ? '' : (string) ($cat['collections'][$idx]['id'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = (string) ($_POST['title'] ?? '');
    $description = (string) ($_POST['description'] ?? '');
    $image = trim((string) ($_POST['image'] ?? ''));
    try {
        $uploadedCover = yonetim_single_image_upload('collection_cover');
        if ($uploadedCover !== null) {
            $image = $uploadedCover;
        }
    } catch (Throwable $e) {
        $saveErr = $e->getMessage();
    }

    $slugs = [];
    if ($saveErr === '') {
        if (!empty($_POST['is_new'])) {
            $slugs = [];
        } else {
            $slugs = array_values(
                array_map(
                    'strval',
                    is_array($cat['collections'][$idx]['productSlugs'] ?? null)
                        ? $cat['collections'][$idx]['productSlugs']
                        : []
                )
            );
        }
    }

    if ($saveErr === '') {
        if (!empty($_POST['is_new'])) {
            $newIdRaw = yonetim_catalog_normalize_collection_id((string) ($_POST['new_collection_id'] ?? ''));
            if ($newIdRaw === '' || !yonetim_catalog_collection_id_valid($newIdRaw)) {
                $saveErr = 'Geçerli bir koleksiyon ID girin (küçük harf, rakam ve tire; örn. erkek-ayakkabi).';
            } elseif (yonetim_catalog_find_collection_index($cat, $newIdRaw) !== null) {
                $saveErr = 'Bu koleksiyon ID zaten kullanılıyor.';
            } else {
                $cat['collections'][] = [
                    'id' => $newIdRaw,
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
                    'productSlugs' => $slugs,
                ];
                try {
                    yonetim_catalog_save($cat);
                    $pdoSeo = ded_pdo();
                    if ($pdoSeo) {
                        ded_seo_overrides_save($pdoSeo, 'collection', $newIdRaw, [
                            'title' => trim((string) ($_POST['seo_title'] ?? '')),
                            'description' => trim((string) ($_POST['seo_description'] ?? '')),
                            'image' => trim((string) ($_POST['seo_image'] ?? '')),
                            'noindex' => isset($_POST['seo_noindex']),
                        ]);
                    }
                    yonetim_flash('Koleksiyon oluşturuldu.');
                    yonetim_redirect('collection', ['id' => $newIdRaw]);
                    exit;
                } catch (Throwable $e) {
                    $saveErr = $e->getMessage();
                }
            }
        } else {
            $origId = (string) ($_POST['collection_id'] ?? '');
            if ($origId !== $collectionId) {
                $saveErr = 'Kayıt uyuşmazlığı. Sayfayı yenileyin.';
            } else {
                $cat['collections'][$idx] = [
                    'id' => $collectionId,
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
                    'productSlugs' => $slugs,
                ];
                try {
                    yonetim_catalog_save($cat);
                    $pdoSeo = ded_pdo();
                    if ($pdoSeo) {
                        ded_seo_overrides_save($pdoSeo, 'collection', $collectionId, [
                            'title' => trim((string) ($_POST['seo_title'] ?? '')),
                            'description' => trim((string) ($_POST['seo_description'] ?? '')),
                            'image' => trim((string) ($_POST['seo_image'] ?? '')),
                            'noindex' => isset($_POST['seo_noindex']),
                        ]);
                    }
                    yonetim_flash('Koleksiyon kaydedildi.');
                    yonetim_redirect('collection', ['id' => $collectionId]);
                    exit;
                } catch (Throwable $e) {
                    $saveErr = $e->getMessage();
                }
            }
        }
    }
}

if ($isNewMode) {
    $c = [
        'id' => '',
        'title' => '',
        'description' => '',
        'image' => '',
        'productSlugs' => [],
    ];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $saveErr !== '') {
        $c['title'] = (string) ($_POST['title'] ?? '');
        $c['description'] = (string) ($_POST['description'] ?? '');
        $c['image'] = trim((string) ($_POST['image'] ?? ''));
        $newCollectionInput = (string) ($_POST['new_collection_id'] ?? '');
        $seoCur = [
            'title' => trim((string) ($_POST['seo_title'] ?? '')),
            'description' => trim((string) ($_POST['seo_description'] ?? '')),
            'image' => trim((string) ($_POST['seo_image'] ?? '')),
            'noindex' => !empty($_POST['seo_noindex']),
        ];
    } else {
        $newCollectionInput = '';
        $seoCur = ded_seo_overrides_empty();
    }
} else {
    $c = $cat['collections'][$idx];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $saveErr !== '') {
        $c['title'] = (string) ($_POST['title'] ?? '');
        $c['description'] = (string) ($_POST['description'] ?? '');
        $c['image'] = trim((string) ($_POST['image'] ?? ''));
        $seoCur = [
            'title' => trim((string) ($_POST['seo_title'] ?? '')),
            'description' => trim((string) ($_POST['seo_description'] ?? '')),
            'image' => trim((string) ($_POST['seo_image'] ?? '')),
            'noindex' => !empty($_POST['seo_noindex']),
        ];
    } else {
        $pdoSeoPanel = ded_pdo();
        $seoCur = $pdoSeoPanel ? ded_seo_collection_overrides($pdoSeoPanel, $collectionId) : ded_seo_overrides_empty();
    }
    $newCollectionInput = '';
}

$coverSrc = yonetim_product_image_admin_src((string) ($c['image'] ?? ''));

yonetim_layout_start('Koleksiyon');
$pageTitle = $isNewMode ? 'Yeni koleksiyon' : (string) ($c['title'] ?? 'Koleksiyon');
yonetim_page_header($pageTitle, 'collections');
if ($saveErr !== '') {
    yonetim_alert('danger', $saveErr);
}
yonetim_form_open(['enctype' => 'multipart/form-data']);
if ($isNewMode) {
?>
  <input type="hidden" name="is_new" value="1">
  <label>Koleksiyon ID <span class="text-danger">*</span></label>
  <input type="text" name="new_collection_id" class="font-monospace" required value="<?= ded_h($newCollectionInput) ?>" placeholder="<?= ded_h('erkek-ayakkabi') ?>" maxlength="190" autocomplete="off">
  <p class="text-muted small mb-0">Vitrin adresi: <code class="small">/koleksiyon/<em>id</em></code> — küçük harf, rakam ve tire.</p>
<?php } else { ?>
  <input type="hidden" name="collection_id" value="<?= ded_h($collectionId) ?>">
  <label>ID</label>
  <input type="text" value="<?= ded_h($collectionId) ?>" disabled>
<?php } ?>
  <label class="mt-3">Başlık <span class="text-danger">*</span></label>
  <input type="text" name="title" required value="<?= ded_h((string) ($c['title'] ?? '')) ?>" placeholder="<?= ded_h('Örn. Çocuk') ?>">
  <p class="text-muted small mb-0">Bu metin <strong>koleksiyonlar</strong> sayfasındaki kart üzerinde ve koleksiyon bağlantısında görünür.</p>
  <label class="mt-3">Açıklama</label>
  <textarea name="description" rows="3"><?= ded_h((string) ($c['description'] ?? '')) ?></textarea>
  <label class="mt-3">Kapak görseli (koleksiyonlar kartı)</label>
  <?php if ($coverSrc !== '') { ?>
  <div class="mb-2 rounded border bg-light p-2 d-inline-block">
    <img src="<?= ded_h($coverSrc) ?>" alt="" class="rounded" style="max-height:120px;max-width:220px;object-fit:cover">
  </div>
  <?php } ?>
  <input type="text" name="image" class="font-monospace" value="<?= ded_h((string) ($c['image'] ?? '')) ?>" placeholder="cdn/shop/files/... veya tam URL">
  <p class="text-muted small">Yol girin veya dosya yükleyin. Medya kütüphanesinden kopyaladığınız yolları da yapıştırabilirsiniz: <a href="<?= ded_h(yonetim_url('media')) ?>" target="_blank" rel="noopener">Medya</a></p>
  <label class="d-block mt-2">Dosyadan yükle</label>
  <input type="file" name="collection_cover" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/gif">
  <p class="text-muted small mt-2 mb-0">Bu koleksiyondaki ürünleri <strong>ürün düzenleme</strong> ekranındaki koleksiyon kutularından ekleyip çıkarabilirsiniz.</p>

  <hr class="my-4">
  <h6 class="fw-semibold mb-2">SEO</h6>
  <label>SEO başlık</label>
  <input type="text" name="seo_title" maxlength="120" value="<?= ded_h((string) $seoCur['title']) ?>">
  <label>Meta açıklama</label>
  <textarea name="seo_description" rows="3" maxlength="320"><?= ded_h((string) $seoCur['description']) ?></textarea>
  <label>OG görsel</label>
  <input type="text" name="seo_image" value="<?= ded_h((string) $seoCur['image']) ?>">
  <label class="d-flex align-items-center gap-2 mt-2">
    <input type="checkbox" name="seo_noindex" value="1"<?= !empty($seoCur['noindex']) ? ' checked' : '' ?>>
    Arama motorlarına gösterme
  </label>
<?php yonetim_form_close(); ?>
<?php yonetim_layout_end(); ?>

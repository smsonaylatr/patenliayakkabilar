<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/vitrinrotalar.php';
require_once dirname(__DIR__) . '/lib/seo.php';

yonetim_require_login();

$slugParam = trim((string) ($_GET['slug'] ?? ''));
$isNew = isset($_GET['new']) && (string) $_GET['new'] === '1' && $slugParam === '';

if (!$isNew && $slugParam === '') {
    yonetim_redirect('pages');
}

$cat = yonetim_catalog_get();
if ($cat === null) {
    yonetim_layout_start('Sayfa');
    yonetim_alert('danger', 'Katalog yüklenemedi.');
    yonetim_layout_end();
    exit;
}

$idx = null;
if (!$isNew) {
    $idx = yonetim_catalog_find_page_index($cat, $slugParam);
    if ($idx === null) {
        yonetim_layout_start('Sayfa');
        yonetim_alert('danger', 'Sayfa bulunamadı.');
        echo '<p class="mt-2"><a href="' . ded_h(yonetim_url('pages')) . '" class="btn btn-sm btn-light border">Listeye dön</a></p>';
        yonetim_layout_end();
        exit;
    }
}

$saveErr = '';
$originalSlug = $isNew ? '' : (string) ($cat['pages'][$idx]['slug'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_page') {
    $slugDel = trim((string) ($_POST['slug'] ?? ''));
    if (!$isNew && $slugDel !== '' && $slugDel === $originalSlug && ($_POST['confirm_delete'] ?? '') === '1') {
        if (yonetim_catalog_delete_page_by_slug($cat, $slugDel)) {
            try {
                yonetim_catalog_save($cat);
                yonetim_flash('Sayfa silindi.');
                yonetim_redirect('pages');
                exit;
            } catch (Throwable $e) {
                $saveErr = $e->getMessage();
            }
        } else {
            $saveErr = 'Sayfa bulunamadı.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') !== 'delete_page') {
    $isNewPost = !empty($_POST['is_new']);
    $newSlug = yonetim_catalog_normalize_page_slug((string) ($_POST['slug'] ?? ''));
    $title = (string) ($_POST['title'] ?? '');
    $description = (string) ($_POST['description'] ?? '');
    $sourceHtml = (string) ($_POST['sourceHtml'] ?? '');

    if ($newSlug === '') {
        $saveErr = 'Slug boş olamaz.';
    } elseif (!yonetim_catalog_page_slug_valid($newSlug)) {
        $saveErr = 'Geçersiz slug. Harf, rakam ve tire kullanın (ör. bize-ulasin).';
    } elseif ($isNewPost) {
        if (!$isNew) {
            $saveErr = 'Geçersiz istek.';
        } elseif (yonetim_catalog_find_page_index($cat, $newSlug) !== null) {
            $saveErr = 'Bu slug zaten kullanılıyor.';
        } else {
            $cat['pages'][] = [
                'slug' => $newSlug,
                'title' => $title,
                'description' => $description,
                'sourceHtml' => $sourceHtml,
            ];
            try {
                yonetim_catalog_save($cat);
                $pdoSeo = ded_pdo();
                if ($pdoSeo) {
                    ded_seo_overrides_save($pdoSeo, 'page', $newSlug, [
                        'title' => trim((string) ($_POST['seo_title'] ?? '')),
                        'description' => trim((string) ($_POST['seo_description'] ?? '')),
                        'image' => trim((string) ($_POST['seo_image'] ?? '')),
                        'noindex' => isset($_POST['seo_noindex']),
                    ]);
                }
                yonetim_flash('Sayfa oluşturuldu.');
                yonetim_redirect('page', ['slug' => $newSlug]);
                exit;
            } catch (Throwable $e) {
                array_pop($cat['pages']);
                $saveErr = $e->getMessage();
            }
        }
    } else {
        $orig = (string) ($_POST['original_slug'] ?? '');
        if ($orig !== $originalSlug) {
            $saveErr = 'Kayıt uyuşmazlığı. Sayfayı yenileyin.';
        } elseif ($newSlug !== $originalSlug && yonetim_catalog_find_page_index($cat, $newSlug) !== null) {
            $saveErr = 'Bu slug başka bir sayfada kullanılıyor.';
        } else {
            $cat['pages'][$idx] = [
                'slug' => $newSlug,
                'title' => $title,
                'description' => $description,
                'sourceHtml' => $sourceHtml,
            ];
            try {
                yonetim_catalog_save($cat);
                $pdoSeo = ded_pdo();
                if ($pdoSeo) {
                    if ($newSlug !== $originalSlug) {
                        ded_seo_overrides_rename($pdoSeo, 'page', $originalSlug, $newSlug);
                    }
                    ded_seo_overrides_save($pdoSeo, 'page', $newSlug, [
                        'title' => trim((string) ($_POST['seo_title'] ?? '')),
                        'description' => trim((string) ($_POST['seo_description'] ?? '')),
                        'image' => trim((string) ($_POST['seo_image'] ?? '')),
                        'noindex' => isset($_POST['seo_noindex']),
                    ]);
                }
                yonetim_flash('Sayfa kaydedildi.');
                yonetim_redirect('page', ['slug' => $newSlug]);
                exit;
            } catch (Throwable $e) {
                $saveErr = $e->getMessage();
            }
        }
    }
}

if ($isNew) {
    $pg = [
        'slug' => '',
        'title' => '',
        'description' => '',
        'sourceHtml' => '',
    ];
} else {
    $pg = $cat['pages'][$idx];
}

$sourceHtmlValue = (string) ($pg['sourceHtml'] ?? '');
if ($sourceHtmlValue === '' && $_SERVER['REQUEST_METHOD'] !== 'POST' && !$isNew) {
    require_once dirname(__DIR__) . '/lib/sablonyukle.php';
    $tpl = ded_template_render('icerik.php');
    if (preg_match('#<div class="prose">(.*?)</div>\s*</div>\s*</div>\s*</div>#s', $tpl, $m)
        || preg_match('#<div class="prose">(.*?)</div>#s', $tpl, $m)) {
        $sourceHtmlValue = trim($m[1]);
    }
}

$previewUrl = $isNew ? '' : ded_storefront_public_url() . ded_vitrin_url('page', ['slug' => (string) ($pg['slug'] ?? '')]);
$pageTitle = $isNew ? 'Yeni sayfa' : (string) ($pg['title'] ?? 'Sayfa');
$pdoSeoPanel = ded_pdo();
$seoCur = ($pdoSeoPanel && !$isNew) ? ded_seo_page_overrides($pdoSeoPanel, $originalSlug) : ded_seo_overrides_empty();

$headerActions = [];
if ($previewUrl !== '') {
    $headerActions[] = ['href' => $previewUrl, 'label' => 'Önizle', 'class' => 'btn btn-sm btn-light border'];
}

yonetim_layout_start($pageTitle);
yonetim_page_header($pageTitle, 'pages', $headerActions);
if ($saveErr !== '') {
    yonetim_alert('danger', $saveErr);
}
yonetim_form_open();
?>
  <?php if ($isNew) { ?>
  <input type="hidden" name="is_new" value="1">
  <?php } else { ?>
  <input type="hidden" name="original_slug" value="<?= ded_h($originalSlug) ?>">
  <?php } ?>
  <label>Slug</label>
  <input type="text" name="slug" value="<?= ded_h((string) ($pg['slug'] ?? '')) ?>" required>

  <label>Başlık</label>
  <input type="text" name="title" value="<?= ded_h((string) ($pg['title'] ?? '')) ?>">

  <label>Meta açıklama</label>
  <textarea name="description" rows="3"><?= ded_h((string) ($pg['description'] ?? '')) ?></textarea>

  <label>İçerik</label>
  <textarea name="sourceHtml" rows="18" class="font-monospace fs-13"><?= ded_h($sourceHtmlValue) ?></textarea>

  <hr class="my-4">
  <h6 class="fw-semibold mb-2">SEO</h6>
  <label>SEO başlık</label>
  <input type="text" name="seo_title" maxlength="120" value="<?= ded_h((string) $seoCur['title']) ?>">
  <label>Meta açıklama (özel)</label>
  <textarea name="seo_description" rows="3" maxlength="320"><?= ded_h((string) $seoCur['description']) ?></textarea>
  <label>OG görsel</label>
  <input type="text" name="seo_image" value="<?= ded_h((string) $seoCur['image']) ?>">
  <label class="d-flex align-items-center gap-2 mt-2">
    <input type="checkbox" name="seo_noindex" value="1"<?= !empty($seoCur['noindex']) ? ' checked' : '' ?>>
    Arama motorlarına gösterme
  </label>
<?php yonetim_form_close(); ?>

<?php if (!$isNew && $originalSlug !== '') { ?>
<form method="post" class="mt-3" onsubmit="return confirm('Bu sayfa kalıcı olarak silinsin mi?');">
  <input type="hidden" name="action" value="delete_page">
  <input type="hidden" name="slug" value="<?= ded_h($originalSlug) ?>">
  <input type="hidden" name="confirm_delete" value="1">
  <button type="submit" class="btn btn-sm btn-outline-danger">Sayfayı sil</button>
</form>
<?php } ?>

<?php yonetim_layout_end(); ?>

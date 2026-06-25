<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/vitrinlayout.php';

yonetim_require_login();

$saveErr = '';
$cat = yonetim_catalog_get();
if ($cat === null) {
    yonetim_layout_start('Site & marka');
    yonetim_alert('danger', 'Katalog yüklenemedi.');
    yonetim_layout_end();
    exit;
}

$s = $cat['site'];
$pdo = yonetim_shop_pdo();
$panel = [];
$logoAlt = '';
$storeLogo = '';
if ($pdo !== null) {
    $L = ded_vitrin_layout_load($pdo);
    $panel = is_array($L['panel'] ?? null) ? $L['panel'] : [];
    $logoAlt = (string) ($L['logo_alt'] ?? '');
    $storeLogo = (string) ($L['logo_path'] ?? '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s['name'] = trim((string) ($_POST['name'] ?? ''));
    $s['title'] = trim((string) ($_POST['title'] ?? ''));
    $s['description'] = trim((string) ($_POST['description'] ?? ''));
    $s['homeCollectionHeading'] = trim((string) ($_POST['homeCollectionHeading'] ?? ''));
    $s['homeCollectionSubtext'] = trim((string) ($_POST['homeCollectionSubtext'] ?? ''));
    $s['homeCollectionMoreUrl'] = trim((string) ($_POST['homeCollectionMoreUrl'] ?? '#')) ?: '#';
    $cat['site'] = $s;

    try {
        yonetim_catalog_save($cat);
        if ($pdo !== null) {
            $L = ded_vitrin_layout_load($pdo);
            $L['logo_alt'] = trim((string) ($_POST['logo_alt'] ?? '')) ?: $s['name'];
            $L['logo_path'] = trim((string) ($_POST['logo_path'] ?? ''));
            $L['panel'] = [
                'name' => trim((string) ($_POST['panel_name'] ?? '')),
                'logo_sm' => trim((string) ($_POST['panel_logo_sm'] ?? '')),
                'logo_light' => trim((string) ($_POST['panel_logo_light'] ?? '')),
                'logo_dark' => trim((string) ($_POST['panel_logo_dark'] ?? '')),
                'favicon' => trim((string) ($_POST['panel_favicon'] ?? '')),
                'footer_line' => trim((string) ($_POST['panel_footer_line'] ?? '')),
            ];
            ded_vitrin_layout_save($pdo, $L);
            yonetim_brand_reset_cache();
        }
        yonetim_flash('Kaydedildi.');
        yonetim_redirect('site');
        exit;
    } catch (Throwable $e) {
        $saveErr = $e->getMessage();
    }
}

yonetim_layout_start('Site & marka');
yonetim_page_header('Site & marka', 'dashboard', [
    ['href' => 'vitrin', 'label' => 'Vitrin', 'class' => 'btn btn-sm btn-light border'],
]);
if ($saveErr !== '') {
    yonetim_alert('danger', $saveErr);
}
yonetim_form_open();
?>
<div class="card border-0 shadow-sm mb-3">
  <div class="card-body">
    <h6 class="fw-semibold mb-3">Mağaza</h6>
    <label>Ad</label>
    <input type="text" name="name" value="<?= ded_h((string) ($s['name'] ?? '')) ?>">
    <label>Logo</label>
    <input type="text" name="logo_path" value="<?= ded_h($storeLogo) ?>">
    <label>Logo alt</label>
    <input type="text" name="logo_alt" value="<?= ded_h($logoAlt) ?>">
    <label>Başlık</label>
    <input type="text" name="title" value="<?= ded_h((string) ($s['title'] ?? '')) ?>">
    <label>Açıklama</label>
    <textarea name="description" rows="3"><?= ded_h((string) ($s['description'] ?? '')) ?></textarea>
    <label>Vitrin başlığı</label>
    <input type="text" name="homeCollectionHeading" value="<?= ded_h((string) ($s['homeCollectionHeading'] ?? '')) ?>">
    <label>Vitrin alt metin</label>
    <textarea name="homeCollectionSubtext" rows="2"><?= ded_h((string) ($s['homeCollectionSubtext'] ?? '')) ?></textarea>
    <label>Daha fazla link</label>
    <input type="text" name="homeCollectionMoreUrl" value="<?= ded_h((string) ($s['homeCollectionMoreUrl'] ?? '#')) ?>">
  </div>
</div>

<div class="card border-0 shadow-sm mb-3">
  <div class="card-body">
    <h6 class="fw-semibold mb-3">Panel</h6>
    <label>Ad</label>
    <input type="text" name="panel_name" value="<?= ded_h((string) ($panel['name'] ?? '')) ?>">
    <label>Logo küçük</label>
    <input type="text" name="panel_logo_sm" value="<?= ded_h((string) ($panel['logo_sm'] ?? '')) ?>">
    <label>Logo açık</label>
    <input type="text" name="panel_logo_light" value="<?= ded_h((string) ($panel['logo_light'] ?? '')) ?>">
    <label>Logo koyu</label>
    <input type="text" name="panel_logo_dark" value="<?= ded_h((string) ($panel['logo_dark'] ?? '')) ?>">
    <label>Favicon</label>
    <input type="text" name="panel_favicon" value="<?= ded_h((string) ($panel['favicon'] ?? '')) ?>">
    <label>Alt bilgi</label>
    <input type="text" name="panel_footer_line" value="<?= ded_h((string) ($panel['footer_line'] ?? '')) ?>">
  </div>
</div>
<?php yonetim_form_close(); ?>
<?php yonetim_layout_end(); ?>

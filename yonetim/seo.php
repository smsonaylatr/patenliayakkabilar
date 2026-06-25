<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/ayarlarui.php';
require_once dirname(__DIR__) . '/lib/ekstra.php';

yonetim_require_login();
$pdo = yonetim_settings_require_pdo();
$base = yonetim_ded_public_url();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patch = [
        'tax_rate_percent' => (float) ($_POST['tax_rate_percent'] ?? 20),
        'tax_included_in_prices' => isset($_POST['tax_included_in_prices']),
        'maintenance_mode' => isset($_POST['maintenance_mode']),
        'maintenance_message' => trim((string) ($_POST['maintenance_message'] ?? '')),
        'ga4_measurement_id' => trim((string) ($_POST['ga4_measurement_id'] ?? '')),
        'meta_pixel_id' => trim((string) ($_POST['meta_pixel_id'] ?? '')),
        'google_site_verification' => trim((string) ($_POST['google_site_verification'] ?? '')),
        'robots_txt_extra' => trim((string) ($_POST['robots_txt_extra'] ?? '')),
        'og_default_image' => trim((string) ($_POST['og_default_image'] ?? '')),
        'twitter_site' => trim((string) ($_POST['twitter_site'] ?? '')),
        'twitter_creator' => trim((string) ($_POST['twitter_creator'] ?? '')),
        'social_links' => trim((string) ($_POST['social_links'] ?? '')),
        'seo_default_keywords' => trim((string) ($_POST['seo_default_keywords'] ?? '')),
        'store_phone' => trim((string) ($_POST['store_phone'] ?? '')),
        'store_email' => trim((string) ($_POST['store_email'] ?? '')),
        'store_address' => trim((string) ($_POST['store_address'] ?? '')),
    ];
    yonetim_settings_save($pdo, $patch);
    yonetim_flash('Kaydedildi.');
    yonetim_redirect('seo');
    exit;
}

$s = ded_shop_settings_get($pdo);
yonetim_layout_start('SEO');
yonetim_page_header('SEO', 'dashboard');
yonetim_form_open();
?>
<div class="row g-3">
  <div class="col-lg-6">
    <h5 class="fw-semibold mb-2">Sosyal paylaşım</h5>
    <label>Varsayılan OG/Twitter görseli</label>
    <input type="text" name="og_default_image" value="<?= ded_h((string) ($s['og_default_image'] ?? '')) ?>">
    <label>Twitter @hesap</label>
    <input type="text" name="twitter_site" value="<?= ded_h((string) ($s['twitter_site'] ?? '')) ?>" placeholder="@marka">
    <label>Twitter yazar</label>
    <input type="text" name="twitter_creator" value="<?= ded_h((string) ($s['twitter_creator'] ?? '')) ?>" placeholder="@yazar">
    <label>Sosyal bağlantılar (satır başına bir URL)</label>
    <textarea name="social_links" rows="4"><?= ded_h((string) ($s['social_links'] ?? '')) ?></textarea>

    <h5 class="fw-semibold mb-2 mt-4">İletişim (Organization)</h5>
    <label>Telefon</label>
    <input type="text" name="store_phone" value="<?= ded_h((string) ($s['store_phone'] ?? '')) ?>">
    <label>E-posta</label>
    <input type="email" name="store_email" value="<?= ded_h((string) ($s['store_email'] ?? '')) ?>">
    <label>Adres</label>
    <textarea name="store_address" rows="2"><?= ded_h((string) ($s['store_address'] ?? '')) ?></textarea>

    <h5 class="fw-semibold mb-2 mt-4">KDV</h5>
    <label>Oran (%)</label>
    <input type="number" step="0.01" name="tax_rate_percent" value="<?= ded_h((string) ($s['tax_rate_percent'] ?? '20')) ?>">
    <label class="d-flex align-items-center gap-2 mt-2">
      <input type="checkbox" name="tax_included_in_prices" value="1" <?= !empty($s['tax_included_in_prices']) ? 'checked' : '' ?>>
      Fiyatlar KDV dahil
    </label>

    <h5 class="fw-semibold mb-2 mt-4">Bakım</h5>
    <label class="d-flex align-items-center gap-2">
      <input type="checkbox" name="maintenance_mode" value="1" <?= !empty($s['maintenance_mode']) ? 'checked' : '' ?>>
      Site kapalı
    </label>
    <label>Mesaj</label>
    <textarea name="maintenance_message" rows="2"><?= ded_h((string) ($s['maintenance_message'] ?? '')) ?></textarea>
  </div>

  <div class="col-lg-6">
    <h5 class="fw-semibold mb-2">Analitik</h5>
    <label>GA4</label>
    <input type="text" name="ga4_measurement_id" value="<?= ded_h((string) ($s['ga4_measurement_id'] ?? '')) ?>" placeholder="G-XXXXXXXX">
    <label>Meta Pixel</label>
    <input type="text" name="meta_pixel_id" value="<?= ded_h((string) ($s['meta_pixel_id'] ?? '')) ?>" placeholder="1234567890">
    <label>Search Console</label>
    <input type="text" name="google_site_verification" value="<?= ded_h((string) ($s['google_site_verification'] ?? '')) ?>">

    <h5 class="fw-semibold mb-2 mt-4">Anahtar kelimeler</h5>
    <label>Varsayılan (virgülle)</label>
    <textarea name="seo_default_keywords" rows="2"><?= ded_h((string) ($s['seo_default_keywords'] ?? '')) ?></textarea>

    <h5 class="fw-semibold mb-2 mt-4">robots.txt ek satırlar</h5>
    <textarea name="robots_txt_extra" rows="4" placeholder="Disallow: /ozel/"><?= ded_h((string) ($s['robots_txt_extra'] ?? '')) ?></textarea>
  </div>
</div>
<?php yonetim_form_close('Kaydet'); ?>

<?php yonetim_panel_open('Sitemap / robots'); ?>
<ul class="list-unstyled mb-0 fs-13">
  <li class="mb-2"><a href="<?= ded_h($base . 'sitemap.xml') ?>" target="_blank" rel="noopener"><?= ded_h($base . 'sitemap.xml') ?></a></li>
  <li><a href="<?= ded_h($base . 'robots.txt') ?>" target="_blank" rel="noopener"><?= ded_h($base . 'robots.txt') ?></a></li>
</ul>
<?php
yonetim_panel_close();
yonetim_layout_end();

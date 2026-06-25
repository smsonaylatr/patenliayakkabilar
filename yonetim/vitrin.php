<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/vitrinlayout.php';

yonetim_require_login();

$pdo = yonetim_shop_pdo();
if ($pdo === null) {
    yonetim_layout_start('Vitrin');
    yonetim_alert('danger', 'Veritabanı bağlantısı yok. config.local.php ve schema.sql içe aktarımını kontrol edin.');
    yonetim_layout_end();
    exit;
}

$saveErr = '';
$L = ded_vitrin_layout_load($pdo);
$tab = (string) ($_GET['tab'] ?? 'genel');
$allowedTabs = ['genel', 'menu', 'footer', 'anasayfa'];
if (!in_array($tab, $allowedTabs, true)) {
    $tab = 'genel';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tab = (string) ($_POST['tab'] ?? 'genel');
    if (!in_array($tab, $allowedTabs, true)) {
        $tab = 'genel';
    }

    if ($tab === 'genel') {
        $L['logo_path'] = trim((string) ($_POST['logo_path'] ?? ''));
        $L['logo_alt'] = trim((string) ($_POST['logo_alt'] ?? ''));
    } elseif ($tab === 'menu') {
        $L['header_menu'] = ded_vitrin_layout_menu_from_post('header');
    } elseif ($tab === 'footer') {
        $L['footer_menu'] = ded_vitrin_layout_menu_from_post('footer');
        $L['footer'] = array_merge($L['footer'] ?? [], [
            'newsletter_heading' => trim((string) ($_POST['footer_newsletter_heading'] ?? '')),
            'policies_title' => trim((string) ($_POST['footer_policies_title'] ?? '')),
            'about_title' => trim((string) ($_POST['footer_about_title'] ?? '')),
            'about_text' => trim((string) ($_POST['footer_about_text'] ?? '')),
            'copyright' => trim((string) ($_POST['footer_copyright'] ?? '')),
            'show_payment_icons' => isset($_POST['footer_show_payment_icons']),
        ]);
    } else {
        $home = $L['home'] ?? [];
        $home['hero'] = [
            'image_desktop' => trim((string) ($_POST['hero_image_desktop'] ?? '')),
            'image_mobile' => trim((string) ($_POST['hero_image_mobile'] ?? '')),
            'subheading' => trim((string) ($_POST['hero_subheading'] ?? '')),
            'heading' => trim((string) ($_POST['hero_heading'] ?? '')),
        ];
        $home['scrolling_text'] = trim((string) ($_POST['scrolling_text'] ?? ''));
        $home['featured_titles'] = [
            trim((string) ($_POST['featured_title_0'] ?? '')),
            trim((string) ($_POST['featured_title_1'] ?? '')),
            trim((string) ($_POST['featured_title_2'] ?? '')),
        ];
        $home['featured_collection_slugs'] = [
            trim((string) ($_POST['featured_slug_0'] ?? '')),
            trim((string) ($_POST['featured_slug_1'] ?? '')),
            trim((string) ($_POST['featured_slug_2'] ?? '')),
        ];
        $home['image_text'] = [
            'image' => trim((string) ($_POST['image_text_image'] ?? '')),
            'heading' => trim((string) ($_POST['image_text_heading'] ?? '')),
            'body_html' => (string) ($_POST['image_text_body_html'] ?? ''),
        ];
        $home['video'] = [
            'youtube_id' => trim((string) ($_POST['video_youtube_id'] ?? '')),
            'heading' => trim((string) ($_POST['video_heading'] ?? '')),
        ];
        $home['rich_text'] = [
            'heading' => trim((string) ($_POST['rich_text_heading'] ?? '')),
            'body_html' => (string) ($_POST['rich_text_body_html'] ?? ''),
        ];
        $home['image_overlay'] = [
            'image' => trim((string) ($_POST['overlay_image'] ?? '')),
            'heading' => trim((string) ($_POST['overlay_heading'] ?? '')),
        ];
        $L['home'] = $home;
    }

    try {
        ded_vitrin_layout_save($pdo, $L);
        yonetim_flash('Vitrin düzeni kaydedildi.');
        yonetim_redirect('vitrin', ['tab' => $tab]);
        exit;
    } catch (Throwable $e) {
        $saveErr = $e->getMessage();
        $L = ded_vitrin_layout_merge($L);
    }
}

$footer = $L['footer'] ?? [];
$home = $L['home'] ?? [];
$hero = $home['hero'] ?? [];
$it = $home['image_text'] ?? [];
$video = $home['video'] ?? [];
$rt = $home['rich_text'] ?? [];
$ov = $home['image_overlay'] ?? [];
$featured = $home['featured_titles'] ?? ['', '', ''];
$featuredSlug = isset($home['featured_collection_slugs']) && is_array($home['featured_collection_slugs'])
    ? $home['featured_collection_slugs']
    : ['', '', ''];

$menuTypes = [
    'home' => 'Ana sayfa',
    'collections' => 'Koleksiyonlar',
    'collection' => 'Koleksiyon (slug)',
    'page' => 'Sayfa (slug)',
    'search' => 'Arama',
    'cart' => 'Sepet',
    'custom' => 'Özel URL',
];

function yonetim_vitrin_menu_rows(string $prefix, array $items, array $menuTypes): void
{
    if ($items === []) {
        $items = [['label' => '', 'type' => 'home', 'slug' => '', 'url' => '']];
    }
    ?>
<div class="table-responsive mb-2">
  <table class="table table-sm align-middle mb-0" data-menu-table="<?= ded_h($prefix) ?>">
    <thead>
      <tr class="text-muted fs-12">
        <th>Etiket</th>
        <th style="width:160px">Tür</th>
        <th>Slug / URL</th>
        <th style="width:48px"></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item) { ?>
      <tr>
        <td><input type="text" class="form-control form-control-sm" name="<?= ded_h($prefix) ?>_label[]" value="<?= ded_h((string) ($item['label'] ?? '')) ?>"></td>
        <td>
          <select class="form-select form-select-sm" name="<?= ded_h($prefix) ?>_type[]">
            <?php foreach ($menuTypes as $k => $lbl) { ?>
            <option value="<?= ded_h($k) ?>"<?= (($item['type'] ?? '') === $k) ? ' selected' : '' ?>><?= ded_h($lbl) ?></option>
            <?php } ?>
          </select>
        </td>
        <td>
          <input type="text" class="form-control form-control-sm" name="<?= ded_h($prefix) ?>_slug[]" value="<?= ded_h((string) ($item['slug'] ?? '')) ?>" placeholder="slug">
          <input type="text" class="form-control form-control-sm mt-1" name="<?= ded_h($prefix) ?>_url[]" value="<?= ded_h((string) ($item['url'] ?? '')) ?>">
        </td>
        <td><button type="button" class="btn btn-sm btn-light border js-menu-del" title="Sil">×</button></td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>
<button type="button" class="btn btn-sm btn-light border js-menu-add" data-prefix="<?= ded_h($prefix) ?>">+ Satır ekle</button>
    <?php
}

yonetim_layout_start('Vitrin düzeni');
yonetim_page_header('Vitrin düzeni', 'dashboard', [
    ['href' => ded_storefront_public_url(), 'label' => 'Siteyi aç', 'class' => 'btn btn-sm btn-light border'],
    ['href' => 'site', 'label' => 'Site & marka', 'class' => 'btn btn-sm btn-light border'],
]);
if ($saveErr !== '') {
    yonetim_alert('danger', $saveErr);
}
?>
<ul class="nav nav-tabs mb-3" role="tablist">
  <?php foreach (['genel' => 'Logo', 'menu' => 'Menü', 'footer' => 'Footer', 'anasayfa' => 'Ana sayfa'] as $k => $lbl) { ?>
  <li class="nav-item">
    <a class="nav-link<?= $tab === $k ? ' active' : '' ?>" href="<?= ded_h(yonetim_url('vitrin', ['tab' => $k])) ?>"><?= ded_h($lbl) ?></a>
  </li>
  <?php } ?>
</ul>

<?php yonetim_form_open(['id' => 'vitrin-form']); ?>
<input type="hidden" name="tab" value="<?= ded_h($tab) ?>">

<?php if ($tab === 'genel') { ?>
  <label>Logo</label>
  <input type="text" name="logo_path" value="<?= ded_h((string) ($L['logo_path'] ?? '')) ?>">
  <label>Logo alt metni</label>
  <input type="text" name="logo_alt" value="<?= ded_h((string) ($L['logo_alt'] ?? '')) ?>">
<?php } elseif ($tab === 'menu') { ?>
  <h6 class="fw-semibold">Menü</h6>
  <?php yonetim_vitrin_menu_rows('header', $L['header_menu'] ?? [], $menuTypes); ?>
<?php } elseif ($tab === 'footer') { ?>
  <label>Bülten başlığı</label>
  <input type="text" name="footer_newsletter_heading" value="<?= ded_h((string) ($footer['newsletter_heading'] ?? '')) ?>">
  <label>Politikalar sütun başlığı</label>
  <input type="text" name="footer_policies_title" value="<?= ded_h((string) ($footer['policies_title'] ?? '')) ?>">
  <h6 class="fw-semibold mt-3">Footer menü</h6>
  <?php yonetim_vitrin_menu_rows('footer', $L['footer_menu'] ?? [], $menuTypes); ?>
  <label class="mt-3">Hakkında başlığı</label>
  <input type="text" name="footer_about_title" value="<?= ded_h((string) ($footer['about_title'] ?? '')) ?>">
  <label>Hakkında metni</label>
  <textarea name="footer_about_text" rows="4"><?= ded_h((string) ($footer['about_text'] ?? '')) ?></textarea>
  <label>Telif metni</label>
  <input type="text" name="footer_copyright" value="<?= ded_h((string) ($footer['copyright'] ?? '')) ?>">
  <div class="form-check mt-2">
    <input class="form-check-input" type="checkbox" name="footer_show_payment_icons" id="payicons" value="1"<?= !empty($footer['show_payment_icons']) ? ' checked' : '' ?>>
    <label class="form-check-label" for="payicons">Ödeme ikonlarını göster</label>
  </div>
<?php } else { ?>
  <h6 class="fw-semibold">Banner</h6>
  <label>Masaüstü görsel</label>
  <input type="text" name="hero_image_desktop" value="<?= ded_h((string) ($hero['image_desktop'] ?? '')) ?>">
  <label>Mobil görsel</label>
  <input type="text" name="hero_image_mobile" value="<?= ded_h((string) ($hero['image_mobile'] ?? '')) ?>">
  <label>Üst satır</label>
  <input type="text" name="hero_subheading" value="<?= ded_h((string) ($hero['subheading'] ?? '')) ?>">
  <label>Ana başlık</label>
  <input type="text" name="hero_heading" value="<?= ded_h((string) ($hero['heading'] ?? '')) ?>">

  <h6 class="fw-semibold mt-4">Kayan yazı</h6>
  <input type="text" name="scrolling_text" value="<?= ded_h((string) ($home['scrolling_text'] ?? '')) ?>">

  <h6 class="fw-semibold mt-4">Öne çıkan şeritler (ana sayfa)</h6>
  <p class="text-muted small">Şablon sırasıyla <strong>1: Çocuk</strong>, <strong>2: Erkek</strong>, <strong>3: Kadın</strong>. Her satır koleksiyon <code class="small">slug</code>’ı yazın; boş ise varsayılan slug kullanılır.</p>
  <?php
  $slugHints = ['cocuk-tekerlekli-ayakkabi', 'erkek-tekerlekli-ayakkabi', 'kadin-tekerlekli-ayakkabi'];
  for ($i = 0; $i < 3; $i++) {
      $ttl = ded_h((string) ($featured[$i] ?? ''));
      $sl = ded_h((string) ($featuredSlug[$i] ?? ''));
      $ph = ded_attr($slugHints[$i] ?? '');
      ?>
  <div class="mb-3 pb-3 border-bottom">
    <label class="fw-semibold d-block"><?= ded_h(sprintf('Şerit %d başlığı', $i + 1)) ?></label>
    <input type="text" name="featured_title_<?= (int) $i ?>" value="<?= $ttl ?>" class="form-control form-control-sm mb-2">
    <label class="small text-muted d-block">Koleksiyon slug</label>
    <input type="text" name="featured_slug_<?= (int) $i ?>" value="<?= $sl ?>" class="form-control form-control-sm font-monospace" placeholder="<?= $ph ?>">
  </div>
  <?php } ?>

  <h6 class="fw-semibold mt-4">Görsel ve metin</h6>
  <label>Görsel</label>
  <input type="text" name="image_text_image" value="<?= ded_h((string) ($it['image'] ?? '')) ?>">
  <label>Başlık</label>
  <input type="text" name="image_text_heading" value="<?= ded_h((string) ($it['heading'] ?? '')) ?>">
  <label>İçerik</label>
  <textarea name="image_text_body_html" rows="6"><?= ded_h((string) ($it['body_html'] ?? '')) ?></textarea>

  <h6 class="fw-semibold mt-4">YouTube video</h6>
  <label>Video ID</label>
  <input type="text" name="video_youtube_id" value="<?= ded_h((string) ($video['youtube_id'] ?? '')) ?>">
  <label>Üst yazı</label>
  <input type="text" name="video_heading" value="<?= ded_h((string) ($video['heading'] ?? '')) ?>">

  <h6 class="fw-semibold mt-4">Metin</h6>
  <label>Başlık</label>
  <input type="text" name="rich_text_heading" value="<?= ded_h((string) ($rt['heading'] ?? '')) ?>">
  <label>Paragraf</label>
  <textarea name="rich_text_body_html" rows="5"><?= ded_h((string) ($rt['body_html'] ?? '')) ?></textarea>

  <h6 class="fw-semibold mt-4">Kapak</h6>
  <label>Görsel</label>
  <input type="text" name="overlay_image" value="<?= ded_h((string) ($ov['image'] ?? '')) ?>">
  <label>Yazı</label>
  <input type="text" name="overlay_heading" value="<?= ded_h((string) ($ov['heading'] ?? '')) ?>">
<?php } ?>

<?php yonetim_form_close(); ?>

<template id="menu-row-tpl">
  <tr>
    <td><input type="text" class="form-control form-control-sm" data-name="label"></td>
    <td><select class="form-select form-select-sm" data-name="type">
      <?php foreach ($menuTypes as $k => $lbl) { ?>
      <option value="<?= ded_h($k) ?>"><?= ded_h($lbl) ?></option>
      <?php } ?>
    </select></td>
    <td>
      <input type="text" class="form-control form-control-sm" data-name="slug" placeholder="slug">
      <input type="text" class="form-control form-control-sm mt-1" data-name="url">
    </td>
    <td><button type="button" class="btn btn-sm btn-light border js-menu-del">×</button></td>
  </tr>
</template>
<?php
yonetim_layout_script(<<<'JS'
<script>
document.querySelectorAll('.js-menu-add').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var prefix = btn.getAttribute('data-prefix');
    var table = document.querySelector('[data-menu-table="' + prefix + '"] tbody');
    var tpl = document.getElementById('menu-row-tpl');
    if (!table || !tpl) return;
    var row = tpl.content.cloneNode(true);
    row.querySelectorAll('[data-name]').forEach(function(el) {
      var n = el.getAttribute('data-name');
      el.name = prefix + '_' + n + '[]';
    });
    table.appendChild(row);
  });
});
document.addEventListener('click', function(e) {
  if (e.target.classList.contains('js-menu-del')) {
    var tr = e.target.closest('tr');
    var tbody = tr && tr.parentElement;
    if (tbody && tbody.querySelectorAll('tr').length > 1) tr.remove();
  }
});
</script>
JS
);
yonetim_layout_end();

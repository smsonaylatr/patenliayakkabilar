<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/medyakutuphane.php';
require_once __DIR__ . '/inc/urunyukle.php';

yonetim_require_login();

$action = (string) ($_POST['action'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== '') {
    try {
        if ($action === 'upload') {
            $uploaded = yonetim_media_save_uploads();
            $msg = $uploaded === []
                ? 'Yüklenecek dosya seçilmedi.'
                : count($uploaded) . ' dosya yüklendi.';
            yonetim_flash($msg);
        } elseif ($action === 'delete') {
            $paths = yonetim_media_paths_from_post();
            $deleted = 0;
            $refs = 0;
            foreach ($paths as $rel) {
                $norm = yonetim_media_normalize_rel($rel);
                if ($norm === null) {
                    continue;
                }
                $refs += yonetim_media_detach_references($norm);
                yonetim_media_delete_file($norm);
                $deleted++;
            }
            yonetim_flash($deleted . ' dosya silindi' . ($refs > 0 ? " · {$refs} katalog bağlantısı kaldırıldı" : '') . '.');
        } elseif ($action === 'detach') {
            $paths = yonetim_media_paths_from_post();
            $refs = 0;
            foreach ($paths as $rel) {
                $norm = yonetim_media_normalize_rel($rel);
                if ($norm !== null) {
                    $refs += yonetim_media_detach_references($norm);
                }
            }
            yonetim_flash($refs > 0 ? "{$refs} katalog bağlantısı kaldırıldı (dosyalar duruyor)." : 'Kaldırılacak bağlantı bulunamadı.');
        } elseif ($action === 'edit') {
            $rel = trim((string) ($_POST['path'] ?? ''));
            $newName = trim((string) ($_POST['new_name'] ?? ''));
            $alt = trim((string) ($_POST['alt'] ?? ''));
            $norm = yonetim_media_normalize_rel($rel);
            if ($norm === null) {
                throw new RuntimeException('Dosya bulunamadı.');
            }
            $final = $norm;
            if ($newName !== '' && $newName !== basename($norm)) {
                $final = yonetim_media_rename_file($norm, $newName);
            }
            yonetim_media_meta_save($final, $alt);
            yonetim_flash('Görsel güncellendi.');
        }
    } catch (Throwable $e) {
        yonetim_flash('Hata: ' . $e->getMessage());
    }
    yonetim_redirect('media');
    exit;
}

$liste = yonetim_media_library_list(500);
$flash = yonetim_flash();

yonetim_layout_start('Medya');
yonetim_panel_open('Görsel kütüphanesi');
?>
<?php if ($flash) { ?>
<div class="alert alert-info border-0 shadow-sm mb-3"><?= ded_h($flash) ?></div>
<?php } ?>

<form method="post" enctype="multipart/form-data" class="card border-0 shadow-sm mb-3">
  <input type="hidden" name="action" value="upload">
  <div class="card-body d-flex flex-wrap align-items-center gap-3">
    <div>
      <label class="form-label mb-1 fs-13">Yeni görsel yükle</label>
      <input type="file" name="media_files[]" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
    </div>
    <button type="submit" class="btn btn-primary btn-sm align-self-end">Yükle</button>
  </div>
</form>

<form method="post" id="yun-media-bulk-form">
  <div class="d-flex flex-wrap align-items-center gap-2 mb-3 yun-media-bulk-bar" id="yun-media-bulk-bar" hidden>
    <span class="fs-13 text-muted"><span id="yun-media-pick-count">0</span> seçili</span>
    <button type="submit" name="action" value="detach" class="btn btn-sm btn-light border" onclick="return confirm('Seçilen görseller ürün/koleksiyon kayıtlarından kaldırılsın mı? Dosyalar silinmez.')">Bağlantıyı kaldır</button>
    <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger" onclick="return confirm('Seçilen dosyalar kalıcı olarak silinsin mi?')">Sil</button>
  </div>

  <div class="row g-2" id="yun-media-grid-admin">
<?php foreach ($liste as $m) {
    $path = (string) ($m['path'] ?? '');
    if ($path === '') {
        continue;
    }
    $src = yonetim_product_image_admin_src($path);
    $usage = yonetim_media_usage_count($path);
    $alt = (string) ($m['alt'] ?? '');
    $size = yonetim_media_format_size((int) ($m['size'] ?? 0));
    ?>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
      <div class="card border shadow-sm h-100 yun-media-lib-card">
        <div class="position-relative">
          <input type="checkbox" class="form-check-input position-absolute top-0 start-0 m-2 yun-media-pick" name="paths[]" value="<?= ded_attr($path) ?>" form="yun-media-bulk-form">
          <img src="<?= ded_h($src) ?>" alt="<?= ded_attr($alt) ?>" class="card-img-top" style="aspect-ratio:1;object-fit:cover;cursor:pointer" data-yun-media-preview="<?= ded_attr($path) ?>">
        </div>
        <div class="card-body p-2">
          <div class="fw-semibold fs-12 text-truncate" title="<?= ded_attr((string) ($m['name'] ?? '')) ?>"><?= ded_h((string) ($m['name'] ?? '')) ?></div>
          <?php if ($alt !== '') { ?>
          <div class="text-muted fs-11 text-truncate" title="<?= ded_attr($alt) ?>"><?= ded_h($alt) ?></div>
          <?php } ?>
          <div class="d-flex justify-content-between align-items-center mt-1">
            <span class="text-muted fs-11"><?= ded_h($size) ?></span>
            <?php if ($usage > 0) { ?>
            <span class="badge bg-primary-subtle text-primary fs-10"><?= (int) $usage ?> kullanım</span>
            <?php } ?>
          </div>
          <div class="btn-group btn-group-sm w-100 mt-2" role="group">
            <button type="button" class="btn btn-light border fs-11" data-yun-media-edit="<?= ded_attr($path) ?>" data-name="<?= ded_attr((string) ($m['name'] ?? '')) ?>" data-alt="<?= ded_attr($alt) ?>">Düzenle</button>
            <button type="button" class="btn btn-light border fs-11" data-yun-media-detach="<?= ded_attr($path) ?>">Kaldır</button>
            <button type="button" class="btn btn-light border fs-11 text-danger" data-yun-media-delete="<?= ded_attr($path) ?>">Sil</button>
          </div>
        </div>
      </div>
    </div>
<?php } ?>
  </div>
</form>
<?php
yonetim_panel_close();
?>

<div class="modal fade" id="yun-media-edit-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" class="modal-content border-0 shadow">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="path" id="yun-media-edit-path" value="">
      <div class="modal-header border-bottom">
        <h5 class="modal-title">Görseli düzenle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fs-13">Dosya adı</label>
          <input type="text" class="form-control" name="new_name" id="yun-media-edit-name" autocomplete="off">
        </div>
        <div class="mb-0">
          <label class="form-label fs-13">Alt metin</label>
          <input type="text" class="form-control" name="alt" id="yun-media-edit-alt" maxlength="240">
        </div>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">İptal</button>
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
</div>

<form method="post" id="yun-media-single-form" class="d-none">
  <input type="hidden" name="path" id="yun-media-single-path" value="">
  <input type="hidden" name="paths[]" id="yun-media-single-paths" value="">
</form>

<script>
(function () {
  var bulkBar = document.getElementById('yun-media-bulk-bar');
  var pickCount = document.getElementById('yun-media-pick-count');
  var picks = document.querySelectorAll('.yun-media-pick');
  var editModalEl = document.getElementById('yun-media-edit-modal');
  var editModal = editModalEl && window.bootstrap ? new bootstrap.Modal(editModalEl) : null;
  var singleForm = document.getElementById('yun-media-single-form');

  function syncBulk() {
    var n = 0;
    picks.forEach(function (cb) { if (cb.checked) n++; });
    if (pickCount) pickCount.textContent = String(n);
    if (bulkBar) bulkBar.hidden = n < 1;
  }
  picks.forEach(function (cb) { cb.addEventListener('change', syncBulk); });

  function postSingle(action, path) {
    if (!singleForm || !path) return;
    singleForm.innerHTML = '';
    var a = document.createElement('input');
    a.type = 'hidden';
    a.name = 'action';
    a.value = action;
    singleForm.appendChild(a);
    var p = document.createElement('input');
    p.type = 'hidden';
    p.name = 'paths[]';
    p.value = path;
    singleForm.appendChild(p);
    singleForm.submit();
  }

  document.querySelectorAll('[data-yun-media-edit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var path = btn.getAttribute('data-yun-media-edit') || '';
      document.getElementById('yun-media-edit-path').value = path;
      document.getElementById('yun-media-edit-name').value = btn.getAttribute('data-name') || '';
      document.getElementById('yun-media-edit-alt').value = btn.getAttribute('data-alt') || '';
      if (editModal) editModal.show();
    });
  });

  document.querySelectorAll('[data-yun-media-detach]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var path = btn.getAttribute('data-yun-media-detach');
      if (!path || !confirm('Bu görsel ürün ve koleksiyon kayıtlarından kaldırılsın mı? Dosya silinmez.')) return;
      postSingle('detach', path);
    });
  });

  document.querySelectorAll('[data-yun-media-delete]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var path = btn.getAttribute('data-yun-media-delete');
      if (!path || !confirm('Bu dosya kalıcı olarak silinsin mi?')) return;
      postSingle('delete', path);
    });
  });

  document.querySelectorAll('[data-yun-media-preview]').forEach(function (img) {
    img.addEventListener('click', function (e) {
      if (e.target && e.target.classList && e.target.classList.contains('yun-media-pick')) return;
      var card = img.closest('.yun-media-lib-card');
      var cb = card && card.querySelector('.yun-media-pick');
      if (cb) { cb.checked = !cb.checked; syncBulk(); }
    });
  });
})();
</script>
<?php
yonetim_layout_end();

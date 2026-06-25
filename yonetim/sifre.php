<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/urunyukle.php';
require_once __DIR__ . '/inc/yerlesim.php';

yonetim_require_login();

$prof = yonetim_panel_profile();
$avatarAdminSrc = yonetim_panel_avatar_src();
$err = '';
$errPw = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = (string) ($_POST['section'] ?? '');

    if ($section === 'profile') {
        $cur = (string) ($_POST['profile_current_pw'] ?? '');
        $uname = (string) ($_POST['username'] ?? '');
        $email = (string) ($_POST['email'] ?? '');
        $removeAv = isset($_POST['remove_avatar']);
        $newRel = null;
        try {
            $up = yonetim_single_image_upload('avatar');
            if ($up !== null) {
                $newRel = $up;
            }
        } catch (Throwable $e) {
            $err = $e->getMessage();
        }
        if ($err === '') {
            $e2 = yonetim_profile_save($cur, $uname, $email, $newRel, $removeAv);
            if ($e2 !== null) {
                $err = $e2;
            } else {
                yonetim_flash('Profil güncellendi.');
                yonetim_redirect('password');
                exit;
            }
        }
        $prof = yonetim_panel_profile();
        $avatarAdminSrc = yonetim_panel_avatar_src();
    } elseif ($section === 'password') {
        $cur = (string) ($_POST['current'] ?? '');
        $n1 = (string) ($_POST['new1'] ?? '');
        $n2 = (string) ($_POST['new2'] ?? '');
        if ($n1 !== $n2) {
            $errPw = 'Yeni şifreler eşleşmiyor.';
        } elseif (strlen($n1) < 6) {
            $errPw = 'Yeni şifre en az 6 karakter olmalı.';
        } elseif (!yonetim_set_password($cur, $n1)) {
            $errPw = 'Mevcut şifre hatalı.';
        } else {
            yonetim_flash('Şifre güncellendi.');
            yonetim_redirect('dashboard');
            exit;
        }
    }
}

yonetim_layout_start('Profil ve güvenlik');
yonetim_page_header('Profil ve güvenlik', 'dashboard');
if ($err !== '') {
    yonetim_alert('danger', $err);
}
if ($errPw !== '') {
    yonetim_alert('danger', $errPw);
}
?>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="card-title mb-0">Profil</h5>
        <p class="text-muted small mb-0">Kullanıcı adı, e-posta ve profil fotoğrafı — değişiklik için mevcut şifrenizi girin.</p>
      </div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data" class="yun-form">
          <input type="hidden" name="section" value="profile">
          <div class="text-center mb-4">
            <?php if ($avatarAdminSrc !== '') { ?>
            <img src="<?= ded_h($avatarAdminSrc) ?>" alt="" class="rounded-circle border shadow-sm mb-2" width="96" height="96" style="object-fit:cover">
            <?php } else { ?>
            <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width:96px;height:96px">
              <i class="iconoir-user fs-1"></i>
            </div>
            <?php } ?>
            <div class="text-start">
              <label class="form-label small">Profil görseli</label>
              <input type="file" name="avatar" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/gif">
              <p class="text-muted small mt-1 mb-0">JPEG, PNG, WebP veya GIF — en fazla 8 MB (ürün görselleri ile aynı klasöre kaydedilir).</p>
              <?php if ($prof['avatar_path']) { ?>
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="yun-av-rm">
                <label class="form-check-label small" for="yun-av-rm">Mevcut görseli kaldır</label>
              </div>
              <?php } ?>
            </div>
          </div>
          <label>Kullanıcı adı <span class="text-danger">*</span></label>
          <input type="text" name="username" required maxlength="190" autocomplete="username"
            value="<?= ded_h($prof['username']) ?>" pattern="[\p{L}\p{N}._\-\s]{1,190}" title="Harf, rakam, boşluk, nokta, tire veya alt çizgi">
          <label>E-posta</label>
          <input type="email" name="email" autocomplete="email" placeholder="isteğe bağlı"
            value="<?= ded_h($prof['email']) ?>">
          <label>Mevcut şifre (doğrulama)</label>
          <input type="password" name="profile_current_pw" required autocomplete="current-password">
          <button type="submit" class="btn btn-primary mt-2">Profili kaydet</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="card-title mb-0">Şifre</h5>
        <p class="text-muted small mb-0">Hesap şifresini güncelle</p>
      </div>
      <div class="card-body">
        <?php yonetim_form_open(['class' => 'yun-form']); ?>
          <input type="hidden" name="section" value="password">
          <label>Mevcut şifre</label>
          <input type="password" name="current" required autocomplete="current-password">
          <label>Yeni şifre</label>
          <input type="password" name="new1" required minlength="6" autocomplete="new-password">
          <label>Yeni şifre (tekrar)</label>
          <input type="password" name="new2" required minlength="6" autocomplete="new-password">
        <?php yonetim_form_close('Şifreyi güncelle'); ?>
      </div>
    </div>
  </div>
</div>
<?php yonetim_layout_end(); ?>

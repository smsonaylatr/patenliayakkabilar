<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';

yonetim_ensure_auth_file();

if (yonetim_logged_in()) {
    yonetim_redirect('dashboard');
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim((string) ($_POST['username'] ?? ''));
    $pw = (string) ($_POST['password'] ?? '');
    if ($user === '' || $pw === '') {
        $err = 'Kullanıcı adı ve şifre girin.';
    } elseif (yonetim_attempt_login($user, $pw)) {
        session_regenerate_id(true);
        $_SESSION['yonetim_ok'] = 1;
        yonetim_session_set_profile_cache();
        yonetim_redirect('dashboard');
        exit;
    } else {
        $err = 'Kullanıcı adı veya şifre hatalı.';
    }
}

$brand = yonetim_brand();
$brandName = $brand['name'];
$brandLogo = $brand['logo_sm_url'];
$favicon = $brand['favicon_url'];
$initial = mb_strtoupper(mb_substr($brandName, 0, 1, 'UTF-8'), 'UTF-8');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="tr" dir="ltr" data-bs-theme="light">
<head>
<?php yonetim_theme_restore_script(); ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="robots" content="noindex,nofollow">
  <base href="<?= ded_h(yonetim_base_href()) ?>">
  <title>Giriş — <?= ded_h($brandName) ?></title>
  <link rel="shortcut icon" href="<?= ded_h($favicon) ?>">
  <link href="<?= ded_h(yonetim_rizz_asset('assets/css/bootstrap.min.css')) ?>" rel="stylesheet" type="text/css">
  <link href="<?= ded_h(yonetim_rizz_asset('assets/css/icons.min.css')) ?>" rel="stylesheet" type="text/css">
  <link href="<?= ded_h(yonetim_rizz_asset('assets/css/app.min.css')) ?>" rel="stylesheet" type="text/css">
  <link href="<?= ded_h(yonetim_panel_asset('rizz.css?v=11')) ?>" rel="stylesheet">
</head>
<body class="yun-auth-body">
  <div class="yun-auth-page d-flex align-items-center min-vh-100 py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-6 col-xxl-5">
          <div class="card border-0 shadow-lg yun-auth-card">
            <div class="card-body p-4 p-md-5">
              <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary mb-3" style="width:64px;height:64px">
                  <?php if ($brand['logo_sm'] !== '' || !str_contains($brandLogo, 'logo-sm.png')) { ?>
                    <img src="<?= ded_h($brandLogo) ?>" alt="" class="img-fluid" style="max-height:40px;object-fit:contain">
                  <?php } else { ?>
                    <span class="fw-bold fs-4"><?= ded_h($initial) ?></span>
                  <?php } ?>
                </div>
                <h1 class="h4 fw-bold mb-1"><?= ded_h($brandName) ?></h1>
                <p class="text-muted small mb-0">Yönetim paneline giriş</p>
              </div>

              <?php if ($err !== '') { ?>
              <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                <i class="iconoir-warning-triangle me-1"></i><?= ded_h($err) ?>
              </div>
              <?php } ?>

              <form method="post" class="vstack gap-3" autocomplete="on">
                <div>
                  <label for="yun-login-user" class="form-label small fw-semibold text-muted text-uppercase">Kullanıcı adı</label>
                  <div class="input-group">
                    <span class="input-group-text border-end-0 bg-body"><i class="iconoir-user text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="yun-login-user" name="username"
                      required autocomplete="username" autocapitalize="none" spellcheck="false"
                      placeholder="Örn. admin" value="<?= ded_h(trim((string) ($_POST['username'] ?? ''))) ?>">
                  </div>
                </div>
                <div>
                  <label for="pw" class="form-label small fw-semibold text-muted text-uppercase">Şifre</label>
                  <div class="input-group">
                    <span class="input-group-text border-end-0 bg-body"><i class="iconoir-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="pw" name="password"
                      required autocomplete="current-password" placeholder="••••••••">
            <button type="button" class="btn btn-light border border-start-0" data-toggle-pw aria-label="Şifreyi göster" tabindex="-1">
              <i class="las la-eye" data-eye-on></i>
              <i class="las la-eye-slash d-none" data-eye-off></i>
            </button>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg mt-2">
                  Giriş yap <i class="iconoir-nav-arrow-right ms-1"></i>
                </button>
              </form>

              <p class="text-center text-muted small mt-4 mb-0">
                <a href="../index.php" class="text-decoration-none">← Mağazaya dön</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
  (function () {
    var btn = document.querySelector('[data-toggle-pw]');
    var input = document.getElementById('pw');
    if (!btn || !input) return;
    var on = btn.querySelector('[data-eye-on]');
    var off = btn.querySelector('[data-eye-off]');
    btn.addEventListener('click', function () {
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      if (on && off) {
        on.classList.toggle('d-none', show);
        off.classList.toggle('d-none', !show);
      }
      btn.setAttribute('aria-label', show ? 'Şifreyi gizle' : 'Şifreyi göster');
      input.focus();
    });
  })();
  </script>
</body>
</html>

<?php declare(strict_types=1);

$brand = yonetim_brand();
$title = (string) ($GLOBALS['yonetim_layout_title'] ?? '');
$flash = $GLOBALS['yonetim_layout_flash'] ?? null;
$shopOk = (bool) ($GLOBALS['yonetim_shop_ok'] ?? false);
$greet = (string) ($GLOBALS['yonetim_greet'] ?? yonetim_greeting());
$yunPanelProf = yonetim_panel_profile();
$yunPanelAv = yonetim_panel_avatar_src();

?>
<!DOCTYPE html>
<html lang="tr" dir="ltr" data-startbar="light" data-bs-theme="light">
<head>
<?php yonetim_theme_restore_script(); ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <base href="<?= ded_h(yonetim_base_href()) ?>">
  <title><?= ded_h($title) ?> — <?= ded_h($brand['name']) ?></title>
  <link rel="shortcut icon" href="<?= ded_h($brand['favicon_url']) ?>">
  <link href="<?= ded_h(yonetim_rizz_asset('assets/css/bootstrap.min.css')) ?>" rel="stylesheet" type="text/css">
  <link href="<?= ded_h(yonetim_rizz_asset('assets/css/icons.min.css')) ?>" rel="stylesheet" type="text/css">
  <link href="<?= ded_h(yonetim_rizz_asset('assets/css/app.min.css')) ?>" rel="stylesheet" type="text/css">
  <link href="<?= ded_h(yonetim_panel_asset('rizz.css?v=11')) ?>" rel="stylesheet">
</head>
<body>
<div class="topbar d-print-none">
  <div class="container-xxl">
    <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">
      <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
        <li>
          <button type="button" class="nav-link mobile-menu-btn nav-icon" id="togglemenu" aria-label="Menü">
            <i class="iconoir-menu-scale"></i>
          </button>
        </li>
        <li class="mx-3 welcome-text d-none d-md-block">
          <h3 class="mb-0 fw-bold text-truncate"><?= ded_h($greet) ?>, <?= ded_h(yonetim_panel_display_label()) ?></h3>
          <h6 class="mb-0 fw-normal text-muted text-truncate fs-14"><?= ded_h($title) ?></h6>
        </li>
      </ul>
      <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
        <li class="hide-phone app-search">
          <form role="search" action="<?= ded_h(yonetim_url('products')) ?>" method="get">
            <input type="search" name="q" class="form-control top-search mb-0" placeholder="Ara…">
            <button type="submit" aria-label="Ara"><i class="iconoir-search"></i></button>
          </form>
        </li>
        <li class="topbar-item">
          <a class="nav-link nav-icon" href="javascript:void(0);" id="light-dark-mode" aria-label="Tema">
            <i class="icofont-moon dark-mode"></i>
            <i class="icofont-sun light-mode"></i>
          </a>
        </li>
        <li class="dropdown topbar-item">
          <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
            <?php if ($yunPanelAv !== '') { ?>
            <img src="<?= ded_h($yunPanelAv) ?>" alt="" width="40" height="40" class="rounded-circle object-fit-cover border" style="object-fit:cover">
            <?php } else { ?>
            <div class="thumb-md bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
              <i class="iconoir-user fs-5"></i>
            </div>
            <?php } ?>
          </a>
          <div class="dropdown-menu dropdown-menu-end py-0">
            <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle gap-2">
              <?php if ($yunPanelAv !== '') { ?>
              <img src="<?= ded_h($yunPanelAv) ?>" alt="" width="40" height="40" class="rounded-circle flex-shrink-0 border" style="object-fit:cover">
              <?php } else { ?>
              <div class="thumb-md bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="iconoir-user fs-5"></i>
              </div>
              <?php } ?>
              <div class="flex-grow-1 overflow-hidden">
                <h6 class="my-0 fw-medium text-dark fs-13 text-truncate"><?= ded_h(yonetim_panel_display_label()) ?></h6>
                <small class="text-muted mb-0 d-block text-truncate"><?= $yunPanelProf['email'] !== '' ? ded_h($yunPanelProf['email']) : ded_h($brand['name']) ?></small>
              </div>
            </div>
            <div class="dropdown-divider mt-0"></div>
            <a class="dropdown-item" href="<?= ded_h(yonetim_url('password')) ?>"><i class="las la-user-circle fs-18 me-1 align-text-bottom"></i> Profil ve güvenlik</a>
            <a class="dropdown-item text-danger" href="<?= ded_h(yonetim_url('logout')) ?>"><i class="las la-power-off fs-18 me-1 align-text-bottom"></i> Çıkış</a>
          </div>
        </li>
      </ul>
    </nav>
  </div>
</div>

<div class="startbar d-print-none">
  <div class="brand">
    <a href="<?= ded_h(yonetim_url('dashboard')) ?>" class="logo">
      <span><img src="<?= ded_h($brand['logo_sm_url']) ?>" alt="" class="logo-sm"></span>
      <span>
        <img src="<?= ded_h($brand['logo_light_url']) ?>" alt="<?= ded_h($brand['name']) ?>" class="logo-lg logo-light">
        <img src="<?= ded_h($brand['logo_dark_url']) ?>" alt="<?= ded_h($brand['name']) ?>" class="logo-lg logo-dark">
      </span>
    </a>
  </div>
  <div class="startbar-menu">
    <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
      <div class="d-flex align-items-start flex-column w-100">
        <ul class="navbar-nav mb-auto w-100 yun-sidebar-nav">
          <?php yonetim_render_sidebar_nav($shopOk); ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="startbar-overlay d-print-none"></div>

<div class="page-wrapper">
  <div class="page-content">
    <div class="container-xxl yun-rizz-content">
      <?php if (is_string($flash) && $flash !== '') { ?>
      <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <?= ded_h($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
      </div>
      <?php } ?>

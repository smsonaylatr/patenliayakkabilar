<?php declare(strict_types=1);

require_once __DIR__ . '/cekirdek.php';
require_once __DIR__ . '/oturum.php';
require_once __DIR__ . '/rotalar.php';
require_once __DIR__ . '/yollar.php';
require_once __DIR__ . '/tema.php';
require_once __DIR__ . '/arayuz.php';
require_once __DIR__ . '/brand.php';

function yonetim_flash(?string $set = null): ?string
{
    if ($set !== null) {
        $_SESSION['yonetim_flash'] = $set;
        return null;
    }
    $m = $_SESSION['yonetim_flash'] ?? null;
    unset($_SESSION['yonetim_flash']);

    return is_string($m) ? $m : null;
}

function yonetim_page_header(string $title, ?string $backHref = null, array $actions = []): void
{
    ?>
<div class="row align-items-center mb-3">
  <div class="col">
    <div class="d-flex align-items-center gap-2">
      <?php if ($backHref !== null && $backHref !== '') { ?>
      <a href="<?= ded_h(yonetim_url($backHref)) ?>" class="btn btn-sm btn-light border"><i class="iconoir-nav-arrow-left"></i></a>
      <?php } ?>
      <div>
        <h4 class="card-title mb-0"><?= ded_h($title) ?></h4>
      </div>
    </div>
  </div>
  <?php if ($actions !== []) { ?>
  <div class="col-auto d-flex flex-wrap gap-2">
    <?php foreach ($actions as $a) {
        $cls = (string) ($a['class'] ?? 'btn btn-sm btn-primary');
        ?>
    <a href="<?= ded_h(yonetim_resolve_href((string) $a['href'])) ?>" class="<?= ded_h($cls) ?>"><?= ded_h((string) $a['label']) ?></a>
    <?php } ?>
  </div>
  <?php } ?>
</div>
    <?php
}

function yonetim_layout_start(string $title): void
{
    require_once __DIR__ . '/magazakopru.php';
    if (yonetim_logged_in() && empty($_SESSION['yonetim_display_name'])) {
        yonetim_session_set_profile_cache();
    }
    $GLOBALS['yonetim_layout_title'] = $title;
    $GLOBALS['yonetim_layout_flash'] = yonetim_flash();
    $GLOBALS['yonetim_shop_ok'] = yonetim_shop_pdo() !== null;
    $GLOBALS['yonetim_greet'] = yonetim_greeting();

    header('Content-Type: text/html; charset=utf-8');
    require __DIR__ . '/header.php';
}

function yonetim_layout_script(string $html): void
{
    $GLOBALS['yonetim_layout_scripts'] = ($GLOBALS['yonetim_layout_scripts'] ?? '') . $html;
}

function yonetim_layout_end(): void
{
    require __DIR__ . '/footer.php';
}

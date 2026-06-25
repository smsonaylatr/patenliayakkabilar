<?php declare(strict_types=1);

function yonetim_greeting(): string
{
    $h = (int) date('G');
    if ($h >= 5 && $h < 12) {
        return 'Günaydın';
    }
    if ($h >= 12 && $h < 18) {
        return 'İyi günler';
    }

    return 'İyi akşamlar';
}

function yonetim_stat_card(string $label, string $value, string $icon, ?string $footnote = null, string $footnoteClass = 'text-success'): void
{
    ?>
<div class="col-md-6 col-xl-4 col-xxl-3">
  <div class="card">
    <div class="card-body">
      <div class="row d-flex justify-content-center border-dashed-bottom pb-3">
        <div class="col-9">
          <p class="text-dark mb-0 fw-semibold fs-14"><?= ded_h($label) ?></p>
          <h3 class="mt-2 mb-0 fw-bold"><?= ded_h($value) ?></h3>
        </div>
        <div class="col-3 align-self-center">
          <div class="d-flex justify-content-center align-items-center thumb-xl bg-primary-subtle rounded-circle mx-auto">
            <i class="<?= ded_h($icon) ?> h4 mb-0 text-primary"></i>
          </div>
        </div>
      </div>
      <?php if ($footnote !== null && $footnote !== '') { ?>
      <p class="mb-0 text-truncate text-muted mt-3"><span class="<?= ded_h($footnoteClass) ?>"><?= ded_h($footnote) ?></span></p>
      <?php } ?>
    </div>
  </div>
</div>
    <?php
}

function yonetim_panel_open(string $title, array $headerActions = []): void
{
    ?>
<div class="card">
  <div class="card-header">
    <div class="row align-items-center">
      <div class="col">
        <h4 class="card-title mb-0"><?= ded_h($title) ?></h4>
      </div>
      <?php if ($headerActions !== []) { ?>
      <div class="col-auto d-flex flex-wrap gap-2">
        <?php foreach ($headerActions as $a) {
            $cls = (string) ($a['class'] ?? 'btn btn-sm btn-primary');
            ?>
        <a href="<?= ded_h(yonetim_resolve_href((string) $a['href'])) ?>" class="<?= ded_h($cls) ?>"><?= ded_h((string) $a['label']) ?></a>
        <?php } ?>
      </div>
      <?php } ?>
    </div>
  </div>
  <div class="card-body pt-0">
    <?php
}

function yonetim_panel_close(): void
{
    echo '</div></div>';
}

function yonetim_table_responsive_open(): void
{
    echo '<div class="table-responsive"><table class="table mb-0 table-hover align-middle">';
}

function yonetim_table_responsive_close(): void
{
    echo '</table></div>';
}

function yonetim_alert(string $type, string $message): void
{
    $cls = match ($type) {
        'danger' => 'alert-danger',
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        default => 'alert-info',
    };
    ?>
<div class="alert <?= ded_h($cls) ?> border-0 shadow-sm mb-3" role="alert"><?= ded_h($message) ?></div>
    <?php
}

function yonetim_search_bar(
    string $q,
    string $placeholder,
    array $hiddenEk,
    string $actionUrl,
    string $alanAdi = 'q'
): void {
    ?>
<form method="get" action="<?= ded_h($actionUrl) ?>" class="card border-0 shadow-sm mb-3">
  <div class="card-body py-2">
    <div class="input-group">
      <?php foreach ($hiddenEk as $hk => $hv) { ?>
      <input type="hidden" name="<?= ded_h((string) $hk) ?>" value="<?= ded_h((string) $hv) ?>">
      <?php } ?>
      <span class="input-group-text bg-transparent border-end-0"><i class="iconoir-search"></i></span>
      <input type="search" name="<?= ded_h($alanAdi) ?>" class="form-control border-start-0"
        value="<?= ded_h($q) ?>" placeholder="<?= ded_h($placeholder) ?>" autocomplete="off">
      <button type="submit" class="btn btn-primary">Ara</button>
    </div>
  </div>
</form>
    <?php
}

function yonetim_form_open(array $attrs = []): void
{
    $class = (string) ($attrs['class'] ?? 'yun-card yun-form card border-0 shadow-sm');
    unset($attrs['class']);
    $buf = '<form method="post" class="' . ded_h($class) . '"';
    foreach ($attrs as $k => $v) {
        $buf .= ' ' . ded_h((string) $k) . '="' . ded_h((string) $v) . '"';
    }
    echo $buf . '>' . "\n";
}

function yonetim_form_close(string $submitEtiket = 'Kaydet'): void
{
    if ($submitEtiket !== '') {
        echo '<button type="submit" class="btn btn-primary">' . ded_h($submitEtiket) . '</button>' . "\n";
    }
    echo "</form>\n";
}

function yonetim_filter_pills(array $ogeler): void
{
    ?>
<div class="d-flex flex-wrap gap-2 mb-3">
  <?php foreach ($ogeler as $o) {
      $href = (string) ($o['href'] ?? '#');
      $label = (string) ($o['label'] ?? '');
      $on = !empty($o['active']);
      $cls = $on ? 'btn btn-primary btn-sm' : 'btn btn-sm btn-light border';
      ?>
  <a href="<?= ded_h($href) ?>" class="<?= ded_h($cls) ?>"><?= ded_h($label) ?></a>
  <?php } ?>
</div>
    <?php
}

function yonetim_trend_badge(float $yuzde, string $altMetin = ''): string
{
    $ok = abs($yuzde) < 0.05;
    if ($ok) {
        $arrow = '→';
        $cls = 'text-muted';
    } elseif ($yuzde > 0) {
        $arrow = '↑';
        $cls = 'text-success';
    } else {
        $arrow = '↓';
        $cls = 'text-danger';
    }
    $sayi = number_format(abs($yuzde), 1, ',', '.');
    $html = '<span class="fw-semibold ' . ded_h($cls) . '">' . ded_h($arrow) . ' %' . ded_h($sayi) . '</span>';
    if ($altMetin !== '') {
        $html .= ' <span class="text-muted fs-12">' . ded_h($altMetin) . '</span>';
    }

    return $html;
}

function yonetim_eco_summary_card(
    string $baslik,
    string $ikonSinif,
    string $sparkKutuId,
    string $anaDeger,
    string $trendHtml,
    string $baglantiEtiket,
    string $sayfaRota,
    bool $detayVurgus = false
): void {
    $bag = yonetim_url($sayfaRota);
    $btn = $detayVurgus ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light border';
    ?>
<div class="card border-0 shadow-sm mb-3">
  <div class="card-body">
    <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
      <div class="flex-grow-1 min-w-0">
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="d-inline-flex thumb-md bg-primary-subtle text-primary rounded-circle align-items-center justify-content-center flex-shrink-0">
            <i class="<?= ded_h($ikonSinif) ?> fs-5"></i>
          </span>
          <span class="text-muted fs-12 fw-semibold text-uppercase letter-spacing"><?= ded_h($baslik) ?></span>
        </div>
        <h3 class="mb-2 fw-bold text-dark"><?= ded_h($anaDeger) ?></h3>
        <div class="mb-0"><?= $trendHtml ?></div>
      </div>
      <div id="<?= ded_h($sparkKutuId) ?>" class="flex-shrink-0" style="min-width:120px;height:40px"></div>
    </div>
    <a href="<?= ded_h($bag) ?>" class="<?= ded_h($btn) ?> mt-3 d-inline-flex align-items-center gap-1">
      <?= ded_h($baglantiEtiket) ?><i class="iconoir-nav-arrow-right fs-14"></i>
    </a>
  </div>
</div>
    <?php
}

function yonetim_eco_mini_stat(string $deger, string $etiket): void
{
    ?>
  <div class="col-6 col-md-3">
    <div class="p-3 rounded border bg-body-secondary text-center mt-2">
      <p class="mb-0 fw-bold fs-16"><?= ded_h($deger) ?></p>
      <span class="text-muted fs-12"><?= ded_h($etiket) ?></span>
    </div>
  </div>
    <?php
}

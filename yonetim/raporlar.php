<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/magaza.php';

yonetim_require_login();
$pdo = yonetim_magaza_pdo();
if (!$pdo) {
    yonetim_layout_start('Raporlar');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

$gun = isset($_GET['days']) ? max(1, (int) $_GET['days']) : 30;
$ozet = ded_rapor_ozet($pdo, $gun);

yonetim_layout_start('Raporlar');
yonetim_panel_open('Satış özeti');
yonetim_filter_pills([
    ['href' => yonetim_url('reports', ['days' => 7]), 'label' => '7 gün', 'active' => $gun === 7],
    ['href' => yonetim_url('reports', ['days' => 30]), 'label' => '30 gün', 'active' => $gun === 30],
    ['href' => yonetim_url('reports', ['days' => 90]), 'label' => '90 gün', 'active' => $gun === 90],
]);
?>
<div class="row g-3">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted fs-12">Sipariş</span><h4 class="mb-0"><?= (int) $ozet['orders'] ?></h4></div></div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted fs-12">Ciro</span><h4 class="mb-0"><?= ded_h(ded_format_price_try_like_theme((float) $ozet['revenue'])) ?></h4></div></div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted fs-12">Sepet ort.</span><h4 class="mb-0"><?= ded_h(ded_format_price_try_like_theme((float) $ozet['avg_basket'])) ?></h4></div></div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted fs-12">Bekleyen / ödenmemiş</span><h4 class="mb-0"><?= (int) $ozet['pending'] ?> / <?= (int) $ozet['unpaid'] ?></h4></div></div>
  </div>
</div>
<?php
yonetim_panel_close();
yonetim_panel_open('Bağlantılar', [
    ['href' => yonetim_url('accounting'), 'label' => 'Muhasebe', 'class' => 'btn btn-sm btn-light border'],
    ['href' => yonetim_url('bestsellers'), 'label' => 'Çok satanlar', 'class' => 'btn btn-sm btn-light border'],
    ['href' => yonetim_url('order_export'), 'label' => 'Sipariş CSV', 'class' => 'btn btn-sm btn-primary'],
]);
yonetim_panel_close();
yonetim_layout_end();

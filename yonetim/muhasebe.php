<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once __DIR__ . '/inc/muhasebeveri.php';

yonetim_require_login();
$pdo = yonetim_shop_pdo();
if (!$pdo) {
    yonetim_layout_start('Muhasebe');
    echo '<p class="text-danger">Mağaza tabloları yok.</p>';
    yonetim_layout_end();
    exit;
}

$acc = yonetim_accounting_data($pdo);
$dash = $acc['dashboard'];
$revGrowth = (float) ($acc['rev_growth'] ?? 0);
$orderGrowth = 0.0;
$prevOrd = 0;
$months = $acc['months'];
if (count($months) >= 2) {
    $prev = $months[count($months) - 2];
    $cur = $months[count($months) - 1];
    $prevOrd = (int) ($prev['orders'] ?? 0);
    $curOrd = (int) ($cur['orders'] ?? 0);
    $orderGrowth = $prevOrd > 0
        ? round((($curOrd - $prevOrd) / $prevOrd) * 100, 1)
        : ($curOrd > 0 ? 100.0 : 0.0);
}

$chartPayload = [
    'spark_revenue' => $acc['spark_revenue'],
    'spark_orders' => $acc['spark_orders'],
    'chart_labels' => $acc['chart_labels'],
    'chart_revenue' => $acc['chart_revenue'],
    'chart_highlight' => $acc['chart_highlight'],
    'payment_paid' => $acc['payment_paid'],
    'payment_awaiting' => $acc['payment_awaiting'],
    'payment_other' => $acc['payment_other'],
];
$chartJson = json_encode($chartPayload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

yonetim_layout_script(
    '<script src="' . ded_h(yonetim_rizz_asset('libs/apexcharts/apexcharts.min.js')) . '"></script>'
    . '<script>window.YONETIM_ACCOUNTING = ' . $chartJson . ';</script>'
    . '<script src="' . ded_h(yonetim_panel_asset('grafikler.js?v=1')) . '"></script>'
);

yonetim_layout_start('Muhasebe');
?>

<div class="row">
  <div class="col-md-12 col-lg-12 col-xl-4">
    <div class="row">
      <div class="col-md-12 col-lg-6 col-xl-12">
        <?php yonetim_eco_summary_card(
            'Toplam ciro',
            'icofont-money-bag',
            'line-revenue',
            yonetim_format_money_tr((float) $acc['revenue_total']),
            yonetim_trend_badge($revGrowth, 'geçen aya göre'),
            'Siparişler',
            'orders',
            false
        ); ?>
      </div>
      <div class="col-md-12 col-lg-6 col-xl-12">
        <?php yonetim_eco_summary_card(
            'Sipariş',
            'icofont-opencart',
            'line-orders',
            (string) (int) ($dash['order_count'] ?? 0),
            yonetim_trend_badge($orderGrowth, 'geçen aya göre'),
            'Detay',
            'orders',
            true
        ); ?>
      </div>
    </div>
  </div>

  <div class="col-md-12 col-lg-12 col-xl-8">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col">
            <h4 class="card-title mb-0">Aylık ciro</h4>
          </div>
          <div class="col-auto">
            <span class="btn btn-light btn-sm disabled"><i class="icofont-calendar fs-5 me-1"></i> Son 12 ay</span>
          </div>
        </div>
      </div>
      <div class="card-body pt-0">
        <div id="monthly_income" class="apex-charts"></div>
        <div class="row">
          <?php
            yonetim_eco_mini_stat(yonetim_format_money_tr((float) $acc['today_revenue']), 'Bugünkü ciro');
yonetim_eco_mini_stat(number_format((float) $acc['conversion_rate'], 1, ',', '.') . '%', 'Ödeme oranı');
yonetim_eco_mini_stat((string) (int) $acc['pending_orders'], 'Bekleyen sipariş');
yonetim_eco_mini_stat(yonetim_format_money_tr((float) $acc['avg_order']), 'Ort. sepet');
?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-8">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col">
            <h4 class="card-title mb-0">Öne çıkan ürünler</h4>
          </div>
          <div class="col-auto">
            <a href="<?= ded_h(yonetim_url('bestsellers')) ?>" class="btn btn-light btn-sm">Çok satanlar</a>
          </div>
        </div>
      </div>
      <div class="card-body pt-0">
        <div class="table-responsive">
          <table class="table mb-0 table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th class="border-top-0">Ürün</th>
                <th class="border-top-0 text-end">Adet</th>
                <th class="border-top-0 text-end">Ciro</th>
                <th class="border-top-0">Durum</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $top = $dash['top_products'] ?? [];
if ($top === []) {
    echo '<tr><td colspan="4" class="text-muted text-center py-4">Henüz satış yok</td></tr>';
}
foreach ($top as $tp) {
    $slug = (string) ($tp['product_slug'] ?? '');
    $title = (string) ($tp['product_title'] ?? $slug);
    $sold = (int) ($tp['sold'] ?? 0);
    $rev = (float) ($tp['rev'] ?? 0);
    ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="thumb-md bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center me-3 flex-shrink-0">
                      <i class="iconoir-box fs-5"></i>
                    </div>
                    <div class="flex-grow-1 text-truncate">
                      <h6 class="m-0"><?= ded_h($title) ?></h6>
                      <span class="fs-12 text-primary"><?= ded_h($slug) ?></span>
                    </div>
                  </div>
                </td>
                <td class="text-end"><?= $sold ?></td>
                <td class="text-end fw-semibold"><?= ded_h(yonetim_format_money_tr($rev)) ?></td>
                <td><span class="badge bg-primary-subtle text-primary px-2">Satışta</span></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-4">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title mb-0">Ödeme dağılımı</h4>
      </div>
      <div class="card-body pt-0">
        <div id="payment_mix" class="apex-charts"></div>
        <div class="row text-center mt-2 g-2">
          <div class="col-4">
            <p class="mb-0 fw-semibold"><?= (int) $acc['payment_paid'] ?></p>
            <small class="text-muted">Ödendi</small>
          </div>
          <div class="col-4">
            <p class="mb-0 fw-semibold"><?= (int) $acc['payment_awaiting'] ?></p>
            <small class="text-muted">Havale</small>
          </div>
          <div class="col-4">
            <p class="mb-0 fw-semibold"><?= (int) $acc['payment_other'] ?></p>
            <small class="text-muted">Diğer</small>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title fs-16">Bu ay</h5>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Ciro</span>
          <span class="fw-semibold"><?= ded_h(yonetim_format_money_tr((float) $acc['month_revenue'])) ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Sipariş</span>
          <span class="fw-semibold"><?= (int) $acc['month_orders'] ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php yonetim_layout_end(); ?>

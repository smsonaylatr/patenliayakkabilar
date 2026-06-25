<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';

yonetim_require_login();

$cat = yonetim_catalog_get();
$nP = $cat ? count($cat['products']) : 0;
$nC = $cat ? count($cat['collections']) : 0;
$nPg = $cat ? count($cat['pages']) : 0;

$shopPdo = yonetim_shop_pdo();
$stats = null;
$recentOrders = [];
if ($shopPdo) {
    $stats = ded_stats_dashboard($shopPdo);
    $recent = ded_orders_list($shopPdo, 6, 0, null);
    $recentOrders = $recent['items'] ?? [];
}

yonetim_layout_start('Özet');
?>
<div class="row justify-content-center g-3">
<?php
yonetim_stat_card('Ürün', (string) $nP, 'iconoir-cart', 'Katalog');
yonetim_stat_card('Koleksiyon', (string) $nC, 'iconoir-view-grid', 'Vitrin');
yonetim_stat_card('Sayfa', (string) $nPg, 'iconoir-journal-page', 'İçerik');
yonetim_stat_card('Kaynak', ded_db_ready() ? 'MySQL' : 'Kapalı', 'iconoir-database', ded_db_ready() ? 'Canlı' : 'Bağlantı yok', 'text-primary');
if ($stats) {
    yonetim_stat_card('Sipariş', (string) (int) ($stats['order_count'] ?? 0), 'iconoir-shopping-bag', 'Toplam');
    yonetim_stat_card('Ciro', number_format((float) ($stats['revenue_total'] ?? 0), 0, ',', '.'), 'iconoir-hand-cash', 'TRY', 'text-success');
    yonetim_stat_card('Bekleyen', (string) (int) ($stats['pending_orders'] ?? 0), 'iconoir-clock', 'Sipariş', (int) ($stats['pending_orders'] ?? 0) > 0 ? 'text-warning' : 'text-muted');
}
?>
</div>

<?php if ($stats) { ?>
<div class="row g-3 mt-1">
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-body">
        <h4 class="card-title mb-3">Hızlı erişim</h4>
        <div class="d-grid gap-2">
          <a href="<?= ded_h(yonetim_url('product', ['new' => 1])) ?>" class="btn btn-primary"><i class="iconoir-plus me-1"></i> Yeni ürün</a>
          <a href="<?= ded_h(yonetim_url('orders')) ?>" class="btn btn-light border"><i class="iconoir-shopping-bag me-1"></i> Siparişler</a>
          <a href="<?= ded_h(yonetim_url('accounting')) ?>" class="btn btn-light border"><i class="iconoir-hand-cash me-1"></i> Muhasebe</a>
          <a href="<?= ded_h(yonetim_url('settings')) ?>" class="btn btn-light border"><i class="iconoir-settings me-1"></i> Ayarlar</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <?php yonetim_panel_open('Son siparişler', [['href' => 'orders', 'label' => 'Tümü', 'class' => 'btn btn-sm btn-light border']]); ?>
    <?php if ($recentOrders === []) { ?>
    <p class="text-muted py-3 mb-0">Henüz sipariş yok.</p>
    <?php } else { ?>
    <?php yonetim_table_responsive_open(); ?>
    <thead class="table-light">
      <tr>
        <th>No</th>
        <th>Müşteri</th>
        <th>Tutar</th>
        <th class="text-end">Detay</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($recentOrders as $o) {
          $id = (int) ($o['id'] ?? 0);
          ?>
      <tr>
        <td class="fw-medium"><?= ded_h((string) ($o['order_number'] ?? '')) ?></td>
        <td><?= ded_h((string) ($o['customer_name'] ?? '')) ?></td>
        <td><?= ded_h((string) ($o['total'] ?? '')) ?></td>
        <td class="text-end"><a href="<?= ded_h(yonetim_url('order', ['id' => $id])) ?>" class="btn btn-sm btn-soft-primary">Aç</a></td>
      </tr>
      <?php } ?>
    </tbody>
    <?php yonetim_table_responsive_close(); ?>
    <?php } ?>
    <?php yonetim_panel_close(); ?>
  </div>
</div>
<?php } else { ?>
<div class="card border-0 shadow-sm mt-1">
  <div class="card-body text-center py-5">
    <i class="iconoir-shopping-bag display-4 text-muted"></i>
    <p class="text-muted mt-3 mb-0">Mağaza kapalı.</p>
  </div>
</div>
<?php } ?>
<?php yonetim_layout_end(); ?>

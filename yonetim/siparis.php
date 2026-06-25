<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/magazadepo.php';
require_once dirname(__DIR__) . '/lib/bildirimgonder.php';

yonetim_require_login();

$pdo = ded_pdo();
if (!$pdo) {
    yonetim_layout_start('Sipariş');
    yonetim_alert('danger', 'Veritabanı yok.');
    yonetim_layout_end();
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$order = $id > 0 ? ded_order_get($pdo, $id) : null;
if (!$order) {
    yonetim_layout_start('Sipariş');
    yonetim_alert('danger', 'Sipariş bulunamadı.');
    echo '<p class="mt-2"><a href="' . ded_h(yonetim_url('orders')) . '" class="btn btn-sm btn-light border">Listeye dön</a></p>';
    yonetim_layout_end();
    exit;
}

$saveErr = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $order;
    $patch = [
        'status' => (string) ($_POST['status'] ?? ''),
        'payment_status' => (string) ($_POST['payment_status'] ?? ''),
        'tracking_number' => (string) ($_POST['tracking_number'] ?? ''),
        'carrier' => (string) ($_POST['carrier'] ?? ''),
        'admin_notes' => (string) ($_POST['admin_notes'] ?? ''),
    ];
    try {
        $new = ded_order_update($pdo, $id, $patch);
        $settings = ded_shop_settings_get($pdo);
        $oldTr = trim((string) ($old['tracking_number'] ?? ''));
        $newTr = trim((string) ($new['tracking_number'] ?? ''));
        if ($newTr !== '' && $newTr !== $oldTr) {
            try {
                ded_notify_shipment($pdo, $new, $settings, $newTr, (string) ($new['carrier'] ?? ''));
            } catch (Throwable) {
            }
        }
        yonetim_flash('Sipariş güncellendi.');
        yonetim_redirect('order', ['id' => $id]);
        exit;
    } catch (Throwable $e) {
        $saveErr = $e->getMessage();
    }
    $order = ded_order_get($pdo, $id) ?? $order;
}

$items = ded_order_items($pdo, $id);

$durumSecenekleri = [
    'pending' => 'Beklemede',
    'processing' => 'Hazırlanıyor',
    'shipped' => 'Kargoya verildi',
    'delivered' => 'Teslim edildi',
    'cancelled' => 'İptal',
];
$odemeSecenekleri = [
    'unpaid' => 'Ödenmedi',
    'paid' => 'Ödendi',
    'awaiting_transfer' => 'Havale bekleniyor',
    'failed' => 'Başarısız',
    'refunded' => 'İade',
];
$curDurum = (string) ($order['status'] ?? '');
$curOdeme = (string) ($order['payment_status'] ?? '');
if ($curDurum !== '' && !array_key_exists($curDurum, $durumSecenekleri)) {
    $durumSecenekleri = [$curDurum => $curDurum] + $durumSecenekleri;
}
if ($curOdeme !== '' && !array_key_exists($curOdeme, $odemeSecenekleri)) {
    $odemeSecenekleri = [$curOdeme => $curOdeme] + $odemeSecenekleri;
}

yonetim_layout_start('Sipariş');
yonetim_page_header('Sipariş ' . (string) ($order['order_number'] ?? ''), 'orders');
if ($saveErr !== '') {
    yonetim_alert('danger', $saveErr);
}
?>

<div class="row g-3">
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom">
        <h4 class="card-title mb-0 fs-16">Özet</h4>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Müşteri</span>
          <span class="fw-semibold text-end"><?= ded_h((string) ($order['customer_name'] ?? '')) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">E-posta</span>
          <span class="text-end fs-13"><?= ded_h((string) ($order['customer_email'] ?? '—')) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Telefon</span>
          <span class="text-end"><?= ded_h((string) ($order['customer_phone'] ?? '—')) ?></span>
        </div>
        <hr>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Ara toplam</span>
          <span><?= ded_h((string) ($order['subtotal'] ?? '')) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">İndirim</span>
          <span><?= ded_h((string) ($order['discount_amount'] ?? '')) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Kargo</span>
          <span><?= ded_h((string) ($order['shipping_fee'] ?? '')) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted fw-semibold">Toplam</span>
          <span class="fw-bold fs-18"><?= ded_h((string) ($order['total'] ?? '')) ?> <?= ded_h((string) ($order['currency'] ?? '')) ?></span>
        </div>
        <?php if (!empty($order['coupon_code'])) { ?>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Kupon</span>
          <span><code><?= ded_h((string) $order['coupon_code']) ?></code></span>
        </div>
        <?php } ?>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Ödeme</span>
          <span class="badge bg-primary-subtle text-primary"><?= ded_h((string) ($order['payment_method'] ?? '')) ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <?php yonetim_panel_open('Sipariş kalemleri'); ?>
    <?php yonetim_table_responsive_open(); ?>
    <thead class="table-light">
      <tr>
        <th>Ürün</th>
        <th>Varyant</th>
        <th class="text-end">Adet</th>
        <th class="text-end">Birim</th>
        <th class="text-end">Satır</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $it) { ?>
      <tr>
        <td><code class="fs-12"><?= ded_h((string) ($it['product_slug'] ?? '')) ?></code></td>
        <td><?= ded_h((string) ($it['variant_label'] ?? '')) ?></td>
        <td class="text-end"><?= (int) ($it['qty'] ?? 0) ?></td>
        <td class="text-end"><?= ded_h((string) ($it['unit_price'] ?? '')) ?></td>
        <td class="text-end fw-semibold"><?= ded_h((string) ($it['line_total'] ?? '')) ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <?php yonetim_table_responsive_close(); ?>
    <?php yonetim_panel_close(); ?>
  </div>
</div>

<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent border-bottom py-3">
        <h4 class="card-title mb-0 fs-16">Yönetim</h4>
        <p class="text-muted small mb-0 mt-1">Sipariş durumu, ödeme, kargo ve dahili notlar.</p>
      </div>
      <div class="card-body pt-3">
        <form method="post" class="yun-order-admin-form">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="ord-status">Sipariş durumu</label>
              <select class="form-select" name="status" id="ord-status">
                <?php foreach ($durumSecenekleri as $val => $lab) {
                    $sel = $curDurum === $val ? ' selected' : '';
                    ?>
                <option value="<?= ded_attr($val) ?>"<?= $sel ?>><?= ded_h($lab) ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="ord-pay">Ödeme durumu</label>
              <select class="form-select" name="payment_status" id="ord-pay">
                <?php foreach ($odemeSecenekleri as $val => $lab) {
                    $sel = $curOdeme === $val ? ' selected' : '';
                    ?>
                <option value="<?= ded_attr($val) ?>"<?= $sel ?>><?= ded_h($lab) ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="ord-carrier">Kargo firması</label>
              <input type="text" class="form-control" name="carrier" id="ord-carrier" autocomplete="organization"
                value="<?= ded_h((string) ($order['carrier'] ?? '')) ?>" placeholder="Örn. Aras, Yurtiçi">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="ord-track">Takip numarası</label>
              <input type="text" class="form-control font-monospace" name="tracking_number" id="ord-track"
                value="<?= ded_h((string) ($order['tracking_number'] ?? '')) ?>" placeholder="Kargo takip no">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold" for="ord-notes">Yönetici notu</label>
              <textarea class="form-control" name="admin_notes" id="ord-notes" rows="4"
                placeholder="Sadece panelde görünür"><?= ded_h((string) ($order['admin_notes'] ?? '')) ?></textarea>
            </div>
          </div>
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-4 mt-2 border-top">
            <a href="<?= ded_h(yonetim_url('orders')) ?>" class="btn btn-light border">← Sipariş listesi</a>
            <button type="submit" class="btn btn-primary px-4">
              Kaydet
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php yonetim_layout_end(); ?>

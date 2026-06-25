<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/magaza.php';

yonetim_require_login();
$pdo = yonetim_magaza_pdo();
$email = trim((string) ($_GET['id'] ?? ''));
if ($pdo === null || $email === '') {
    yonetim_redirect('customers');
}
$detay = ded_musteri_detay($pdo, $email);
if ($detay === null) {
    yonetim_layout_start('Müşteri');
    yonetim_alert('danger', 'Kayıt bulunamadı.');
    yonetim_layout_end();
    exit;
}

yonetim_layout_start('Müşteri');
yonetim_page_header((string) $detay['name'], 'customers');
yonetim_panel_open('Özet');
?>
<div class="row g-3 mb-0">
  <div class="col-md-4"><span class="text-muted d-block fs-12">E-posta</span><code><?= ded_h((string) $detay['email']) ?></code></div>
  <div class="col-md-4"><span class="text-muted d-block fs-12">Telefon</span><?= ded_h((string) $detay['phone']) ?></div>
  <div class="col-md-4"><span class="text-muted d-block fs-12">Sipariş</span><?= (int) $detay['order_count'] ?></div>
  <div class="col-md-4"><span class="text-muted d-block fs-12">Toplam</span><?= ded_h(ded_format_price_try_like_theme((float) $detay['spent'])) ?></div>
</div>
<?php
yonetim_panel_close();
yonetim_panel_open('Sipariş geçmişi');
yonetim_table_responsive_open();
?>
<thead class="table-light"><tr><th>No</th><th>Tarih</th><th>Tutar</th><th>Durum</th><th></th></tr></thead>
<tbody>
<?php foreach ($detay['orders'] as $o) {
    $oid = (int) ($o['id'] ?? 0); ?>
  <tr>
    <td><code class="fs-12"><?= ded_h((string) ($o['order_number'] ?? '')) ?></code></td>
    <td class="text-muted fs-13"><?= ded_h((string) ($o['created_at'] ?? '')) ?></td>
    <td><?= ded_h(ded_format_price_try_like_theme((float) ($o['total'] ?? 0))) ?></td>
    <td><span class="badge bg-light text-dark border"><?= ded_h((string) ($o['status'] ?? '')) ?></span></td>
    <td class="text-end"><a class="btn btn-sm btn-soft-primary" href="<?= ded_h(yonetim_url('order', ['id' => $oid])) ?>">Aç</a></td>
  </tr>
<?php } ?>
</tbody>
<?php
yonetim_table_responsive_close();
yonetim_panel_close();
yonetim_layout_end();

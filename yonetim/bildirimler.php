<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';

yonetim_require_login();
$pdo = yonetim_shop_pdo();
if (!$pdo) {
    yonetim_layout_start('Bildirimler');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

$items = ded_notification_log_list($pdo, 250);

yonetim_layout_start('Bildirimler');
yonetim_panel_open('Bildirim günlüğü', [
    ['href' => 'bulk_messages', 'label' => 'Toplu mesaj', 'class' => 'btn btn-sm btn-primary'],
]);
?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr>
    <th>ID</th>
    <th>Kanal</th>
    <th>Alıcı</th>
    <th>İçerik</th>
    <th>Durum</th>
    <th>Tarih</th>
  </tr>
</thead>
<tbody>
  <?php foreach ($items as $it) { ?>
  <tr>
    <td class="text-muted fs-12"><?= (int) ($it['id'] ?? 0) ?></td>
    <td><span class="badge bg-primary-subtle text-primary"><?= ded_h((string) ($it['channel'] ?? '')) ?></span></td>
    <td><code class="fs-12"><?= ded_h((string) ($it['recipient'] ?? '')) ?></code></td>
    <td>
      <span class="fw-medium d-block"><?= ded_h((string) ($it['subject'] ?? '')) ?></span>
      <small class="text-muted"><?= ded_h(mb_strimwidth((string) ($it['body_preview'] ?? ''), 0, 100, '…', 'UTF-8')) ?></small>
    </td>
    <td><?= ded_h((string) ($it['status'] ?? '')) ?></td>
    <td class="text-muted fs-12 text-nowrap"><?= ded_h((string) ($it['created_at'] ?? '')) ?></td>
  </tr>
  <?php } ?>
</tbody>
<?php yonetim_table_responsive_close(); ?>
<?php yonetim_panel_close(); ?>
<?php yonetim_layout_end(); ?>

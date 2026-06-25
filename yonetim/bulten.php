<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/ekstra.php';

yonetim_require_login();
$pdo = yonetim_magaza_pdo();
if (!$pdo) {
    yonetim_layout_start('Bülten');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = (string) ($_POST['act'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);
    if ($act === 'toggle' && $id > 0) {
        ded_newsletter_set_active($pdo, $id, (int) ($_POST['active'] ?? 0) === 1);
        yonetim_flash('Kayıt güncellendi.');
    }
    if ($act === 'delete' && $id > 0) {
        ded_newsletter_delete($pdo, $id);
        yonetim_flash('Abone silindi.');
    }
    yonetim_redirect('newsletter');
    exit;
}

$q = trim((string) ($_GET['q'] ?? ''));
$data = ded_newsletter_list($pdo, 300, 0, $q);

yonetim_layout_start('Bülten');
yonetim_page_header('Bülten aboneleri', 'dashboard');
yonetim_panel_open('Aboneler (' . (int) $data['total'] . ')');
yonetim_search_bar($q, 'E-posta veya ad', [], ded_h(yonetim_url('newsletter')));
?>
<?php yonetim_table_responsive_open(); ?>
<thead class="table-light">
  <tr><th>E-posta</th><th>Ad</th><th>Kaynak</th><th>Durum</th><th>Tarih</th><th></th></tr>
</thead>
<tbody>
<?php foreach ($data['items'] as $row) {
    $id = (int) ($row['id'] ?? 0);
    $active = (int) ($row['active'] ?? 0) === 1;
    ?>
  <tr>
    <td><code class="fs-12"><?= ded_h((string) ($row['email'] ?? '')) ?></code></td>
    <td><?= ded_h((string) ($row['name'] ?? '')) ?></td>
    <td class="text-muted fs-13"><?= ded_h((string) ($row['source'] ?? '')) ?></td>
    <td><span class="badge <?= $active ? 'bg-success-subtle text-success' : 'bg-light text-muted' ?>"><?= $active ? 'Aktif' : 'Pasif' ?></span></td>
    <td class="text-muted fs-13"><?= ded_h((string) ($row['created_at'] ?? '')) ?></td>
    <td class="text-end text-nowrap">
      <form method="post" class="d-inline">
        <input type="hidden" name="act" value="toggle">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="active" value="<?= $active ? '0' : '1' ?>">
        <button type="submit" class="btn btn-sm btn-light border"><?= $active ? 'Pasifleştir' : 'Aktifleştir' ?></button>
      </form>
      <form method="post" class="d-inline" onsubmit="return confirm('Silinsin mi?');">
        <input type="hidden" name="act" value="delete">
        <input type="hidden" name="id" value="<?= $id ?>">
        <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
      </form>
    </td>
  </tr>
<?php } ?>
</tbody>
<?php
yonetim_table_responsive_close();
yonetim_panel_close();
yonetim_layout_end();

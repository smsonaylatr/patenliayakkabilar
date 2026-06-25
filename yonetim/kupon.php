<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';

yonetim_require_login();
$pdo = yonetim_shop_pdo();
if (!$pdo) {
    yonetim_layout_start('Kupon');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$row = null;
if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM ded_coupons WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC) ?: null;
    if (!$row) {
        yonetim_layout_start('Kupon');
        yonetim_alert('danger', 'Kupon bulunamadı.');
        echo '<p class="mt-2"><a href="' . ded_h(yonetim_url('coupons')) . '" class="btn btn-sm btn-light border">Listeye dön</a></p>';
        yonetim_layout_end();
        exit;
    }
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'code' => (string) ($_POST['code'] ?? ''),
        'discount_type' => (string) ($_POST['discount_type'] ?? 'percent'),
        'discount_value' => (float) ($_POST['discount_value'] ?? 0),
        'min_subtotal' => (float) ($_POST['min_subtotal'] ?? 0),
        'max_uses' => trim((string) ($_POST['max_uses'] ?? '')),
        'starts_at' => trim((string) ($_POST['starts_at'] ?? '')),
        'ends_at' => trim((string) ($_POST['ends_at'] ?? '')),
        'active' => !empty($_POST['active']),
    ];
    if ($data['max_uses'] === '') {
        $data['max_uses'] = null;
    }
    try {
        $newId = ded_coupon_save($pdo, $id > 0 ? $id : null, $data);
        yonetim_flash('Kupon kaydedildi.');
        yonetim_redirect('coupon', ['id' => $newId]);
        exit;
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$title = $row ? ('Kupon: ' . ($row['code'] ?? '')) : 'Yeni kupon';
yonetim_layout_start($title);
yonetim_page_header($title, 'coupons');
if ($err !== '') {
    yonetim_alert('danger', $err);
}
yonetim_form_open();
?>
  <label>Kod</label>
  <input type="text" name="code" required value="<?= ded_h((string) ($row['code'] ?? '')) ?>" class="font-monospace">
  <label>İndirim tipi</label>
  <select name="discount_type">
    <?php
    $t = (string) ($row['discount_type'] ?? 'percent');
foreach (['percent' => 'Yüzde (%)', 'fixed' => 'Sabit tutar'] as $k => $lab) {
    $sel = $t === $k ? ' selected' : '';
    echo '<option value="' . ded_h($k) . '"' . $sel . '>' . ded_h($lab) . '</option>';
}
?>
  </select>
  <label>Değer</label>
  <input type="number" name="discount_value" step="0.01" value="<?= ded_h((string) ($row['discount_value'] ?? '0')) ?>">
  <label>Minimum sepet tutarı</label>
  <input type="number" name="min_subtotal" step="0.01" value="<?= ded_h((string) ($row['min_subtotal'] ?? '0')) ?>">
  <label>Maks. kullanım (boş = sınırsız)</label>
  <input type="number" name="max_uses" value="<?= $row && isset($row['max_uses']) && $row['max_uses'] !== null ? ded_h((string) $row['max_uses']) : '' ?>">
  <div class="yun-row2">
    <div>
      <label>Başlangıç</label>
      <input type="date" name="starts_at" value="<?= ded_h(substr((string) ($row['starts_at'] ?? ''), 0, 10)) ?>">
    </div>
    <div>
      <label>Bitiş</label>
      <input type="date" name="ends_at" value="<?= ded_h(substr((string) ($row['ends_at'] ?? ''), 0, 10)) ?>">
    </div>
  </div>
  <label class="yun-check d-flex align-items-center gap-2 mt-2">
    <input type="checkbox" name="active" value="1" class="form-check-input m-0"<?= !$row || !empty($row['active']) ? ' checked' : '' ?>>
    <span>Aktif</span>
  </label>
<?php yonetim_form_close(); ?>
<?php yonetim_layout_end(); ?>

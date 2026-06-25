<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/ekstra.php';

yonetim_require_login();
$pdo = yonetim_magaza_pdo();
if (!$pdo) {
    yonetim_layout_start('SSS');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isNew = isset($_GET['new']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = (string) ($_POST['act'] ?? 'save');
    if ($act === 'delete') {
        $delId = (int) ($_POST['id'] ?? 0);
        if ($delId > 0) {
            ded_faq_delete($pdo, $delId);
            yonetim_flash('Soru silindi.');
        }
        yonetim_redirect('faq');
        exit;
    }
    $saveId = (int) ($_POST['id'] ?? 0);
    $saveId = $saveId > 0 ? $saveId : null;
    ded_faq_save(
        $pdo,
        $saveId,
        trim((string) ($_POST['question'] ?? '')),
        trim((string) ($_POST['answer'] ?? '')),
        (int) ($_POST['sort_order'] ?? 0),
        isset($_POST['active']),
    );
    yonetim_flash('SSS kaydedildi.');
    yonetim_redirect('faq');
    exit;
}

if ($isNew || $id > 0) {
    $row = $id > 0 ? ded_faq_get($pdo, $id) : null;
    if ($id > 0 && $row === null) {
        yonetim_redirect('faq');
        exit;
    }
    yonetim_layout_start($isNew ? 'Yeni SSS' : 'SSS düzenle');
    yonetim_page_header($isNew ? 'Yeni soru' : 'SSS düzenle', 'faq');
    yonetim_form_open();
    ?>
  <input type="hidden" name="id" value="<?= $id ?>">
  <label>Soru</label>
  <input type="text" name="question" required value="<?= ded_h((string) ($row['question'] ?? '')) ?>">
  <label>Cevap</label>
  <textarea name="answer" rows="5" required><?= ded_h((string) ($row['answer'] ?? '')) ?></textarea>
  <label>Sıra</label>
  <input type="number" name="sort_order" value="<?= (int) ($row['sort_order'] ?? 0) ?>">
  <label class="d-flex align-items-center gap-2 mt-2">
    <input type="checkbox" name="active" value="1" <?= !isset($row['active']) || (int) ($row['active'] ?? 1) ? 'checked' : '' ?>> Yayında
  </label>
    <?php
    yonetim_form_close('Kaydet');
    yonetim_layout_end();
    exit;
}

$items = ded_faq_list($pdo, false);
$qFaq = mb_strtolower(trim((string) ($_GET['q'] ?? '')), 'UTF-8');
if ($qFaq !== '') {
    $items = array_values(array_filter($items, static function ($it) use ($qFaq) {
        $hay = mb_strtolower((string) ($it['question'] ?? '') . ' ' . (string) ($it['answer'] ?? ''), 'UTF-8');
        return str_contains($hay, $qFaq);
    }));
}
yonetim_layout_start('SSS');
yonetim_page_header('Sık sorulan sorular', 'dashboard', [
    ['href' => yonetim_url('faq', ['new' => 1]), 'label' => 'Yeni soru', 'class' => 'btn btn-sm btn-primary'],
]);
yonetim_panel_open('Sorular (' . count($items) . ')');
yonetim_search_bar(trim((string) ($_GET['q'] ?? '')), 'Soru veya cevap', [], ded_h(yonetim_url('faq')));
yonetim_table_responsive_open();
?>
<thead class="table-light"><tr><th>Sıra</th><th>Soru</th><th>Durum</th><th></th></tr></thead>
<tbody>
<?php foreach ($items as $it) {
    $fid = (int) ($it['id'] ?? 0); ?>
  <tr>
    <td><?= (int) ($it['sort_order'] ?? 0) ?></td>
    <td class="fw-medium"><?= ded_h((string) ($it['question'] ?? '')) ?></td>
    <td><?= (int) ($it['active'] ?? 0) ? '<span class="badge bg-success-subtle text-success">Aktif</span>' : '<span class="badge bg-light text-muted">Kapalı</span>' ?></td>
    <td class="text-end text-nowrap">
      <a href="<?= ded_h(yonetim_url('faq', ['id' => $fid])) ?>" class="btn btn-sm btn-soft-primary">Düzenle</a>
      <form method="post" class="d-inline" onsubmit="return confirm('Silinsin mi?');">
        <input type="hidden" name="act" value="delete">
        <input type="hidden" name="id" value="<?= $fid ?>">
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

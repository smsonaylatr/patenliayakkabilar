<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';

$st = strtolower(trim((string) ($_GET['st'] ?? '')));
$num = trim((string) ($_GET['num'] ?? ''));
$t = ded_h($num !== '' ? $num : '—');
$ok = $st === 'ok';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $ok ? 'Ödeme tamamlandı' : 'Ödeme' ?> | Laykids</title>
  <style>
    body { margin:0; font-family:system-ui,sans-serif; background:#141416; color:#e8e8ea; min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .card { max-width:520px; padding:2rem; border:1px solid #2e2e34; border-radius:14px; background:#1c1c20; }
    h1 { margin:0 0 0.5rem; font-size:1.35rem; }
    p { color:#9a9aa0; line-height:1.55; }
    .num { color:#c9a962; font-weight:700; font-size:1.1rem; }
    a { display:inline-block; margin-top:1rem; padding:0.6rem 1.1rem; border-radius:10px; background:#6b1c24; color:#fff; text-decoration:none; font-weight:600; }
    .warn { color:#fecaca; }
  </style>
</head>
<body>
  <div class="card">
    <h1><?= $ok ? 'İşlem alındı' : 'Ödeme tamamlanamadı' ?></h1>
    <?php if ($ok) { ?>
      <p>Ödemeniz başarıyla tamamlandıysa siparişiniz kısa süre içinde <strong>Ödendi</strong> olarak güncellenir. Sipariş numaranız:</p>
      <p class="num"><?= $t ?></p>
      <p class="warn">Not: Onay PayTR bildirimi ile sunucuya işlenir; gecikme olursa birkaç dakika bekleyin.</p>
    <?php } else { ?>
      <p>Kart ödeme işlemi iptal edildi veya tamamlanmadı. Sipariş kaydınız varsa numara:</p>
      <p class="num"><?= $t ?></p>
      <p>Tekrar denemek için sepete dönebilir veya mağaza ile iletişime geçebilirsiniz.</p>
    <?php } ?>
    <a href="index.php">Ana sayfa</a>
    <a href="<?= ded_h(ded_vitrin_url('cart')) ?>" style="margin-left:0.5rem;background:transparent;border:1px solid #3f3f46;color:#e8e8ea">Sepet</a>
  </div>
</body>
</html>

<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';

$num = trim((string) ($_GET['num'] ?? ''));
header('Content-Type: text/html; charset=utf-8');
$t = ded_h($num !== '' ? $num : '—');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sipariş alındı</title>
  <style>
    body { margin:0; font-family:system-ui,sans-serif; background:#141416; color:#e8e8ea; min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .card { max-width:520px; padding:2rem; border:1px solid #2e2e34; border-radius:14px; background:#1c1c20; }
    h1 { margin:0 0 0.5rem; font-size:1.35rem; }
    p { color:#9a9aa0; line-height:1.55; }
    .num { color:#c9a962; font-weight:700; font-size:1.1rem; }
    a { display:inline-block; margin-top:1rem; padding:0.6rem 1.1rem; border-radius:10px; background:#6b1c24; color:#fff; text-decoration:none; font-weight:600; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Teşekkürler</h1>
    <p>Siparişiniz alındı. Sipariş numaranız:</p>
    <p class="num"><?php echo $t; ?></p>
    <p>E-posta adresinize özet gönderilmiş olabilir (SMTP / bildirim ayarlarına bağlıdır). Sorularınız için mağaza ile iletişime geçebilirsiniz.</p>
    <a href="index.php">Ana sayfa</a>
  </div>
</body>
</html>

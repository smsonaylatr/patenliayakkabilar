<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once __DIR__ . '/inc/yerlesim.php';
require_once dirname(__DIR__) . '/lib/posta.php';
require_once dirname(__DIR__) . '/lib/smsnetgsm.php';

yonetim_require_login();
$pdo = yonetim_shop_pdo();
if (!$pdo) {
    yonetim_layout_start('Toplu mesaj');
    yonetim_alert('danger', 'Mağaza tabloları yok.');
    yonetim_layout_end();
    exit;
}

$settings = ded_shop_settings_get($pdo);
$report = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $channel = (string) ($_POST['channel'] ?? '');
    $subject = (string) ($_POST['subject'] ?? 'Bilgilendirme');
    $message = trim((string) ($_POST['message'] ?? ''));
    $manual = (string) ($_POST['manual_list'] ?? '');
    $addEmails = !empty($_POST['src_order_emails']);
    $addPhones = !empty($_POST['src_order_phones']);
    $max = max(10, min(500, (int) ($_POST['max_recipients'] ?? 200)));

    if ($message === '' || !in_array($channel, ['email', 'sms'], true)) {
        $err = 'Kanal ve mesaj zorunlu.';
    } else {
        $emails = [];
        $phones = [];
        foreach (preg_split('/\R/', $manual) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (str_contains($line, '@')) {
                $e = strtolower($line);
                if (filter_var($e, FILTER_VALIDATE_EMAIL)) {
                    $emails[$e] = true;
                }
            } else {
                $p = preg_replace('/\D+/', '', $line);
                if ($p !== '' && strlen($p) >= 10) {
                    $phones[$p] = true;
                }
            }
        }
        if ($addEmails) {
            foreach (ded_shop_distinct_customer_emails($pdo, 5000) as $e) {
                $emails[$e] = true;
            }
        }
        if ($addPhones) {
            foreach (ded_shop_distinct_customer_phones($pdo, 5000) as $p) {
                $phones[$p] = true;
            }
        }

        if ($channel === 'email') {
            $list = array_keys($emails);
            if ($list === []) {
                $err = 'Geçerli e-posta yok.';
            } else {
                $list = array_slice($list, 0, $max);
                $ok = 0;
                $fail = 0;
                foreach ($list as $to) {
                    $html = '<p>' . nl2br(ded_h($message)) . '</p>';
                    $sent = ded_mail_send($settings, $to, $subject, $html, $message);
                    ded_notification_log_insert(
                        $pdo,
                        'email',
                        $to,
                        $subject,
                        mb_strimwidth($message, 0, 200, '…', 'UTF-8'),
                        $sent ? 'sent' : 'failed',
                        ['bulk' => true]
                    );
                    $sent ? $ok++ : $fail++;
                }
                $report = "E-posta: {$ok} gönderildi, {$fail} başarısız (liste: " . count($list) . ').';
            }
        } else {
            $list = array_keys($phones);
            if ($list === []) {
                $err = 'Geçerli telefon yok.';
            } else {
                $list = array_slice($list, 0, $max);
                $ok = 0;
                $fail = 0;
                $chunkSize = 80;
                for ($i = 0; $i < count($list); $i += $chunkSize) {
                    $chunk = array_slice($list, $i, $chunkSize);
                    $nums = implode(',', $chunk);
                    $res = ded_toplus_sms_send($settings, $nums, $message);
                    foreach ($chunk as $one) {
                        ded_notification_log_insert(
                            $pdo,
                            'sms',
                            $one,
                            'bulk',
                            mb_strimwidth($message, 0, 200, '…', 'UTF-8'),
                            $res['ok'] ? 'sent' : 'failed',
                            array_merge($res, ['bulk' => true, 'chunk' => true])
                        );
                    }
                    if ($res['ok']) {
                        $ok += count($chunk);
                    } else {
                        $fail += count($chunk);
                    }
                }
                $report = "SMS: {$ok} / " . count($list) . ' numara işlendi. Başarısız: ' . $fail . '.';
            }
        }
    }
}

yonetim_layout_start('Toplu mesaj');
yonetim_page_header('Toplu mesaj', 'notifications');
if ($err !== '') {
    yonetim_alert('danger', $err);
}
if ($report !== '') {
    yonetim_alert('success', $report);
}
yonetim_form_open(['id' => 'bulk-form']);
?>
  <label>Kanal</label>
  <select name="channel">
    <option value="email">E-posta</option>
    <option value="sms">SMS (Toplus)</option>
  </select>
  <label>E-posta konusu</label>
  <input type="text" name="subject" value="Laykids bilgilendirme">
  <label>Mesaj</label>
  <textarea name="message" rows="8" required></textarea>
  <label>Alıcı listesi</label>
  <textarea name="manual_list" rows="4"></textarea>
  <label class="yun-check d-flex align-items-center gap-2">
    <input type="checkbox" name="src_order_emails" value="1" class="form-check-input m-0">
    <span>Sipariş e-postalarını ekle</span>
  </label>
  <label class="yun-check d-flex align-items-center gap-2">
    <input type="checkbox" name="src_order_phones" value="1" class="form-check-input m-0">
    <span>Sipariş telefonlarını ekle</span>
  </label>
  <label>Azami alıcı</label>
  <input type="number" name="max_recipients" value="200" min="10" max="500">
<?php yonetim_form_close('Gönder'); ?>
<script>
document.getElementById('bulk-form')?.addEventListener('submit', function (e) {
  if (!confirm('Toplu gönderim başlasın mı?')) e.preventDefault();
});
</script>
<?php yonetim_layout_end(); ?>

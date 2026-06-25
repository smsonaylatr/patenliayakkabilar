<?php

declare(strict_types=1);

require_once __DIR__ . '/posta.php';
require_once __DIR__ . '/smsnetgsm.php';
require_once __DIR__ . '/magazadepo.php';

function ded_notify_order_placed(PDO $pdo, array $order, array $settings): void
{
    $id = (int) ($order['id'] ?? 0);
    $num = (string) ($order['order_number'] ?? '');
    $email = (string) ($order['customer_email'] ?? '');
    $phone = (string) ($order['customer_phone'] ?? '');
    $total = number_format((float) ($order['total'] ?? 0), 2, ',', '.');

    if (!empty($settings['notify_order_email']) && $email !== '') {
        $subject = 'Sipariş alındı: ' . $num;
        $pm = (string) ($order['payment_method'] ?? '');
        if ($pm === 'cod') {
            $payBlock = nl2br(ded_h((string) ($settings['cod_instructions'] ?? '')));
        } else {
            $payBlock = nl2br(ded_h((string) ($settings['bank_instructions'] ?? '')));
        }
        $html = '<p>Merhaba ' . ded_h((string) ($order['customer_name'] ?? '')) . ',</p>'
            . '<p>Siparişiniz kaydedildi. Sipariş numaranız: <strong>' . ded_h($num) . '</strong></p>'
            . '<p>Toplam: <strong>' . ded_h($total) . ' ₺</strong></p>'
            . '<p>Ödeme / teslimat bilgisi:</p><p>' . $payBlock . '</p>'
            . '<p>Teşekkürler.</p>';
        $text = "Sipariş: {$num}\nToplam: {$total} TL\n";
        $ok = ded_mail_send($settings, $email, $subject, $html, $text);
        ded_notification_log_insert(
            $pdo,
            'email',
            $email,
            $subject,
            mb_substr(strip_tags($html), 0, 500),
            $ok ? 'sent' : 'failed',
            ['order_id' => $id]
        );
    }

    if (!empty($settings['notify_order_sms']) && $phone !== '') {
        $msg = 'Siparis alindi: ' . $num . ' Tutar: ' . $total . ' TL';
        $res = ded_toplus_sms_send($settings, $phone, $msg);
        ded_notification_log_insert(
            $pdo,
            'sms',
            $phone,
            'order',
            $msg,
            $res['ok'] ? 'sent' : 'failed',
            ['order_id' => $id, 'detail' => $res['detail'] ?? '']
        );
    }
}

function ded_notify_shipment(PDO $pdo, array $order, array $settings, string $tracking, string $carrier): void
{
    $id = (int) ($order['id'] ?? 0);
    $num = (string) ($order['order_number'] ?? '');
    $email = (string) ($order['customer_email'] ?? '');
    $phone = (string) ($order['customer_phone'] ?? '');
    $c = $carrier !== '' ? $carrier : 'Kargo';

    if (!empty($settings['notify_ship_email']) && $email !== '') {
        $subject = 'Kargonuz yola çıktı: ' . $num;
        $html = '<p>Merhaba ' . ded_h((string) ($order['customer_name'] ?? '')) . ',</p>'
            . '<p><strong>' . ded_h($c) . '</strong> için takip numaranız:</p>'
            . '<p><strong>' . ded_h($tracking) . '</strong></p>'
            . '<p>Sipariş no: ' . ded_h($num) . '</p>';
        $ok = ded_mail_send($settings, $email, $subject, $html, strip_tags($html));
        ded_notification_log_insert(
            $pdo,
            'email',
            $email,
            $subject,
            $tracking,
            $ok ? 'sent' : 'failed',
            ['order_id' => $id, 'type' => 'ship']
        );
    }

    if (!empty($settings['notify_ship_sms']) && $phone !== '') {
        $msg = 'Siparis ' . $num . ' kargoda. ' . $c . ' takip: ' . $tracking;
        $res = ded_toplus_sms_send($settings, $phone, $msg);
        ded_notification_log_insert(
            $pdo,
            'sms',
            $phone,
            'ship',
            $msg,
            $res['ok'] ? 'sent' : 'failed',
            ['order_id' => $id]
        );
    }
}

<?php

declare(strict_types=1);

function ded_paytr_configured(array $settings): bool
{
    return trim((string) ($settings['paytr_merchant_id'] ?? '')) !== ''
        && trim((string) ($settings['paytr_merchant_key'] ?? '')) !== ''
        && trim((string) ($settings['paytr_merchant_salt'] ?? '')) !== '';
}

function ded_paytr_merchant_oid(int $orderId): string
{
    return 'DED' . $orderId;
}

function ded_paytr_parse_order_id_from_oid(string $merchantOid): ?int
{
    if (preg_match('/^DED(\d{1,18})$/', $merchantOid, $m)) {
        return (int) $m[1];
    }
    return null;
}

function ded_paytr_user_basket_b64(array $lines): string
{
    $b = [];
    foreach ($lines as $L) {
        $title = (string) ($L['title'] ?? $L['slug'] ?? 'Ürün');
        $lineTotal = (float) ($L['line_total'] ?? 0);
        $qty = max(1, (int) ($L['qty'] ?? 1));
        $unit = $qty > 0 ? round($lineTotal / $qty, 2) : $lineTotal;
        $b[] = [$title, number_format($unit, 2, '.', ''), $qty];
    }
    if ($b === []) {
        $b[] = ['Sipariş', '0.00', 1];
    }
    return base64_encode(json_encode($b, JSON_UNESCAPED_UNICODE));
}

function ded_paytr_currency_code(string $currency): string
{
    $c = strtoupper(trim($currency));
    return $c === 'TRY' ? 'TL' : $c;
}

function ded_paytr_get_iframe_token(
    array $settings,
    array $orderRow,
    array $normLines,
    string $userIp,
    string $okUrl,
    string $failUrl
): array {
    if (!ded_paytr_configured($settings)) {
        return ['ok' => false, 'error' => 'paytr_not_configured'];
    }
    $oid = (int) ($orderRow['id'] ?? 0);
    if ($oid <= 0) {
        return ['ok' => false, 'error' => 'bad_order'];
    }
    $merchant_id = trim((string) $settings['paytr_merchant_id']);
    $merchant_key = trim((string) $settings['paytr_merchant_key']);
    $merchant_salt = trim((string) $settings['paytr_merchant_salt']);
    $merchant_oid = ded_paytr_merchant_oid($oid);
    $email = strtolower(trim((string) ($orderRow['customer_email'] ?? '')));
    if ($email === '') {
        return ['ok' => false, 'error' => 'bad_email'];
    }
    $total = (float) ($orderRow['total'] ?? 0);
    $payment_amount = (int) round($total * 100);
    if ($payment_amount < 1) {
        return ['ok' => false, 'error' => 'bad_amount'];
    }
    $currency = ded_paytr_currency_code((string) ($orderRow['currency'] ?? 'TRY'));
    $test_mode = !empty($settings['paytr_test_mode']) ? '1' : '0';
    $no_inst = (int) ($settings['paytr_no_installment'] ?? 0) ? 1 : 0;
    $max_inst = (int) ($settings['paytr_max_installment'] ?? 0);
    $user_basket = ded_paytr_user_basket_b64($normLines);
    $hash_str = $merchant_id . $userIp . $merchant_oid . $email . $payment_amount . $user_basket . $no_inst . $max_inst . $currency . $test_mode;
    $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

    $debug_on = !empty($settings['paytr_debug_on']) ? 1 : 0;
    $post = [
        'merchant_id' => $merchant_id,
        'user_ip' => substr($userIp, 0, 39),
        'merchant_oid' => $merchant_oid,
        'email' => substr($email, 0, 100),
        'payment_amount' => $payment_amount,
        'paytr_token' => $paytr_token,
        'user_basket' => $user_basket,
        'debug_on' => $debug_on,
        'no_installment' => $no_inst,
        'max_installment' => $max_inst,
        'user_name' => substr(trim((string) ($orderRow['customer_name'] ?? '')), 0, 60),
        'user_address' => substr(trim((string) ($orderRow['shipping_address_line'] ?? '')), 0, 400),
        'user_phone' => substr(trim((string) ($orderRow['customer_phone'] ?? '')), 0, 20),
        'merchant_ok_url' => substr($okUrl, 0, 400),
        'merchant_fail_url' => substr($failUrl, 0, 400),
        'timeout_limit' => 30,
        'currency' => $currency,
        'test_mode' => $test_mode,
        'lang' => 'tr',
    ];

    if (!function_exists('curl_init')) {
        return ['ok' => false, 'error' => 'curl_required'];
    }
    $ch = curl_init('https://www.paytr.com/odeme/api/get-token');
    if ($ch === false) {
        return ['ok' => false, 'error' => 'curl_init'];
    }
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_HTTPHEADER => ['Expect:'],
    ]);
    $raw = curl_exec($ch);
    $cerr = curl_error($ch);
    curl_close($ch);
    if ($raw === false) {
        return ['ok' => false, 'error' => 'paytr_network', 'reason' => $cerr];
    }
    $decoded = json_decode((string) $raw, true);
    if (!is_array($decoded)) {
        return ['ok' => false, 'error' => 'paytr_bad_response', 'reason' => substr((string) $raw, 0, 200)];
    }
    if (($decoded['status'] ?? '') === 'success' && !empty($decoded['token'])) {
        return ['ok' => true, 'token' => (string) $decoded['token']];
    }
    $reason = (string) ($decoded['reason'] ?? $raw);
    return ['ok' => false, 'error' => 'paytr_declined', 'reason' => $reason];
}

function ded_paytr_verify_callback_hash(array $post, string $merchant_key, string $merchant_salt): bool
{
    $oid = (string) ($post['merchant_oid'] ?? '');
    $status = (string) ($post['status'] ?? '');
    $total_amount = (string) ($post['total_amount'] ?? '');
    $hash = (string) ($post['hash'] ?? '');
    if ($oid === '' || $status === '' || $hash === '') {
        return false;
    }
    $calc = base64_encode(hash_hmac('sha256', $oid . $merchant_salt . $status . $total_amount, $merchant_key, true));
    return hash_equals($calc, $hash);
}

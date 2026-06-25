<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/paytr.php';

function ded_shop_ready(?PDO $pdo = null): bool
{
    $pdo = $pdo ?? ded_pdo();
    if (!$pdo) {
        return false;
    }
    try {
        $pdo->query('SELECT 1 FROM ded_orders LIMIT 1');
        return true;
    } catch (Throwable) {
        return false;
    }
}

function ded_shop_default_settings(): array
{
    return [
        'checkout_mode' => 'manual_transfer',

        'checkout_intro' => '',
        'bank_instructions' => 'Ödemeyi havale/EFT ile tamamlayın. Açıklama kısmına sipariş numaranızı yazın.',

        'cod_instructions' => 'Kapıda ödeme: Teslimatta kargo görevlisine nakit veya POS ile ödeyebilirsiniz.',

        'payment_demo_notice' => 'Demo modu: sipariş kaydı otomatik olarak ÖDENDİ işlenir (gerçek tahsilat yok).',
        'default_shipping_fee' => 0,
        'shipping_note' => '',
        'currency' => 'TRY',
        'smtp_host' => '',
        'smtp_port' => 587,
        'smtp_encryption' => 'tls',
        'smtp_user' => '',
        'smtp_pass' => '',
        'mail_from' => '',
        'mail_from_name' => 'Mağaza',
        'toplus_api_base' => 'https://panel.toplusms.tc/api/v1/',
        'toplus_username' => '',
        'toplus_password' => '',
        'toplus_caption' => '',
        'toplus_encoding' => 'tr',
        'notify_order_email' => true,
        'notify_order_sms' => false,
        'notify_ship_email' => true,
        'notify_ship_sms' => false,

        'checkout_gateways' => '',

        'shopify_checkout_url' => '',
        'paytr_merchant_id' => '',
        'paytr_merchant_key' => '',
        'paytr_merchant_salt' => '',
        'paytr_test_mode' => true,

        'paytr_debug_on' => false,
        'paytr_no_installment' => false,
        'paytr_max_installment' => 0,
        'paytr_checkout_note' => 'Kart bilgileriniz PayTR güvenli ödeme ekranında alınır.',
        'store_phone' => '',
        'store_whatsapp' => '',
        'store_email' => '',
        'store_address' => '',
        'tax_rate_percent' => 20,
        'tax_included_in_prices' => true,
        'maintenance_mode' => false,
        'maintenance_message' => 'Site geçici olarak kapalı.',
        'ga4_measurement_id' => '',
        'meta_pixel_id' => '',
        'robots_txt_extra' => '',
        'google_site_verification' => '',
        'og_default_image' => '',
        'twitter_site' => '',
        'twitter_creator' => '',
        'social_links' => '',
        'seo_default_keywords' => '',
    ];
}

function ded_shop_checkout_gateways_filtered(array $settings, array $candidates): array
{
    $out = [];
    foreach ($candidates as $x) {
        if (!is_string($x) || $x === '') {
            continue;
        }
        if ($x === 'paytr' && !ded_paytr_configured($settings)) {
            continue;
        }
        if ($x === 'shopify_redirect' && trim((string) ($settings['shopify_checkout_url'] ?? '')) === '') {
            continue;
        }
        $out[] = $x;
    }

    return array_values(array_unique($out));
}

function ded_shop_checkout_gateways_resolved(array $settings): array
{
    $allowed = ['manual_transfer', 'cod', 'paytr', 'shopify_redirect', 'demo_completed'];
    $csv = trim((string) ($settings['checkout_gateways'] ?? ''));
    if ($csv !== '') {
        $list = [];
        foreach (array_map('trim', explode(',', $csv)) as $p) {
            if ($p !== '' && in_array($p, $allowed, true)) {
                $list[] = $p;
            }
        }

        return ded_shop_checkout_gateways_filtered($settings, $list);
    }
    $mode = (string) ($settings['checkout_mode'] ?? 'manual_transfer');
    if ($mode === 'disabled') {
        return [];
    }
    if (!in_array($mode, $allowed, true)) {
        $mode = 'manual_transfer';
    }

    return ded_shop_checkout_gateways_filtered($settings, [$mode]);
}

function ded_shopify_build_redirect_url(array $settings, array $orderRow): string
{
    $base = trim((string) ($settings['shopify_checkout_url'] ?? ''));
    if ($base === '') {
        return '';
    }
    $num = rawurlencode((string) ($orderRow['order_number'] ?? ''));
    $sep = strpos($base, '?') !== false ? '&' : '?';

    return $base . $sep . 'ded_ref=' . $num;
}

function ded_shop_settings_merge(array $patch): array
{
    $defaults = ded_shop_default_settings();
    foreach ($patch as $k => $v) {
        if (!array_key_exists($k, $defaults)) {
            continue;
        }
        if ($k === 'default_shipping_fee') {
            $defaults[$k] = is_numeric($v) ? (float) $v : $defaults[$k];
            continue;
        }
        if ($k === 'smtp_port') {
            $defaults[$k] = is_numeric($v) ? (int) $v : $defaults[$k];
            continue;
        }
        if (in_array($k, ['notify_order_email', 'notify_order_sms', 'notify_ship_email', 'notify_ship_sms'], true)) {
            $defaults[$k] = (bool) $v;
            continue;
        }
        if ($k === 'checkout_mode') {
            $allowed = ['disabled', 'manual_transfer', 'demo_completed', 'cod', 'paytr', 'shopify_redirect'];
            $sv = is_string($v) ? $v : (is_scalar($v) ? (string) $v : '');
            $defaults[$k] = in_array($sv, $allowed, true) ? $sv : $defaults[$k];
            continue;
        }
        if (in_array($k, ['paytr_test_mode', 'paytr_debug_on', 'paytr_no_installment'], true)) {
            $defaults[$k] = !empty($v);
            continue;
        }
        if ($k === 'paytr_max_installment') {
            $defaults[$k] = max(0, min(12, is_numeric($v) ? (int) $v : $defaults[$k]));
            continue;
        }
        $defaults[$k] = is_string($v) ? $v : (is_scalar($v) ? (string) $v : $defaults[$k]);
    }
    return $defaults;
}

function ded_shop_settings_get(PDO $pdo): array
{
    $row = $pdo->query('SELECT settings_json FROM ded_shop_settings WHERE id = 1')->fetch(PDO::FETCH_ASSOC);
    $j = [];
    if ($row && isset($row['settings_json'])) {
        $decoded = json_decode((string) $row['settings_json'], true);
        $j = is_array($decoded) ? $decoded : [];
    }
    return ded_shop_settings_merge($j);
}

function ded_shop_settings_save(PDO $pdo, array $incoming): void
{
    $merged = ded_shop_settings_merge(array_merge(ded_shop_settings_get($pdo), $incoming));
    $json = json_encode($merged, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $stmt = $pdo->prepare('INSERT INTO ded_shop_settings (id, settings_json) VALUES (1, :j)
        ON DUPLICATE KEY UPDATE settings_json = VALUES(settings_json)');
    $stmt->execute([':j' => $json]);
}

function ded_shop_public_config(PDO $pdo): array
{
    $s = ded_shop_settings_get($pdo);
    $gw = ded_shop_checkout_gateways_resolved($s);
    $legacyMode = $gw === [] ? 'disabled' : (count($gw) === 1 ? $gw[0] : 'multi');

    return [
        'checkout_mode' => $legacyMode,
        'checkout_gateways' => $gw,
        'checkout_multi' => count($gw) > 1,
        'checkout_intro' => (string) ($s['checkout_intro'] ?? ''),
        'bank_instructions' => $s['bank_instructions'],
        'cod_instructions' => (string) ($s['cod_instructions'] ?? ''),
        'payment_demo_notice' => (string) ($s['payment_demo_notice'] ?? ''),
        'default_shipping_fee' => (float) $s['default_shipping_fee'],
        'shipping_note' => $s['shipping_note'],
        'currency' => $s['currency'],
        'paytr_checkout_note' => (string) ($s['paytr_checkout_note'] ?? ''),
        'store_phone' => (string) ($s['store_phone'] ?? ''),
        'store_email' => (string) ($s['store_email'] ?? ''),
        'tax_rate_percent' => (float) ($s['tax_rate_percent'] ?? 20),
        'tax_included_in_prices' => !empty($s['tax_included_in_prices']),
        'maintenance_mode' => !empty($s['maintenance_mode']),
    ];
}

function ded_coupon_row_by_code(PDO $pdo, string $code): ?array
{
    $code = trim($code);
    if ($code === '') {
        return null;
    }
    $stmt = $pdo->prepare('SELECT * FROM ded_coupons WHERE UPPER(code) = UPPER(?) LIMIT 1');
    $stmt->execute([$code]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    return $r ?: null;
}

function ded_coupon_evaluate(PDO $pdo, string $code, float $subtotal): array
{
    $c = ded_coupon_row_by_code($pdo, $code);
    if (!$c) {
        return ['ok' => false, 'message' => 'Kupon bulunamadı.', 'discount' => 0.0, 'coupon' => null];
    }
    if (!(int) ($c['active'] ?? 0)) {
        return ['ok' => false, 'message' => 'Bu kupon aktif değil.', 'discount' => 0.0, 'coupon' => $c];
    }
    $today = gmdate('Y-m-d');
    if (!empty($c['starts_at']) && (string) $c['starts_at'] > $today) {
        return ['ok' => false, 'message' => 'Kupon henüz geçerli değil.', 'discount' => 0.0, 'coupon' => $c];
    }
    if (!empty($c['ends_at']) && (string) $c['ends_at'] < $today) {
        return ['ok' => false, 'message' => 'Kuponun süresi dolmuş.', 'discount' => 0.0, 'coupon' => $c];
    }
    $max = $c['max_uses'];
    if ($max !== null && (int) $max >= 0 && (int) ($c['used_count'] ?? 0) >= (int) $max) {
        return ['ok' => false, 'message' => 'Kupon kullanım limiti doldu.', 'discount' => 0.0, 'coupon' => $c];
    }
    $min = (float) ($c['min_subtotal'] ?? 0);
    if ($subtotal < $min) {
        return [
            'ok' => false,
            'message' => 'Bu kupon için minimum sepet: ' . number_format($min, 2, ',', '.') . ' ₺',
            'discount' => 0.0,
            'coupon' => $c,
        ];
    }
    $type = (string) ($c['discount_type'] ?? 'percent');
    $val = (float) ($c['discount_value'] ?? 0);
    $discount = 0.0;
    if ($type === 'percent') {
        $discount = round($subtotal * ($val / 100.0), 2);
    } else {
        $discount = min($subtotal, round($val, 2));
    }
    return ['ok' => true, 'message' => 'Kupon uygulandı.', 'discount' => $discount, 'coupon' => $c];
}

function ded_coupon_increment_used(PDO $pdo, int $couponId): void
{
    $pdo->prepare('UPDATE ded_coupons SET used_count = used_count + 1 WHERE id = ?')->execute([$couponId]);
}

function ded_shop_normalize_cart_lines(PDO $pdo, array $lines): array
{
    $currency = 'TRY';
    $out = [];
    $subtotal = 0.0;
    foreach ($lines as $line) {
        if (!is_array($line)) {
            continue;
        }
        $slug = trim((string) ($line['slug'] ?? ''));
        $title = (string) ($line['title'] ?? '');
        $qty = max(1, (int) ($line['qty'] ?? 1));
        $variant = (string) ($line['variant'] ?? '');
        $price = (float) ($line['price'] ?? 0);
        $image = (string) ($line['image'] ?? '');

        $stmt = $pdo->prepare(
            'SELECT p.price AS base_price, p.currency AS cur, v.price AS vprice, v.currency AS vcur
             FROM ded_products p
             LEFT JOIN ded_product_variants v ON v.product_id = p.id AND v.name = ?
             WHERE p.slug = ? LIMIT 1'
        );
        $stmt->execute([$variant !== '' ? $variant : '__none__', $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $useV = $variant !== '' && $row['vprice'] !== null;
            $serverPrice = $useV ? (float) $row['vprice'] : (float) $row['base_price'];
            $currency = (string) ($useV ? ($row['vcur'] ?? $currency) : ($row['cur'] ?? $currency));
            if ($serverPrice > 0) {
                $price = $serverPrice;
            }
        }
        $lineTotal = round($price * $qty, 2);
        $subtotal += $lineTotal;
        $out[] = [
            'slug' => $slug,
            'title' => $title,
            'price' => $price,
            'qty' => $qty,
            'variant' => $variant,
            'image' => $image,
            'line_total' => $lineTotal,
        ];
    }
    return ['lines' => $out, 'subtotal' => round($subtotal, 2), 'currency' => $currency];
}

function ded_order_create(PDO $pdo, array $payload): array
{
    $settings = ded_shop_settings_get($pdo);
    $gwList = ded_shop_checkout_gateways_resolved($settings);
    if ($gwList === []) {
        throw new RuntimeException('checkout_disabled');
    }
    if (count($gwList) > 1) {
        $chosen = trim((string) ($payload['gateway'] ?? ''));
        if (!in_array($chosen, $gwList, true)) {
            throw new RuntimeException('gateway_required');
        }
        $mode = $chosen;
    } else {
        $mode = $gwList[0];
    }

    $linesIn = $payload['lines'] ?? [];
    if (!is_array($linesIn) || $linesIn === []) {
        throw new RuntimeException('empty_cart');
    }

    $norm = ded_shop_normalize_cart_lines($pdo, $linesIn);
    if ($norm['lines'] === []) {
        throw new RuntimeException('empty_cart');
    }

    $customer = $payload['customer'] ?? [];
    $shipping = $payload['shipping'] ?? [];
    $email = strtolower(trim((string) ($customer['email'] ?? '')));
    $name = trim((string) ($customer['name'] ?? ''));
    $phone = trim((string) ($customer['phone'] ?? ''));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('bad_email');
    }
    if ($name === '') {
        throw new RuntimeException('bad_name');
    }

    $addr = trim((string) ($shipping['address_line'] ?? ''));
    $city = trim((string) ($shipping['city'] ?? ''));
    $country = trim((string) ($shipping['country'] ?? 'TR')) ?: 'TR';

    $couponCode = trim((string) ($payload['coupon_code'] ?? ''));
    $discount = 0.0;
    $couponId = null;
    if ($couponCode !== '') {
        $ev = ded_coupon_evaluate($pdo, $couponCode, $norm['subtotal']);
        if (!$ev['ok']) {
            throw new RuntimeException('coupon_invalid');
        }
        $discount = (float) $ev['discount'];
        $couponId = (int) ($ev['coupon']['id'] ?? 0);
        if ($couponId <= 0) {
            throw new RuntimeException('coupon_invalid');
        }
    }

    $shipFee = (float) ($settings['default_shipping_fee'] ?? 0);
    $total = max(0, round($norm['subtotal'] - $discount + $shipFee, 2));
    $currency = (string) ($norm['currency'] ?? ($settings['currency'] ?? 'TRY'));

    $paymentStatus = 'unpaid';
    $paymentMethod = 'manual_transfer';
    if ($mode === 'demo_completed') {
        $paymentStatus = 'paid';
        $paymentMethod = 'demo';
    } elseif ($mode === 'cod') {
        $paymentStatus = 'unpaid';
        $paymentMethod = 'cod';
    } elseif ($mode === 'paytr') {
        $paymentStatus = 'unpaid';
        $paymentMethod = 'paytr_pending';
    } elseif ($mode === 'shopify_redirect') {
        $paymentStatus = 'unpaid';
        $paymentMethod = 'shopify_redirect';
    }

    $orderNumber = 'ORD-' . gmdate('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

    $cartJson = json_encode($norm['lines'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO ded_orders
            (order_number, customer_email, customer_name, customer_phone,
             shipping_address_line, shipping_city, shipping_country, cart_json,
             subtotal, discount_amount, shipping_fee, total, currency, coupon_code,
             status, payment_status, payment_method, created_at, updated_at)
             VALUES
            (:num, :em, :nm, :ph, :ad, :ci, :co, :cj, :sub, :disc, :ship, :tot, :cur, :cc,
             :st, :ps, :pm, NOW(), NOW())'
        );
        $stmt->execute([
            ':num' => $orderNumber,
            ':em' => $email,
            ':nm' => $name,
            ':ph' => $phone,
            ':ad' => $addr,
            ':ci' => $city,
            ':co' => $country,
            ':cj' => $cartJson,
            ':sub' => $norm['subtotal'],
            ':disc' => $discount,
            ':ship' => $shipFee,
            ':tot' => $total,
            ':cur' => $currency,
            ':cc' => $couponCode !== '' ? $couponCode : null,
            ':st' => 'pending',
            ':ps' => $paymentStatus,
            ':pm' => $paymentMethod,
        ]);
        $orderId = (int) $pdo->lastInsertId();

        $ins = $pdo->prepare(
            'INSERT INTO ded_order_items
            (order_id, product_slug, product_title, unit_price, qty, variant_label, line_total)
            VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        foreach ($norm['lines'] as $L) {
            $ins->execute([
                $orderId,
                (string) $L['slug'],
                (string) $L['title'],
                (float) $L['price'],
                (int) $L['qty'],
                (string) $L['variant'],
                (float) $L['line_total'],
            ]);
        }

        if ($couponId) {
            ded_coupon_increment_used($pdo, $couponId);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    return ded_order_get($pdo, $orderId) ?? ['order_number' => $orderNumber, 'id' => $orderId];
}

function ded_order_get(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM ded_orders WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    return $r ?: null;
}

function ded_order_by_number(PDO $pdo, string $num): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM ded_orders WHERE order_number = ? LIMIT 1');
    $stmt->execute([$num]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    return $r ?: null;
}

function ded_order_items(PDO $pdo, int $orderId): array
{
    $stmt = $pdo->prepare('SELECT * FROM ded_order_items WHERE order_id = ? ORDER BY id ASC');
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ded_orders_list(PDO $pdo, int $limit = 50, int $offset = 0, ?string $status = null): array
{
    $limit = max(1, min(200, $limit));
    $offset = max(0, $offset);
    $where = '';
    $params = [];
    if ($status !== null && $status !== '') {
        $where = ' WHERE status = ? ';
        $params[] = $status;
    }
    $cntStmt = $pdo->prepare('SELECT COUNT(*) AS c FROM ded_orders' . $where);
    $cntStmt->execute($params);
    $total = (int) ($cntStmt->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);

    $sql = 'SELECT * FROM ded_orders' . $where . ' ORDER BY id DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return ['items' => $items, 'total' => $total];
}

function ded_order_update(PDO $pdo, int $id, array $patch): array
{
    $row = ded_order_get($pdo, $id);
    if (!$row) {
        throw new RuntimeException('not_found');
    }
    $allowed = [
        'status' => true,
        'payment_status' => true,
        'payment_method' => true,
        'tracking_number' => true,
        'carrier' => true,
        'admin_notes' => true,
    ];
    $sets = [];
    $params = [':id' => $id];
    foreach ($patch as $k => $v) {
        if (!isset($allowed[$k])) {
            continue;
        }
        $sets[] = "`{$k}` = :{$k}";
        $params[":{$k}"] = $v;
    }
    if ($sets === []) {
        return $row;
    }
    $sets[] = 'updated_at = NOW()';
    $sql = 'UPDATE ded_orders SET ' . implode(', ', $sets) . ' WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return ded_order_get($pdo, $id) ?? $row;
}

function ded_order_mark_paid_paytr(PDO $pdo, int $orderId, int $totalAmountKurusPost): bool
{
    $row = ded_order_get($pdo, $orderId);
    if (!$row) {
        return false;
    }
    if (($row['payment_status'] ?? '') === 'paid') {
        return true;
    }
    $expected = (int) round(((float) ($row['total'] ?? 0)) * 100);
    if ($expected < 1) {
        return false;
    }
    if ($totalAmountKurusPost + 2 < $expected) {
        return false;
    }
    ded_order_update($pdo, $orderId, ['payment_status' => 'paid', 'payment_method' => 'paytr']);

    return true;
}

function ded_coupons_list(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM ded_coupons ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
}

function ded_coupon_save(PDO $pdo, ?int $id, array $data): int
{
    $code = strtoupper(trim((string) ($data['code'] ?? '')));
    if ($code === '') {
        throw new RuntimeException('bad_code');
    }
    $type = (string) ($data['discount_type'] ?? 'percent');
    if (!in_array($type, ['percent', 'fixed'], true)) {
        $type = 'percent';
    }
    $val = (float) ($data['discount_value'] ?? 0);
    $min = (float) ($data['min_subtotal'] ?? 0);
    $maxUses = $data['max_uses'];
    $maxUses = $maxUses === null || $maxUses === '' ? null : (int) $maxUses;
    $starts = $data['starts_at'] ?? null;
    $ends = $data['ends_at'] ?? null;
    $starts = $starts === '' || $starts === null ? null : (string) $starts;
    $ends = $ends === '' || $ends === null ? null : (string) $ends;
    $active = (int) (!empty($data['active']));

    if ($id) {
        $stmt = $pdo->prepare(
            'UPDATE ded_coupons SET code = ?, discount_type = ?, discount_value = ?, min_subtotal = ?,
             max_uses = ?, starts_at = ?, ends_at = ?, active = ? WHERE id = ?'
        );
        $stmt->execute([$code, $type, $val, $min, $maxUses, $starts, $ends, $active, $id]);
        return $id;
    }
    $stmt = $pdo->prepare(
        'INSERT INTO ded_coupons (code, discount_type, discount_value, min_subtotal, max_uses, starts_at, ends_at, active)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([$code, $type, $val, $min, $maxUses, $starts, $ends, $active]);
    return (int) $pdo->lastInsertId();
}

function ded_coupon_delete(PDO $pdo, int $id): void
{
    $pdo->prepare('DELETE FROM ded_coupons WHERE id = ?')->execute([$id]);
}

function ded_stats_dashboard(PDO $pdo): array
{
    $orders = (int) ($pdo->query('SELECT COUNT(*) AS c FROM ded_orders')->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
    $revRow = $pdo->query(
        "SELECT COALESCE(SUM(total),0) AS r FROM ded_orders WHERE payment_status IN ('paid','awaiting_transfer')"
    )->fetch(PDO::FETCH_ASSOC);
    $revenue = (float) ($revRow['r'] ?? 0);
    $pending = (int) ($pdo->query("SELECT COUNT(*) AS c FROM ded_orders WHERE status = 'pending'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);

    $top = $pdo->query(
        "SELECT oi.product_slug, oi.product_title, SUM(oi.qty) AS sold, SUM(oi.line_total) AS rev
         FROM ded_order_items oi
         INNER JOIN ded_orders o ON o.id = oi.order_id
         WHERE o.payment_status IN ('paid','awaiting_transfer')
         GROUP BY oi.product_slug, oi.product_title
         ORDER BY sold DESC
         LIMIT 15"
    )->fetchAll(PDO::FETCH_ASSOC);

    return [
        'order_count' => $orders,
        'revenue_total' => $revenue,
        'pending_orders' => $pending,
        'top_products' => $top,
    ];
}

function ded_stats_monthly(PDO $pdo, int $months): array
{
    $months = max(1, min(36, $months));
    $stmt = $pdo->prepare(
        "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym,
                COUNT(*) AS oc,
                COALESCE(SUM(total),0) AS rev
         FROM ded_orders
         WHERE payment_status IN ('paid','awaiting_transfer')
           AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL ? MONTH)
         GROUP BY ym
         ORDER BY ym ASC"
    );
    $stmt->execute([$months]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'ym' => (string) $r['ym'],
            'revenue' => (float) $r['rev'],
            'orders' => (int) $r['oc'],
        ];
    }
    return $out;
}

function ded_notification_log_list(PDO $pdo, int $limit = 100): array
{
    $limit = max(1, min(500, $limit));
    $stmt = $pdo->prepare('SELECT * FROM ded_notification_log ORDER BY id DESC LIMIT ' . (int) $limit);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ded_notification_log_insert(PDO $pdo, string $channel, string $recipient, string $subject, string $preview, string $status, ?array $meta): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO ded_notification_log (channel, recipient, subject, body_preview, status, meta_json)
         VALUES (?,?,?,?,?,?)'
    );
    $mj = $meta === null ? null : json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $stmt->execute([$channel, $recipient, $subject, $preview, $status, $mj]);
}

function ded_shop_bestsellers(PDO $pdo, int $limit = 80, ?int $days = null): array
{
    $limit = max(1, min(500, $limit));
    $where = "WHERE o.payment_status IN ('paid','awaiting_transfer')";
    $params = [];
    if ($days !== null && $days > 0) {
        $where .= ' AND o.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)';
        $params[] = $days;
    }
    $sql = "SELECT oi.product_slug AS slug, oi.product_title AS title,
                   SUM(oi.qty) AS sold, SUM(oi.line_total) AS revenue
            FROM ded_order_items oi
            INNER JOIN ded_orders o ON o.id = oi.order_id
            {$where}
            GROUP BY oi.product_slug, oi.product_title
            ORDER BY sold DESC
            LIMIT " . (int) $limit;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ded_shop_distinct_customer_emails(PDO $pdo, int $limit = 2000): array
{
    $limit = max(50, min(20000, $limit));
    $sql = 'SELECT LOWER(TRIM(customer_email)) AS em FROM ded_orders
            WHERE customer_email IS NOT NULL AND TRIM(customer_email) <> \'\'
            GROUP BY em
            ORDER BY MAX(id) DESC
            LIMIT ' . (int) $limit;
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    $out = [];
    foreach ($rows as $em) {
        $em = (string) $em;
        if ($em !== '' && filter_var($em, FILTER_VALIDATE_EMAIL)) {
            $out[] = $em;
        }
    }
    return $out;
}

function ded_shop_distinct_customer_phones(PDO $pdo, int $limit = 2000): array
{
    $limit = max(50, min(20000, $limit));
    $sql = 'SELECT customer_phone FROM ded_orders
            WHERE customer_phone IS NOT NULL AND TRIM(customer_phone) <> \'\'
            GROUP BY customer_phone
            ORDER BY MAX(id) DESC
            LIMIT ' . (int) $limit;
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    $out = [];
    foreach ($rows as $ph) {
        $ph = preg_replace('/\D+/', '', (string) $ph);
        if ($ph !== '' && strlen($ph) >= 10) {
            $out[$ph] = true;
        }
    }
    return array_keys($out);
}

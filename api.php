<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once DED_ROOT . '/lib/kimlik.php';
require_once DED_ROOT . '/lib/katalogdepo.php';
require_once DED_ROOT . '/lib/magazadepo.php';
require_once DED_ROOT . '/lib/bildirimgonder.php';
require_once DED_ROOT . '/lib/ekstra.php';

header('Content-Type: application/json; charset=utf-8');

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '') {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
    header('Access-Control-Allow-Headers: Authorization, X-Authorization, Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function ded_json_out($data, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function ded_bearer_token(): ?string
{
    $try = static function (string $h): ?string {
        if ($h !== '' && preg_match('/Bearer\s+(\S+)/i', $h, $m)) {
            return $m[1];
        }
        return null;
    };
    foreach (
        [
            (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? ''),
            (string) ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? ''),
            (string) ($_SERVER['HTTP_X_AUTHORIZATION'] ?? ''),
        ] as $h
    ) {
        $t = $try($h);
        if ($t !== null) {
            return $t;
        }
    }
    if (function_exists('apache_request_headers')) {
        foreach (apache_request_headers() as $k => $v) {
            if (strcasecmp((string) $k, 'Authorization') === 0) {
                $t = $try((string) $v);
                if ($t !== null) {
                    return $t;
                }
            }
        }
    }
    return null;
}

function ded_require_token(): void
{
    $auth = ded_read_auth();
    $t = ded_bearer_token();
    if ($t === null || $t === '' || empty($auth['api_token']) || !hash_equals((string) $auth['api_token'], $t)) {
        ded_json_out(['ok' => false, 'error' => 'auth_required'], 401);
    }
}

function ded_catalog_schema_check(array $c): bool
{
    return isset($c['site'], $c['products'], $c['collections'], $c['pages'])
        && is_array($c['products']) && is_array($c['collections']) && is_array($c['pages']);
}

function ded_storage(): string
{
    return ded_db_ready() ? 'mysql' : 'unavailable';
}

function ded_catalog_get_merged(): ?array
{
    $pdo = ded_pdo();
    if (!$pdo || !ded_db_ready()) {
        return null;
    }
    $cat = ded_catalog_fetch($pdo);
    $cat['version'] = (int) ($cat['version'] ?? 1);
    $cat['updatedAt'] = gmdate('c');

    return $cat;
}

ded_ensure_auth_file();

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

$raw = file_get_contents('php://input');
$input = $raw !== '' ? json_decode($raw, true) : [];
if (!is_array($input)) {
    $input = [];
}

if ($method === 'GET' && $path === 'ping') {
    $pdo = ded_pdo();
    ded_json_out([
        'ok' => true,
        'storage' => ded_storage(),
        'mysql' => ded_db_ready(),
        'db_connected' => $pdo !== null,
        'config' => is_readable(DED_CONFIG_FILE),
        'api' => 'ded-api',
    ]);
}

if ($method === 'GET' && $path === 'public-shop-config') {
    header('Access-Control-Allow-Origin: *');
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable', 'hint' => 'php tools/magazatablo.php'], 503);
    }
    ded_json_out(['ok' => true, 'config' => ded_shop_public_config($pdo)]);
}

if ($method === 'POST' && $path === 'public-newsletter') {
    header('Access-Control-Allow-Origin: *');
    $pdo = ded_pdo();
    if (!$pdo) {
        ded_json_out(['ok' => false, 'message' => 'Servis kullanılamıyor.'], 503);
    }
    if (trim((string) ($input['website'] ?? '')) !== '') {
        ded_json_out(['ok' => false, 'message' => 'Geçersiz istek.'], 400);
    }
    $ev = ded_newsletter_subscribe(
        $pdo,
        (string) ($input['email'] ?? ''),
        (string) ($input['name'] ?? ''),
        (string) ($input['source'] ?? 'api'),
    );
    ded_json_out(['ok' => $ev['ok'], 'message' => $ev['message']], $ev['ok'] ? 200 : 400);
}

if ($method === 'POST' && $path === 'public-review') {
    header('Access-Control-Allow-Origin: *');
    $pdo = ded_pdo();
    if (!$pdo) {
        ded_json_out(['ok' => false, 'message' => 'Servis kullanılamıyor.'], 503);
    }
    if (trim((string) ($input['website'] ?? '')) !== '') {
        ded_json_out(['ok' => false, 'message' => 'Geçersiz istek.'], 400);
    }
    $ev = ded_review_submit(
        $pdo,
        (string) ($input['product_slug'] ?? ''),
        (string) ($input['author_name'] ?? ''),
        (string) ($input['author_email'] ?? ''),
        (int) ($input['rating'] ?? 5),
        (string) ($input['body'] ?? ''),
    );
    ded_json_out(['ok' => $ev['ok'], 'message' => $ev['message']], $ev['ok'] ? 200 : 400);
}

if ($method === 'GET' && $path === 'public-faq') {
    header('Access-Control-Allow-Origin: *');
    $pdo = ded_pdo();
    if (!$pdo) {
        ded_json_out(['ok' => false, 'items' => []], 503);
    }
    $items = ded_faq_list($pdo, true);
    ded_json_out(['ok' => true, 'items' => $items]);
}

if ($method === 'GET' && $path === 'public-reviews') {
    header('Access-Control-Allow-Origin: *');
    $pdo = ded_pdo();
    $slug = trim((string) ($_GET['slug'] ?? ''));
    if (!$pdo || $slug === '') {
        ded_json_out(['ok' => false, 'items' => []], 400);
    }
    ded_json_out(['ok' => true, 'items' => ded_reviews_for_product($pdo, $slug)]);
}

if ($method === 'POST' && $path === 'public-validate-coupon') {
    header('Access-Control-Allow-Origin: *');
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $code = trim((string) ($input['code'] ?? ''));
    $sub = (float) ($input['subtotal'] ?? 0);
    if ($code === '') {
        ded_json_out(['ok' => false, 'error' => 'code_required'], 400);
    }
    $ev = ded_coupon_evaluate($pdo, $code, $sub);
    ded_json_out([
        'ok' => $ev['ok'],
        'message' => $ev['message'],
        'discount' => $ev['discount'],
    ], $ev['ok'] ? 200 : 400);
}

if ($method === 'POST' && $path === 'public-create-order') {
    header('Access-Control-Allow-Origin: *');
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    if (trim((string) ($input['website'] ?? '')) !== '') {
        ded_json_out(['ok' => false, 'error' => 'invalid'], 400);
    }
    try {
        $order = ded_order_create($pdo, is_array($input) ? $input : []);
    } catch (Throwable $e) {
        $map = [
            'checkout_disabled' => 403,
            'empty_cart' => 400,
            'bad_email' => 400,
            'bad_name' => 400,
            'coupon_invalid' => 400,
            'gateway_required' => 400,
        ];
        $msg = $e->getMessage();
        $code = $map[$msg] ?? 400;
        ded_json_out(['ok' => false, 'error' => $msg], $code);
    }
    $full = ded_order_get($pdo, (int) ($order['id'] ?? 0)) ?? $order;
    $settings = ded_shop_settings_get($pdo);
    try {
        ded_notify_order_placed($pdo, $full, $settings);
    } catch (Throwable) {
    }
    $out = [
        'ok' => true,
        'order' => [
            'id' => (int) ($full['id'] ?? 0),
            'order_number' => (string) ($full['order_number'] ?? ''),
            'total' => (float) ($full['total'] ?? 0),
            'currency' => (string) ($full['currency'] ?? 'TRY'),
            'payment_status' => (string) ($full['payment_status'] ?? ''),
            'payment_method' => (string) ($full['payment_method'] ?? ''),
        ],
    ];
    if (($full['payment_method'] ?? '') === 'paytr_pending') {
        $base = ded_storefront_public_url();
        $onum = rawurlencode((string) ($full['order_number'] ?? ''));
        $cart = json_decode((string) ($full['cart_json'] ?? '[]'), true);
        if (!is_array($cart)) {
            $cart = [];
        }
        $tok = ded_paytr_get_iframe_token(
            $settings,
            $full,
            $cart,
            ded_client_ip(),
            $base . '/paytrdonus.php?st=ok&num=' . $onum,
            $base . '/paytrdonus.php?st=fail&num=' . $onum
        );
        if ($tok['ok']) {
            $out['paytr'] = ['iframe_token' => $tok['token']];
        } else {
            $out['paytr_error'] = $tok['error'] . (isset($tok['reason']) ? ': ' . $tok['reason'] : '');
        }
    }
    if (($full['payment_method'] ?? '') === 'shopify_redirect') {
        $rurl = ded_shopify_build_redirect_url($settings, $full);
        if ($rurl !== '') {
            $out['redirect_url'] = $rurl;
        }
    }
    ded_json_out($out);
}

if ($method === 'GET' && $path === 'public-home') {
    header('Access-Control-Allow-Origin: *');
    require_once DED_ROOT . '/lib/vuehome.php';
    $pdo = ded_pdo();
    if (!$pdo || !ded_db_ready()) {
        ded_json_out(['ok' => false, 'error' => 'catalog_unavailable'], 503);
    }
    ded_json_out(['ok' => true, 'home' => ded_vue_home_api_payload($pdo)]);
}

if ($method === 'GET' && $path === 'public-catalog') {
    header('Access-Control-Allow-Origin: *');
    $cat = ded_catalog_get_merged();
    if ($cat === null) {
        ded_json_out(['ok' => false, 'error' => 'catalog_unavailable', 'hint' => 'Veritabanı ve katalog tabloları gerekli.'], 503);
    }
    foreach ($cat['products'] ?? [] as &$p) {
        if (is_array($p)) {
            unset($p['sourceHtml']);
        }
    }
    unset($p);
    foreach ($cat['collections'] ?? [] as &$c) {
        if (is_array($c)) {
            unset($c['sourceHtml']);
        }
    }
    unset($c);
    foreach ($cat['pages'] ?? [] as &$pg) {
        if (is_array($pg)) {
            unset($pg['sourceHtml']);
        }
    }
    unset($pg);
    ded_json_out(['ok' => true, 'catalog' => $cat, 'storage' => ded_storage()]);
}

if ($method === 'POST' && $path === 'login') {
    ded_ensure_auth_file();
    $auth = ded_read_auth();
    $pw = (string) ($input['password'] ?? $_POST['password'] ?? '');
    if ($pw === '' || empty($auth['password_hash']) || !password_verify($pw, (string) $auth['password_hash'])) {
        ded_json_out(['ok' => false, 'error' => 'invalid_password'], 401);
    }
    $auth['api_token'] = bin2hex(random_bytes(32));
    ded_write_auth($auth);
    ded_json_out(['ok' => true, 'token' => $auth['api_token']]);
}

if ($method === 'POST' && $path === 'logout') {
    $auth = ded_read_auth();
    $auth['api_token'] = null;
    ded_write_auth($auth);
    ded_json_out(['ok' => true]);
}

if ($method === 'GET' && $path === 'me') {
    $auth = ded_read_auth();
    $t = ded_bearer_token();
    $ok = $t && !empty($auth['api_token']) && hash_equals((string) $auth['api_token'], $t);
    ded_json_out(['ok' => true, 'authenticated' => $ok, 'storage' => ded_storage()]);
}

if ($method === 'POST' && $path === 'password') {
    ded_require_token();
    $auth = ded_read_auth();
    $current = (string) ($input['current'] ?? '');
    $next = (string) ($input['new'] ?? '');
    if ($next === '' || strlen($next) < 6) {
        ded_json_out(['ok' => false, 'error' => 'weak_password'], 400);
    }
    if (empty($auth['password_hash']) || !password_verify($current, (string) $auth['password_hash'])) {
        ded_json_out(['ok' => false, 'error' => 'invalid_current'], 401);
    }
    $auth['password_hash'] = password_hash($next, PASSWORD_DEFAULT);
    $auth['api_token'] = bin2hex(random_bytes(32));
    ded_write_auth($auth);
    ded_json_out(['ok' => true, 'token' => $auth['api_token']]);
}

if ($method === 'GET' && $path === 'catalog') {
    ded_require_token();
    $cat = ded_catalog_get_merged();
    if ($cat === null) {
        ded_json_out([
            'ok' => false,
            'error' => ded_db_ready() ? 'catalog_empty' : 'database_unavailable',
            'hint' => 'config.local.php + schema.sql içe aktarımı veya php tools/katalogaktar.php',
        ], 404);
    }
    $cat['updatedAt'] = $cat['updatedAt'] ?? gmdate('c');
    ded_json_out(['ok' => true, 'catalog' => $cat, 'storage' => ded_storage()]);
}

if ($method === 'PUT' && $path === 'catalog') {
    ded_require_token();
    $cat = $input['catalog'] ?? null;
    if (!is_array($cat) || !ded_catalog_schema_check($cat)) {
        ded_json_out(['ok' => false, 'error' => 'invalid_catalog'], 400);
    }
    $cat['version'] = (int) ($cat['version'] ?? 1);
    $cat['updatedAt'] = gmdate('c');

    $pdo = ded_pdo();
    if (!$pdo || !ded_db_ready()) {
        ded_json_out(['ok' => false, 'error' => 'database_unavailable'], 503);
    }
    try {
        ded_catalog_save($pdo, $cat);
    } catch (Throwable $e) {
        ded_json_out(['ok' => false, 'error' => 'mysql_save_failed', 'detail' => $e->getMessage()], 500);
    }
    ded_json_out(['ok' => true, 'storage' => 'mysql']);
}

if ($method === 'GET' && $path === 'media') {
    ded_require_token();
    $base = DED_ROOT . '/cdn';
    $list = [];
    if (is_dir($base)) {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {

            if (!$file->isFile()) {
                continue;
            }
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'ico'], true)) {
                continue;
            }
            $full = $file->getPathname();
            $rel = str_replace(['\\', '//'], ['/', '/'], substr($full, strlen(DED_ROOT) + 1));
            $list[] = ['path' => $rel, 'size' => $file->getSize()];
        }
    }
    usort($list, fn ($a, $b) => strcmp($a['path'], $b['path']));
    ded_json_out(['ok' => true, 'items' => $list]);
}

if ($method === 'GET' && $path === 'shop-stats') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable', 'hint' => 'php tools/magazatablo.php'], 503);
    }
    ded_json_out(['ok' => true, 'stats' => ded_stats_dashboard($pdo)]);
}

if ($method === 'GET' && $path === 'shop-accounting') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $months = (int) ($_GET['months'] ?? 12);
    ded_json_out(['ok' => true, 'series' => ded_stats_monthly($pdo, $months)]);
}

if ($method === 'GET' && $path === 'shop-settings') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    ded_json_out(['ok' => true, 'settings' => ded_shop_settings_get($pdo)]);
}

if ($method === 'PUT' && $path === 'shop-settings') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $incoming = $input['settings'] ?? $input;
    if (!is_array($incoming)) {
        ded_json_out(['ok' => false, 'error' => 'invalid_settings'], 400);
    }
    ded_shop_settings_save($pdo, $incoming);
    ded_json_out(['ok' => true, 'settings' => ded_shop_settings_get($pdo)]);
}

if ($method === 'GET' && $path === 'shop-orders') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $limit = (int) ($_GET['limit'] ?? 50);
    $offset = (int) ($_GET['offset'] ?? 0);
    $st = isset($_GET['status']) ? trim((string) $_GET['status']) : '';
    $r = ded_orders_list($pdo, $limit, $offset, $st === '' ? null : $st);
    ded_json_out(['ok' => true, 'items' => $r['items'], 'total' => $r['total']]);
}

if ($method === 'GET' && $path === 'shop-order') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        ded_json_out(['ok' => false, 'error' => 'bad_id'], 400);
    }
    $row = ded_order_get($pdo, $id);

    if (!$row) {
        ded_json_out(['ok' => false, 'error' => 'not_found'], 404);
    }
    $items = ded_order_items($pdo, $id);
    $cart = json_decode((string) ($row['cart_json'] ?? '[]'), true);
    if (!is_array($cart)) {
        $cart = [];
    }
    ded_json_out(['ok' => true, 'order' => $row, 'items' => $items, 'cart' => $cart]);
}

if (($method === 'PATCH' || $method === 'POST') && $path === 'shop-order-update') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $id = (int) ($input['id'] ?? 0);
    if ($id <= 0) {
        ded_json_out(['ok' => false, 'error' => 'bad_id'], 400);
    }
    $old = ded_order_get($pdo, $id);
    if (!$old) {
        ded_json_out(['ok' => false, 'error' => 'not_found'], 404);
    }
    $patch = [];
    foreach (['status', 'payment_status', 'tracking_number', 'carrier', 'admin_notes'] as $k) {
        if (array_key_exists($k, $input)) {
            $patch[$k] = (string) $input[$k];
        }
    }
    try {
        $new = ded_order_update($pdo, $id, $patch);
    } catch (Throwable $e) {
        ded_json_out(['ok' => false, 'error' => $e->getMessage()], 400);
    }
    $settings = ded_shop_settings_get($pdo);
    $oldTr = trim((string) ($old['tracking_number'] ?? ''));
    $newTr = trim((string) ($new['tracking_number'] ?? ''));
    if ($newTr !== '' && $newTr !== $oldTr) {
        try {
            ded_notify_shipment($pdo, $new, $settings, $newTr, (string) ($new['carrier'] ?? ''));
        } catch (Throwable) {
        }
    }
    ded_json_out(['ok' => true, 'order' => $new]);
}

if ($method === 'GET' && $path === 'shop-coupons') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    ded_json_out(['ok' => true, 'items' => ded_coupons_list($pdo)]);
}

if ($method === 'POST' && $path === 'shop-coupon') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $cid = isset($input['id']) ? (int) $input['id'] : 0;
    $cid = $cid > 0 ? $cid : null;
    try {
        $nid = ded_coupon_save($pdo, $cid, $input);
    } catch (Throwable) {
        ded_json_out(['ok' => false, 'error' => 'save_failed'], 400);
    }
    ded_json_out(['ok' => true, 'id' => $nid]);
}

if ($method === 'DELETE' && $path === 'shop-coupon') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        ded_json_out(['ok' => false, 'error' => 'bad_id'], 400);
    }
    ded_coupon_delete($pdo, $id);
    ded_json_out(['ok' => true]);
}

if ($method === 'GET' && $path === 'shop-notifications') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $limit = (int) ($_GET['limit'] ?? 100);
    ded_json_out(['ok' => true, 'items' => ded_notification_log_list($pdo, $limit)]);
}

if ($method === 'POST' && $path === 'shop-test-email') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $to = trim((string) ($input['to'] ?? ''));
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        ded_json_out(['ok' => false, 'error' => 'bad_email'], 400);
    }
    $settings = ded_shop_settings_get($pdo);
    $sub = (string) ($input['subject'] ?? 'Laykids test e-postası');
    $html = (string) ($input['body'] ?? '<p>Test mesajı.</p>');
    $ok = ded_mail_send($settings, $to, $sub, $html, strip_tags($html));
    ded_notification_log_insert($pdo, 'email', $to, $sub, 'test', $ok ? 'sent' : 'failed', null);
    ded_json_out(['ok' => $ok]);
}

if ($method === 'POST' && $path === 'shop-test-sms') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $num = trim((string) ($input['phone'] ?? ''));
    if ($num === '') {
        ded_json_out(['ok' => false, 'error' => 'bad_phone'], 400);
    }
    $settings = ded_shop_settings_get($pdo);
    $msg = (string) ($input['message'] ?? 'Laykids SMS test');
    $res = ded_toplus_sms_send($settings, $num, $msg);
    ded_notification_log_insert(
        $pdo,
        'sms',
        $num,
        'test',
        $msg,
        $res['ok'] ? 'sent' : 'failed',
        $res
    );
    ded_json_out(['ok' => $res['ok'], 'detail' => $res['detail'] ?? '', 'raw' => $res['raw'] ?? '']);
}

if ($method === 'POST' && $path === 'shop-send-customer-message') {
    ded_require_token();
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        ded_json_out(['ok' => false, 'error' => 'shop_unavailable'], 503);
    }
    $oid = (int) ($input['order_id'] ?? 0);
    $channel = (string) ($input['channel'] ?? '');
    $message = trim((string) ($input['message'] ?? ''));
    if ($oid <= 0 || $message === '' || !in_array($channel, ['email', 'sms'], true)) {
        ded_json_out(['ok' => false, 'error' => 'bad_request'], 400);
    }
    $order = ded_order_get($pdo, $oid);
    if (!$order) {
        ded_json_out(['ok' => false, 'error' => 'not_found'], 404);
    }
    $settings = ded_shop_settings_get($pdo);
    if ($channel === 'email') {
        $em = (string) ($order['customer_email'] ?? '');
        if ($em === '') {
            ded_json_out(['ok' => false, 'error' => 'no_email'], 400);
        }
        $sub = (string) ($input['subject'] ?? ('Sipariş ' . ($order['order_number'] ?? '')));
        $ok = ded_mail_send($settings, $em, $sub, '<p>' . nl2br(ded_h($message)) . '</p>', $message);
        ded_notification_log_insert($pdo, 'email', $em, $sub, $message, $ok ? 'sent' : 'failed', ['order_id' => $oid, 'manual' => true]);
        ded_json_out(['ok' => $ok]);
    }
    $phone = (string) ($order['customer_phone'] ?? '');
    if ($phone === '') {
        ded_json_out(['ok' => false, 'error' => 'no_phone'], 400);
    }
    $res = ded_toplus_sms_send($settings, $phone, $message);
    ded_notification_log_insert($pdo, 'sms', $phone, 'manual', $message, $res['ok'] ? 'sent' : 'failed', ['order_id' => $oid]);
    ded_json_out(['ok' => $res['ok'], 'detail' => $res['detail'] ?? '']);
}

if ($method === 'POST' && $path === 'upload') {
    ded_require_token();
    if (empty($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        ded_json_out(['ok' => false, 'error' => 'no_file'], 400);
    }
    $targetDir = DED_ROOT . '/' . UPLOAD_SUBDIR;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $orig = basename((string) $_FILES['file']['name']);
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true)) {
        ded_json_out(['ok' => false, 'error' => 'bad_extension'], 400);
    }
    $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($orig, PATHINFO_FILENAME)) ?? 'file';
    $name = $safe . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = $targetDir . '/' . $name;
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
        ded_json_out(['ok' => false, 'error' => 'move_failed'], 500);
    }
    $rel = UPLOAD_SUBDIR . '/' . $name;
    ded_json_out(['ok' => true, 'path' => str_replace('\\', '/', $rel)]);
}

ded_json_out(['ok' => false, 'error' => 'not_found'], 404);

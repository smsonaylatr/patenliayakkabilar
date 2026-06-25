<?php

declare(strict_types=1);

require_once __DIR__ . '/magazadepo.php';

function ded_musteri_listesi(PDO $pdo, int $limit = 200, int $offset = 0, string $q = ''): array
{
    $limit = max(1, min(500, $limit));
    $offset = max(0, $offset);
    $q = trim($q);
    $where = "WHERE customer_email <> ''";
    $params = [];
    if ($q !== '') {
        $where .= ' AND (customer_email LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ?)';
        $like = '%' . $q . '%';
        $params = [$like, $like, $like];
    }
    $sqlCnt = "SELECT COUNT(*) FROM (
        SELECT LOWER(TRIM(customer_email)) AS em
        FROM ded_orders {$where}
        GROUP BY em
    ) t";
    $stmtCnt = $pdo->prepare($sqlCnt);
    $stmtCnt->execute($params);
    $total = (int) $stmtCnt->fetchColumn();

    $sql = "SELECT LOWER(TRIM(customer_email)) AS email,
                   MAX(customer_name) AS name,
                   MAX(customer_phone) AS phone,
                   COUNT(*) AS order_count,
                   SUM(total) AS spent,
                   MAX(created_at) AS last_order
            FROM ded_orders {$where}
            GROUP BY email
            ORDER BY last_order DESC
            LIMIT {$limit} OFFSET {$offset}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return ['total' => $total, 'items' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
}

function ded_musteri_detay(PDO $pdo, string $email): ?array
{
    $email = strtolower(trim($email));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return null;
    }
    $stmt = $pdo->prepare(
        'SELECT * FROM ded_orders WHERE LOWER(TRIM(customer_email)) = ? ORDER BY id DESC LIMIT 50'
    );
    $stmt->execute([$email]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($orders === []) {
        return null;
    }
    $first = $orders[0];
    $spent = 0.0;
    foreach ($orders as $o) {
        $spent += (float) ($o['total'] ?? 0);
    }

    return [
        'email' => $email,
        'name' => (string) ($first['customer_name'] ?? ''),
        'phone' => (string) ($first['customer_phone'] ?? ''),
        'order_count' => count($orders),
        'spent' => $spent,
        'orders' => $orders,
    ];
}

function ded_stok_dusuk(PDO $pdo, int $esik = 5, int $limit = 200): array
{
    $esik = max(0, $esik);
    $limit = max(1, min(500, $limit));
    $sql = 'SELECT p.slug, p.title, p.brand, v.name AS variant_name, v.sku, v.stock_qty, v.in_stock
            FROM ded_product_variants v
            INNER JOIN ded_products p ON p.id = v.product_id
            WHERE v.stock_qty <= ?
            ORDER BY v.stock_qty ASC, p.title ASC
            LIMIT ' . (int) $limit;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$esik]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ded_iadeler(PDO $pdo, int $limit = 100): array
{
    $limit = max(1, min(300, $limit));
    $stmt = $pdo->prepare(
        "SELECT * FROM ded_orders WHERE status IN ('cancelled','refunded','returned')
         ORDER BY updated_at DESC LIMIT {$limit}"
    );
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ded_markalar_listesi(PDO $pdo): array
{
    return $pdo->query(
        "SELECT TRIM(brand) AS brand, COUNT(*) AS cnt
         FROM ded_products
         WHERE TRIM(brand) <> ''
         GROUP BY TRIM(brand)
         ORDER BY cnt DESC, brand ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
}

function ded_siparis_csv(PDO $pdo, ?string $status = null): void
{
    $list = ded_orders_list($pdo, 5000, 0, $status !== '' && $status !== null ? $status : null);
    $items = $list['items'] ?? [];
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=siparisler-' . date('Y-m-d') . '.csv');
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    fputcsv($out, ['No', 'Müşteri', 'E-posta', 'Telefon', 'Toplam', 'Durum', 'Ödeme', 'Tarih'], ';');
    foreach ($items as $row) {
        fputcsv($out, [
            (string) ($row['order_number'] ?? ''),
            (string) ($row['customer_name'] ?? ''),
            (string) ($row['customer_email'] ?? ''),
            (string) ($row['customer_phone'] ?? ''),
            (string) ($row['total'] ?? ''),
            (string) ($row['status'] ?? ''),
            (string) ($row['payment_status'] ?? ''),
            (string) ($row['created_at'] ?? ''),
        ], ';');
    }
    fclose($out);
    exit;
}

function ded_rapor_ozet(PDO $pdo, int $gun = 30): array
{
    $gun = max(1, min(365, $gun));
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) AS orders,
                COALESCE(SUM(total),0) AS revenue,
                COALESCE(AVG(total),0) AS avg_basket
         FROM ded_orders
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
           AND payment_status IN ('paid','awaiting_transfer')"
    );
    $stmt->execute([$gun]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $pending = (int) $pdo->query("SELECT COUNT(*) FROM ded_orders WHERE status = 'pending'")->fetchColumn();
    $unpaid = (int) $pdo->query("SELECT COUNT(*) FROM ded_orders WHERE payment_status = 'unpaid'")->fetchColumn();

    return [
        'orders' => (int) ($row['orders'] ?? 0),
        'revenue' => (float) ($row['revenue'] ?? 0),
        'avg_basket' => (float) ($row['avg_basket'] ?? 0),
        'pending' => $pending,
        'unpaid' => $unpaid,
        'days' => $gun,
    ];
}

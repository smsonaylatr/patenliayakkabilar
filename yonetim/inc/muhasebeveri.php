<?php declare(strict_types=1);


function yonetim_accounting_data(PDO $pdo): array
{
    $dashboard = ded_stats_dashboard($pdo);
    $months = yonetim_fill_monthly_series(ded_stats_monthly($pdo, 14), 12);

    $paidWhere = "payment_status IN ('paid','awaiting_transfer')";

    $todayRev = (float) ($pdo->query(
        "SELECT COALESCE(SUM(total),0) FROM ded_orders WHERE {$paidWhere} AND DATE(created_at) = CURDATE()"
    )->fetchColumn() ?: 0);

    $monthRev = (float) ($pdo->query(
        "SELECT COALESCE(SUM(total),0) FROM ded_orders WHERE {$paidWhere}
         AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())"
    )->fetchColumn() ?: 0);

    $monthOrders = (int) ($pdo->query(
        "SELECT COUNT(*) FROM ded_orders WHERE {$paidWhere}
         AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())"
    )->fetchColumn() ?: 0);

    $prevMonthRev = (float) ($pdo->query(
        "SELECT COALESCE(SUM(total),0) FROM ded_orders WHERE {$paidWhere}
         AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
         AND MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))"
    )->fetchColumn() ?: 0);

    $paidOrders = (int) ($pdo->query(
        "SELECT COUNT(*) FROM ded_orders WHERE {$paidWhere}"
    )->fetchColumn() ?: 0);

    $revenueTotal = (float) ($dashboard['revenue_total'] ?? 0);
    $avgOrder = $paidOrders > 0 ? $revenueTotal / $paidOrders : 0.0;

    $revGrowth = $prevMonthRev > 0
        ? round((($monthRev - $prevMonthRev) / $prevMonthRev) * 100, 1)
        : ($monthRev > 0 ? 100.0 : 0.0);

    $sparkRev = [];
    $sparkOrd = [];
    foreach ($months as $m) {
        $sparkRev[] = round((float) $m['revenue'], 2);
        $sparkOrd[] = (int) $m['orders'];
    }
    while (count($sparkRev) < 11) {
        $sparkRev[] = 0;
        $sparkOrd[] = 0;
    }

    $chartLabels = [];
    $chartRevenue = [];
    $chartOrders = [];
    $curYm = date('Y-m');
    $highlightIndex = 0;
    $i = 0;
    foreach ($months as $m) {
        $chartLabels[] = yonetim_month_label_tr((string) $m['ym']);
        $chartRevenue[] = round((float) $m['revenue'], 2);
        $chartOrders[] = (int) $m['orders'];
        if (($m['ym'] ?? '') === $curYm) {
            $highlightIndex = $i;
        }
        $i++;
    }

    $paidShare = (int) ($pdo->query("SELECT COUNT(*) FROM ded_orders WHERE payment_status = 'paid'")->fetchColumn() ?: 0);
    $awaitShare = (int) ($pdo->query("SELECT COUNT(*) FROM ded_orders WHERE payment_status = 'awaiting_transfer'")->fetchColumn() ?: 0);
    $otherShare = max(0, (int) ($dashboard['order_count'] ?? 0) - $paidShare - $awaitShare);

    $orderTotal = max(1, (int) ($dashboard['order_count'] ?? 0));
    $conversionRate = round((($paidShare + $awaitShare) / $orderTotal) * 100, 1);

    return [
        'dashboard' => $dashboard,
        'months' => $months,
        'today_revenue' => $todayRev,
        'month_revenue' => $monthRev,
        'month_orders' => $monthOrders,
        'revenue_total' => $revenueTotal,
        'avg_order' => $avgOrder,
        'rev_growth' => $revGrowth,
        'spark_revenue' => array_slice($sparkRev, -11),
        'spark_orders' => array_slice($sparkOrd, -11),
        'chart_labels' => $chartLabels,
        'chart_revenue' => $chartRevenue,
        'chart_orders' => $chartOrders,
        'chart_highlight' => $highlightIndex,
        'payment_paid' => $paidShare,
        'payment_awaiting' => $awaitShare,
        'payment_other' => $otherShare,
        'conversion_rate' => $conversionRate,
        'pending_orders' => (int) ($dashboard['pending_orders'] ?? 0),
    ];
}


function yonetim_fill_monthly_series(array $months, int $count = 12): array
{
    $count = max(1, min(36, $count));
    $byYm = [];
    foreach ($months as $m) {
        $byYm[(string) $m['ym']] = $m;
    }
    $out = [];
    for ($i = $count - 1; $i >= 0; $i--) {
        $ym = date('Y-m', strtotime('-' . $i . ' months'));
        $out[] = $byYm[$ym] ?? ['ym' => $ym, 'revenue' => 0.0, 'orders' => 0];
    }

    return $out;
}

function yonetim_month_label_tr(string $ym): string
{
    $parts = explode('-', $ym);
    if (count($parts) !== 2) {
        return $ym;
    }
    $m = (int) $parts[1];
    $names = ['', 'Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'];

    return $names[$m] ?? $ym;
}

function yonetim_format_money_tr(float $n): string
{
    return number_format($n, 2, ',', '.') . ' ₺';
}

<?php

declare(strict_types=1);




function ded_parse_price_input(mixed $raw): float
{
    if (is_int($raw) || is_float($raw)) {
        return max(0, (float) $raw);
    }
    $s = trim((string) $raw);
    if ($s === '') {
        return 0.0;
    }
    $s = preg_replace('/[^\d.,\-]/u', '', $s) ?? '';
    if ($s === '' || $s === '-') {
        return 0.0;
    }

    $lastComma = strrpos($s, ',');
    $lastDot = strrpos($s, '.');

    if ($lastComma !== false && $lastDot !== false) {
        if ($lastComma > $lastDot) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } else {
            $s = str_replace(',', '', $s);
        }
    } elseif ($lastComma !== false) {
        $decLen = strlen($s) - $lastComma - 1;
        if ($decLen > 0 && $decLen <= 2) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } else {
            $s = str_replace(',', '', $s);
        }
    } elseif ($lastDot !== false) {
        $decLen = strlen($s) - $lastDot - 1;
        $dotCount = substr_count($s, '.');
        if ($dotCount > 1 || ($decLen > 2)) {
            $s = str_replace('.', '', $s);
        }
    }

    return max(0, (float) $s);
}


function ded_format_price_try_like_theme(float $n): string
{
    return number_format($n, 2, '.', ',') . 'TL';
}


function ded_format_price_input_tr(float $n): string
{
    return number_format($n, 2, ',', '.');
}


function ded_format_price_display_tr(float $n, string $currency = 'TRY'): string
{
    $cur = $currency === 'TRY' ? ' TL' : (' ' . $currency);

    return number_format($n, 2, ',', '.') . $cur;
}


function ded_patch_sale_price_html(string $html, float $price, int $limit = 1): string
{
    $formatted = ded_format_price_try_like_theme($price);

    return preg_replace(
        '#(<sale-price[^>]*>\s*<span class="sr-only">[^<]*</span>)[^<]*(</sale-price>)#s',
        '${1}' . $formatted . '${2}',
        $html,
        $limit
    ) ?? $html;
}


function ded_product_compare_at_from_row(?array $row): ?float
{
    if ($row === null) {
        return null;
    }
    $v = $row['compare_at_price'] ?? $row['compareAtPrice'] ?? null;
    if ($v === null || $v === '') {
        return null;
    }
    $x = (float) $v;

    return $x > 0 ? $x : null;
}


function ded_product_compare_at_from_catalog_product(array $p): ?float
{
    $raw = $p['compareAtPrice'] ?? $p['compare_at_price'] ?? null;
    if ($raw === null || $raw === '') {
        return null;
    }
    if (is_int($raw) || is_float($raw)) {
        $x = (float) $raw;
    } else {
        $x = ded_parse_price_input($raw);
    }

    return $x > 0 ? $x : null;
}


function ded_product_discount_pricing_promo_visible(?array $productRow, float $effectiveSalePrice): bool
{
    $cap = ded_product_compare_at_from_row($productRow);

    return $cap !== null && $cap > $effectiveSalePrice + 0.009;
}


function ded_patch_compare_at_price_html(string $html, float $compareAtPrice, int $limit = -1): string
{
    $formatted = ded_format_price_try_like_theme($compareAtPrice);

    return preg_replace(
        '#(<compare-at-price[^>]*>\s*<span class="sr-only">[^<]*</span>)[^<]*(</compare-at-price>)#s',
        '${1}' . $formatted . '${2}',
        $html,
        $limit
    ) ?? $html;
}

function ded_strip_discount_pricing_markup(string $html): string
{
    $html = preg_replace('#<compare-at-price\b[^>]*>.*?</compare-at-price>#s', '', $html) ?? $html;
    $html = preg_replace('#<on-sale-badge\b[^>]*>.*?</on-sale-badge>#s', '', $html) ?? $html;

    return $html;
}

function ded_sale_price_strip_promo_sale_class(string $html, int $limit = -1): string
{
    return preg_replace_callback(
        '#<sale-price\b([^>]*)>#s',
        static function (array $m): string {
            $a = preg_replace('/\btext-on-sale\b/', '', $m[1]) ?? $m[1];
            $a = preg_replace('/\s{2,}/', ' ', $a) ?? $a;

            return '<sale-price' . $a . '>';
        },
        $html,
        $limit
    ) ?? $html;
}


function ded_sale_price_ensure_promo_sale_class(string $html, int $limit = -1): string
{
    return preg_replace_callback(
        '#<sale-price\b([^>]*)>#s',
        static function (array $m): string {
            $a = $m[1];
            if (preg_match('/\btext-on-sale\b/', $a)) {
                return '<sale-price' . $a . '>';
            }
            if (preg_match('/\bclass="([^"]*)"/', $a, $cm)) {
                $newClass = trim($cm[1] . ' text-on-sale');
                $a2 = preg_replace('/\bclass="[^"]*"/', 'class="' . $newClass . '"', $a, 1);

                return '<sale-price' . ($a2 ?? $a) . '>';
            }

            return '<sale-price class="text-on-sale"' . $a . '>';
        },
        $html,
        $limit
    ) ?? $html;
}

function ded_sale_price_normalize_sr_label(string $html, bool $discountMode, int $limit = -1): string
{
    $label = $discountMode ? 'İndirimli fiyat' : 'Fiyat';

    return preg_replace(
        '#(<sale-price[^>]*>\s*<span class="sr-only">)[^<]*(</span>)#s',
        '$1' . $label . '$2',
        $html,
        $limit
    ) ?? $html;
}

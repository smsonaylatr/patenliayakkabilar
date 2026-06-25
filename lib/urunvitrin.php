<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function ded_product_page_patch_variants(string $html, array $variants, string $currency = 'TRY', float $listPrice = 0.0): string
{
    if ($variants === []) {
        return $html;
    }

    $opts = '';
    $buttons = '';
    $swatches = '';
    $firstName = '';
    $firstPrice = 0.0;
    $i = 0;
    foreach ($variants as $v) {
        $name = trim((string) ($v['name'] ?? ''));
        if ($name === '') {
            continue;
        }
        $price = (float) ($v['price'] ?? 0);
        if ($price <= 0 && $listPrice > 0) {
            $price = $listPrice;
        }
        $cur = (string) ($v['currency'] ?? $currency);
        $inStock = !isset($v['in_stock']) ? !empty($v['inStock']) : (bool) $v['in_stock'];
        if ($i === 0) {
            $firstName = $name;
            $firstPrice = $price;
        }
        $sel = $i === 0 ? ' selected="selected"' : '';
        $opts .= '<option' . $sel . ' value="' . $i . '" data-ded-price="' . ded_attr((string) $price) . '"'
            . ($inStock ? '' : ' disabled') . '>' . ded_h($name) . '</option>';
        $aria = $i === 0 ? ' aria-selected="true"' : '';
        $buttons .= '<button type="button" class="popover-listbox__option" role="option" value="'
            . ded_attr($name) . '"' . $aria . ' data-option-value data-ded-variant-idx="' . $i . '">'
            . ded_h($name) . '</button>';
        $checked = $i === 0 ? ' checked="checked"' : '';
        $swId = 'ded-swatch-' . $i;
        $swatches .= '<input class="sr-only" type="radio" name="option1" value="' . ded_attr($name) . '" id="'
            . $swId . '"' . $checked . ' data-ded-variant-idx="' . $i . '">'
            . '<label class="block-swatch" for="' . $swId . '" data-option-value><span>' . ded_h($name) . '</span></label>';
        $i++;
    }
    if ($opts === '') {
        return $html;
    }

    $html = preg_replace_callback(
        '#<variant-picker\b([^>]*)>\s*<fieldset\b([^>]*variant-picker__option[^>]*)>#',
        static function (array $m) use ($opts): string {
            $vpAttrs = $m[1];
            $fsAttrs = $m[2];
            if (!preg_match('#\bform=["\']([^"\']+)["\']#', $vpAttrs, $fm)) {
                return '<variant-picker' . $vpAttrs . '><fieldset' . $fsAttrs . '>';
            }
            $formId = $fm[1];
            $sid = 'ded-beden-' . substr(md5($formId . '|ded'), 0, 12);
            $native =
                '<div class="ded-beden-native-fallback form-control"><label class="block-label text-subdued" for="'
                . ded_attr($sid) . '">' . ded_h('Beden') . '</label>'
                . '<select id="' . ded_attr($sid) . '" class="select select--native ded-beden-native-select" name="id" form="'
                . ded_attr($formId) . '" data-ded-native-variant-select>'
                . $opts . '</select></div>';

            return '<variant-picker' . $vpAttrs . ' data-ded-native-picker="1"><fieldset' . $fsAttrs . '>' . $native;
        },
        $html
    ) ?? $html;

    $html = preg_replace(
        '#<x-listbox([^>]*data-option-selector[^>]*)>.*?</x-listbox>#is',
        '<x-listbox$1>'
        . '<input type="hidden" name="option1" value="' . ded_attr($firstName) . '">'
        . $buttons . '</x-listbox>',
        $html,
        1
    ) ?? $html;

    $html = preg_replace(
        '#(<span id="popover-[^"]*-selected-value">)[^<]*(</span>)#',
        '$1' . ded_h($firstName) . '$2',
        $html
    ) ?? $html;

    $html = preg_replace(
        '#<variant-option-value([^>]*)>[^<]*</variant-option-value>#',
        '<variant-option-value$1>' . ded_h($firstName) . '</variant-option-value>',
        $html
    ) ?? $html;

    if ($swatches !== '' && preg_match('#<motion-list[^>]*>.*?</motion-list>#is', $html)) {
        $html = preg_replace(
            '#<div data-option-selector class="variant-picker__option-values[^"]*"[^>]*>.*?</motion-list>#is',
            '<motion-list data-option-selector class="variant-picker__option-values h-stack gap-2 wrap">'
            . $swatches . '</motion-list>',
            $html,
            1
        ) ?? $html;
    }

    if ($swatches !== '') {
        $html = preg_replace(
            '#<div\b([^>]*\bdata-option-selector\b[^>]*\bvariant-picker__option-values\b[^>]*)>.*?</div>#is',
            '<div$1>' . $swatches . '</div>',
            $html
        ) ?? $html;
    }

    $html = preg_replace(
        '#</fieldset>\s*<noscript>\s*<div class="form-control"[^>]*>[\s\S]*?</noscript>#',
        '</fieldset>',
        $html
    ) ?? $html;

    $html = preg_replace(
        '#<input\b(?=[^>]*type=["\']hidden["\'])(?=[^>]*\bdisabled\b)[^>]*\bname=["\']id["\'][^>]*>#i',
        '',
        $html
    ) ?? $html;

    $salePrice = $firstPrice > 0 ? $firstPrice : $listPrice;
    if ($salePrice > 0) {
        $html = ded_patch_sale_price_html($html, $salePrice, -1);
    }

    return $html;
}

function ded_product_variants_for_boot(array $variantRows): array
{
    $out = [];
    foreach ($variantRows as $v) {
        $name = trim((string) ($v['name'] ?? ''));
        if ($name === '') {
            continue;
        }
        $out[] = [
            'name' => $name,
            'price' => (float) ($v['price'] ?? 0),
            'currency' => (string) ($v['currency'] ?? 'TRY'),
            'sku' => isset($v['sku']) && $v['sku'] !== '' && $v['sku'] !== null ? (string) $v['sku'] : null,
            'inStock' => !isset($v['in_stock']) ? !empty($v['inStock']) : (bool) $v['in_stock'],
        ];
    }

    return $out;
}

<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/katalogdepo.php';
require_once __DIR__ . '/vitrin.php';
require_once __DIR__ . '/sablonyukle.php';

function ded_rewrite_samefolder_collection_hrefs(string $html, PDO $pdo): string
{
    foreach (ded_collections_all($pdo) as $c) {
        $id = (string) $c['id'];
        $html = str_replace('href="' . $id . '.html"', 'href="' . ded_vitrin_url('collection', ['slug' => $id]) . '"', $html);
    }
    return $html;
}

function ded_strip_first_product_card_for_template(string $cardHtml): string
{
    $cardHtml = preg_replace('#<div class="product-card__badge-list"[^>]*>.*?</div>#s', '<div class="product-card__badge-list" ></div>', $cardHtml, 1) ?? $cardHtml;
    $cardHtml = preg_replace('#<compare-at-price[^>]*>.*?</compare-at-price>#s', '', $cardHtml, 1) ?? $cardHtml;
    return $cardHtml;
}

function ded_patch_quick_buy_content_template(string $html, string $slug, string $title, string $productHref): string
{
    if (!str_contains($html, 'quick-buy-content')) {
        return $html;
    }

    return preg_replace_callback(
        '#<template\s+id="quick-buy-content">.*?</template>#s',
        static function (array $m) use ($slug, $title, $productHref): string {
            $block = $m[0];
            $block = preg_replace('/\bhandle="[^"]*"/', 'handle="' . ded_attr($slug) . '"', $block) ?? $block;
            $block = preg_replace(
                '#<a\s+[^>]*class="[^"]*\bbold\b[^"]*"[^>]*>.*?</a>#s',
                '<a href="' . ded_attr($productHref) . '" class="bold justify-self-start">' . ded_h($title) . '</a>',
                $block,
                1
            ) ?? $block;

            return $block;
        },
        $html,
        1
    ) ?? $html;
}

function ded_apply_product_discount_pricing_markup(string $html, array $productRow, array $variantRows = []): string
{
    $html = preg_replace('#<on-sale-badge\b[^>]*>.*?</on-sale-badge>#s', '', $html) ?? $html;

    $eff = ded_product_effective_sale_price($productRow, $variantRows);
    if (!ded_product_discount_pricing_promo_visible($productRow, $eff)) {
        $html = ded_strip_discount_pricing_markup($html);
        $html = ded_sale_price_strip_promo_sale_class($html);
        $html = ded_sale_price_normalize_sr_label($html, false);

        return $html;
    }

    $cap = ded_product_compare_at_from_row($productRow);
    if ($cap === null) {
        return $html;
    }

    $html = ded_patch_compare_at_price_html($html, $cap);
    $html = ded_sale_price_ensure_promo_sale_class($html);
    $html = ded_sale_price_normalize_sr_label($html, true);

    return $html;
}

function ded_product_card_from_prototype(string $proto, array $row, array $images, string $imageContext = 'index'): string
{
    $slug = (string) $row['slug'];
    $proto = ded_strip_first_product_card_for_template($proto);
    $qb = 'qb' . preg_replace('/[^a-z0-9]/i', '', bin2hex(random_bytes(6)));
    $proto = preg_replace('/quick-buy-\d+/', 'quick-buy-' . $qb, $proto) ?? $proto;
    $proto = preg_replace('/handle="[^"]*"/', 'handle="' . ded_attr($slug) . '"', $proto) ?? $proto;
    $productHref = ded_vitrin_url('product', ['slug' => $slug]);
    $hEsc = ded_attr($productHref);

    $proto = preg_replace_callback(
        '#href=(["\'])([^"\']*)\1#iu',
        static function (array $m) use ($hEsc): string {
            $q = $m[1];
            $url = $m[2];
            $pathParts = preg_split('/[?#]/', $url, 2);
            $pathPart = $pathParts[0] ?? $url;
            $isProductHref = preg_match('#/(?:urun|products)/#iu', $pathPart)
                || preg_match('#^(?:urun|products)/#iu', $pathPart)
                || preg_match('#urun\.php\?slug=#iu', $url);
            if ($isProductHref) {
                return 'href=' . $q . $hEsc . $q;
            }

            return $m[0];
        },
        $proto
    ) ?? $proto;

    if ($images !== []) {
        $proto = ded_patch_html_product_images($proto, $images, $imageContext, 'product-card__image');
    }

    $proto = preg_replace(
        '#(<span class="product-card__title"><a href="[^"]*" class="bold">)(.*?)(</a></span>)#s',
        '$1' . ded_h((string) $row['title']) . '$3',
        $proto,
        1
    ) ?? $proto;

    $effective = (float) $row['price'];
    $proto = ded_patch_sale_price_html($proto, $effective, 1);

    $cap = ded_product_compare_at_from_row($row);
    $promo = $cap !== null && $cap > $effective + 0.009;

    if (!$promo) {
        $proto = ded_sale_price_strip_promo_sale_class($proto, 1);
        $proto = ded_sale_price_normalize_sr_label($proto, false, 1);
    } else {
        $compareBlock =
            '<compare-at-price class="text-subdued line-through"><span class="sr-only">' . ded_h('Normal fiyat') . '</span>'
            . ded_format_price_try_like_theme($cap) . '</compare-at-price>';
        $proto = preg_replace('#(</sale-price>)#', '$1' . $compareBlock, $proto, 1) ?? $proto;
        $proto = ded_sale_price_normalize_sr_label($proto, true, 1);
    }

    $audCode = ded_product_audience_normalize($row['audience'] ?? null);
    $audLabel = ded_product_audience_label($audCode);

    $badges = [];
    if ($audLabel !== '') {
        $badges[] = '<span class="badge badge--audience">' . ded_h($audLabel) . '</span>';
    }
    if ($badges !== []) {
        $proto = preg_replace(
            '#<div class="product-card__badge-list"\s*>(\s*)</div>#',
            '<div class="product-card__badge-list" >' . implode('', $badges) . '</div>',
            $proto,
            1
        ) ?? $proto;
    }

    return $proto;
}

function ded_listing_footer_script(): string
{
    return <<<'HTML'
<script>
(function(){
  document.addEventListener('change', function(ev){
    var t = ev.target;
    var fSidebar = t.closest && t.closest('.collection__facets form.facets-vertical');
    if (fSidebar && t.form === fSidebar && t.matches('input.switch,input.field[type="number"]')) {
      fSidebar.submit();
      return;
    }

    var fTop = t.closest && t.closest('form.ded-collection-sort-top');
    if (fTop && t.matches('select.ded-collection-sort-inline')) {
      fTop.submit();
      return;
    }

    var fDrawer = t.closest && t.closest('#facets-drawer form.facets-vertical');
    if (fDrawer && t.form === fDrawer && t.matches('input[type="radio"][name="sort_by"]')) {
      fDrawer.submit();
    }
  }, true);
})();

(function(){
  function setDdOpen(dd, open) {
    if (!dd) return;
    var trig = dd.querySelector('.ded-sort-dropdown__trigger');
    var pan = dd.querySelector('.ded-sort-dropdown__panel');
    if (trig) trig.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (pan) pan.hidden = !open;
    dd.classList.toggle('is-open', !!open);
  }

  document.addEventListener('click', function(ev){
    var opt = ev.target.closest && ev.target.closest('.ded-sort-dropdown__option[data-ded-sort-value]');
    if (opt) {
      var dd = opt.closest('.ded-sort-dropdown');
      var form = dd && dd.closest('form');
      if (!form || !dd) return;
      var hid = form.querySelector('input.ded-sort-dropdown__hidden[name="sort_by"]');
      if (!hid) return;
      var val = opt.getAttribute('data-ded-sort-value') || '';
      hid.value = val;
      var valSpan = dd.querySelector('.ded-sort-dropdown__value');
      if (valSpan) valSpan.textContent = (opt.textContent || '').trim();
      dd.querySelectorAll('.ded-sort-dropdown__option[data-ded-sort-value]').forEach(function(b){
        var sel = (b === opt || b.getAttribute('data-ded-sort-value') === val);
        b.setAttribute('aria-selected', sel ? 'true' : 'false');
        b.classList.toggle('is-selected', sel);
      });
      setDdOpen(dd, false);
      form.submit();
      ev.preventDefault();
      return;
    }
    var trig = ev.target.closest && ev.target.closest('.ded-sort-dropdown__trigger');
    if (trig) {
      var dd = trig.closest('.ded-sort-dropdown');
      if (!dd) return;
      var openNow = dd.classList.contains('is-open');
      document.querySelectorAll('.ded-sort-dropdown.is-open').forEach(function(d){ setDdOpen(d, false); });
      setDdOpen(dd, !openNow);
      ev.preventDefault();
      ev.stopPropagation();
      return;
    }
    document.querySelectorAll('.ded-sort-dropdown.is-open').forEach(function(d){ setDdOpen(d, false); });
  });

  document.addEventListener('keydown', function(ev){
    if (ev.key !== 'Escape') return;
    document.querySelectorAll('.ded-sort-dropdown.is-open').forEach(function(dd){ setDdOpen(dd, false); });
  });
})();
</script>
HTML;
}

function ded_listing_strip_sort_radio_block_inner(string $inner, string $sort): string
{
    $out = preg_replace_callback(
        '#<input\b([^>]*\btype=(["\'])radio\2[^>]*name=(["\'])sort_by\3[^>]*)>#iu',
        static function (array $m): string {
            $attrs = preg_replace('#\s+checked\b#iu', '', $m[1]);

            return '<input' . $attrs . '>';
        },
        $inner
    ) ?? $inner;

    $sq = preg_quote($sort, '#');

    return (string) preg_replace_callback(
        '#(<input\b[^>]*name=(["\'])sort_by\2[^>]*\bvalue=(["\'])' . $sq . '\3[^>]*)>#iu',
        static function (array $m): string {
            $tag = $m[1];
            if (!preg_match('#\bchecked\b#iu', $tag)) {
                $tag .= ' checked';
            }

            return $tag . '>';
        },
        $out,
        1
    );
}

function ded_rewrite_listing_price_field(string $fullInputTag, float $vmin, float $vmax, ?float $cur, bool $isGte): string
{
    if (!preg_match('#^<\s*input\b([^>]*)>#ius', trim($fullInputTag), $m)) {
        return $fullInputTag;
    }

    $core = preg_replace('#\s+(?:min|max|placeholder|value)\s*=\s*(["\'])[^"\']*\1#iu', '', $m[1]) ?? $m[1];
    $vminI = max(0, (int) floor($vmin));
    $vmaxI = max(1, (int) ceil($vmax));
    $placeholder = $isGte ? $vminI : $vmaxI;

    $extras = sprintf(
        ' min="%s" max="%s" placeholder="%s" step="%s"',
        ded_attr((string) $vminI),
        ded_attr((string) $vmaxI),
        ded_attr((string) $placeholder),
        ded_attr('1')
    );

    if ($cur !== null) {
        $vFmt = preg_replace('#\.(?:0+|00[1-9])$#', '', number_format(max(0.0, $cur), 4, '.', ''));
        $extras .= sprintf(' value="%s"', ded_attr($vFmt !== '' ? $vFmt : '0'));
    }

    return '<input ' . trim($core) . $extras . '>';
}

function ded_collection_sort_dropdown_markup(string $actionEsc, string $sort, string $extrasHiddenHtml): string
{

    $opts = [
        ['v' => 'manual', 'l' => ded_h('Öne çıkan')],
        ['v' => 'most-relevant', 'l' => ded_h('En alakalı')],
        ['v' => 'best-selling', 'l' => ded_h('En çok satan')],
        ['v' => 'title-ascending', 'l' => ded_h('Alfabetik olarak, A-Z')],
        ['v' => 'title-descending', 'l' => ded_h('Alfabetik olarak, Z-A')],
        ['v' => 'price-ascending', 'l' => ded_h('Fiyat, düşükten yükseğe')],
        ['v' => 'price-descending', 'l' => ded_h('Fiyat, yüksekten düşüğe')],
        ['v' => 'created-ascending', 'l' => ded_h('Tarih, eskiden yeniye')],
        ['v' => 'created-descending', 'l' => ded_h('Tarih, yeniden eskiye')],
    ];

    $allowedSort = array_column($opts, 'v');
    if (!in_array($sort, $allowedSort, true)) {
        $sort = 'manual';
    }

    $currentLabel = $opts[0]['l'];

    foreach ($opts as $o) {
        if ($o['v'] === $sort) {
            $currentLabel = $o['l'];
            break;
        }
    }

    $lis = '';

    foreach ($opts as $o) {
        $isSel = $sort === $o['v'];
        $lis .= '<li role="presentation"><button type="button" class="ded-sort-dropdown__option'
            . ($isSel ? ' is-selected' : '') . '" role="option" tabindex="-1" data-ded-sort-value="'
            . ded_attr($o['v']) . '" aria-selected="' . ($isSel ? 'true' : 'false') . '">' . $o['l'] . '</button></li>';

    }

    $chevron = '<svg class="ded-sort-dropdown__svg" width="10" height="10" viewBox="0 0 8 6" aria-hidden="true"><path d="m1 1.5 3 3 3-3" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path></svg>';

    return '<form method="GET" action="' . $actionEsc . '" class="ded-collection-sort-top">'
        . $extrasHiddenHtml
        . '<div class="ded-sort-dropdown"><input type="hidden" name="sort_by" class="ded-sort-dropdown__hidden" value="'
        . ded_attr($sort) . '">'
        . '<button type="button" class="ded-sort-dropdown__trigger" aria-expanded="false" aria-haspopup="listbox" aria-label="'
        . ded_attr('Sıralama') . '">'
        . '<span class="ded-sort-dropdown__heading"><span class="ded-sort-dropdown__title">' . ded_h('Şuna göre sırala::') . '</span>'
        . '<span class="ded-sort-dropdown__value">' . $currentLabel . '</span></span>'
        . '<span class="ded-sort-dropdown__icon-circle">' . $chevron . '</span></button><ul class="ded-sort-dropdown__panel" hidden role="listbox" tabindex="-1">'
        . $lis . '</ul></div></form>';
}

function ded_patch_collection_listing_markup(string $html, string $collectionUrlRaw, array $ctrl, array $priceBounds): string
{
    $sort = (string) ($ctrl['sort'] ?? 'manual');
    $inStockOnly = !empty($ctrl['in_stock_only']);

    $pgte = $ctrl['price_gte'] ?? null;

    $plte = $ctrl['price_lte'] ?? null;
    $pbMin = max(0.0, (float) ($priceBounds['min'] ?? 0.0));
    $pbMax = max(1.0, (float) ($priceBounds['max'] ?? 8000.0));

    $curGte = $pgte !== null && $pgte !== '' && is_numeric((string) $pgte) ? (float) $pgte : null;

    $curLte = $plte !== null && $plte !== '' && is_numeric((string) $plte) ? (float) $plte : null;

    $html = preg_replace('#(\bname=)(["\'])filter\\.v\\.price\\.gte\\2#u', '$1$2filter_v_price_gte$2', $html) ?? $html;
    $html = preg_replace('#(\bname=)(["\'])filter\\.v\\.price\\.lte\\2#u', '$1$2filter_v_price_lte$2', $html) ?? $html;
    $html = preg_replace('#(\bname=)(["\'])filter\\.v\\.availability\\2#u', '$1$2filter_v_availability$2', $html) ?? $html;

    $actionEsc = ded_attr((string) $collectionUrlRaw);

    $html = preg_replace_callback(
        '#(?<!\w)action=(["\'])https?:[^"\']*\/collections\/[^"\']*\1#u',
        static fn (): string => 'action="' . $actionEsc . '"',
        $html
    ) ?? $html;

    $html = preg_replace('#\s+is=("|\')facet-form\1#u', '', $html) ?? $html;
    $html = preg_replace('#\s+update-on-change\b#iu', '', $html) ?? $html;

    $html = preg_replace_callback(
        '#<button\b([^>]*)>#iu',
        static function (array $m): string {
            $attrs = $m[1];
            if (!preg_match('#\bis\s*=\s*(["\'])facet-apply-button\1#u', $attrs)) {
                return '<button' . $attrs . '>';
            }
            $attrsClean = preg_replace('#\bis\s*=\s*(["\'])facet-apply-button\1\s*#iu', '', $attrs);
            $attrsClean = preg_replace('#\btype\s*=\s*(["\'])button\1\s*#iu', '', $attrsClean);

            return '<button type="submit" ' . trim($attrsClean) . '>';
        },
        $html
    ) ?? $html;

    $hiddenSortEsc = ded_attr((string) $sort);
    $html = preg_replace_callback(
        '#<form\b([^>]*\bclass="facets-vertical"[^>]*)>#i',
        static function (array $m) use ($actionEsc, $hiddenSortEsc): string {

            $attrs = preg_replace('#\saction=(["\'])[^"\']*\1#u', '', $m[1]);

            $attrsTrim = trim($attrs);

            return '<form action="' . $actionEsc . '"' . ($attrsTrim !== '' ? ' ' . $attrsTrim : '')

                . '><input type="hidden" name="sort_by" value="' . $hiddenSortEsc . '">';
        },
        $html
    ) ?? $html;

    $extrasSortHidden = '';
    if ($inStockOnly) {
        $extrasSortHidden .= '<input type="hidden" name="filter_v_availability" value="1">';
    }
    if ($curGte !== null) {

        $extrasSortHidden .= '<input type="hidden" name="filter_v_price_gte" value="' . ded_attr((string) $curGte) . '">';
    }
    if ($curLte !== null) {

        $extrasSortHidden .= '<input type="hidden" name="filter_v_price_lte" value="' . ded_attr((string) $curLte) . '">';
    }

    $sortBar = ded_collection_sort_dropdown_markup($actionEsc, $sort, $extrasSortHidden);

    $html = preg_replace(
        '#<facet-sort-by\b[\s\S]*?</facet-sort-by>#iu',
        $sortBar,
        $html,
        1
    ) ?? $html;

    $html = preg_replace_callback(
        '#(<details\b[^>]*\bid=(["\'])accordion-sort-by\2[^>]*>)(\s*)([\s\S]*?)(\s*</details>)#iu',
        static function (array $m) use ($sort): string {

            return $m[1] . $m[3] . ded_listing_strip_sort_radio_block_inner((string) $m[4], $sort) . $m[5];
        },
        $html,
        1
    ) ?? $html;

    $html = preg_replace_callback(
        '#<input\b([^>]*\bname=("|\')filter_v_availability\2[^>]*)>#iu',
        static function (array $m) use ($inStockOnly): string {
            $attrs = preg_replace('#\s+checked\b#iu', '', $m[1]);
            if ($inStockOnly && !preg_match('#\bchecked\b#iu', $attrs)) {
                $attrs .= ' checked';
            }

            return '<input' . $attrs . '>';
        },
        $html
    ) ?? $html;

    $html = preg_replace_callback(
        '#<input\b[^>]{0,12000}?name=("|\')filter_v_price_gte\1[^>]*>#isu',
        static function (array $m) use ($pbMin, $pbMax, $curGte): string {
            return ded_rewrite_listing_price_field($m[0], $pbMin, $pbMax, $curGte, true);
        },
        $html
    ) ?? $html;

    $html = preg_replace_callback(
        '#<input\b[^>]{0,12000}?name=("|\')filter_v_price_lte\1[^>]*>#isu',
        static function (array $m) use ($pbMin, $pbMax, $curLte): string {
            return ded_rewrite_listing_price_field($m[0], $pbMin, $pbMax, $curLte, false);
        },
        $html
    ) ?? $html;

    $footer = ded_listing_footer_script();
    $pos = strripos($html, '</body>');
    if ($pos !== false) {

        $html = substr_replace($html, "\n" . $footer . "\n", $pos, 0);
    } else {
        $html .= "\n" . $footer . "\n";
    }

    return $html;
}

function ded_render_search_listing_controls(string $query, array $ctrl, array $priceBounds): string
{

    $pbMin = max(0.0, (float) ($priceBounds['min'] ?? 0));

    $pbMax = max(1.0, (float) ($priceBounds['max'] ?? 8000.0));

    $sort = (string) ($ctrl['sort'] ?? 'manual');

    $inStock = !empty($ctrl['in_stock_only']);
    $pgte = $ctrl['price_gte'] ?? null;
    $plte = $ctrl['price_lte'] ?? null;

    $curGteUsed = ($pgte !== null && $pgte !== '' && is_numeric((string) $pgte));

    $curLteUsed = ($plte !== null && $plte !== '' && is_numeric((string) $plte));

    $sel = fn (string $v): string => ($sort === $v ? ' selected' : '');

    $actionEsc = ded_attr(ded_vitrin_url('search'));

    $qEsc = ded_attr(trim($query));

    $vminEsc = ded_attr((string) (int) floor($pbMin));

    $vmaxEsc = ded_attr((string) (int) ceil($pbMax));

    $gteValEsc = ded_attr(($curGteUsed ? trim((string) $pgte) : ''));

    $lteValEsc = ded_attr(($curLteUsed ? trim((string) $plte) : ''));

    $stockChecked = $inStock ? ' checked' : '';

    return '<section class="ded-search-list-controls v-stack gap-3 sm:flex-row sm:flex-wrap" style="gap:16px;margin:1rem 0 24px">'

        . '<form method="GET" action="' . $actionEsc . '" '

        . ' aria-label="' . ded_attr('Filtre ve sıralama') . '" class="v-stack gap-3 md:flex-row md:flex-wrap md:items-end" '

        . 'style="gap:14px;padding:14px;background:rgba(240,240,240,.28);border-radius:8px;align-items:flex-start">'
        . '<input type="hidden" name="q" value="' . $qEsc . '" autocomplete="off">'

        . '<label class="bold text-with-icon" style="display:inline-flex;align-items:center;gap:8px;flex-wrap:wrap">'
        . '<input type="checkbox" class="checkbox" name="filter_v_availability" value="1"' . $stockChecked . '>'

        . '<span>' . ded_h('Yalnızca stoktakiler') . '</span></label>'

        . '<span class="text-subdued text-sm" style="align-self:center">' . ded_h('Fiyat ₺') . '</span>'

        . '<input class="field" type="number" name="filter_v_price_gte" '

        . ' aria-label="' . ded_attr('En az') . '" min="' . $vminEsc . '" max="' . $vmaxEsc . '" '

        . ' placeholder="' . $vminEsc . '" step="1" value="' . $gteValEsc . '"><span style="align-self:center">—</span>'

        . '<input class="field" type="number" name="filter_v_price_lte" '

        . ' aria-label="' . ded_attr('En çok') . '" min="' . $vminEsc . '" max="' . $vmaxEsc . '" '

        . ' placeholder="' . $vmaxEsc . '" step="1" value="' . $lteValEsc . '">'

        . '<select class="field" style="min-width:12rem" name="sort_by" aria-label="' . ded_attr('Sıralama') . '">'

        . '<option value="manual"' . $sel('manual') . '>' . ded_h('Öne çıkan') . '</option>'

        . '<option value="most-relevant"' . $sel('most-relevant') . '>' . ded_h('En alakalı') . '</option>'

        . '<option value="best-selling"' . $sel('best-selling') . '>' . ded_h('En çok satan') . '</option>'

        . '<option value="title-ascending"' . $sel('title-ascending') . '>' . ded_h('A-Z') . '</option>'

        . '<option value="title-descending"' . $sel('title-descending') . '>' . ded_h('Z-A') . '</option>'

        . '<option value="price-ascending"' . $sel('price-ascending') . '>' . ded_h('Fiyat (düşük-yüksek)') . '</option>'

        . '<option value="price-descending"' . $sel('price-descending') . '>' . ded_h('Fiyat (yüksek-düşük)') . '</option>'

        . '<option value="created-ascending"' . $sel('created-ascending') . '>' . ded_h('Eski tarih önce') . '</option>'

        . '<option value="created-descending"' . $sel('created-descending') . '>' . ded_h('Yeni tarih önce') . '</option>'
        . '</select>'

        . '<button type="submit" class="button">' . ded_h('Uygula') . '</button></form></section>';
}

function ded_search_template_product_card_proto(): ?string
{

    static $memo = false;
    static $resolved = false;
    if ($resolved) {
        return $memo;
    }
    $resolved = true;
    $kolHtml = ded_template_render('koleksiyon.php');
    if (!is_string($kolHtml) || !preg_match('/<product-card\b[^>]*>.*?<\/product-card>/s', $kolHtml, $cm)) {
        $memo = null;

        return null;
    }
    $memo = $cm[0];

    return $memo;
}

function ded_search_page_apply_db_results(?PDO $pdo, string $html, string $q): string
{
    $formNeedle = '<form class="main-search-form"';
    $formStart = strpos($html, $formNeedle);
    if ($formStart === false) {
        return $html;
    }
    $formClosePos = strpos($html, '</form>', $formStart);
    if ($formClosePos === false) {
        return $html;
    }
    $formEnd = $formClosePos + strlen('</form>');

    $formBlock = substr($html, $formStart, $formEnd - $formStart);

    $qTrim = trim($q);
    $formBlock = preg_replace_callback(
        '#\bname=(["\'])q\1\s+value=(["\'])[^"\']*\2#u',
        static function (array $m) use ($qTrim): string {
            $esc = $qTrim === '' ? '' : ded_attr($qTrim);

            return 'name=' . $m[1] . 'q' . $m[1] . ' value=' . $m[2] . $esc . $m[2];
        },
        $formBlock,
        1
    ) ?? $formBlock;

    $toolbarHtml = '';
    $inject = '';

    $listingCtrl = ded_listing_parse_controls($_GET);
    if ($qTrim !== '' && $pdo instanceof PDO) {
        try {
            $rowsRaw = ded_products_search_quick($pdo, $qTrim, 120);
        } catch (Throwable) {
            $rowsRaw = [];
        }

        $boundsPub = ded_listing_effective_price_bounds_for_slugs($pdo, array_column($rowsRaw, 'slug'));

        $toolbarHtml = ded_render_search_listing_controls($qTrim, $listingCtrl, $boundsPub);

        $rows = ded_products_apply_listing_controls($pdo, $rowsRaw, $listingCtrl);

        $proto = ded_search_template_product_card_proto();

        if ($rowsRaw === []) {
            $inject = $toolbarHtml
                . '<div class="ded-search-results v-stack gap-6 mt-10 sm:mt-14">'
                . '<p class="text-center h6 text-subdued">' . ded_h('Sonuç bulunamadı.') . '</p></div>';
        } elseif ($proto !== null && $proto !== '') {
            $cardsHtml = '';
            foreach ($rows as $pr) {
                $slug = (string) ($pr['slug'] ?? '');
                if ($slug === '') {
                    continue;
                }
                $det = ded_product_by_slug($pdo, $slug);
                $imgs = ($det !== null) ? ($det['images'] ?? []) : [];
                $cardRow = $det !== null ? $det['row'] : $pr;
                unset($cardRow['_ded_thumb']);
                if ($det !== null) {
                    $cardRow['price'] = ded_product_effective_sale_price($det['row'], $det['variants'] ?? []);
                }
                $cardsHtml .= ded_product_card_from_prototype($proto, $cardRow, $imgs);
            }
            $n = 0;
            foreach ($rows as $r) {
                if (($r['slug'] ?? '') !== '') {
                    ++$n;
                }
            }

            $inject = $toolbarHtml
                . '<div class="ded-search-results v-stack gap-8 mt-10 sm:mt-14">'

                . '<p class="text-center text-subdued">' . ded_h('“' . $qTrim . '” için ')
                . '<span class="bold">' . ded_h((string) $n) . '</span>' . ded_h(' ürün') . '</p>';

            if ($n === 0 && $rowsRaw !== []) {
                $inject .= '<p class="text-center text-subdued h6">' . ded_h('Seçilen filtrelere uygun ürün yok; sıralama veya fiyat filtresini değiştirin.') . '</p>';
            }

            if ($cardsHtml !== '') {
                $inject .= '<reveal-items selector=".product-list > *"><product-list class="product-list" role="region" aria-live="polite">'
                    . $cardsHtml
                    . '</product-list></reveal-items>';
            }
            $inject .= '</div>';
        }

        if ($inject === '') {
            $lines = '';
            foreach ($rows as $pr) {
                $slug = (string) ($pr['slug'] ?? '');
                $title = (string) ($pr['title'] ?? '');
                if ($slug === '' || $title === '') {
                    continue;
                }
                $href = ded_vitrin_url('product', ['slug' => $slug]);
                $lines .= '<li><a class="link-faded" href="' . ded_attr($href) . '">' . ded_h($title) . '</a></li>';
            }
            if ($lines !== '') {
                $inject = $toolbarHtml
                    . '<div class="ded-search-results v-stack gap-6 mt-10 sm:mt-14">'
                    . '<p class="text-subdued text-center">' . ded_h('“' . $qTrim . '” için ürünler') . '</p>'
                    . '<ul class="v-stack gap-3 justify-center" role="list">' . $lines . '</ul></div>';
            }
        }
        if ($inject === '' && $qTrim !== '') {

            $inject = $toolbarHtml
                . '<div class="ded-search-results v-stack gap-6 mt-10 sm:mt-14">'

                . '<p class="text-subdued text-center">' . ded_h('Gösterilecek sonuç yok.') . '</p></div>';
        }
    }

    $assembled = substr($html, 0, $formStart) . $formBlock . $inject . substr($html, $formEnd);

    if ($inject !== '') {

        $sf = ded_listing_footer_script();

        $pBody = strripos($assembled, '</body>');

        if ($pBody !== false) {
            $assembled = substr_replace($assembled, "\n" . $sf . "\n", $pBody, 0);
        }
    }

    return $assembled;
}

function ded_collection_page_render(PDO $pdo, string $slug): ?string
{
    $data = ded_collection_by_id($pdo, $slug);
    if ($data === null) {
        return null;
    }

    $html = ded_template_render('koleksiyon.php');
    $hasProductProto = preg_match('/<product-card\b[^>]*>.*?<\/product-card>/s', $html, $cm);
    $productProto = $hasProductProto ? $cm[0] : '';

    $html = ded_html_rewrite_context($html, 'collections_dir');
    $html = ded_rewrite_samefolder_collection_hrefs($html, $pdo);

    $c = $data['row'];
    $title = (string) $c['title'];
    $desc = strip_tags((string) $c['description']);

    $listingCtrl = ded_listing_parse_controls($_GET);

    $boundsSlugs = [];

    foreach ($data['products'] ?? [] as $pw) {

        $sid = trim((string) ($pw['slug'] ?? ''));

        if ($sid !== '') {

            $boundsSlugs[] = $sid;

        }

    }

    $priceBoundsArr = ded_listing_effective_price_bounds_for_slugs($pdo, $boundsSlugs);

    $shownProducts = ded_products_apply_listing_controls($pdo, $data['products'] ?? [], $listingCtrl);

    require_once __DIR__ . '/seo.php';
    $seo = ded_seo_collection_overrides($pdo, $slug);
    $listForLd = [];
    foreach ($shownProducts as $p) {
        $listForLd[] = [
            'name' => (string) ($p['title'] ?? ''),
            'url' => ded_vitrin_url('product', ['slug' => (string) ($p['slug'] ?? '')]),
        ];
    }
    ded_seo_set_context([
        'type' => 'page',
        'route' => 'collection',
        'title' => $seo['title'] ?: $title,
        'description' => $seo['description'] ?: $desc,
        'image' => $seo['image'] ?: (string) ($c['image_path'] ?? ''),
        'url' => ded_seo_absolute_url(ded_vitrin_url('collection', ['slug' => $slug])),
        'noindex' => (bool) $seo['noindex'],
        'breadcrumbs' => [
            ['name' => 'Ana sayfa', 'url' => ded_vitrin_url('home')],
            ['name' => 'Koleksiyonlar', 'url' => ded_vitrin_url('collections')],
            ['name' => $title, 'url' => ded_vitrin_url('collection', ['slug' => $slug])],
        ],
        'jsonld' => $listForLd === [] ? [] : [ded_seo_build_itemlist($title, $listForLd)],
    ]);

    if (!$hasProductProto || $productProto === '') {

        $listingUrlCollectBare = ded_vitrin_url('collection', ['slug' => $slug]);

        $html = ded_patch_collection_listing_markup($html, $listingUrlCollectBare, $listingCtrl, $priceBoundsArr);
        require_once __DIR__ . '/sepetsayfa.php';

        return ded_vitrin_finalize_document(ded_vitrin_rewrite_html_links(ded_theme_inject_cart_core_global($html)));
    }
    $proto = $productProto;
    $cardsHtml = '';
    foreach ($shownProducts as $p) {
        $det = ded_product_by_slug($pdo, (string) $p['slug']);
        $imgs = ($det !== null) ? ($det['images'] ?? []) : [];
        $cardRow = $det !== null ? $det['row'] : $p;
        if ($det !== null) {
            $cardRow['price'] = ded_product_effective_sale_price($det['row'], $det['variants'] ?? []);
        }
        $cardsHtml .= ded_product_card_from_prototype($proto, $cardRow, $imgs);
    }

    $html = preg_replace(
        '/<reveal-items selector="\.product-list > \*">\s*<product-list class="product-list"[^>]*>.*?<\/product-list>\s*<\/reveal-items>/s',
        '<reveal-items selector=".product-list > *"><product-list class="product-list" role="region" aria-live="polite">' . $cardsHtml . '</product-list></reveal-items>',
        $html,
        1
    ) ?? $html;

    $n = count($shownProducts);
    $html = preg_replace('#<p class="text-center">\s*\d+\s*ürün\s*</p>#', '<p class="text-center">' . $n . ' ürün</p>', $html, 1) ?? $html;

    $html = preg_replace('#<div class="v-stack gap-4 md:hidden">\s*<p class="text-center">\s*\d+\s*ürün\s*</p>#', '<div class="v-stack gap-4 md:hidden"><p class="text-center">' . $n . ' ürün</p>', $html, 1) ?? $html;

    $listingUrlCollect = ded_vitrin_url('collection', ['slug' => $slug]);

    $html = ded_patch_collection_listing_markup($html, $listingUrlCollect, $listingCtrl, $priceBoundsArr);

    require_once __DIR__ . '/sepetsayfa.php';
    $html = ded_theme_inject_cart_core_global($html);

    return ded_vitrin_finalize_document(ded_vitrin_rewrite_html_links($html));
}

function ded_product_page_render(PDO $pdo, string $slug, string $imageContext = 'index'): ?string
{
    $det = ded_product_by_slug($pdo, $slug);
    if ($det === null) {
        return null;
    }

    $row = $det['row'];
    $images = $det['images'];
    $variants = $det['variants'] ?? [];
    $effectiveSale = ded_product_effective_sale_price($row, is_array($variants) ? $variants : []);
    $html = ded_template_render('urun.php');
    $html = ded_html_rewrite_context($html, 'products_dir');

    $title = (string) $row['title'];
    $desc = strip_tags((string) $row['description']);

    require_once __DIR__ . '/seo.php';
    $seo = ded_seo_product_overrides($pdo, $slug);
    $variantsForLd = [];
    if (is_array($variants)) {
        foreach ($variants as $v) {
            $variantsForLd[] = [
                'name' => (string) ($v['name'] ?? ''),
                'price' => (float) ($v['price'] ?? $row['price']),
                'currency' => (string) ($v['currency'] ?? $row['currency']),
                'sku' => (string) ($v['sku'] ?? ''),
                'inStock' => (bool) ($v['in_stock'] ?? true),
            ];
        }
    }
    $availability = 'in stock';
    if ($variantsForLd !== []) {
        $any = false;
        foreach ($variantsForLd as $v) {
            if ($v['inStock']) {
                $any = true;
                break;
            }
        }
        $availability = $any ? 'in stock' : 'out of stock';
    }
    ded_seo_set_context([
        'type' => 'product',
        'route' => 'product',
        'title' => $seo['title'] ?: $title,
        'description' => $seo['description'] ?: $desc,
        'image' => $seo['image'] ?: (string) ($images[0] ?? ''),
        'url' => ded_seo_absolute_url(ded_vitrin_url('product', ['slug' => $slug])),
        'noindex' => (bool) $seo['noindex'],
        'breadcrumbs' => [
            ['name' => 'Ana sayfa', 'url' => ded_vitrin_url('home')],
            ['name' => 'Koleksiyonlar', 'url' => ded_vitrin_url('collections')],
            ['name' => $title, 'url' => ded_vitrin_url('product', ['slug' => $slug])],
        ],
        'jsonld' => [
            ded_seo_build_product([
                'title' => $title,
                'description' => $desc,
                'slug' => $slug,
                'brand' => (string) ($row['brand'] ?? ''),
                'price' => $effectiveSale,
                'currency' => (string) ($row['currency'] ?? 'TRY'),
            ], $images, $variantsForLd),
        ],
        'extra' => [
            'product_price' => number_format($effectiveSale, 2, '.', ''),
            'product_currency' => (string) ($row['currency'] ?? 'TRY'),
            'product_availability' => $availability,
        ],
    ]);

    $html = preg_replace(
        '/<h1 class="product-info__title[^"]*"[^>]*>.*?<\/h1>/s',
        '<h1 class="product-info__title h3" >' . ded_h($title) . '</h1>',
        $html,
        1
    ) ?? $html;

    $audLab = ded_product_audience_label(ded_product_audience_normalize($row['audience'] ?? null));
    if ($audLab !== '') {
        $html = preg_replace(
            '#(<h1 class="product-info__title[^"]*"[^>]*>.*?</h1>)#s',
            '$1<p class="product-info__audience" style="margin:0.35rem 0 0;font-size:0.92rem;opacity:0.82">' . ded_h($audLab) . '</p>',
            $html,
            1
        ) ?? $html;
    }

    $html = ded_patch_sale_price_html($html, $effectiveSale, -1);

    $variantRows = is_array($det['variants'] ?? null) ? $det['variants'] : [];

    if ($images !== [] && preg_match('#(<product-gallery\b.*?</product-gallery>)#s', $html, $gm)) {
        $gal = $gm[1];
        $newGal = ded_product_gallery_apply_images($gal, $images, $title, $imageContext);
        $html = str_replace($gal, $newGal, $html);
    }

    if ($images !== []) {
        $html = preg_replace_callback(
            '#<variant-media\b[^>]*>.*?</variant-media>#s',
            static function (array $m) use ($images, $imageContext): string {
                return ded_patch_html_product_images($m[0], $images, $imageContext);
            },
            $html
        ) ?? $html;
    }

    if ($imageContext === 'index') {
        $html = ded_storefront_strip_parent_cdn_paths($html);
    }

    if ($variantRows !== []) {
        require_once __DIR__ . '/urunvitrin.php';
        $html = ded_product_page_patch_variants(
            $html,
            $variantRows,
            (string) ($row['currency'] ?? 'TRY'),
            max((float) ($row['price'] ?? 0), $effectiveSale)
        );
    }

    $html = ded_apply_product_discount_pricing_markup($html, $row, $variantRows);

    $html = ded_patch_quick_buy_content_template(
        $html,
        $slug,
        $title,
        ded_vitrin_url('product', ['slug' => $slug])
    );

    require_once __DIR__ . '/sepetsayfa.php';
    $bootVariants = [];
    if ($variantRows !== []) {
        require_once __DIR__ . '/urunvitrin.php';
        $bootVariants = ded_product_variants_for_boot($variantRows);
    }
    $html = ded_cart_inject_product_scripts($html, $slug, $row, $images, $bootVariants);

    return ded_vitrin_finalize_document(ded_vitrin_rewrite_html_links($html));
}

function ded_collections_list_render(PDO $pdo): ?string
{
    require_once __DIR__ . '/seo.php';
    $cols = ded_collections_all($pdo);
    $listForLd = [];
    foreach ($cols as $col) {
        $listForLd[] = [
            'name' => (string) ($col['title'] ?? ''),
            'url' => ded_vitrin_url('collection', ['slug' => (string) ($col['id'] ?? '')]),
        ];
    }
    ded_seo_set_context([
        'type' => 'page',
        'route' => 'collections',
        'title' => 'Koleksiyonlar',
        'description' => 'Tüm koleksiyonlarımız: çocuk, erkek, kadın tekerlekli ayakkabı modelleri.',
        'url' => ded_seo_absolute_url(ded_vitrin_url('collections')),
        'breadcrumbs' => [
            ['name' => 'Ana sayfa', 'url' => ded_vitrin_url('home')],
            ['name' => 'Koleksiyonlar', 'url' => ded_vitrin_url('collections')],
        ],
        'jsonld' => $listForLd === [] ? [] : [ded_seo_build_itemlist('Koleksiyonlar', $listForLd)],
    ]);

    $raw = ded_template_render('koleksiyonliste.php');

    if (!preg_match('#<a href="collections/[a-z0-9\-]+\.html" class="collection-card"[^>]*>.*?</a>#is', $raw, $am)) {
        require_once __DIR__ . '/sepetsayfa.php';

        return ded_theme_inject_cart_core_global(ded_html_rewrite_context($raw, 'collections_list'));
    }
    $protoRaw = $am[0];
    $html = ded_html_rewrite_context($raw, 'collections_list');
    $proto = ded_html_rewrite_context($protoRaw, 'collections_list');

    $out = '';
    foreach (ded_collections_all($pdo) as $col) {
        $id = (string) $col['id'];
        $card = $proto;
        $listHref = ded_attr(ded_vitrin_url('collection', ['slug' => $id]));
        $card = preg_replace('#\bhref="[^"]*"#', 'href="' . $listHref . '"', $card, 1) ?? $card;
        $img = (string) ($col['image_path'] ?? '');
        if ($img !== '') {
            $srcFull = ded_storefront_image_src($img, 'index');
            $card = preg_replace('#\bsrc="[^"]*"#', 'src="' . ded_attr($srcFull) . '"', $card, 1) ?? $card;
            $card = preg_replace('#\s+srcset="[^"]*"#i', '', $card, 1) ?? $card;
        }
        $card = preg_replace('#<p class="h2">[^<]*</p>#', '<p class="h2">' . ded_h((string) $col['title']) . '</p>', $card, 1) ?? $card;
        $out .= $card;
    }

    $html = preg_replace(
        '#<collection-list class="collection-list">\s*.*?</collection-list>#s',
        '<collection-list class="collection-list">' . "\n            " . $out . '</collection-list>',
        $html,
        1
    ) ?? $html;

    require_once __DIR__ . '/sepetsayfa.php';
    $html = ded_theme_inject_cart_core_global($html);

    return ded_vitrin_finalize_document(ded_vitrin_rewrite_html_links($html));
}

function ded_cms_page_render(PDO $pdo, string $slug): ?string
{
    $row = ded_cms_page_by_slug($pdo, $slug);
    if ($row === null) {
        return null;
    }

    $html = ded_template_render('icerik.php');
    $html = ded_html_rewrite_context($html, 'pages_dir');

    $title = (string) ($row['title'] ?? '');
    $desc = (string) ($row['description'] ?? '');
    $sourceHtml = trim((string) ($row['source_html'] ?? ''));
    $plain = strip_tags($desc !== '' ? $desc : $title);

    require_once __DIR__ . '/seo.php';
    $seo = ded_seo_page_overrides($pdo, $slug);
    ded_seo_set_context([
        'type' => 'page',
        'route' => 'page',
        'title' => $seo['title'] ?: $title,
        'description' => $seo['description'] ?: $plain,
        'image' => $seo['image'],
        'url' => ded_seo_absolute_url(ded_vitrin_url('page', ['slug' => $slug])),
        'noindex' => (bool) $seo['noindex'],
        'breadcrumbs' => [
            ['name' => 'Ana sayfa', 'url' => ded_vitrin_url('home')],
            ['name' => $title, 'url' => ded_vitrin_url('page', ['slug' => $slug])],
        ],
    ]);

    $html = preg_replace('#<h1 class="h1 text-center">.*?</h1>#s', '<h1 class="h1 text-center">' . ded_h($title) . '</h1>', $html, 1) ?? $html;

    if ($sourceHtml !== '') {
        $html = preg_replace(
            '#<div class="prose">.*?</div>#s',
            '<div class="prose">' . $sourceHtml . '</div>',
            $html,
            1
        ) ?? $html;
    } elseif ($desc !== '') {
        $html = preg_replace(
            '#<div class="prose">\s*.*?\s*</div>#s',
            '<div class="prose">' . nl2br(ded_h($desc)) . '</div>',
            $html,
            1
        ) ?? $html;
    }

    require_once __DIR__ . '/sepetsayfa.php';

    $html = ded_theme_inject_cart_core_global($html);

    return ded_vitrin_finalize_document(ded_vitrin_rewrite_html_links($html));
}

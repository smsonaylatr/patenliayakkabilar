<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/katalogdepo.php';

function ded_rewrite_internal_page_links(string $html): string
{
    return preg_replace_callback(
        '#href="(?:\.\./)?pages/([^"]+)"#i',
        static function (array $m): string {
            $path = rawurldecode($m[1]);
            $slug = preg_replace('/\.html$/iu', '', $path);

            return 'href="' . ded_vitrin_url('page', ['slug' => $slug]) . '"';
        },
        $html
    ) ?? $html;
}

function ded_rewrite_same_dir_page_html_links(string $html): string
{
    return preg_replace_callback(
        '#href="((?:[%a-z0-9ıİğĞüÜşŞöÖçÇ\-]|i̇)+)\.html"#iu',
        static function (array $m): string {
            $path = rawurldecode($m[1]);
            if ($path === '' || str_contains($path, '/') || str_contains($path, ':')) {
                return $m[0];
            }

            return 'href="' . ded_vitrin_url('page', ['slug' => $path]) . '"';
        },
        $html
    ) ?? $html;
}

function ded_rewrite_product_page_bare_slug_html_links(string $html): string
{
    return preg_replace_callback(
        '#href=(["\'])([\p{L}\p{N}][\p{L}\p{N}_\-]*)\.html\1#iu',
        static function (array $m): string {
            $slug = rawurldecode($m[2]);
            $low = function_exists('mb_strtolower')
                ? mb_strtolower($slug, 'UTF-8')
                : strtolower($slug);
            static $skip = [
                'index',
                'cart',
                'checkout',
                'search',
                'collections',
                'collection',
            ];
            if ($slug === '' || in_array($low, $skip, true)) {
                return $m[0];
            }

            return 'href=' . $m[1] . ded_vitrin_url('product', ['slug' => $slug]) . $m[1];
        },
        $html
    ) ?? $html;
}

function ded_html_rewrite_context(string $html, string $context): string
{
    $html = preg_replace('#(\.\./)+cdn/#', 'cdn/', $html) ?? $html;

    $rewriteCollectionLinks = static function (string $h): string {
        return preg_replace_callback(
            '#href="(?:\.\./)?collections/([^"]+?)\.html"#i',
            static function (array $m): string {
                $slug = rawurldecode($m[1]);

                return 'href="' . ded_vitrin_url('collection', ['slug' => $slug]) . '"';
            },
            $h
        ) ?? $h;
    };
    $rewriteProductLinks = static function (string $h): string {
        return preg_replace_callback(
            '#href="(?:\.\./)?products/([^"]+?)\.html"#i',
            static function (array $m): string {
                $slug = rawurldecode($m[1]);

                return 'href="' . ded_vitrin_url('product', ['slug' => $slug]) . '"';
            },
            $h
        ) ?? $h;
    };

    if ($context === 'collections_dir') {
        $html = str_replace('../index.html', ded_vitrin_url('home'), $html);
        $html = $rewriteCollectionLinks($html);
        $html = $rewriteProductLinks($html);
        $html = preg_replace('#href="\.\./collections\.html"#i', 'href="' . ded_vitrin_url('collections') . '"', $html) ?? $html;
        $html = preg_replace('#href="\.\./search\.html"#i', 'href="' . ded_vitrin_url('search') . '"', $html) ?? $html;
        $html = preg_replace('#href="\.\./cart\.html"#i', 'href="' . ded_vitrin_url('cart') . '"', $html) ?? $html;
        $html = preg_replace('#href="\.\./checkout\.html"#i', 'href="' . ded_vitrin_url('checkout') . '"', $html) ?? $html;
        $html = ded_rewrite_internal_page_links($html);

        return ded_vitrin_rewrite_html_links($html);
    }

    if ($context === 'products_dir') {
        $html = str_replace('../index.html', ded_vitrin_url('home'), $html);
        $html = $rewriteCollectionLinks($html);
        $html = $rewriteProductLinks($html);
        $html = ded_rewrite_product_page_bare_slug_html_links($html);
        $html = preg_replace('#href="\.\./collections\.html"#i', 'href="' . ded_vitrin_url('collections') . '"', $html) ?? $html;
        $html = preg_replace('#href="\.\./search\.html"#i', 'href="' . ded_vitrin_url('search') . '"', $html) ?? $html;
        $html = preg_replace('#href="\.\./cart\.html"#i', 'href="' . ded_vitrin_url('cart') . '"', $html) ?? $html;
        $html = preg_replace('#href="\.\./checkout\.html"#i', 'href="' . ded_vitrin_url('checkout') . '"', $html) ?? $html;
        $html = ded_rewrite_internal_page_links($html);

        return ded_vitrin_rewrite_html_links($html);
    }

    if ($context === 'pages_dir') {
        $html = str_replace('../index.html', ded_vitrin_url('home'), $html);
        $html = $rewriteCollectionLinks($html);
        $html = $rewriteProductLinks($html);
        $html = preg_replace('#href="\.\./collections\.html"#i', 'href="' . ded_vitrin_url('collections') . '"', $html) ?? $html;
        $html = preg_replace('#href="\.\./search\.html"#i', 'href="' . ded_vitrin_url('search') . '"', $html) ?? $html;
        $html = preg_replace('#href="\.\./cart\.html"#i', 'href="' . ded_vitrin_url('cart') . '"', $html) ?? $html;
        $html = preg_replace('#href="\.\./checkout\.html"#i', 'href="' . ded_vitrin_url('checkout') . '"', $html) ?? $html;
        $html = ded_rewrite_internal_page_links($html);
        $html = ded_rewrite_same_dir_page_html_links($html);

        return ded_vitrin_rewrite_html_links($html);
    }

    if (in_array($context, ['collections_list', 'index', 'cart_root', 'search_root'], true)) {
        $html = $rewriteCollectionLinks($html);
        $html = $rewriteProductLinks($html);
        $html = preg_replace('#href="collections\.html"#i', 'href="' . ded_vitrin_url('collections') . '"', $html) ?? $html;
        $html = preg_replace('#href="collections/all\.html"#i', 'href="' . ded_vitrin_url('collections') . '"', $html) ?? $html;
        $html = str_replace('href="index.html"', 'href="' . ded_vitrin_url('home') . '"', $html);
        $html = preg_replace('#href="checkout\.html"#i', 'href="' . ded_vitrin_url('checkout') . '"', $html) ?? $html;
        $html = preg_replace('#href="cart\.html"#i', 'href="' . ded_vitrin_url('cart') . '"', $html) ?? $html;
        $html = preg_replace('#href="search\.html"#i', 'href="' . ded_vitrin_url('search') . '"', $html) ?? $html;
        $html = ded_rewrite_internal_page_links($html);

        return ded_vitrin_rewrite_html_links($html);
    }

    return ded_vitrin_rewrite_html_links($html);
}

function ded_render_collection_card_html(array $col): string
{
    $slug = ded_attr($col['id']);
    $title = ded_attr($col['title']);
    $imgPath = ded_attr($col['image_path'] ?? '');
    $href = ded_vitrin_url('collection', ['slug' => (string) $col['id']]);
    $svg = '<svg role="presentation" focusable="false" width="40" height="40" class="icon icon-circle-button-right-clipped" viewBox="0 0 24 24">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 24c6.627 0 12-5.373 12-12S18.627 0 12 0 0 5.373 0 12s5.373 12 12 12ZM10.47 9.53 12.94 12l-2.47 2.47 1.06 1.06 3-3 .53-.53-.53-.53-3-3-1.06 1.06Z" fill="currentColor"></path>
      </svg>';
    return '<a href="' . ded_attr($href) . '" class="collection-card  shadow" reveal-js>
              <div class="content-over-media group rounded-sm" style="--content-over-media-overlay: 0 0 0 / 0.3">'
        . ($imgPath !== ''
            ? '<img src="' . $imgPath . '" alt="' . $title . '" loading="lazy" width="1230" height="1230" sizes="(max-width: 699px) 73vw, 533px" class="zoom-image">'
            : '')
        . '<div class="collection-card__content-wrapper text-custom place-self-center text-center" style="--text-color: 255 255 255"><div class="collection-card__content prose"><p class="h2">'
        . $title
        . '</p>
                    </div>' . $svg . '</div></div>
            </a>';
}

function ded_storefront_patch_index(PDO $pdo, string $html): string
{
    require_once __DIR__ . '/theme/anaekran.php';

    return ded_patch_index_apply($pdo, $html);
}

function ded_product_main_image(array $productRow, array $imagePaths): string
{
    if ($imagePaths !== []) {
        return (string) $imagePaths[0];
    }
    return '';
}

function ded_storefront_image_src(string $path, string $context = 'index'): string
{
    $path = trim(str_replace('\\', '/', $path));
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if (str_starts_with($path, '//')) {
        return $path;
    }
    $path = ltrim($path, '/');
    if ($context === 'products_dir' && !str_starts_with($path, '../')) {
        return '../' . $path;
    }

    return $path;
}

function ded_product_gallery_apply_images(string $galleryHtml, array $imagePaths, string $title, string $context = 'index'): string
{
    if ($imagePaths === []) {
        return $galleryHtml;
    }

    $alt = ded_attr($title);
    $slides = '';
    $thumbs = '';
    foreach (array_values($imagePaths) as $i => $path) {
        $src = ded_storefront_image_src((string) $path, $context);
        if ($src === '') {
            continue;
        }
        $mediaId = 'ded-img-' . $i;
        $slides .= '<div class="product-gallery__media snap-center" data-media-type="image" data-media-id="'
            . $mediaId . '"><img src="' . ded_attr($src) . '" alt="' . $alt . '" width="1200" height="1200" loading="'
            . ($i === 0 ? 'eager' : 'lazy') . '"' . ($i === 0 ? ' fetchpriority="high"' : ' fetchpriority="auto"')
            . ' sizes="(max-width: 740px) calc(100vw - 40px), (max-width: 999px) calc(100vw - 64px), min(730px, 40vw)" class="rounded"></div>';
        $thumbs .= '<button type="button" class="product-gallery__thumbnail" aria-current="'
            . ($i === 0 ? 'true' : 'false') . '" aria-label="' . ($i + 1) . ' ögesine git">'
            . '<img src="' . ded_attr($src) . '" alt="' . $alt . '" width="1200" height="1200" loading="lazy" sizes="(max-width: 699px) 56px, 64px" class="object-contain rounded-sm">'
            . '</button>';
    }
    if ($slides === '') {
        return $galleryHtml;
    }

    $galleryHtml = preg_replace(
        '#(<media-carousel\b[^>]*>).*?(</media-carousel>)#s',
        '$1' . $slides . '$2',
        $galleryHtml,
        1
    ) ?? $galleryHtml;
    $galleryHtml = preg_replace(
        '#(<page-dots\b[^>]*>).*?(</page-dots>)#s',
        '$1' . $thumbs . '$2',
        $galleryHtml,
        1
    ) ?? $galleryHtml;

    return $galleryHtml;
}

function ded_storefront_strip_parent_cdn_paths(string $html): string
{
    return preg_replace('#((?:src|href|poster)=["\'])\.\./cdn/#', '$1cdn/', $html) ?? $html;
}

function ded_patch_html_product_images(string $html, array $imagePaths, string $context = 'index', ?string $imgClassNeedle = null): string
{
    if ($imagePaths === []) {
        return $html;
    }

    $ix = 0;
    $pattern = $imgClassNeedle !== null
        ? '#<img\b(?=[^>]*class="[^"]*' . preg_quote($imgClassNeedle, '#') . ')[^>]*>#i'
        : '#<img\b[^>]*>#i';

    return preg_replace_callback(
        $pattern,
        static function (array $m) use ($imagePaths, &$ix, $context): string {
            $tag = $m[0];
            $path = $imagePaths[$ix % count($imagePaths)];
            $ix++;
            $src = ded_storefront_image_src((string) $path, $context);
            if ($src === '') {
                return $tag;
            }
            if (preg_match('#\ssrc="[^"]*"#i', $tag)) {
                $tag = preg_replace('#\ssrc="[^"]*"#i', ' src="' . ded_attr($src) . '"', $tag, 1) ?? $tag;
            } else {
                $tag = preg_replace('#<img#i', '<img src="' . ded_attr($src) . '"', $tag, 1) ?? $tag;
            }
            $tag = preg_replace('#\s+srcset="[^"]*"#i', '', $tag) ?? $tag;

            return $tag;
        },
        $html
    ) ?? $html;
}

function ded_format_money(float $n, string $cur): string
{
    if ($cur === 'TRY') {
        return number_format($n, 2, ',', '.') . ' ₺';
    }
    return number_format($n, 2, '.', ',') . ' ' . $cur;
}

<?php

declare(strict_types=1);

require_once __DIR__ . '/vitrinrotalar.php';

function ded_vitrin_layout_defaults(): array
{
    return [
        'logo_path' => 'cdn/shop/files/Ekran_Resmi_2026-05-01_19.36.35-Photoroom_0591d2d2-1fae-496c-8108-f04e2b9ec17e1bed.png?v=1777653429&width=599',
        'logo_alt' => 'Laykids',
        'panel' => [
            'name' => '',
            'logo_sm' => '',
            'logo_light' => '',
            'logo_dark' => '',
            'favicon' => '',
            'footer_line' => '',
        ],
        'announcement' => ['text' => '', 'url' => ''],
        'header_menu' => [
            ['label' => 'Ana sayfa', 'type' => 'home', 'slug' => ''],
            ['label' => 'Katalog', 'type' => 'collections', 'slug' => ''],
            ['label' => 'Çocuk', 'type' => 'collection', 'slug' => 'cocuk-tekerlekli-ayakkabi'],
            ['label' => 'Kadın', 'type' => 'collection', 'slug' => 'kadin-tekerlekli-ayakkabi'],
            ['label' => 'Erkek', 'type' => 'collection', 'slug' => 'erkek-tekerlekli-ayakkabi'],
        ],
        'footer' => [
            'newsletter_heading' => 'Özel Fırsatlar Ve Yeni Ürünlerden Haberdar Olmak için Kayıt olun',
            'policies_title' => 'Politikalar',
            'about_title' => 'Laykids Hakkında',
            'about_text' => 'Laykids, çocuk tekerlekli ayakkabı kategorisinde yenilikçi, kaliteli ve güvenilir ürünler sunan modern bir markadır. Çocukların özgürce hareket edebilmesi, eğlenirken kendilerini ifade edebilmesi ve her adımda özgüven kazanması için tasarlanır.',
            'copyright' => '© 2026, Laykidsofficial.',
            'show_payment_icons' => true,
        ],
        'footer_menu' => [
            ['label' => 'Koşullar', 'type' => 'page', 'slug' => 'kosullar'],
            ['label' => 'Kargo Politikası', 'type' => 'page', 'slug' => 'kargo-politikasi'],
            ['label' => 'Bize Ulaşın', 'type' => 'page', 'slug' => 'bize-ulasin'],
            ['label' => 'Gizlilik Politikası', 'type' => 'page', 'slug' => 'gizlilik-politikasi'],
            ['label' => 'İade ve Değişim Politikası', 'type' => 'page', 'slug' => 'iade-ve-degisim-politikasi'],
        ],
        'home' => [
            'hero' => [
                'image_desktop' => 'cdn/shop/files/Adsiz_tasarim_13a9d4.png?v=1778715326&width=2800',
                'image_mobile' => 'cdn/shop/files/Adsiz_tasarim_142548.png?v=1778715461&width=1200',
                'subheading' => 'BİR DOKUNUŞ İLE',
                'heading' => 'ÖZGÜRCE DOLAŞ',
            ],
            'scrolling_text' => 'HER YERDE KAY',
            'featured_titles' => ['Çocuk', 'Erkek', 'Kadın'],
            'featured_collection_slugs' => [
                'cocuk-tekerlekli-ayakkabi',
                'erkek-tekerlekli-ayakkabi',
                'kadin-tekerlekli-ayakkabi',
            ],
            'image_text' => [
                'image' => 'cdn/shop/files/IMG_64789809.jpg?v=1778714384&width=2304',
                'heading' => 'GÜVENİLİR TASARIM VE YÜKSEK KALİTE',
                'body_html' => '• PU Deri + EVA + Kauçuk<br/>• Terlemeyi önleyen file kumaş<br/>• 0.12 inç çelik mekanizma<br/>• Kilitleme/açma düğmesi<br/>• Yanlışlıkla açılmaya karşı koruma<br/>Aşınmaya dayanıklı malzemeler, güvenilir ve stabil platform. Çift sıra tekerlek tasarımı ekstra denge sağlar. Maksimum kullanıcı ağırlığı 220 lb\'ye kadar.',
            ],
            'video' => [
                'youtube_id' => 'oE_jD1fZ9sw',
                'heading' => 'Elif Sinem TV ailesinin tercihi Laykids oldu.',
            ],
            'rich_text' => [
                'heading' => 'Premium Tekerlekli Ayakkabı & Patenli Ayakkabı Koleksiyonu',
                'body_html' => '<p>Laykids, premium tekerlekli ayakkabı ve patenli ayakkabı modellerini modern tasarım ile buluşturan özel bir koleksiyondur. LED ışıklı modeller, rahat kullanım sağlayan yapısı ve dikkat çekici tasarımlarıyla hem çocuklar hem gençler için eğlenceyi ve konforu bir araya getirir. Günlük kullanıma uygun premium patenli ayakkabı modellerimizi keşfedin.</p>',
            ],
            'image_overlay' => [
                'image' => 'cdn/shop/files/IMG_62745894.jpg?v=1778714450&width=2304',
                'heading' => 'LAYKİDS İLE ZAMANINI NASIL GEÇİRİYORSUN?',
            ],
        ],
    ];
}

function ded_vitrin_layout_merge(array $stored): array
{
    $defaults = ded_vitrin_layout_defaults();
    foreach ($defaults as $key => $val) {
        if (!array_key_exists($key, $stored)) {
            $stored[$key] = $val;
            continue;
        }
        if (is_array($val) && is_array($stored[$key])) {
            if (array_is_list($val)) {
                if ($stored[$key] === []) {
                    $stored[$key] = $val;
                }
            } else {
                $stored[$key] = ded_vitrin_layout_merge_section($val, $stored[$key]);
            }
        }
    }

    return $stored;
}

function ded_vitrin_layout_merge_section(array $defaults, array $stored): array
{
    foreach ($defaults as $k => $v) {
        if (!array_key_exists($k, $stored)) {
            $stored[$k] = $v;
        } elseif (is_array($v) && is_array($stored[$k])) {
            $stored[$k] = ded_vitrin_layout_merge_section($v, $stored[$k]);
        }
    }

    return $stored;
}

function ded_vitrin_layout_ensure_table(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS ded_vitrin_layout (
            id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
            layout_json LONGTEXT NOT NULL,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT chk_vitrin_layout_singleton CHECK (id = 1)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function ded_vitrin_layout_migrate_home_featured_kadin_erkek(array &$layout): void
{
    if (!isset($layout['home']) || !is_array($layout['home'])) {
        return;
    }
    $home = &$layout['home'];
    $oldSlugs = ['cocuk-tekerlekli-ayakkabi', 'kadin-tekerlekli-ayakkabi', 'erkek-tekerlekli-ayakkabi'];
    $s = $home['featured_collection_slugs'] ?? null;
    if (is_array($s) && array_values($s) === $oldSlugs) {
        $home['featured_collection_slugs'] = [$oldSlugs[0], $oldSlugs[2], $oldSlugs[1]];
    }
    $oldTitles = ['Çocuk', 'Kadın', 'Erkek'];
    $t = $home['featured_titles'] ?? null;
    if (is_array($t) && array_values($t) === $oldTitles) {
        $home['featured_titles'] = [$oldTitles[0], $oldTitles[2], $oldTitles[1]];
    }
}

function ded_vitrin_layout_load(PDO $pdo): array
{
    ded_vitrin_layout_ensure_table($pdo);
    $row = $pdo->query('SELECT layout_json FROM ded_vitrin_layout WHERE id = 1')->fetchColumn();
    if ($row === false || $row === '') {
        return ded_vitrin_layout_defaults();
    }
    $decoded = json_decode((string) $row, true);
    $layout = ded_vitrin_layout_merge(is_array($decoded) ? $decoded : []);
    ded_vitrin_layout_migrate_home_featured_kadin_erkek($layout);

    return $layout;
}

function ded_vitrin_layout_save(PDO $pdo, array $layout): void
{
    ded_vitrin_layout_ensure_table($pdo);
    $json = json_encode(ded_vitrin_layout_merge($layout), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        throw new RuntimeException('layout_json_encode_failed');
    }
    $st = $pdo->prepare(
        'INSERT INTO ded_vitrin_layout (id, layout_json) VALUES (1, ?)
         ON DUPLICATE KEY UPDATE layout_json = VALUES(layout_json)'
    );
    $st->execute([$json]);
}

function ded_vitrin_menu_item_url(array $item): string
{
    $type = (string) ($item['type'] ?? 'custom');
    $slug = trim((string) ($item['slug'] ?? ''));
    $url = trim((string) ($item['url'] ?? ''));

    return match ($type) {
        'home' => ded_vitrin_url('home'),
        'collections' => ded_vitrin_url('collections'),
        'collection' => $slug !== '' ? ded_vitrin_url('collection', ['slug' => $slug]) : ded_vitrin_url('collections'),
        'page' => $slug !== '' ? ded_vitrin_url('page', ['slug' => $slug]) : ded_vitrin_url('home'),
        'search' => ded_vitrin_url('search'),
        'cart' => ded_vitrin_url('cart'),
        'checkout' => ded_vitrin_url('checkout'),
        default => $url !== '' ? $url : ded_vitrin_url('home'),
    };
}

function ded_vitrin_render_header_menu_items(array $items): string
{
    $html = '';
    foreach ($items as $item) {
        $label = trim((string) ($item['label'] ?? ''));
        if ($label === '') {
            continue;
        }
        $href = ded_attr(ded_vitrin_menu_item_url($item));
        $html .= '<li><a href="' . $href . '" class="bold link-faded-reverse">' . ded_h($label) . '</a></li>';
    }

    return $html;
}

function ded_vitrin_render_mobile_menu_items(array $items): string
{
    $html = '';
    foreach ($items as $item) {
        $label = trim((string) ($item['label'] ?? ''));
        if ($label === '') {
            continue;
        }
        $href = ded_attr(ded_vitrin_menu_item_url($item));
        $html .= '<li class="h3 sm:h4"><a href="' . $href . '" class="group block w-full">'
            . '<span><span class="reversed-link">' . ded_h($label) . '</span></span></a></li>';
    }

    return $html;
}

function ded_vitrin_render_footer_menu_items(array $items): string
{
    $html = '';
    foreach ($items as $item) {
        $label = trim((string) ($item['label'] ?? ''));
        if ($label === '') {
            continue;
        }
        $href = ded_attr(ded_vitrin_menu_item_url($item));
        $html .= '<li><a href="' . $href . '" class="inline-block link-faded break-all">' . ded_h($label) . '</a></li>';
    }

    return $html;
}

function ded_vitrin_patch_img_src(string $html, string $needleClass, string $newSrc): string
{
    if ($newSrc === '') {
        return $html;
    }
    $src = ded_attr($newSrc);
    $pattern = '#(<img\b[^>]*class="[^"]*' . preg_quote($needleClass, '#') . '[^"]*"[^>]*\ssrc=")[^"]*(")~i';

    return preg_replace($pattern, '$1' . $src . '$2', $html, 1) ?? $html;
}

function ded_vitrin_catalog_footer_fragment(): string
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $cached = '';
    if (!function_exists('ded_template_render')) {
        require_once __DIR__ . '/sablonyukle.php';
    }
    $tpl = DED_ROOT . '/templates/koleksiyon.php';
    if (is_readable($tpl)) {
        $src = ded_template_render('koleksiyon.php');
        if (preg_match('#<footer\b[^>]*\bid="shopify-section-footer"[^>]*>.*?</footer>#is', $src, $m)) {
            $cached = $m[0];
        }
    }

    return $cached;
}

function ded_vitrin_footer_is_intact(string $html): bool
{
    return str_contains($html, 'id="shopify-section-footer"')
        && str_contains($html, 'footer__block-list')
        && str_contains($html, 'footer__block--newsletter');
}

function ded_vitrin_graft_catalog_footer(string $html): string
{
    if (ded_vitrin_footer_is_intact($html)) {
        return $html;
    }

    $footer = ded_vitrin_catalog_footer_fragment();
    if ($footer === '') {
        return $html;
    }

    $html = preg_replace('#<footer\b[^>]*\bid="shopify-section-footer"[^>]*>.*?</footer>#is', '', $html) ?? $html;
    $html = preg_replace('#<div class="footer__aside\b[^>]*>[\s\S]*?</div>\s*(?=</div>\s*</div>\s*<script|</body>)#i', '', $html, 1) ?? $html;

    if (preg_match('#</main>#i', $html)) {
        return preg_replace('#</main>#i', '</main>' . $footer, $html, 1) ?? $html;
    }

    return preg_replace('#</body>#i', $footer . '</body>', $html, 1) ?? $html;
}

function ded_vitrin_apply_global_layout(string $html, ?PDO $pdo = null): string
{
    $pdo = $pdo ?? ded_pdo();
    if ($pdo === null) {
        return $html;
    }

    $L = ded_vitrin_layout_load($pdo);
    $logo = trim((string) ($L['logo_path'] ?? ''));
    $logoAlt = trim((string) ($L['logo_alt'] ?? 'Laykids'));

    if ($logo !== '') {
        $html = preg_replace(
            '~(<a[^>]*class="header__logo"[^>]*>.*?<img\b[^>]*\ssrc=")[^"]*(")~i',
            '$1' . ded_attr($logo) . '$2',
            $html,
            1
        ) ?? $html;
        $html = preg_replace(
            '~(<div class="footer__block footer__block--newsletter"[^>]*>\s*<img\b[^>]*\ssrc=")[^"]*(")~i',
            '$1' . ded_attr($logo) . '$2',
            $html,
            1
        ) ?? $html;
        $html = preg_replace(
            '~(<img\b[^>]*class="header__logo-image"[^>]*\salt=")[^"]*(")~i',
            '$1' . ded_attr($logoAlt) . '$2',
            $html,
            1
        ) ?? $html;
        $html = preg_replace(
            '#(<span class="sr-only">)[^<]*(</span></a>\s*</h1>)#i',
            '$1' . ded_h($logoAlt) . '$2',
            $html,
            1
        ) ?? $html;
        $html = preg_replace(
            '#(<a href="[^"]*" class="header__logo">)#i',
            '<a href="' . ded_attr(ded_vitrin_url('home')) . '" class="header__logo">',
            $html,
            1
        ) ?? $html;
    }

    $headerItems = ded_vitrin_render_header_menu_items($L['header_menu'] ?? []);
    if ($headerItems !== '') {
        $html = preg_replace(
            '#<nav class="header__link-list[^"]*"[^>]*>\s*<ul class="contents"[^>]*>.*?</ul>\s*</nav>#s',
            '<nav class="header__link-list  wrap" role="navigation"><ul class="contents" role="list">' . $headerItems . '</ul></nav>',
            $html,
            1
        ) ?? $html;
    }

    $mobileItems = ded_vitrin_render_mobile_menu_items($L['header_menu'] ?? []);
    if ($mobileItems !== '') {
        $html = preg_replace(
            '#<navigation-drawer[^>]*id="header-sidebar-menu"[^>]*>.*?<ul class="v-stack gap-4">.*?</ul>#s',
            '<navigation-drawer mobile-opening="bottom" open-from="left" id="header-sidebar-menu" class="navigation-drawer drawer lg:hidden" >'
            . '<button is="close-button" aria-label="Kapat"class="sm-max:hidden"><svg role="presentation" stroke-width="2" focusable="false" width="19" height="19" class="icon icon-close" viewBox="0 0 24 24">'
            . '<path d="M17.658 6.343 6.344 17.657M17.658 17.657 6.344 6.343" stroke="currentColor"></path></svg></button>'
            . '<div class="panel-list__wrapper"><div class="panel"><div class="panel__wrapper" ><div class="panel__scroller v-stack gap-8"><ul class="v-stack gap-4">'
            . $mobileItems . '</ul></div></div></div></div></navigation-drawer>',
            $html,
            1
        ) ?? $html;
    }

    $footer = $L['footer'] ?? [];
    $newsletter = trim((string) ($footer['newsletter_heading'] ?? ''));
    if ($newsletter !== '') {
        $html = preg_replace(
            '#(<div class="footer__block footer__block--newsletter"[^>]*>.*?<p class="h6">).*?(</p>)#s',
            '$1' . ded_h($newsletter) . '$2',
            $html,
            1
        ) ?? $html;
    }

    $policiesTitle = trim((string) ($footer['policies_title'] ?? ''));
    $footerMenu = ded_vitrin_render_footer_menu_items($L['footer_menu'] ?? []);
    if ($footerMenu !== '') {
        $title = $policiesTitle !== '' ? ded_h($policiesTitle) : 'Politikalar';
        $html = preg_replace(
            '#<div class="footer__block footer__block--menu"[^>]*>.*?</ul>\s*</div>#s',
            '<div class="footer__block footer__block--menu" ><p class="bold">' . $title . '</p><ul class="v-stack gap-3" role="list">' . $footerMenu . '</ul></div>',
            $html,
            1
        ) ?? $html;
    }

    $aboutTitle = trim((string) ($footer['about_title'] ?? ''));
    $aboutText = trim((string) ($footer['about_text'] ?? ''));
    if ($aboutTitle !== '') {
        $html = preg_replace(
            '#(<div class="footer__block footer__block--text"[^>]*>\s*<p class="bold">).*?(</p>)#s',
            '$1' . ded_h($aboutTitle) . '$2',
            $html,
            1
        ) ?? $html;
    }
    if ($aboutText !== '') {
        $html = preg_replace(
            '#(<div class="footer__block footer__block--text"[^>]*>.*?<div class="prose[^"]*"[^>]*>\s*<p>).*?(</p>)#s',
            '$1' . nl2br(ded_h($aboutText)) . '$2',
            $html,
            1
        ) ?? $html;
    }

    $bultenAction = ded_attr(rtrim(ded_vitrin_web_base(), '/') . '/bulten.php');
    $html = preg_replace(
        '#(<form[^>]*id="footer-newsletter"[^>]*\s)action="[^"]*"#i',
        '$1action="' . $bultenAction . '"',
        $html,
        1
    ) ?? $html;
    $copyright = trim((string) ($footer['copyright'] ?? ''));
    if ($copyright !== '') {
        $html = preg_replace(
            '#<p class="footer__copyright[^"]*">.*?</p>#s',
            '<p class="footer__copyright text-sm text-subdued">' . ded_h($copyright) . '</p>',
            $html,
            1
        ) ?? $html;
    }

    if (empty($footer['show_payment_icons'])) {
        $html = preg_replace(
            '#<div class="footer__payment-icons[^"]*">.*?</div>#s',
            '',
            $html,
            1
        ) ?? $html;
    }

    return $html;
}

function ded_vitrin_apply_home_layout(string $html, ?PDO $pdo = null): string
{
    $pdo = $pdo ?? ded_pdo();
    if ($pdo === null) {
        return $html;
    }

    $home = ded_vitrin_layout_load($pdo)['home'] ?? [];
    $hero = $home['hero'] ?? [];

    if (($hero['image_desktop'] ?? '') !== '') {
        $html = preg_replace(
            '~(<img\b[^>]*class="hidden sm:block"[^>]*\ssrc=")[^"]*(")~i',
            '$1' . ded_attr((string) $hero['image_desktop']) . '$2',
            $html,
            1
        ) ?? $html;
    }
    if (($hero['image_mobile'] ?? '') !== '') {
        $html = preg_replace(
            '~(<img\b[^>]*class="sm:hidden"[^>]*\ssrc=")[^"]*(")~i',
            '$1' . ded_attr((string) $hero['image_mobile']) . '$2',
            $html,
            1
        ) ?? $html;
    }
    $sub = trim((string) ($hero['subheading'] ?? ''));
    $head = trim((string) ($hero['heading'] ?? ''));
    if ($sub !== '' || $head !== '') {
        $html = preg_replace(
            '#<p class="bold" data-sequence="subheading">.*?</p><p class="h4" data-sequence="heading"><split-lines>.*?</split-lines></p>#s',
            '<p class="bold" data-sequence="subheading">' . ded_h($sub) . '</p><p class="h4" data-sequence="heading"><split-lines>' . ded_h($head) . '</split-lines></p>',
            $html,
            1
        ) ?? $html;
    }

    $scroll = trim((string) ($home['scrolling_text'] ?? ''));
    if ($scroll !== '') {
        $html = preg_replace(
            '#<span class="scrolling-text__text heading " >[^<]*</span>#',
            '<span class="scrolling-text__text heading " >' . ded_h($scroll) . '</span>',
            $html,
            1
        ) ?? $html;
    }

    $titlesCfg = isset($home['featured_titles']) && is_array($home['featured_titles']) ? $home['featured_titles'] : [];
    $fallbackTitles = ['Çocuk', 'Erkek', 'Kadın'];
    $featuredSlugs = isset($home['featured_collection_slugs']) && is_array($home['featured_collection_slugs'])
        ? $home['featured_collection_slugs']
        : [];
    $fallbackSlugs = [
        'cocuk-tekerlekli-ayakkabi',
        'erkek-tekerlekli-ayakkabi',
        'kadin-tekerlekli-ayakkabi',
    ];

    $featuredIdx = 0;
    $html = preg_replace_callback(
        '#<section\b[^>]*shopify-section--featured-collection[^>]*>[\s\S]*?</section>#s',
        static function (array $m) use (
            &$featuredIdx,
            $titlesCfg,
            $featuredSlugs,
            $fallbackTitles,
            $fallbackSlugs
        ): string {
            $blk = $m[0];
            $title = isset($titlesCfg[$featuredIdx]) ? trim((string) $titlesCfg[$featuredIdx]) : '';
            if ($title === '') {
                $title = $fallbackTitles[$featuredIdx] ?? '';
            }
            if ($title !== '') {
                $blk = preg_replace(
                    '#(<h2[^>]*><split-lines>)(.*?)(</split-lines></h2>)#s',
                    '$1' . ded_h($title) . '$3',
                    $blk,
                    1
                ) ?? $blk;
            }

            $slug = isset($featuredSlugs[$featuredIdx]) ? trim((string) $featuredSlugs[$featuredIdx]) : '';
            if ($slug === '') {
                $slug = trim((string) ($fallbackSlugs[$featuredIdx] ?? ''));
            }
            ++$featuredIdx;

            if ($slug !== '') {
                $href = ded_vitrin_url('collection', ['slug' => $slug]);
                $blk = preg_replace(
                    '#(<section-header\b[^>]*>[\s\S]*?<a\s+href=")[^"]*(")#s',
                    '$1' . ded_attr($href) . '$2',
                    $blk,
                    1
                ) ?? $blk;
            }

            return $blk;
        },
        $html
    ) ?? $html;

    $it = $home['image_text'] ?? [];
    if (($it['image'] ?? '') !== '') {
        $html = preg_replace(
            '~(<multiple-images-with-text-image-list[^>]*><img\b[^>]*\ssrc=")[^"]*(")~i',
            '$1' . ded_attr((string) $it['image']) . '$2',
            $html,
            1
        ) ?? $html;
    }
    if (($it['heading'] ?? '') !== '') {
        $html = preg_replace(
            '#(<multiple-images-with-text-content-list[^>]*>.*?<p class="h1"[^>]*><split-lines>)(.*?)(</split-lines></p>)#s',
            '$1' . ded_h((string) $it['heading']) . '$3',
            $html,
            1
        ) ?? $html;
    }
    if (($it['body_html'] ?? '') !== '') {
        $html = preg_replace(
            '#(</split-lines></p><p>).*?(</p></div></multiple-images-with-text-content-list>)#s',
            '$1' . (string) $it['body_html'] . '$2',
            $html,
            1
        ) ?? $html;
    }

    $video = $home['video'] ?? [];
    $yt = trim((string) ($video['youtube_id'] ?? ''));
    if ($yt !== '') {
        $embed = 'https://www.youtube.com/embed/' . rawurlencode($yt) . '?playsinline=1&amp;autoplay=1&amp;controls=0&amp;mute=1&amp;loop=1&amp;playlist=' . rawurlencode($yt) . '&amp;enablejsapi=1&amp;rel=0&amp;modestbranding=1';
        $html = preg_replace(
            '#src="https://www\.youtube\.com/embed/[^"]+"#',
            'src="' . ded_attr($embed) . '"',
            $html,
            1
        ) ?? $html;
    }
    if (($video['heading'] ?? '') !== '') {
        $html = preg_replace(
            '#(<section[^>]*shopify-section--video[^>]*>.*?<div class="prose"><p class="h1"[^>]*>).*?(</p></div>)#s',
            '$1' . ded_h((string) $video['heading']) . '$2',
            $html,
            1
        ) ?? $html;
    }

    $rt = $home['rich_text'] ?? [];
    if (($rt['heading'] ?? '') !== '') {
        $html = preg_replace(
            '#(<section[^>]*shopify-section--rich-text[^>]*>.*?<p class="h2 hyphenate"[^>]*>).*?(</p><div)#s',
            '$1' . ded_h((string) $rt['heading']) . '$2',
            $html,
            1
        ) ?? $html;
    }
    if (($rt['body_html'] ?? '') !== '') {
        $html = preg_replace(
            '#(<section[^>]*shopify-section--rich-text[^>]*>.*?<p class="h2 hyphenate"[^>]*>.*?</p><div[^>]*>).*?(</div></div>\s*</div>\s*</div></section>)#s',
            '$1' . (string) $rt['body_html'] . '$2',
            $html,
            1
        ) ?? $html;
    }

    $ov = $home['image_overlay'] ?? [];
    if (($ov['image'] ?? '') !== '') {
        $html = preg_replace(
            '~(<section[^>]*shopify-section--image-with-text-overlay[^>]*>.*?<image-banner[^>]*>.*?<img\b[^>]*\ssrc=")[^"]*(")~i',
            '$1' . ded_attr((string) $ov['image']) . '$2',
            $html,
            1
        ) ?? $html;
    }
    if (($ov['heading'] ?? '') !== '') {
        $html = preg_replace(
            '#(<section[^>]*shopify-section--image-with-text-overlay[^>]*>.*?<split-lines>)(.*?)(</split-lines></p>)#s',
            '$1' . ded_h((string) $ov['heading']) . '$3',
            $html,
            1
        ) ?? $html;
    }

    return ded_vitrin_graft_catalog_footer($html);
}

function ded_vitrin_layout_menu_from_post(string $prefix): array
{
    $labels = $_POST[$prefix . '_label'] ?? [];
    if (!is_array($labels)) {
        return [];
    }
    $types = $_POST[$prefix . '_type'] ?? [];
    $slugs = $_POST[$prefix . '_slug'] ?? [];
    $urls = $_POST[$prefix . '_url'] ?? [];
    $items = [];
    foreach ($labels as $i => $label) {
        $label = trim((string) $label);
        if ($label === '') {
            continue;
        }
        $items[] = [
            'label' => $label,
            'type' => trim((string) ($types[$i] ?? 'custom')) ?: 'custom',
            'slug' => trim((string) ($slugs[$i] ?? '')),
            'url' => trim((string) ($urls[$i] ?? '')),
        ];
    }

    return $items;
}

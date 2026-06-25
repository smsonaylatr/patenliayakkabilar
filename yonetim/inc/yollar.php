<?php declare(strict_types=1);

require_once __DIR__ . '/rotalar.php';

yonetim_request_fix_windows_uri();

yonetim_request_normalize_query();

function yonetim_request_fix_windows_uri(): void
{
    if (PHP_SAPI === 'cli') {
        return;
    }
    $uri = str_replace('\\', '/', (string) ($_SERVER['REQUEST_URI'] ?? ''));
    if (!preg_match('#/[A-Za-z]:/#', $uri)) {
        return;
    }
    $rawPath = parse_url($uri, PHP_URL_PATH) ?: '';
    $fixed = yonetim_sanitize_request_path($rawPath);
    if ($fixed === '' || $fixed === $rawPath) {
        return;
    }
    $q = parse_url($uri, PHP_URL_QUERY);
    header('Location: ' . $fixed . (is_string($q) && $q !== '' ? '?' . $q : ''), true, 301);
    exit;
}

function yonetim_web_base(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    if (defined('YONETIM_WEB_BASE')) {
        $base = yonetim_finalize_web_base(rtrim((string) YONETIM_WEB_BASE, '/'));

        return $base;
    }

    $uriPath = yonetim_sanitize_request_path(
        parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: ''
    );
    $fromUri = yonetim_web_base_from_uri($uriPath);
    if ($fromUri !== null) {
        $base = yonetim_finalize_web_base($fromUri);

        return $base;
    }

    $panelDir = str_replace('\\', '/', dirname(__DIR__));
    $resolved = realpath($panelDir);
    if (is_string($resolved)) {
        $panelDir = str_replace('\\', '/', $resolved);
    }

    $docRoot = (string) ($_SERVER['DOCUMENT_ROOT'] ?? '');
    $docResolved = $docRoot !== '' ? realpath($docRoot) : false;
    if (is_string($docResolved)) {
        $docRoot = str_replace('\\', '/', $docResolved);
    } else {
        $docRoot = rtrim(str_replace('\\', '/', $docRoot), '/');
    }

    if ($docRoot !== '' && strlen($docRoot) <= strlen($panelDir)
        && strncasecmp($panelDir, $docRoot, strlen($docRoot)) === 0) {
        $tail = trim(substr($panelDir, strlen($docRoot)), '/');
        $base = yonetim_finalize_web_base($tail === '' ? '' : '/' . $tail);

        return $base;
    }

    $script = yonetim_sanitize_request_path((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = rtrim(dirname($script), '/');
    if ($dir === '' || $dir === '.' || $dir === '/') {
        $base = yonetim_finalize_web_base('');
    } else {
        $base = yonetim_finalize_web_base($dir);
    }

    return $base;
}

function yonetim_web_base_from_uri(string $uriPath): ?string
{
    if ($uriPath === '' || $uriPath === '/') {
        return null;
    }

    $segments = array_values(yonetim_route_paths());
    usort($segments, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

    foreach ($segments as $seg) {
        $seg = str_replace('\\', '/', $seg);
        $quoted = preg_quote($seg, '#');
        if (preg_match('#^(.*)/' . $quoted . '/?$#u', $uriPath, $m)) {
            return $m[1] === '' ? '' : $m[1];
        }
    }

    return null;
}

function yonetim_sanitize_request_path(string $path): string
{
    $path = str_replace('\\', '/', trim($path));
    if ($path === '') {
        return '';
    }

    if (preg_match('#^(?:/)?([A-Za-z]):[/]+(.*)$#', $path, $m)) {
        $path = '/' . ltrim($m[2], '/');
    } elseif (preg_match('#^/[A-Za-z]:/(.*)$#', $path, $m)) {
        $path = '/' . ltrim($m[1], '/');
    }

    if (preg_match('#/htdocs/(.+)$#i', $path, $m)) {
        $path = '/' . ltrim($m[1], '/');
    }

    if (preg_match('#^[A-Za-z]:#', $path)) {
        return '';
    }

    return $path;
}

function yonetim_finalize_web_base(string $base): string
{
    $base = yonetim_sanitize_request_path($base);
    $base = rtrim($base, '/');
    if ($base === '' || $base === '/') {
        return '';
    }
    if (!str_starts_with($base, '/')) {
        $base = '/' . $base;
    }
    if (preg_match('#^[A-Za-z]:#', $base) || str_contains($base, ':/')) {
        return '';
    }

    return $base;
}

function yonetim_base_href(): string
{
    $b = yonetim_web_base();

    return ($b === '' ? '/' : $b . '/');
}

function yonetim_resolve_href(string $href): string
{
    $href = trim($href);
    if ($href === '') {
        return yonetim_url('dashboard');
    }
    if (preg_match('#^https?://#i', $href) || str_starts_with($href, '/')) {
        return $href;
    }

    return yonetim_url($href);
}

function yonetim_url(string $page, array $query = []): string
{
    if (str_contains($page, '?')) {
        [$page, $qs] = explode('?', $page, 2);
        parse_str($qs, $parsed);
        $query = array_merge($parsed, $query);
    }

    $internal = yonetim_route_normalize_page($page);
    $paths = yonetim_route_paths();
    $path = $paths[$internal] ?? $internal;
    $prefix = yonetim_web_base();

    if ($internal === 'index' && $path === 'giris') {
        $url = yonetim_base_href();
    } else {
        $url = ($prefix === '' ? '' : $prefix) . '/' . $path;
    }

    if ($query !== []) {
        $url .= '?' . http_build_query(yonetim_query_to_tr($query), '', '&', PHP_QUERY_RFC3986);
    }

    return $url;
}

function yonetim_href(string $href): string
{
    return yonetim_url($href);
}

function yonetim_redirect(string $page, array $query = [], int $code = 302): never
{
    header('Location: ' . yonetim_url($page, $query), true, $code);
    exit;
}

function yonetim_current_page(): string
{
    $script = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''), '.php');
    if ($script !== '' && $script !== 'index') {
        return $script;
    }

    $uriPath = yonetim_sanitize_request_path(
        parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: ''
    );
    $base = yonetim_web_base();
    if ($base !== '' && str_starts_with($uriPath, $base)) {
        $trPath = trim(rawurldecode(substr($uriPath, strlen($base))), '/');
        if ($trPath !== '') {
            $rev = yonetim_route_reverse();
            if (isset($rev[$trPath])) {
                return $rev[$trPath];
            }
        }
    }

    return $script !== '' ? $script : 'index';
}

function yonetim_panel_asset(string $relative): string
{
    $relative = ltrim(str_replace('\\', '/', $relative), '/');
    $prefix = yonetim_web_base();

    return ($prefix === '' ? '' : $prefix) . '/assets/' . $relative;
}

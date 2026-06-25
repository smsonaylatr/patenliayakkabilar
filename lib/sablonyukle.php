<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function ded_template_render(string $basename): string
{
    if (preg_match('/^[a-z0-9_][a-z0-9_.\-]*\.php$/i', $basename) !== 1) {
        throw new InvalidArgumentException('invalid_template');
    }
    $path = DED_ROOT . '/templates/' . $basename;
    if (!is_readable($path)) {
        throw new RuntimeException('template_missing:' . $basename);
    }
    if (!defined('DED_LOADING_TEMPLATE')) {
        define('DED_LOADING_TEMPLATE', true);
    }
    ob_start();
    include $path;

    return ob_get_clean() ?: '';
}

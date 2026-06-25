<?php declare(strict_types=1);

require_once __DIR__ . '/magazakopru.php';

function yonetim_settings_nav_items(): array
{
    return [
        ['id' => 'settings', 'href' => 'settings', 'label' => 'Özet'],
        ['id' => 'settings_checkout', 'href' => 'settings_checkout', 'label' => 'Ödeme'],
        ['id' => 'settings_paytr', 'href' => 'settings_paytr', 'label' => 'PayTR'],
        ['id' => 'settings_shipping', 'href' => 'settings_shipping', 'label' => 'Kargo'],
        ['id' => 'settings_smtp', 'href' => 'settings_smtp', 'label' => 'E-posta'],
        ['id' => 'settings_sms', 'href' => 'settings_sms', 'label' => 'SMS'],
        ['id' => 'settings_notifications', 'href' => 'settings_notifications', 'label' => 'Bildirimler'],
        ['id' => 'settings_contact', 'href' => 'settings_contact', 'label' => 'İletişim'],
    ];
}

function yonetim_settings_require_pdo(): PDO
{
    $pdo = yonetim_shop_pdo();
    if (!$pdo) {
        yonetim_layout_start('Ayarlar');
        yonetim_page_header('Ayarlar');
        echo '<div class="alert alert-danger border-0 shadow-sm"><code>magazatablo.php</code> çalıştırın.</div>';
        yonetim_layout_end();
        exit;
    }

    return $pdo;
}

function yonetim_settings_preserve_secrets(array $patch, array $prev, array $secretKeys): array
{
    foreach ($secretKeys as $key) {
        $v = $patch[$key] ?? '';
        if (is_string($v) && trim($v) === '') {
            $patch[$key] = (string) ($prev[$key] ?? '');
        }
    }

    return $patch;
}

function yonetim_settings_save(PDO $pdo, array $patch): void
{
    if (isset($patch['toplus_api_base']) && trim((string) $patch['toplus_api_base']) === '') {
        $patch['toplus_api_base'] = 'https://panel.toplusms.tc/api/v1/';
    }
    if (isset($patch['default_shipping_fee'])) {
        $patch['default_shipping_fee'] = ded_parse_price_input($patch['default_shipping_fee']);
    }
    ded_shop_settings_save($pdo, $patch);
}

function yonetim_settings_page_head(string $activeId, string $title): void
{
    $cur = yonetim_current_page();
    $back = $activeId === 'settings' ? 'dashboard' : 'settings';
    yonetim_page_header($title, $back);
    ?>
<nav class="yun-settings-tabs" aria-label="Ayarlar">
  <?php foreach (yonetim_settings_nav_items() as $item) {
      $on = $cur === $item['id'] || ($activeId !== '' && $activeId === $item['id']);
      ?>
  <a href="<?= ded_h(yonetim_resolve_href((string) $item['href'])) ?>" class="yun-settings-tab<?= $on ? ' is-active' : '' ?>"><?= ded_h($item['label']) ?></a>
  <?php } ?>
</nav>
    <?php
}

function yonetim_ded_public_url(): string
{
    $s = $_SERVER;
    $https = (!empty($s['HTTPS']) && (string) $s['HTTPS'] !== 'off')
        || (strtolower((string) ($s['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = (string) ($s['HTTP_HOST'] ?? 'localhost');
    $script = str_replace('\\', '/', (string) ($s['SCRIPT_NAME'] ?? ''));
    $dir = dirname($script);
    $root = preg_replace('#/yonetim$#', '', $dir) ?: $dir;

    return rtrim($scheme . '://' . $host . $root, '/');
}

function yonetim_settings_form_actions(string $saveLabel = 'Kaydet'): void
{
    ?>
<div class="mt-3 pt-3 border-top">
  <button type="submit" class="btn btn-primary"><?= ded_h($saveLabel) ?></button>
</div>
    <?php
}

function yonetim_settings_form_open(array $attrs = []): void
{
    $class = 'card border-0 shadow-sm mb-3 yun-form ' . (string) ($attrs['class'] ?? '');
    unset($attrs['class']);
    $attrStr = '';
    foreach ($attrs as $k => $v) {
        $attrStr .= ' ' . ded_h($k) . '="' . ded_h($v) . '"';
    }
    echo '<form method="post" class="' . ded_h(trim($class)) . '"' . $attrStr . '><div class="card-body">';
}

function yonetim_settings_form_close(): void
{
    yonetim_settings_form_actions();
    echo '</div></form>';
}

function yonetim_settings_test_form_open(string $title = 'Test'): void
{
    ?>
<form method="post" class="card border-0 shadow-sm mb-3 yun-form">
  <div class="card-header bg-transparent border-bottom py-3">
    <h4 class="card-title mb-0 fs-16"><?= ded_h($title) ?></h4>
  </div>
  <div class="card-body">
    <?php
}

function yonetim_settings_test_form_close(string $btnLabel = 'Gönder'): void
{
    ?>
    <button type="submit" class="btn btn-light border mt-2"><?= ded_h($btnLabel) ?></button>
  </div>
</form>
    <?php
}

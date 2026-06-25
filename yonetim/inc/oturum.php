<?php declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/lib/kimlik.php';

function yonetim_ensure_auth_file(): void
{
    ded_panel_auth_ensure();
}

function yonetim_read_auth(): array
{
    return ded_panel_auth_read();
}

function yonetim_write_auth(array $a): void
{
    ded_panel_auth_write($a);
}

function yonetim_logged_in(): bool
{
    return !empty($_SESSION['yonetim_ok']);
}

function yonetim_require_login(): void
{
    if (!yonetim_logged_in()) {
        require_once __DIR__ . '/rotalar.php';
        require_once __DIR__ . '/yollar.php';
        yonetim_redirect('index');
    }
}

function yonetim_verify_password(string $pw): bool
{
    yonetim_ensure_auth_file();
    $auth = yonetim_read_auth();
    if ($pw === '' || empty($auth['password_hash'])) {
        return false;
    }

    return password_verify($pw, (string) $auth['password_hash']);
}

function yonetim_attempt_login(string $username, string $password): bool
{
    yonetim_ensure_auth_file();
    $auth = yonetim_read_auth();
    $expected = trim((string) ($auth['username'] ?? 'admin'));
    if ($expected === '') {
        $expected = 'admin';
    }
    if (mb_strtolower($username) !== mb_strtolower($expected)) {
        return false;
    }

    return yonetim_verify_password($password);
}

function yonetim_panel_profile(): array
{
    yonetim_ensure_auth_file();
    $a = yonetim_read_auth();

    return [
        'username' => (string) ($a['username'] ?? 'admin'),
        'email' => (string) ($a['email'] ?? ''),
        'avatar_path' => isset($a['avatar_path']) && $a['avatar_path'] !== '' ? (string) $a['avatar_path'] : null,
    ];
}

function yonetim_panel_avatar_src(): string
{
    require_once __DIR__ . '/urunyukle.php';
    $p = yonetim_panel_profile()['avatar_path'];
    if ($p === null || $p === '') {
        return '';
    }

    return yonetim_product_image_admin_src($p);
}

function yonetim_panel_display_label(): string
{
    if (!empty($_SESSION['yonetim_display_name'])) {
        return (string) $_SESSION['yonetim_display_name'];
    }
    $u = yonetim_panel_profile()['username'];

    return $u !== '' ? $u : 'Yönetici';
}

function yonetim_session_set_profile_cache(): void
{
    $_SESSION['yonetim_display_name'] = yonetim_panel_profile()['username'];
}

function yonetim_profile_save(string $currentPassword, string $username, string $email, ?string $newAvatarRelPath, bool $removeAvatar): ?string
{
    if (!yonetim_verify_password($currentPassword)) {
        return 'Mevcut şifre hatalı.';
    }
    $u = trim($username);
    if ($u === '' || mb_strlen($u) > 190) {
        return 'Kullanıcı adı 1–190 karakter olmalı.';
    }
    if (!preg_match('/^[\p{L}\p{N}._\-\s]{1,190}$/u', $u)) {
        return 'Kullanıcı adında yalnızca harf, rakam, boşluk, nokta, tire ve alt çizgi kullanılabilir.';
    }
    $em = trim($email);
    if ($em !== '' && !filter_var($em, FILTER_VALIDATE_EMAIL)) {
        return 'Geçerli bir e-posta girin veya alanı boş bırakın.';
    }
    $auth = yonetim_read_auth();
    $av = $auth['avatar_path'] ?? null;
    if ($removeAvatar) {
        $av = null;
    } elseif ($newAvatarRelPath !== null && $newAvatarRelPath !== '') {
        $av = $newAvatarRelPath;
    }
    yonetim_write_auth([
        'username' => $u,
        'email' => $em,
        'avatar_path' => $av,
    ]);
    yonetim_session_set_profile_cache();

    return null;
}

function yonetim_set_password(string $current, string $next): bool
{
    $auth = yonetim_read_auth();
    if (empty($auth['password_hash']) || !password_verify($current, (string) $auth['password_hash'])) {
        return false;
    }
    if (strlen($next) < 6) {
        return false;
    }
    $auth['password_hash'] = password_hash($next, PASSWORD_DEFAULT);
    $auth['api_token'] = null;
    yonetim_write_auth($auth);

    return true;
}

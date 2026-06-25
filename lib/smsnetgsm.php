<?php

declare(strict_types=1);

function ded_toplus_sms_send(array $settings, string $numbers, string $message): array
{
    $base = rtrim((string) ($settings['toplus_api_base'] ?? ''), '/') . '/';
    $user = (string) ($settings['toplus_username'] ?? '');
    $pass = (string) ($settings['toplus_password'] ?? '');
    $caption = (string) ($settings['toplus_caption'] ?? '');
    $encoding = (string) ($settings['toplus_encoding'] ?? 'tr');
    if ($user === '' || $pass === '') {
        return ['ok' => false, 'detail' => 'missing_credentials'];
    }
    $hash = preg_match('/^[a-f0-9]{32}$/i', $pass) ? $pass : md5($pass);
    $url = $base . 'sms/send/normal';
    $payload = [
        'caption' => $caption,
        'message' => $message,
        'numbers' => $numbers,
        'encoding' => $encoding,
    ];
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $auth = base64_encode($user . ':' . $hash);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json; charset=UTF-8',
                'Authorization: Basic ' . $auth,
            ],
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);
        $raw = (string) curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    } else {
        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAuthorization: Basic {$auth}\r\n",
                'content' => $body,
                'timeout' => 20,
            ],
        ]);
        $raw = (string) @file_get_contents($url, false, $ctx);
        $code = 200;
        if (isset($http_response_header[0]) && preg_match('#HTTP/\S+\s+(\d+)#', $http_response_header[0], $m)) {
            $code = (int) $m[1];
        }
    }

    $ok = $code >= 200 && $code < 300;
    return ['ok' => $ok, 'raw' => $raw, 'detail' => $ok ? 'sent' : 'http_' . $code];
}

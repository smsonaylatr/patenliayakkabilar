<?php

declare(strict_types=1);

function ded_mail_send(array $settings, string $to, string $subject, string $htmlBody, string $textBody = ''): bool
{
    $to = trim($to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $host = trim((string) ($settings['smtp_host'] ?? ''));
    $port = (int) ($settings['smtp_port'] ?? 587);
    $enc = strtolower((string) ($settings['smtp_encryption'] ?? 'tls'));
    $user = (string) ($settings['smtp_user'] ?? '');
    $pass = (string) ($settings['smtp_pass'] ?? '');
    $from = trim((string) ($settings['mail_from'] ?? ''));
    $fromName = (string) ($settings['mail_from_name'] ?? 'Mağaza');
    if ($from === '' || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
        $from = $user !== '' && filter_var($user, FILTER_VALIDATE_EMAIL) ? $user : 'noreply@localhost';
    }

    if ($host === '') {
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . ded_mail_encode_name($fromName) . " <{$from}>";
        return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $htmlBody, implode("\r\n", $headers));
    }

    return ded_smtp_send($host, $port, $enc, $user, $pass, $from, $fromName, $to, $subject, $htmlBody, $textBody);
}

function ded_mail_encode_name(string $name): string
{
    if (preg_match('/[^\x20-\x7E]/', $name)) {
        return '=?UTF-8?B?' . base64_encode($name) . '?=';
    }
    return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $name) . '"';
}

function ded_smtp_send(
    string $host,
    int $port,
    string $enc,
    string $user,
    string $pass,
    string $from,
    string $fromName,
    string $to,
    string $subject,
    string $htmlBody,
    string $textBody
): bool {
    $remote = ($enc === 'ssl') ? 'ssl://' . $host . ':' . $port : $host . ':' . $port;
    $errno = 0;
    $errstr = '';
    $fp = @stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
    if (!$fp) {
        return false;
    }
    stream_set_timeout($fp, 20);
    $read = fn () => fgets($fp, 8192);
    $expect = function (string $line, array $codes) use ($read): bool {
        if ($line === false || $line === '') {
            return false;
        }
        $code = (int) substr($line, 0, 3);
        return in_array($code, $codes, true);
    };

    $greet = $read();
    if (!$expect($greet, [220])) {
        fclose($fp);
        return false;
    }
    $ehloHost = 'ded.local';
    fwrite($fp, 'EHLO ' . $ehloHost . "\r\n");
    $buf = '';
    while ($ln = $read()) {
        $buf .= $ln;
        if (strlen($ln) < 4 || $ln[3] === ' ') {
            break;
        }
    }
    if ($enc === 'tls' && $port !== 465) {
        fwrite($fp, "STARTTLS\r\n");
        $r = $read();
        if (!$expect($r, [220])) {
            fclose($fp);
            return false;
        }
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($fp);
            return false;
        }
        fwrite($fp, 'EHLO ' . $ehloHost . "\r\n");
        while ($ln = $read()) {
            if (strlen($ln) < 4 || $ln[3] === ' ') {
                break;
            }
        }
    }
    if ($user !== '' && $pass !== '') {
        fwrite($fp, "AUTH LOGIN\r\n");
        if (!$expect($read(), [334])) {
            fclose($fp);
            return false;
        }
        fwrite($fp, base64_encode($user) . "\r\n");
        if (!$expect($read(), [334])) {
            fclose($fp);
            return false;
        }
        fwrite($fp, base64_encode($pass) . "\r\n");
        if (!$expect($read(), [235])) {
            fclose($fp);
            return false;
        }
    }

    fwrite($fp, 'MAIL FROM:<' . $from . ">\r\n");
    if (!$expect($read(), [250])) {
        fclose($fp);
        return false;
    }
    fwrite($fp, 'RCPT TO:<' . $to . ">\r\n");
    if (!$expect($read(), [250, 251])) {
        fclose($fp);
        return false;
    }
    fwrite($fp, "DATA\r\n");
    if (!$expect($read(), [354])) {
        fclose($fp);
        return false;
    }

    $boundary = 'ded_' . bin2hex(random_bytes(8));
    $subj = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $fromHdr = ded_mail_encode_name($fromName) . " <{$from}>";
    $headers = [];
    $headers[] = 'From: ' . $fromHdr;
    $headers[] = 'To: <' . $to . '>';
    $headers[] = 'Subject: ' . $subj;
    $headers[] = 'MIME-Version: 1.0';
    if ($textBody === '') {
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $body = $htmlBody;
    } else {
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
        $body = "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n{$textBody}\r\n"
            . "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n{$htmlBody}\r\n"
            . "--{$boundary}--\r\n";
    }
    $data = implode("\r\n", $headers) . "\r\n\r\n" . str_replace("\r\n.\r\n", "\r\n..\r\n", $body) . "\r\n.\r\n";
    fwrite($fp, $data);
    if (!$expect($read(), [250])) {
        fclose($fp);
        return false;
    }
    fwrite($fp, "QUIT\r\n");
    fclose($fp);
    return true;
}

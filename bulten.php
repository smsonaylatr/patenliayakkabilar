<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/ekstra.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'POST gerekli'], JSON_UNESCAPED_UNICODE);
    exit;
}

$pdo = ded_pdo();
if (!$pdo) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'message' => 'Veritabanı yok'], JSON_UNESCAPED_UNICODE);
    exit;
}

$email = '';
$name = '';
$honeypot = '';
$in = [];
if (str_contains((string) ($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json')) {
    $raw = file_get_contents('php://input');
    $in = $raw !== '' ? json_decode($raw, true) : [];
    if (is_array($in)) {
        $email = (string) ($in['email'] ?? '');
        $name = (string) ($in['name'] ?? '');
        $honeypot = (string) ($in['website'] ?? '');
    }
} else {
    $email = (string) ($_POST['contact']['email'] ?? $_POST['email'] ?? '');
    $name = (string) ($_POST['contact']['name'] ?? $_POST['name'] ?? '');
    $honeypot = (string) ($_POST['website'] ?? '');
}

if (trim($honeypot) !== '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Geçersiz'], JSON_UNESCAPED_UNICODE);
    exit;
}

$ev = ded_newsletter_subscribe($pdo, $email, $name, 'footer');
http_response_code($ev['ok'] ? 200 : 400);
echo json_encode(['ok' => $ev['ok'], 'message' => $ev['message']], JSON_UNESCAPED_UNICODE);

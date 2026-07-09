<?php
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=patenli_db;charset=utf8mb4";
$pdo = new PDO($dsn, "root", "");

$res = $pdo->query('SELECT id, name, price, discount_price FROM products');
print_r($res->fetchAll(PDO::FETCH_ASSOC));

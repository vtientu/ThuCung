<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../Model/Database.php';
$orders = query_db('SELECT * FROM orders ORDER BY id DESC')->fetchAll();
foreach ($orders as $order) {
    $order['items'] = query_db('SELECT * FROM order_items WHERE order_id = ?', [$order['id']])->fetchAll();
    echo json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}

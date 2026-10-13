<?php
// Chỉ cho phép đọc dữ liệu đơn từ dòng lệnh, không trả dữ liệu khách hàng qua HTTP.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
$path = __DIR__ . '/../storage/orders.php';
if (!is_file($path)) {
    echo "Chưa có đơn hàng.\n";
    exit;
}
$handle = fopen($path, 'rb');
flock($handle, LOCK_SH);
while (($line = fgets($handle)) !== false) {
    if (preg_match('~^/\* ([A-Za-z0-9+/=]+) \*/$~', trim($line), $match)) {
        $order = json_decode(base64_decode($match[1], true), true);
        if (is_array($order)) {
            echo json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        }
    }
}
flock($handle, LOCK_UN);
fclose($handle);

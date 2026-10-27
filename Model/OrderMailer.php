<?php
require_once __DIR__ . '/SmtpMailer.php';

function pet_confirmation_body(array $order, array $items): string
{
    $money = static fn($value) => number_format((float) $value, 0, ',', '.') . ' đ';
    $lines = [
        'Mochi Pet đã nhận đơn ' . $order['code'] . '. Cảm ơn bạn đã mua quà cho bé!',
        'Trạng thái: Chờ xử lý | Thanh toán khi nhận hàng (COD)',
        'Người nhận: ' . $order['name'],
        'Email: ' . $order['email'],
        'Số điện thoại: ' . $order['phone'],
        'Địa chỉ: ' . $order['address'], '', 'Quà cho bé:',
    ];
    foreach ($items as $item) {
        $product = $item['product'];
        $lines[] = $product['name'] . ' × ' . $item['quantity'] . ' — ' . $money($product['price'] * $item['quantity']);
    }
    $lines[] = 'Tiền hàng: ' . $money($order['subtotal']);
    $lines[] = 'Phí giao hàng: ' . $money($order['shipping']);
    $lines[] = 'Tổng thanh toán: ' . $money($order['total']);
    return implode("\n", $lines);
}

function send_pet_confirmation(array $order, array $items): void
{
    try {
        $config = require __DIR__ . '/../config/mail.php';
        if (!$config['enabled']) {
            error_log('[Mochi mail] disabled; order id=' . (int) $order['id']);
            return;
        }
        send_order_smtp($config, $order['email'], 'Mochi Pet · Xác nhận đơn ' . $order['code'], pet_confirmation_body($order, $items));
    } catch (Throwable $error) {
        error_log('[Mochi mail] confirmation failed; order id=' . (int) $order['id']);
    }
}

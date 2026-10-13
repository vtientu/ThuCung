<?php
function order_statuses(): array
{
    return ['pending' => 'Chờ xử lý', 'confirmed' => 'Đã xác nhận', 'shipped' => 'Đang giao', 'delivered' => 'Đã giao', 'cancelled' => 'Đã hủy'];
}

function create_pet_order(string $name, string $phone, string $address, string $token): array
{
    $connection = db();
    $connection->beginTransaction();
    try {
        $existing = query_db('SELECT * FROM orders WHERE checkout_token = ? AND cart_key = ? FOR UPDATE', [$token, cart_key()])->fetch();
        if ($existing) {
            $connection->commit();
            return $existing;
        }
        if (!hash_equals($_SESSION['checkout_token'], $token)) {
            throw new DomainException('Phiên đặt hàng đã thay đổi. Vui lòng tải lại giỏ hàng.');
        }
        $cart = query_db('SELECT product_id, quantity FROM cart_items WHERE cart_key = ? ORDER BY product_id FOR UPDATE', [cart_key()])->fetchAll();
        if (!$cart) {
            throw new DomainException('Giỏ hàng đang trống.');
        }
        $items = [];
        $subtotal = 0;
        foreach ($cart as $row) {
            $product = pet_product((int) $row['product_id'], true);
            $quantity = (int) $row['quantity'];
            if (!$product || $quantity < 1 || $quantity > min(20, (int) $product['stock'])) {
                throw new DomainException('Một sản phẩm không còn đủ hàng. Vui lòng kiểm tra lại giỏ hàng.');
            }
            $items[] = ['product' => $product, 'quantity' => $quantity];
            $subtotal += (int) $product['price'] * $quantity;
        }
        $shipping = $subtotal >= 399000 ? 0 : 30000;
        $code = 'MP' . date('ymd') . strtoupper(bin2hex(random_bytes(5)));
        query_db('INSERT INTO orders (code, checkout_token, cart_key, user_id, name, phone, address, subtotal, shipping, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [$code, $token, cart_key(), current_user()['id'] ?? null, $name, $phone, $address, $subtotal, $shipping, $subtotal + $shipping]);
        $orderId = (int) $connection->lastInsertId();
        foreach ($items as $item) {
            $product = $item['product'];
            query_db('INSERT INTO order_items (order_id, product_id, name, price, quantity) VALUES (?, ?, ?, ?, ?)', [$orderId, $product['id'], $product['name'], $product['price'], $item['quantity']]);
            query_db('UPDATE products SET stock = stock - ? WHERE id = ?', [$item['quantity'], $product['id']]);
        }
        query_db('DELETE FROM cart_items WHERE cart_key = ?', [cart_key()]);
        $order = query_db('SELECT * FROM orders WHERE id = ?', [$orderId])->fetch();
        $connection->commit();
        return $order;
    } catch (Throwable $error) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }
        throw $error;
    }
}

function change_order_status(int $id, string $next): void
{
    $connection = db();
    $connection->beginTransaction();
    try {
        $order = query_db('SELECT * FROM orders WHERE id = ? FOR UPDATE', [$id])->fetch();
        $allowed = ['pending' => ['confirmed', 'cancelled'], 'confirmed' => ['shipped', 'cancelled'], 'shipped' => ['delivered'], 'delivered' => [], 'cancelled' => []];
        if (!$order || !in_array($next, $allowed[$order['status']], true)) {
            throw new DomainException('Không thể chuyển đơn hàng sang trạng thái này.');
        }
        if ($next === 'cancelled') {
            $items = query_db('SELECT product_id, quantity FROM order_items WHERE order_id = ? ORDER BY product_id', [$id])->fetchAll();
            foreach ($items as $item) {
                if ($item['product_id']) {
                    query_db('UPDATE products SET stock = stock + ? WHERE id = ?', [$item['quantity'], $item['product_id']]);
                }
            }
        }
        query_db('UPDATE orders SET status = ? WHERE id = ?', [$next, $id]);
        $connection->commit();
    } catch (Throwable $error) {
        if ($connection->inTransaction()) { $connection->rollBack(); }
        throw $error;
    }
}

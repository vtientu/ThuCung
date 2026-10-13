<?php
function cart_key(): string
{
    $user = current_user();
    return $user ? 'user:' . $user['id'] : 'guest:' . $_SESSION['guest_cart'];
}

function load_cart_products(): array
{
    $key = cart_key();
    // Không giữ sản phẩm ẩn thành mục vô hình chặn thanh toán.
    query_db(
        'DELETE ci FROM cart_items ci JOIN products p ON p.id=ci.product_id JOIN categories c ON c.id=p.category_id WHERE ci.cart_key=? AND (p.active=0 OR c.active=0)',
        [$key],
    );
    $rows = query_db(
        'SELECT p.*, c.slug AS category, ci.quantity FROM cart_items ci JOIN products p ON p.id=ci.product_id JOIN categories c ON c.id=p.category_id WHERE ci.cart_key=? AND p.active=1 AND c.active=1 ORDER BY p.id',
        [$key],
    )->fetchAll();
    return array_column($rows, null, 'id');
}

function set_cart_quantity(int $id, int $quantity): void
{
    if ($quantity <= 0) {
        query_db('DELETE FROM cart_items WHERE cart_key = ? AND product_id = ?', [cart_key(), $id]);
        return;
    }
    $product = pet_product($id);
    if (!$product || $quantity > min(20, (int) $product['stock'])) {
        throw new DomainException('Số lượng vượt quá tồn kho (tối đa 20 sản phẩm mỗi loại).');
    }
    query_db(
        'INSERT INTO cart_items (cart_key, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), updated_at = CURRENT_TIMESTAMP',
        [cart_key(), $id, $quantity],
    );
}

function merge_guest_cart(int $userId): void
{
    $connection = db();
    $connection->beginTransaction();
    try {
        $rows = query_db(
            'SELECT ci.*, p.stock FROM cart_items ci JOIN products p ON p.id = ci.product_id WHERE ci.cart_key = ? FOR UPDATE',
            ['guest:' . $_SESSION['guest_cart']],
        )->fetchAll();
        foreach ($rows as $row) {
            $existing = query_db(
                'SELECT quantity FROM cart_items WHERE cart_key = ? AND product_id = ? FOR UPDATE',
                ['user:' . $userId, $row['product_id']],
            )->fetchColumn();
            $quantity = min(20, (int) $row['stock'], (int) $existing + (int) $row['quantity']);
            if ($quantity > 0) {
                query_db(
                    'INSERT INTO cart_items (cart_key, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)',
                    ['user:' . $userId, $row['product_id'], $quantity],
                );
            }
        }
        query_db('DELETE FROM cart_items WHERE cart_key = ?', ['guest:' . $_SESSION['guest_cart']]);
        $connection->commit();
    } catch (Throwable $error) {
        $connection->rollBack();
        throw $error;
    }
}

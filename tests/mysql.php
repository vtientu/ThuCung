<?php
/** Run only against a separately imported database ending in _test. */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}
require __DIR__ . '/../Controller/Bootstrap.php';
require __DIR__ . '/../Model/Cart.php';
require __DIR__ . '/../Model/OrderStore.php';
if (!str_ends_with(database_config()['name'], '_test')) {
    fwrite(STDERR, "Refusing to run: configure a separate database with a name ending in _test.\n");
    exit(1);
}
$checks = 0;
$exitCode = 0;
$categoryId = $productId = $userId = null;
$guestKey = cart_key();
function check_test(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
}
try {
    $suffix = bin2hex(random_bytes(6));
    query_db('INSERT INTO categories (slug,name) VALUES (?,?)', [
        'test-' . $suffix,
        'Danh mục kiểm thử',
    ]);
    $categoryId = (int) db()->lastInsertId();
    query_db(
        "INSERT INTO products (category_id,name,brand,price,description,stock) VALUES (?,'Original product','MOCHI TEST',100000,'Integration fixture',2)",
        [$categoryId],
    );
    $productId = (int) db()->lastInsertId();
    set_cart_quantity($productId, 2);
    $_SESSION['checkout_token'] = bin2hex(random_bytes(32));
    query_db('UPDATE products SET stock=1 WHERE id=?', [$productId]);
    $blocked = false;
    try {
        create_pet_order(
            'Khách test',
            '0901234567',
            '12 Đường Test, Thành phố Test',
            $_SESSION['checkout_token'],
        );
    } catch (DomainException $error) {
        $blocked = true;
    }
    check_test($blocked, 'Checkout must reject stock changed after adding to cart');
    check_test(
        (int) query_db('SELECT COUNT(*) FROM orders WHERE cart_key=?', [
            $guestKey,
        ])->fetchColumn() === 0,
        'Failed checkout must not create order',
    );
    check_test(
        (int) query_db('SELECT stock FROM products WHERE id=?', [$productId])->fetchColumn() === 1,
        'Failed checkout must not decrement stock',
    );
    check_test((int) load_cart_products()[$productId]['quantity'] === 2, 'Failed checkout preserves cart');
    query_db('UPDATE products SET stock=2 WHERE id=?', [$productId]);
    $order = create_pet_order(
        'Khách test',
        '0901234567',
        '12 Đường Test, Thành phố Test',
        $_SESSION['checkout_token'],
    );
    check_test((int) $order['total'] === 230000, 'Server prices and shipping');
    check_test(
        (int) query_db('SELECT stock FROM products WHERE id=?', [$productId])->fetchColumn() === 0,
        'Inventory decremented',
    );
    check_test(load_cart_products() === [], 'Cart emptied after commit');
    $again = create_pet_order(
        'Khách test',
        '0901234567',
        '12 Đường Test, Thành phố Test',
        $_SESSION['checkout_token'],
    );
    check_test($again['id'] === $order['id'], 'Repeated checkout is idempotent');
    check_test(
        (int) query_db('SELECT COUNT(*) FROM orders WHERE cart_key=?', [
            $guestKey,
        ])->fetchColumn() === 1,
        'No duplicate order',
    );
    query_db("UPDATE products SET name='Edited product',price=200000 WHERE id=?", [$productId]);
    $snapshot = query_db('SELECT name,price FROM order_items WHERE order_id=?', [
        $order['id'],
    ])->fetch();
    check_test(
        $snapshot['name'] === 'Original product' && (int) $snapshot['price'] === 100000,
        'Order retains historical name/price',
    );
    change_order_status((int) $order['id'], 'cancelled');
    check_test(
        (int) query_db('SELECT stock FROM products WHERE id=?', [$productId])->fetchColumn() === 2,
        'Cancel restores inventory',
    );
    $blocked = false;
    try {
        change_order_status((int) $order['id'], 'cancelled');
    } catch (DomainException $error) {
        $blocked = true;
    }
    check_test(
        $blocked &&
            (int) query_db('SELECT stock FROM products WHERE id=?', [$productId])->fetchColumn() ===
                2,
        'Repeat cancel cannot restore twice',
    );
    set_cart_quantity($productId, 1);
    query_db('UPDATE products SET active=0 WHERE id=?', [$productId]);
    check_test(load_cart_products() === [], 'Hidden products pruned from cart');
    check_test(
        (int) query_db('SELECT COUNT(*) FROM cart_items WHERE cart_key=?', [
            $guestKey,
        ])->fetchColumn() === 0,
        'No hidden ghost items left in SQL',
    );
    query_db('UPDATE products SET active=1 WHERE id=?', [$productId]);
    $hash = password_hash('Integration-' . $suffix, PASSWORD_DEFAULT);
    query_db('INSERT INTO users(name,email,password_hash) VALUES (?,?,?)', [
        'Test customer',
        'test-' . $suffix . '@example.test',
        $hash,
    ]);
    $userId = (int) db()->lastInsertId();
    set_cart_quantity($productId, 1);
    query_db('INSERT INTO cart_items(cart_key,product_id,quantity) VALUES (?,?,1)', [
        'user:' . $userId,
        $productId,
    ]);
    merge_guest_cart($userId);
    $_SESSION['user_id'] = $userId;
    check_test((int) load_cart_products()[$productId]['quantity'] === 2, 'Guest and signed-in carts merge');
    check_test(
        (int) query_db('SELECT COUNT(*) FROM cart_items WHERE cart_key=?', [
            $guestKey,
        ])->fetchColumn() === 0,
        'Guest cart removed after merge',
    );
    $_SESSION['checkout_token'] = bin2hex(random_bytes(32));
    $second = create_pet_order(
        'Khách test',
        '0901234567',
        '12 Đường Test, Thành phố Test',
        $_SESSION['checkout_token'],
    );
    check_test(
        (int) $second['shipping'] === 0 && (int) $second['total'] === 400000,
        'Free shipping threshold',
    );
    check_test((int) $second['user_id'] === $userId, 'Order belongs to authenticated user');
    change_order_status((int) $second['id'], 'confirmed');
    change_order_status((int) $second['id'], 'shipped');
    change_order_status((int) $second['id'], 'delivered');
    check_test(
        query_db('SELECT status FROM orders WHERE id=?', [$second['id']])->fetchColumn() ===
            'delivered',
        'Full order state lifecycle',
    );
    $blocked = false;
    try {
        change_order_status((int) $second['id'], 'cancelled');
    } catch (DomainException $error) {
        $blocked = true;
    }
    check_test($blocked, 'Delivered order cannot be cancelled');
    echo json_encode(['checks' => $checks, 'result' => 'passed'], JSON_PRETTY_PRINT) . "\n";
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage() . "\n");
    $exitCode = 1;
} finally {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    if ($productId) {
        query_db(
            'DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE cart_key=? OR user_id=?)',
            [$guestKey, $userId],
        );
        query_db('DELETE FROM orders WHERE cart_key=? OR user_id=?', [$guestKey, $userId]);
        query_db('DELETE FROM cart_items WHERE product_id=?', [$productId]);
        query_db('DELETE FROM products WHERE id=?', [$productId]);
    }
    if ($categoryId) {
        query_db('DELETE FROM categories WHERE id=?', [$categoryId]);
    }
    if ($userId) {
        query_db('DELETE FROM users WHERE id=?', [$userId]);
    }
}

exit($exitCode);

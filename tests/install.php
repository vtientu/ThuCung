<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}
require __DIR__ . '/../Model/Database.php';
$config = database_config();
if (!str_ends_with($config['name'], '_test')) {
    fwrite(STDERR, "Refusing to run installer tests outside a separate _test database.\n");
    exit(1);
}
$endpoint = !empty($config['socket'])
    ? 'unix_socket=' . $config['socket']
    : 'host=' . $config['host'] . ';port=' . (int) ($config['port'] ?? 3306);
$connection = new PDO(
    'mysql:' . $endpoint . ';dbname=' . $config['name'] . ';charset=utf8mb4',
    $config['user'],
    $config['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
);
if ($connection->query('SHOW TABLES')->fetchAll()) {
    fwrite(
        STDERR,
        "Use an EMPTY disposable _test database; installer tests preserve their fixtures for inspection.\n",
    );
    exit(1);
}
$checks = 0;
function check_install(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
}
try {
    initialize_database($connection, array_merge($config, ['auto_setup' => false]));
    check_install(
        $connection->query('SHOW TABLES')->fetchAll() === [],
        'Disabled bootstrap must not create tables',
    );
    $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
    // Simulate an interrupted first installation, preserving a customized existing category.
    foreach (schema_statements($schema) as $statement) {
        if (str_starts_with($statement, 'CREATE TABLE IF NOT EXISTS categories')) {
            $connection->exec($statement);
        }
    }
    $connection->exec(
        "INSERT INTO categories(id,slug,name) VALUES(1,'food','Existing custom food')",
    );
    initialize_database($connection, $config);
    check_install(
        count($connection->query('SHOW TABLES')->fetchAll()) === 8,
        'All application tables created',
    );
    check_install(
        (int) $connection->query('SELECT COUNT(*) FROM products')->fetchColumn() === 8,
        'Eight products seeded',
    );
    check_install(
        (int) $connection->query('SELECT COUNT(*) FROM categories')->fetchColumn() === 5,
        'Five categories seeded',
    );
    check_install(
        $connection->query('SELECT name FROM categories WHERE id=1')->fetchColumn() ===
            'Existing custom food',
        'Partial-install custom data preserved',
    );
    check_install(
        installed_schema_version($connection) === 1,
        'Installation marked ready only after seed',
    );
    check_install(
        (int) $connection->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0,
        'No default administrator credentials',
    );
    $connection->exec("UPDATE products SET name='My edited food',price=123000,stock=17 WHERE id=1");
    $connection->exec(
        "INSERT INTO users(name,email,password_hash,role) VALUES('Existing admin','seed-test@example.test','not-a-login-hash','admin')",
    );
    $connection->exec(
        "UPDATE settings SET setting_value='1' WHERE setting_key='admin_initialized'",
    );
    $connection->exec(
        "INSERT INTO orders(code,checkout_token,cart_key,name,phone,address,subtotal,shipping,total) VALUES('KEEP-MY-ORDER',REPEAT('a',64),'guest:seed-test','Existing customer','0901234567','An existing address',123000,30000,153000)",
    );
    $orderId = (int) $connection->lastInsertId();
    $connection->exec(
        "INSERT INTO order_items(order_id,product_id,name,price,quantity) VALUES($orderId,1,'Historical food',123000,1)",
    );
    $connection->exec('DELETE FROM products WHERE id=8');
    // Normal reload must not reseed even a removed initial product.
    initialize_database($connection, $config);
    check_install(
        (int) $connection->query('SELECT COUNT(*) FROM products')->fetchColumn() === 7,
        'Reload does not resurrect deleted seed',
    );
    check_install(
        $connection->query('SELECT name FROM products WHERE id=1')->fetchColumn() ===
            'My edited food',
        'Edited product name preserved',
    );
    check_install(
        (int) $connection->query('SELECT price FROM products WHERE id=1')->fetchColumn() === 123000,
        'Edited price preserved',
    );
    check_install(
        (int) $connection->query('SELECT stock FROM products WHERE id=1')->fetchColumn() === 17,
        'Inventory preserved',
    );
    check_install(
        (int) $connection->query('SELECT COUNT(*) FROM users')->fetchColumn() === 1,
        'Account preserved',
    );
    check_install(
        (int) $connection
            ->query('SELECT total FROM orders WHERE code="KEEP-MY-ORDER"')
            ->fetchColumn() === 153000,
        'Order preserved',
    );
    check_install(
        $connection
            ->query('SELECT name FROM order_items WHERE order_id=' . $orderId)
            ->fetchColumn() === 'Historical food',
        'Order detail snapshot preserved',
    );
    check_install(
        $connection
            ->query("SELECT setting_value FROM settings WHERE setting_key='admin_initialized'")
            ->fetchColumn() === '1',
        'Admin setup remains locked',
    );
    // Older installations missing the version marker receive an additive setup, not a reset.
    $connection->exec("DELETE FROM settings WHERE setting_key='schema_version'");
    initialize_database($connection, $config);
    check_install(
        installed_schema_version($connection) === 1,
        'Previous schema gains version marker',
    );
    check_install(
        (int) $connection->query('SELECT price FROM products WHERE id=1')->fetchColumn() === 123000,
        'Additive bootstrap does not overwrite existing seed row',
    );
    check_install(
        (int) $connection->query('SELECT COUNT(*) FROM users')->fetchColumn() === 1 &&
            (int) $connection->query('SELECT COUNT(*) FROM orders')->fetchColumn() === 1,
        'Upgrade preserves users and orders',
    );
    $pieces = schema_statements(
        "-- Ignore this; comment\nINSERT INTO example VALUES('a;b', 'it''s good'); /* ; */ SELECT 'x;--y';",
    );
    check_install(
        count($pieces) === 2 &&
            str_contains($pieces[0], "'a;b'") &&
            str_contains($pieces[1], "'x;--y'"),
        'Parser preserves quoted semicolons and ignores comments',
    );
    echo json_encode(['checks' => $checks, 'result' => 'passed'], JSON_PRETTY_PRINT) . "\n";
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage() . "\n");
    exit(1);
}

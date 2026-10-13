<?php
function database_config(): array
{
    static $config;
    if ($config === null) {
        $path = getenv('MOCHI_CONFIG_FILE') ?: __DIR__ . '/../config/database.php';
        $config = require is_file($path) ? $path : __DIR__ . '/../config/database.example.php';
    }
    return $config;
}

function db(): PDO
{
    static $connection;
    if ($connection === null) {
        $config = database_config();
        $endpoint = !empty($config['socket'])
            ? 'unix_socket=' . $config['socket']
            : 'host=' . $config['host'] . ';port=' . (int) ($config['port'] ?? 3306);
        $connection = new PDO(
            'mysql:' . $endpoint . ';dbname=' . $config['name'] . ';charset=utf8mb4',
            $config['user'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
        );
        $connection->exec("SET time_zone = '+07:00'");
    }
    return $connection;
}

function query_db(string $sql, array $parameters = []): PDOStatement
{
    $statement = db()->prepare($sql);
    $statement->execute($parameters);
    return $statement;
}

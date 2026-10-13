<?php
require_once __DIR__ . '/Install.php';
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
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $dsn = 'mysql:' . $endpoint . ';charset=utf8mb4';
        try {
            $newConnection = new PDO($dsn . ';dbname=' . $config['name'], $config['user'], $config['password'], $options);
        } catch (PDOException $error) {
            $local = !empty($config['socket']) || in_array($config['host'], ['127.0.0.1', 'localhost', '::1'], true);
            if ((int) ($error->errorInfo[1] ?? 0) !== 1049 || !$local || ($config['auto_setup'] ?? true) === false) {
                throw $error;
            }
            // Only bootstrap a missing local database; never replace existing data.
            $newConnection = new PDO($dsn, $config['user'], $config['password'], $options);
            $database = '`' . str_replace('`', '``', $config['name']) . '`';
            $newConnection->exec('CREATE DATABASE IF NOT EXISTS ' . $database . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $newConnection->exec('USE ' . $database);
        }
        $newConnection->exec("SET time_zone = '+07:00'");
        initialize_database($newConnection, $config);
        $connection = $newConnection;
    }
    return $connection;
}

function query_db(string $sql, array $parameters = []): PDOStatement
{
    $statement = db()->prepare($sql);
    $statement->execute($parameters);
    return $statement;
}

<?php
const MOCHI_SCHEMA_VERSION = 4;

function installed_schema_version(PDO $connection): int
{
    try {
        $statement = $connection->prepare(
            "SELECT setting_value FROM settings WHERE setting_key = 'schema_version'",
        );
        $statement->execute();
        return (int) $statement->fetchColumn();
    } catch (PDOException $error) {
        // Chỉ database chưa có bảng settings mới được xem là chưa khởi tạo.
        if ((int) ($error->errorInfo[1] ?? 0) !== 1146) {
            throw $error;
        }
        return 0;
    }
}

/** Tách SQL ở dấu ; ngoài chuỗi/chú thích. Không chạy dữ liệu hoặc mã từ HTTP. */
function schema_statements(string $sql): array
{
    $statements = [];
    $buffer = '';
    $quote = null;
    $length = strlen($sql);
    for ($i = 0; $i < $length; $i++) {
        $character = $sql[$i];
        $next = $sql[$i + 1] ?? '';
        if ($quote !== null) {
            $buffer .= $character;
            if ($character === '\\' && $next !== '') {
                $buffer .= $next;
                $i++;
            } elseif ($character === $quote) {
                if ($next === $quote) {
                    $buffer .= $next;
                    $i++;
                } else {
                    $quote = null;
                }
            }
            continue;
        }
        if (
            ($character === '-' &&
                $next === '-' &&
                in_array($sql[$i + 2] ?? ' ', [' ', "\t", "\n", "\r", "\v", "\f"], true)) ||
            $character === '#'
        ) {
            while ($i < $length && $sql[$i] !== "\n") {
                $i++;
            }
            $buffer .= "\n";
            continue;
        }
        if ($character === '/' && $next === '*') {
            $end = strpos($sql, '*/', $i + 2);
            if ($end === false) {
                throw new PDOException('Schema contains an unclosed comment.');
            }
            $i = $end + 1;
            $buffer .= ' ';
            continue;
        }
        if (in_array($character, ["'", '"', '`'], true)) {
            $quote = $character;
        }
        if ($character === ';') {
            if (trim($buffer) !== '') {
                $statements[] = trim($buffer);
            }
            $buffer = '';
        } else {
            $buffer .= $character;
        }
    }
    if ($quote !== null) {
        throw new PDOException('Schema contains an unclosed string.');
    }
    if (trim($buffer) !== '') {
        $statements[] = trim($buffer);
    }
    return $statements;
}

function initialize_database(PDO $connection, array $config): void
{
    if (
        ($config['auto_setup'] ?? true) === false ||
        installed_schema_version($connection) >= MOCHI_SCHEMA_VERSION
    ) {
        return;
    }
    // Khóa theo database để hai lượt truy cập đầu không cùng chạy DDL/seed.
    $lockName = 'mochi:bootstrap:' . sha1($config['name']);
    $lock = $connection->prepare('SELECT GET_LOCK(?, 10)');
    $lock->execute([$lockName]);
    if ((int) $lock->fetchColumn() !== 1) {
        throw new PDOException('Database initialization is busy; retry the request.');
    }
    try {
        if (installed_schema_version($connection) >= MOCHI_SCHEMA_VERSION) {
            return;
        }
        $path = __DIR__ . '/../database/schema.sql';
        $sql = is_readable($path) ? file_get_contents($path) : false;
        if ($sql === false) {
            throw new PDOException('Cannot read database/schema.sql.');
        }
        foreach (schema_statements($sql) as $statement) {
            $connection->exec($statement);
        }
        if (!$connection->query("SHOW COLUMNS FROM orders LIKE 'email'")->fetch()) {
            $connection->exec("ALTER TABLE orders ADD COLUMN email VARCHAR(254) NOT NULL DEFAULT ''");
        }
        // Update only image filenames for the eight seeded products.
        $imageUpdates = [
            1 => 'hat-dinh-duong-meo.png',
            2 => 'bat-gom-pastel.png',
            3 => 'do-choi-xuong-mem.png',
            4 => 'vong-co.png',
            5 => 'nem-cloud.png',
            6 => 'sua-tam.png',
            7 => 'can-cau-long-vu.png',
            8 => 'snack-cun.png',
        ];
        $seededNames = [
            1 => 'Hạt dinh dưỡng cho mèo',
            2 => 'Bát ăn gốm Pastel',
            3 => 'Đồ chơi xương mềm',
            4 => 'Vòng cổ Everyday',
            5 => 'Nệm êm Cloud Bed',
            6 => 'Sữa tắm dịu nhẹ Oat',
            7 => 'Cần câu lông vũ',
            8 => 'Snack thưởng cho cún',
        ];
        $updateImage = $connection->prepare('UPDATE products SET image=? WHERE id=? AND name=?');
        foreach ($imageUpdates as $productId => $image) {
            $updateImage->execute([$image, $productId, $seededNames[$productId]]);
        }
        // Đánh dấu chỉ sau khi mọi bảng và dữ liệu khởi tạo đã thành công.
        $connection->exec(
            "UPDATE settings SET setting_value='4' WHERE setting_key='schema_version'",
        );
    } finally {
        $release = $connection->prepare('SELECT RELEASE_LOCK(?)');
        $release->execute([$lockName]);
    }
}

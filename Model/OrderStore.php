<?php
/** Lưu đơn cục bộ; file PHP chỉ chứa chú thích nên không trả dữ liệu qua HTTP. */
function save_pet_order(array $order): bool
{
    $directory = __DIR__ . '/../storage';
    if (!is_dir($directory) && !mkdir($directory, 0700, true)) {
        return false;
    }

    $handle = fopen($directory . '/orders.php', 'c+b');
    if ($handle === false) {
        return false;
    }
    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return false;
    }

    fseek($handle, 0, SEEK_END);
    $prefix = ftell($handle) === 0 ? "<?php\n" : '';
    $record = base64_encode(json_encode($order, JSON_UNESCAPED_UNICODE));
    $contents = $prefix . '/* ' . $record . " */\n";
    $saved = fwrite($handle, $contents) === strlen($contents);
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
    return $saved;
}

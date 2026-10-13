<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once __DIR__ . '/../Model/PetCatalog.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    session_start();
}
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
$_SESSION['guest_cart'] ??= bin2hex(random_bytes(24));

function csrf(): void
{
    echo '<input type="hidden" name="csrf" value="' . h($_SESSION['csrf']) . '">';
}

function verify_csrf(): void
{
    if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        http_response_code(403);
        exit('Phiên đã hết hạn. Vui lòng tải lại trang.');
    }
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $user = query_db('SELECT id, name, email, phone, address, role FROM users WHERE id = ? AND active = 1', [$_SESSION['user_id']])->fetch();
    return $user ?: null;
}

function redirect_to(string $url): void
{
    header('Location: ' . $url, true, 303);
    exit;
}

function require_admin(): array
{
    $user = current_user();
    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        exit('Bạn không có quyền truy cập quản trị. Hãy đăng nhập tài khoản admin.');
    }
    return $user;
}

function database_error(Throwable $error): void
{
    error_log('Mochi Pet database error: ' . $error->getMessage());
    http_response_code(503);
    echo '<!doctype html><html lang="vi"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Mochi Pet · Cấu hình dữ liệu</title><body style="background:#fbf8f2;color:#343d32;font:16px Arial;padding:10%;line-height:1.8"><h1>Mochi chưa kết nối được dữ liệu</h1><p>Kiểm tra cấu hình MySQL trong <strong>config/database.php</strong> và nhập file <strong>database/schema.sql</strong> bằng phpMyAdmin.</p><p>Đọc README để cấu hình XAMPP hoặc InfinityFree. Không có đơn hàng nào được báo thành công khi chưa lưu vào MySQL.</p></body></html>';
    exit;
}

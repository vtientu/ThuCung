<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once __DIR__ . '/../Model/PetCatalog.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
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
    static $cachedId = null;
    static $cachedUser = null;
    $id = (int) ($_SESSION['user_id'] ?? 0);
    if ($id === 0) {
        return null;
    }
    // Cache chỉ trong request hiện tại; request sau luôn kiểm tra lại trạng thái/role.
    if ($cachedId !== $id) {
        $cachedUser =
            query_db(
                'SELECT id, name, email, phone, address, role FROM users WHERE id = ? AND active = 1',
                [$id],
            )->fetch() ?:
            null;
        $cachedId = $id;
    }
    return $cachedUser;
}

function redirect_to(string $url): void
{
    header('Location: ' . $url, true, 303);
    exit();
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
    $code = $error instanceof PDOException ? (int) ($error->errorInfo[1] ?? 0) : 0;
    $hint = match ($code) {
        1049 => 'Database chưa tồn tại. Kiểm tra tên database; với MySQL local, bật auto_setup để web tự tạo database.',
        1044, 1142 => 'Tài khoản MySQL chưa có đủ quyền. Cần quyền tạo database/bảng và đọc, ghi dữ liệu; hoặc nhờ quản trị viên import database/schema.sql.',
        1045 => 'MySQL từ chối tài khoản hoặc mật khẩu. Kiểm tra user và password trong config/database.php.',
        2002, 2003 => 'Không kết nối được máy chủ MySQL. Bật MySQL trong XAMPP và kiểm tra host, port trong cấu hình.',
        default => 'Kiểm tra cấu hình MySQL và xem dòng Mochi Pet database error trong Apache → Logs → error.log để biết nguyên nhân cụ thể.',
    };
    echo '<!doctype html><html lang="vi"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Mochi Pet · Cấu hình dữ liệu</title><body style="background:#fbf8f2;color:#343d32;font:16px Arial;padding:10%;line-height:1.8"><h1>Mochi chưa kết nối được dữ liệu</h1><p>' . htmlspecialchars($hint, ENT_QUOTES, 'UTF-8') . '</p><p>Web tự tạo database local, bảng và dữ liệu mẫu khi auto_setup bật. Đọc README để cấu hình máy dùng mật khẩu hoặc port riêng.</p></body></html>';
    exit();
}

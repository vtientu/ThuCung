<?php
require __DIR__ . '/Controller/Bootstrap.php';
$error = '';
$created = false;
$transactionStarted = false;
try {
    $initialized = query_db("SELECT setting_value FROM settings WHERE setting_key = 'admin_initialized'")->fetchColumn();
    if ($initialized === '1' || query_db("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn() > 0) {
        http_response_code(403);
        exit('Quản trị đã được khởi tạo. Trang thiết lập đã khóa.');
    }
    $configuredToken = (string) (database_config()['setup_token'] ?? '');
    if (strlen($configuredToken) < 24) {
        http_response_code(503);
        exit('Đặt setup_token ngẫu nhiên dài ít nhất 24 ký tự trong config/database.php trước khi tạo admin.');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (!hash_equals($configuredToken, (string) ($_POST['setup_token'] ?? ''))) {
            http_response_code(403);
            $error = 'Mã thiết lập không đúng.';
        } elseif (!$name || text_length($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190 || strlen($password) < 10 || strlen($password) > 72 || $password !== ($_POST['confirm_password'] ?? '')) {
            $error = 'Thông tin chưa hợp lệ. Mật khẩu cần 10–72 byte và xác nhận khớp nhau.';
        } else {
            db()->beginTransaction();
            $transactionStarted = true;
            $state = query_db("SELECT setting_value FROM settings WHERE setting_key = 'admin_initialized' FOR UPDATE")->fetchColumn();
            if ($state !== '0' || query_db('SELECT id FROM users WHERE email = ?', [$email])->fetchColumn()) {
                db()->rollBack();
                $error = 'Trang thiết lập đã khóa hoặc email đã tồn tại. Dùng email chưa đăng ký.';
            } else {
                query_db("INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,'admin')", [$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
                query_db("UPDATE settings SET setting_value = '1' WHERE setting_key = 'admin_initialized'");
                db()->commit();
                $created = true;
            }
        }
    }
} catch (PDOException $exception) {
    if ($transactionStarted && db()->inTransaction()) { db()->rollBack(); }
    database_error($exception);
}
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Khởi tạo quản trị · Mochi Pet</title><link rel="stylesheet" href="assets/style.css"></head><body class="auth-body"><main class="auth-card setup-card"><a class="logo" href="index.php">mochi<span class="logo-pet">pet.</span></a><span class="eyebrow">THIẾT LẬP MỘT LẦN</span><h1>Tạo quản trị viên</h1><?php if ($created): ?><p>Đã tạo admin và khóa trang thiết lập. Xóa setup_token khỏi cấu hình hoặc xóa setup.php trên hosting.</p><a class="button" href="auth.php">Đăng nhập quản trị →</a><?php else: ?><?php if ($error): ?><p class="form-error" role="alert"><?= h($error) ?></p><?php endif; ?><form method="post" class="checkout-form"><?php csrf(); ?><label>Mã thiết lập<input type="password" name="setup_token" autocomplete="off" required></label><label>Họ và tên<input name="name" maxlength="100" required></label><label>Email admin<input type="email" name="email" maxlength="190" required></label><label>Mật khẩu<input type="password" name="password" minlength="10" maxlength="72" required></label><label>Nhập lại mật khẩu<input type="password" name="confirm_password" minlength="10" maxlength="72" required></label><button class="button">Tạo admin và khóa thiết lập</button></form><?php endif; ?></main></body></html>

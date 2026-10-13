<?php
require_once __DIR__ . '/../Model/Cart.php';
$mode = $_GET['mode'] ?? 'login';
if (!in_array($mode, ['login', 'register'], true)) {
    $mode = 'login';
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'logout') {
        unset($_SESSION['user_id'], $_SESSION['last_order']);
        $_SESSION['guest_cart'] = bin2hex(random_bytes(24));
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        session_regenerate_id(true);
        redirect_to('index.php');
    }
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $key = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? 'local') . '|' . $email);
    $attempt = query_db(
        'SELECT failures FROM login_attempts WHERE attempt_key = ? AND last_attempt > DATE_SUB(NOW(), INTERVAL 15 MINUTE)',
        [$key],
    )->fetchColumn();
    try {
        if ((int) $attempt >= 6) {
            throw new DomainException('Bạn đã thử quá nhiều lần. Vui lòng thử lại sau 15 phút.');
        }
        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 190 ||
            strlen($password) > 72
        ) {
            throw new DomainException('Email hoặc mật khẩu chưa hợp lệ.');
        }
        if ($mode === 'register') {
            $name = trim($_POST['name'] ?? '');
            if (
                !$name ||
                text_length($name) > 100 ||
                strlen($password) < 10 ||
                $password !== ($_POST['confirm_password'] ?? '')
            ) {
                throw new DomainException(
                    'Nhập họ tên và mật khẩu ít nhất 10 ký tự, xác nhận khớp nhau.',
                );
            }
            if (query_db('SELECT id FROM users WHERE email = ?', [$email])->fetchColumn()) {
                throw new DomainException('Email này đã được đăng ký.');
            }
            try {
                query_db('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)', [
                    $name,
                    $email,
                    password_hash($password, PASSWORD_DEFAULT),
                ]);
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    throw new DomainException('Email này đã được đăng ký.');
                }
                throw $exception;
            }
            $userId = (int) db()->lastInsertId();
        } else {
            $account = query_db('SELECT * FROM users WHERE email = ? AND active = 1', [
                $email,
            ])->fetch();
            if (!$account || !password_verify($password, $account['password_hash'])) {
                query_db(
                    'INSERT INTO login_attempts (attempt_key, failures) VALUES (?, 1) ON DUPLICATE KEY UPDATE failures = IF(last_attempt < DATE_SUB(NOW(), INTERVAL 15 MINUTE), 1, failures + 1), last_attempt = NOW()',
                    [$key],
                );
                throw new DomainException('Email hoặc mật khẩu không đúng.');
            }
            $userId = (int) $account['id'];
            if (password_needs_rehash($account['password_hash'], PASSWORD_DEFAULT)) {
                query_db('UPDATE users SET password_hash = ? WHERE id = ?', [
                    password_hash($password, PASSWORD_DEFAULT),
                    $userId,
                ]);
            }
        }
        merge_guest_cart($userId);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        $_SESSION['checkout_token'] = bin2hex(random_bytes(32));
        query_db('DELETE FROM login_attempts WHERE attempt_key = ?', [$key]);
        $_SESSION['notice'] = 'Chào mừng bạn đến với Mochi!';
        redirect_to(current_user()['role'] === 'admin' ? 'admin.php' : 'index.php?page=account');
    } catch (DomainException $exception) {
        $error = $exception->getMessage();
    }
}

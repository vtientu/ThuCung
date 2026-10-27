<?php
require_once __DIR__ . '/../Model/Cart.php';
require_once __DIR__ . '/../Model/OrderStore.php';
$_SESSION['checkout_token'] ??= bin2hex(random_bytes(32));
$cartProducts = load_cart_products();
$_SESSION['cart'] = array_map(fn($product) => (int) $product['quantity'], $cartProducts);
$notice = $_SESSION['notice'] ?? null;
unset($_SESSION['notice']);
$checkoutError = '';
$accountError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    try {
        if ($action === 'add') {
            set_cart_quantity(
                $id,
                ($_SESSION['cart'][$id] ?? 0) + max(1, min(20, (int) ($_POST['quantity'] ?? 1))),
            );
            $_SESSION['notice'] = 'Đã thêm vào giỏ hàng. Bé sắp có quà rồi!';
        } elseif ($action === 'update') {
            set_cart_quantity($id, max(0, (int) ($_POST['quantity'] ?? 0)));
        } elseif ($action === 'checkout') {
            $email = trim((string) ($_POST['email'] ?? ''));
            if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new DomainException('Vui lòng nhập email nhận xác nhận đơn hàng hợp lệ.');
            }
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            if (
                !$name ||
                text_length($name) > 100 ||
                text_length($address) > 500 ||
                !preg_match('/^(?:\+84|0)[0-9]{9,10}$/', $phone) ||
                text_length($address) < 10
            ) {
                throw new DomainException(
                    'Vui lòng nhập họ tên, số điện thoại hợp lệ và địa chỉ đầy đủ (ít nhất 10 ký tự).',
                );
            }
            $token = (string) ($_POST['checkout_token'] ?? '');
            if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
                throw new DomainException('Phiên đặt hàng không hợp lệ. Vui lòng tải lại trang.');
            }
            $_SESSION['last_order'] = create_pet_order($name, $phone, $address, $token, $email);
            $_SESSION['checkout_token'] = bin2hex(random_bytes(32));
            redirect_to('index.php?page=success');
        } elseif ($action === 'profile') {
            $user = current_user();
            if (!$user) {
                redirect_to('auth.php');
            }
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            if (
                !$name ||
                text_length($name) > 100 ||
                text_length($address) > 500 ||
                ($phone && !preg_match('/^(?:\+84|0)[0-9]{9,10}$/', $phone))
            ) {
                throw new DomainException('Thông tin tài khoản chưa hợp lệ.');
            }
            query_db('UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?', [
                $name,
                $phone,
                $address,
                $user['id'],
            ]);
            $_SESSION['notice'] = 'Đã cập nhật thông tin tài khoản.';
            redirect_to('index.php?page=account');
        } elseif ($action === 'password') {
            $user = current_user();
            if (!$user) {
                redirect_to('auth.php');
            }
            $hash = query_db('SELECT password_hash FROM users WHERE id = ?', [
                $user['id'],
            ])->fetchColumn();
            $password = (string) ($_POST['password'] ?? '');
            if (
                !password_verify((string) ($_POST['current_password'] ?? ''), $hash) ||
                strlen($password) < 10 ||
                strlen($password) > 72 ||
                $password !== ($_POST['confirm_password'] ?? '')
            ) {
                throw new DomainException(
                    'Kiểm tra mật khẩu hiện tại; mật khẩu mới cần 10–72 byte và xác nhận khớp nhau.',
                );
            }
            query_db('UPDATE users SET password_hash = ? WHERE id = ?', [
                password_hash($password, PASSWORD_DEFAULT),
                $user['id'],
            ]);
            session_regenerate_id(true);
            $_SESSION['notice'] = 'Đã đổi mật khẩu.';
            redirect_to('index.php?page=account');
        }
        $return = $_POST['return'] ?? 'home';
        $target = in_array($return, ['home', 'shop', 'cart'], true) ? $return : 'home';
        redirect_to('index.php?page=' . $target . ($target === 'shop' ? '#products' : ''));
    } catch (DomainException $error) {
        if (in_array($action, ['profile', 'password'], true)) {
            $accountError = $error->getMessage();
        } else {
            $checkoutError = $error->getMessage();
        }
        $cartProducts = load_cart_products();
        $_SESSION['cart'] = array_map(fn($product) => (int) $product['quantity'], $cartProducts);
    }
}
$user = current_user();
if (($_GET['page'] ?? '') === 'account' && !$user) {
    redirect_to('auth.php');
}
$cartCount = array_sum($_SESSION['cart']);
$cartTotal = array_sum(
    array_map(fn($product) => (int) $product['price'] * (int) $product['quantity'], $cartProducts),
);

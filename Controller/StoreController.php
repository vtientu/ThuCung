<?php
require_once __DIR__ . '/../Model/PetCatalog.php';
require_once __DIR__ . '/../Model/OrderStore.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
$_SESSION['cart'] ??= [];
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
$notice = $_SESSION['notice'] ?? null;
unset($_SESSION['notice']);
$checkoutError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(403); exit('Phiên đã hết hạn. Vui lòng tải lại trang.');
    }
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    if ($action === 'add' && pet_product($id)) {
        $_SESSION['cart'][$id] = min(20, ($_SESSION['cart'][$id] ?? 0) + max(1, min(20, (int) ($_POST['quantity'] ?? 1))));
        $_SESSION['notice'] = 'Đã thêm vào giỏ hàng. Bé sắp có quà rồi!';
    } elseif ($action === 'update' && pet_product($id)) {
        $quantity = max(0, min(20, (int) ($_POST['quantity'] ?? 0)));
        if ($quantity === 0) { unset($_SESSION['cart'][$id]); } else { $_SESSION['cart'][$id] = $quantity; }
    } elseif ($action === 'checkout') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        if (!$name || mb_length($name) > 100 || mb_length($address) > 500 || !preg_match('/^(?:\+84|0)[0-9]{9,10}$/', $phone) || mb_length($address) < 10 || !$_SESSION['cart']) {
            $checkoutError = 'Vui lòng nhập họ tên, số điện thoại hợp lệ và địa chỉ đầy đủ (ít nhất 10 ký tự).';
        } else {
            $items = []; $total = 0;
            foreach ($_SESSION['cart'] as $productId => $quantity) {
                $product = pet_product((int) $productId);
                if ($product) { $items[] = ['id' => $productId, 'name' => $product['name'], 'quantity' => $quantity, 'price' => $product['price']]; $total += $product['price'] * $quantity; }
            }
            $shipping = $total >= 399000 ? 0 : 30000;
            $order = ['code' => 'MP' . date('ymd') . strtoupper(bin2hex(random_bytes(3))), 'created_at' => date(DATE_ATOM), 'name' => $name, 'phone' => $phone, 'address' => $address, 'items' => $items, 'total' => $total + $shipping, 'payment' => 'COD', 'status' => 'Chờ xử lý'];
            $saved = save_pet_order($order);
            if ($saved === false) { $checkoutError = 'Chưa lưu được đơn hàng. Vui lòng kiểm tra quyền ghi thư mục storage.'; }
            else { $_SESSION['last_order'] = $order; $_SESSION['cart'] = []; header('Location: index.php?page=success'); exit; }
        }
    }
    if (!$checkoutError) {
        $return = $_POST['return'] ?? 'home';
        $target = in_array($return, ['home', 'shop', 'cart'], true) ? $return : 'home';
        header('Location: index.php?page=' . $target . ($target === 'shop' ? '#products' : '')); exit;
    }
}
function mb_length(string $value): int { return preg_match_all('/./us', $value); }
$cartCount = array_sum($_SESSION['cart']);
$cartTotal = 0;
foreach ($_SESSION['cart'] as $id => $quantity) { $product = pet_product((int) $id); if ($product) { $cartTotal += $product['price'] * $quantity; } }

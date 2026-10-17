<?php
require_once __DIR__ . '/../Model/OrderStore.php';
$admin = require_admin();
$section = $_GET['section'] ?? 'dashboard';
if (!in_array($section, ['dashboard', 'products', 'categories', 'orders', 'customers'], true)) {
    $section = 'dashboard';
}
$error = '';
$notice = $_SESSION['notice'] ?? null;
unset($_SESSION['notice']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    $newImage = null;
    try {
        if ($action === 'product-save') {
            $name = trim($_POST['name'] ?? '');
            $brand = trim($_POST['brand'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $badge = trim($_POST['badge'] ?? '');
            $category = (int) ($_POST['category_id'] ?? 0);
            $pet = $_POST['pet'] ?? '';
            $color = $_POST['color'] ?? '';
            $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_INT);
            $old = filter_var($_POST['old'] ?? '0', FILTER_VALIDATE_INT);
            $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT);
            if (
                !$name ||
                text_length($name) > 150 ||
                !$brand ||
                text_length($brand) > 100 ||
                text_length($description) > 10000 ||
                text_length($badge) > 30 ||
                !query_db('SELECT id FROM categories WHERE id = ?', [$category])->fetchColumn() ||
                !in_array($pet, ['all', 'dog', 'cat'], true) ||
                !in_array($color, ['peach', 'green', 'yellow', 'pink', 'lavender'], true) ||
                $price === false ||
                $price < 1000 ||
                $price > 999999999 ||
                $old === false ||
                $old < 0 ||
                $old > 999999999 ||
                ($old > 0 && $old < $price) ||
                $stock === false ||
                $stock < 0 ||
                $stock > 1000000
            ) {
                throw new DomainException('Kiểm tra tên, danh mục, giá, giá cũ và tồn kho hợp lệ.');
            }
            $existing = $id
                ? query_db('SELECT * FROM products WHERE id = ?', [$id])->fetch()
                : null;
            if ($id && !$existing) {
                throw new DomainException('Sản phẩm không tồn tại.');
            }
            $image = $_POST['image'] ?? 'food';
            if (
                !in_array(
                    $image,
                    [
                        'food', 'bowl', 'bone', 'collar', 'bed', 'shampoo', 'feather', 'snack',
                        'hat-dinh-duong-meo.png', 'bat-gom-pastel.png', 'do-choi-xuong-mem.png',
                        'vong-co.png', 'nem-cloud.png', 'sua-tam.png', 'can-cau-long-vu.png',
                        'snack-cun.png',
                    ],
                    true,
                )
            ) {
                $image = $existing['image'] ?? 'food';
            }
            if (($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['photo'];
                $info = $file['error'] === UPLOAD_ERR_OK ? @getimagesize($file['tmp_name']) : false;
                $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                if (
                    !$info ||
                    !isset($types[$info['mime']]) ||
                    $file['size'] > 2 * 1024 * 1024 ||
                    $info[0] > 6000 ||
                    $info[1] > 6000
                ) {
                    throw new DomainException(
                        'Ảnh phải là JPG/PNG/WebP, tối đa 2 MB và 6000 px mỗi chiều.',
                    );
                }
                $image = 'pet-' . bin2hex(random_bytes(16)) . '.' . $types[$info['mime']];
                $uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Upload';
                if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
                    throw new DomainException('KhÃ´ng táº¡o Ä‘Æ°á»£c thÆ° má»¥c Upload/.');
                }
                $newImage = $uploadDirectory . DIRECTORY_SEPARATOR . $image;
                if (!move_uploaded_file($file['tmp_name'], $newImage)) {
                    throw new DomainException('Không lưu được ảnh. Kiểm tra quyền ghi Upload/.');
                }
            }
            $values = [
                $category,
                $name,
                $brand,
                $pet,
                $price,
                $old,
                $image,
                $color,
                $badge,
                $description,
                $stock,
                isset($_POST['active']) ? 1 : 0,
            ];
            if ($id) {
                query_db(
                    'UPDATE products SET category_id=?,name=?,brand=?,pet=?,price=?,`old`=?,image=?,color=?,badge=?,description=?,stock=?,active=? WHERE id=?',
                    [...$values, $id],
                );
            } else {
                query_db(
                    'INSERT INTO products (category_id,name,brand,pet,price,`old`,image,color,badge,description,stock,active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
                    $values,
                );
            }
        } elseif ($action === 'product-hide') {
            query_db('UPDATE products SET active = 0 WHERE id = ?', [$id]);
        } elseif ($action === 'category-save') {
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if (
                !$name ||
                text_length($name) > 100 ||
                !preg_match('/^[a-z0-9][a-z0-9-]{0,49}$/', $slug) ||
                $slug === 'all'
            ) {
                throw new DomainException(
                    'Tên danh mục và slug (chữ thường, số, dấu -; không dùng all) chưa hợp lệ.',
                );
            }
            if (
                query_db('SELECT id FROM categories WHERE slug = ? AND id <> ?', [
                    $slug,
                    $id,
                ])->fetchColumn()
            ) {
                throw new DomainException('Slug đã tồn tại.');
            }
            if ($id && !query_db('SELECT id FROM categories WHERE id=?', [$id])->fetchColumn()) {
                throw new DomainException('Danh mục không tồn tại.');
            }
            $active = isset($_POST['active']) ? 1 : 0;
            if ($id) {
                query_db('UPDATE categories SET name=?,slug=?,active=? WHERE id=?', [
                    $name,
                    $slug,
                    $active,
                    $id,
                ]);
            } else {
                query_db('INSERT INTO categories(name,slug,active) VALUES (?,?,?)', [
                    $name,
                    $slug,
                    $active,
                ]);
            }
        } elseif ($action === 'order-status') {
            change_order_status($id, (string) ($_POST['status'] ?? ''));
        } elseif ($action === 'customer-state') {
            query_db("UPDATE users SET active = ? WHERE id = ? AND role = 'customer'", [
                isset($_POST['active']) ? 1 : 0,
                $id,
            ]);
        } else {
            throw new DomainException('Thao tác không hợp lệ.');
        }
        $_SESSION['notice'] = 'Đã lưu thay đổi vào MySQL.';
        redirect_to('admin.php?section=' . $section);
    } catch (DomainException $exception) {
        if ($newImage && is_file($newImage)) {
            unlink($newImage);
        }
        $error = $exception->getMessage();
    } catch (PDOException $exception) {
        if ($newImage && is_file($newImage)) {
            unlink($newImage);
        }
        throw $exception;
    }
}
$editId = (int) ($_GET['edit'] ?? 0);

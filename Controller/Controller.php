<?php
session_start();
include('../Model/Model.php');
$data = new data_shop();
$user = new data_user();

// =========================
// CHUYỂN TRANG
// =========================
function redirect($url, $message = '')
{
    if ($message != '') {
        echo "<script>
                alert('$message');
                window.location='$url';
              </script>";
    } else {
        header("Location: $url");
    }
    exit();
}
// =========================
// KIỂM TRA QUYỀN ADMIN
// =========================
function checkAdmin()
{
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        redirect('../View/Info.php', 'Bạn không có quyền thực hiện thao tác này!');
    }
}

// =========================
// 1. THÊM SẢN PHẨM
// =========================
if (isset($_POST['txtsub'])) {

    checkAdmin();

    $image = '';

    if (isset($_FILES['txtfile']) && !empty($_FILES['txtfile']['name'])) {
        $image = $_FILES['txtfile']['name'];
        move_uploaded_file(
            $_FILES['txtfile']['tmp_name'],
            '../Upload/' . $image
        );
    }

    $result = $data->insert_shop(
        $_POST['txttensp'],
        $_POST['txtmasp'],
        $_POST['txtdanhmuc'],
        $_POST['txtsotien'],
        $_POST['txtmota'],
        $image
    );

    if ($result) {
        redirect('../View/select.php', 'Thêm sản phẩm thành công!');
    }

    redirect('../View/select.php', 'Thêm sản phẩm thất bại!');
}

// =========================
// 2. XÓA SẢN PHẨM
// =========================
if (isset($_GET['xoa']) || isset($_GET['XOA'])) {

    checkAdmin();

    $id = isset($_GET['xoa']) ? $_GET['xoa'] : $_GET['XOA'];

    if ($data->delete_shop_id($id)) {
        redirect('../View/select.php', 'Xóa sản phẩm thành công!');
    }

    redirect('../View/select.php', 'Xóa sản phẩm thất bại!');
}

// =========================
// 3. MỞ TRANG CẬP NHẬT
// =========================
if (isset($_GET['SUA']) && !isset($_POST['txt_sub_update'])) {

    checkAdmin();

    $id = intval($_GET['SUA']);

    header("Location: ../View/update.php?SUA=$id");
    exit();
}

// =========================
// 4. CẬP NHẬT SẢN PHẨM
// =========================
if (isset($_POST['txt_sub_update'])) {

    checkAdmin();

    $id = intval($_GET['SUA']);

    // Lấy ảnh cũ
    $oldImage = '';

    $oldProduct = $data->select_shop_id($id);

    if ($oldProduct && mysqli_num_rows($oldProduct) > 0) {
        $product = mysqli_fetch_assoc($oldProduct);
        $oldImage = $product['Hinhanh'];
    }

    // Nếu chọn ảnh mới thì upload
    $image = $oldImage;

    if (isset($_FILES['txtfile']) && !empty($_FILES['txtfile']['name'])) {

        $image = $_FILES['txtfile']['name'];

        move_uploaded_file(
            $_FILES['txtfile']['tmp_name'],
            '../Upload/' . $image
        );
    }

    $result = $data->update_shop_id(
        $_POST['txttensp'],
        $_POST['txtmasp'],
        $_POST['txtdanhmuc'],
        $_POST['txtsotien'],
        $_POST['txtmota'],
        $image,
        $id
    );

    if ($result) {
        redirect('../View/select.php', 'Cập nhật sản phẩm thành công!');
    }

    redirect('../View/select.php', 'Cập nhật sản phẩm thất bại!');
}

// =========================
// 5. ĐĂNG NHẬP
// =========================
if (isset($_POST['txtsub_login'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username == '' || $password == '') {
        redirect(
            '../View/login.php',
            'Vui lòng nhập đầy đủ tài khoản và mật khẩu!'
        );
    }

    $result = $user->login($username, $password);

    if ($result && mysqli_num_rows($result) == 1) {

        $account = mysqli_fetch_assoc($result);

        $_SESSION['user'] = $account['username'];
        $_SESSION['role'] = $account['role'];

        if ($account['role'] == 'admin') {
            redirect('../View/admin.php', 'Đăng nhập Admin thành công!');
        }

        redirect('../View/Info.php', 'Đăng nhập thành công!');
    }

    redirect(
        '../View/login.php',
        'Tên đăng nhập hoặc mật khẩu không đúng!'
    );
}

// =========================
// 6. CẬP NHẬT TRẠNG THÁI ĐƠN
// =========================
if (isset($_POST['capnhat_trangthai'])) {

    checkAdmin();

    $id = intval($_POST['id_donhang']);
    $status = $_POST['trangthai'];

    if ($data->update_trangthai_donhang($id, $status)) {
        redirect(
            '../View/admin.php?page=donhang',
            'Cập nhật trạng thái đơn hàng thành công!'
        );
    }

    redirect(
        '../View/admin.php?page=donhang',
        'Cập nhật trạng thái đơn hàng thất bại!'
    );
}

// =========================
// 7. TẠO ĐƠN HÀNG
// =========================
if (isset($_POST['tao_don_hang'])) {

    $hoten = trim($_POST['hoten']);
    $sdt = trim($_POST['sdt']);
    $diachi = trim($_POST['diachi']);

    $tongtien = floatval($_POST['tongtien']);

    $phuongthucthanhtoan =
        trim($_POST['phuongthucthanhtoan']);

    $ghichu = isset($_POST['ghichu'])
        ? trim($_POST['ghichu'])
        : '';

    if ($hoten == '' || $sdt == '' || $diachi == '') {

        echo "<script>
                alert('Vui lòng nhập đầy đủ thông tin đặt hàng!');
                window.history.back();
              </script>";

        exit();
    }

    $result = $data->insert_donhang(
        $hoten,
        $sdt,
        $diachi,
        $tongtien,
        $phuongthucthanhtoan,
        $ghichu
    );

    if ($result) {

        echo "<script>
                alert('Đặt hàng thành công!');
                localStorage.removeItem('nks_cart');
                window.location='../View/Info.php';
              </script>";

        exit();
    }

    echo "Lỗi tạo đơn hàng!";
    exit();
}

// =========================
// 8. ĐĂNG KÝ THÀNH VIÊN
// =========================
if (isset($_POST['txtsub_register'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $hoten = trim($_POST['hoten']);
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $diachi = trim($_POST['diachi']);

    // Kiểm tra dữ liệu
    if (
        $username == '' ||
        $password == '' ||
        $hoten == '' ||
        $email == '' ||
        $sdt == '' ||
        $diachi == ''
    ) {
        redirect(
            '../View/Info.php',
            'Vui lòng nhập đầy đủ thông tin đăng ký!'
        );
    }

    // Kiểm tra tài khoản đã tồn tại
    $check = $user->check_user($username);

    if ($check && mysqli_num_rows($check) > 0) {
        redirect(
            '../View/Info.php',
            'Tên đăng nhập đã tồn tại!'
        );
    }

    // Thêm tài khoản
    $result = $user->insert_user(
        $username,
        $password,
        $hoten,
        $email,
        $sdt,
        $diachi
    );

    if ($result) {

        $_SESSION['user'] = $username;
        $_SESSION['role'] = 'customer';

        redirect(
            '../View/welcome.php',
            'Đăng ký thành công!'
        );
    }

    redirect(
        '../View/Info.php',
        'Đăng ký thất bại!'
    );
}

?>
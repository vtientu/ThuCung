
<?php
include("Connect.php");

class data_shop
{
    // =========================
    // QUẢN LÝ SẢN PHẨM
    // =========================

    // Thêm sản phẩm
    public function insert_shop($tensp, $masp, $danhmuc, $sotien, $mota, $hinhanh)
    {
        global $conn;

        $tensp = mysqli_real_escape_string($conn, $tensp);
        $masp = mysqli_real_escape_string($conn, $masp);
        $danhmuc = mysqli_real_escape_string($conn, $danhmuc);
        $sotien = floatval($sotien);
        $mota = mysqli_real_escape_string($conn, $mota);
        $hinhanh = mysqli_real_escape_string($conn, $hinhanh);

        $sql = "INSERT INTO ngockhanhsport
                (Tensanpham, Masanpham, Danhmuc, Sotien, Mota, Hinhanh)
                VALUES ('$tensp', '$masp', '$danhmuc', '$sotien', '$mota', '$hinhanh')";

        return mysqli_query($conn, $sql);
    }

    // Hiển thị tất cả sản phẩm
    public function select_shop()
    {
        global $conn;
        return mysqli_query($conn, "SELECT * FROM ngockhanhsport");
    }

    // Đếm sản phẩm
    public function count_sanpham()
    {
        global $conn;
        return mysqli_query($conn, "SELECT COUNT(*) AS total FROM ngockhanhsport");
    }

    // Hiển thị danh mục
    public function select_danhmuc()
    {
        global $conn;

        $sql = "SELECT DISTINCT Danhmuc
                FROM ngockhanhsport
                WHERE Danhmuc IS NOT NULL AND Danhmuc != ''
                ORDER BY Danhmuc ASC";

        return mysqli_query($conn, $sql);
    }

    // Đếm danh mục
    public function count_danhmuc()
    {
        global $conn;

        $sql = "SELECT COUNT(DISTINCT Danhmuc) AS total
                FROM ngockhanhsport
                WHERE Danhmuc IS NOT NULL AND Danhmuc != ''";

        return mysqli_query($conn, $sql);
    }

    // Lấy sản phẩm theo danh mục
    public function select_sanpham_danhmuc($danhmuc)
    {
        global $conn;

        $danhmuc = mysqli_real_escape_string($conn, $danhmuc);

        $sql = "SELECT * FROM ngockhanhsport
                WHERE Danhmuc='$danhmuc'
                ORDER BY Tensanpham ASC";

        return mysqli_query($conn, $sql);
    }

    // Lấy sản phẩm theo ID
    public function select_shop_id($id)
    {
        global $conn;

        $id = intval($id);
        return mysqli_query($conn, "SELECT * FROM ngockhanhsport WHERE Id_shop=$id");
    }

    // Xóa sản phẩm
    public function delete_shop_id($id)
    {
        global $conn;

        $id = intval($id);
        return mysqli_query($conn, "DELETE FROM ngockhanhsport WHERE Id_shop=$id");
    }

    // Cập nhật sản phẩm
    public function update_shop_id($tensp, $masp, $danhmuc, $sotien, $mota, $hinhanh, $id)
    {
        global $conn;

        $tensp = mysqli_real_escape_string($conn, $tensp);
        $masp = mysqli_real_escape_string($conn, $masp);
        $danhmuc = mysqli_real_escape_string($conn, $danhmuc);
        $sotien = floatval($sotien);
        $mota = mysqli_real_escape_string($conn, $mota);
        $hinhanh = mysqli_real_escape_string($conn, $hinhanh);
        $id = intval($id);

        $sql = "UPDATE ngockhanhsport SET
                Tensanpham='$tensp',
                Masanpham='$masp',
                Danhmuc='$danhmuc',
                Sotien='$sotien',
                Mota='$mota',
                Hinhanh='$hinhanh'
                WHERE Id_shop=$id";

        return mysqli_query($conn, $sql);
    }

    // =========================
    // QUẢN LÝ ĐƠN HÀNG
    // =========================

    // Thêm đơn hàng
    public function insert_donhang($hoten, $sdt, $diachi, $tongtien, $phuongthucthanhtoan, $ghichu = '')
    {
        global $conn;

        $hoten = mysqli_real_escape_string($conn, $hoten);
        $sdt = mysqli_real_escape_string($conn, $sdt);
        $diachi = mysqli_real_escape_string($conn, $diachi);
        $tongtien = floatval($tongtien);
        $phuongthucthanhtoan = mysqli_real_escape_string($conn, $phuongthucthanhtoan);
        $ghichu = mysqli_real_escape_string($conn, $ghichu);
        $ngaydat = date('Y-m-d H:i:s');

        $sql = "INSERT INTO donhang
                (hoten, sdt, diachi, tongtien, phuongthucthanhtoan, trangthai, ngaydat, ghichu)
                VALUES
                ('$hoten', '$sdt', '$diachi', '$tongtien',
                 '$phuongthucthanhtoan', 'Chờ xử lý', '$ngaydat', '$ghichu')";

        return mysqli_query($conn, $sql);
    }

    // Hiển thị đơn hàng
    public function select_donhang()
    {
        global $conn;
        return mysqli_query($conn, "SELECT * FROM donhang ORDER BY Id_donhang DESC");
    }

    // Đếm đơn hàng
    public function count_donhang()
    {
        global $conn;
        return mysqli_query($conn, "SELECT COUNT(*) AS total FROM donhang");
    }

    // Đếm đơn hàng theo trạng thái
    public function count_donhang_trangthai($trangthai)
    {
        global $conn;

        $trangthai = mysqli_real_escape_string($conn, $trangthai);

        $sql = "SELECT COUNT(*) AS total
                FROM donhang
                WHERE trangthai='$trangthai'";

        return mysqli_query($conn, $sql);
    }

    // Cập nhật trạng thái đơn hàng
    public function update_trangthai_donhang($id, $trangthai)
    {
        global $conn;

        $id = intval($id);
        $trangthai = mysqli_real_escape_string($conn, $trangthai);

        $sql = "UPDATE donhang
                SET trangthai='$trangthai'
                WHERE Id_donhang=$id";

        return mysqli_query($conn, $sql);
    }

    // Lấy đơn hàng theo số điện thoại
    public function select_donhang_sdt($sdt)
    {
        global $conn;

        $sdt = mysqli_real_escape_string($conn, $sdt);

        $sql = "SELECT * FROM donhang
                WHERE sdt='$sdt'
                ORDER BY Id_donhang DESC";

        return mysqli_query($conn, $sql);
    }

    // Thống kê đơn hàng của khách hàng
    public function thongke_donhang_khach($sdt)
    {
        global $conn;

        $sdt = mysqli_real_escape_string($conn, $sdt);

        $sql = "SELECT COUNT(*) AS tongdon,
                       COALESCE(SUM(tongtien), 0) AS tongtien,
                       MAX(ngaydat) AS donhang_gannhat
                FROM donhang
                WHERE sdt='$sdt'";

        return mysqli_query($conn, $sql);
    }

    // Tổng doanh thu từ đơn đã giao
    public function count_tongdoanhthu()
    {
        global $conn;

        $sql = "SELECT COALESCE(SUM(tongtien), 0) AS total
                FROM donhang
                WHERE trangthai='Đã giao'";

        return mysqli_query($conn, $sql);
    }

    // Giữ lại hàm cũ để tương thích nếu trang khác đang sử dụng
    public function tong_doanhthu()
    {
        return $this->count_tongdoanhthu();
    }

    // Thống kê đơn hàng và doanh thu theo ngày
    public function thongke_donhang_theo_ngay()
    {
        global $conn;

        $sql = "SELECT DATE(ngaydat) AS ngay,
                       COUNT(*) AS sodon,
                       COALESCE(SUM(
                           CASE WHEN trangthai='Đã giao'
                                THEN tongtien ELSE 0 END
                       ), 0) AS doanhthu
                FROM donhang
                WHERE ngaydat >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                  AND ngaydat < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                GROUP BY DATE(ngaydat)
                ORDER BY ngay ASC";

        return mysqli_query($conn, $sql);
    }
}


// =========================
// QUẢN LÝ TÀI KHOẢN
// =========================

class data_user
{
    // Đăng nhập
    public function login($user, $pass)
    {
        global $conn;

        $user = mysqli_real_escape_string($conn, $user);
        $pass = mysqli_real_escape_string($conn, $pass);

        $sql = "SELECT * FROM `user`
                WHERE username='$user'
                  AND password='$pass'
                LIMIT 1";

        return mysqli_query($conn, $sql);
    }
    // Đăng ký thành viên
    public function insert_user($username, $password, $hoten, $email, $sdt, $diachi)
    {
        global $conn;
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);
        $hoten = mysqli_real_escape_string($conn, $hoten);
        $email = mysqli_real_escape_string($conn, $email);
        $sdt = mysqli_real_escape_string($conn, $sdt);
        $diachi = mysqli_real_escape_string($conn, $diachi);
        $sql = "INSERT INTO `user`
                (username, password, hoten, email, sdt, diachi, role)
                VALUES
                ('$username', '$password', '$hoten', '$email', '$sdt', '$diachi', 'customer')";
        return mysqli_query($conn, $sql);
    }
    // Kiểm tra tài khoản đã tồn tại
    public function check_user($username)
    {
        global $conn;
        $username = mysqli_real_escape_string($conn, $username);
        return mysqli_query(
            $conn,
            "SELECT * FROM `user` WHERE username='$username'"
        );
    }
    // Hiển thị danh sách khách hàng
    public function select_user()
    {
        global $conn;
        $sql = "SELECT * FROM `user`
                WHERE role='customer'
                ORDER BY username ASC";

        return mysqli_query($conn, $sql);
    }
    // Đếm khách hàng
    public function count_khachhang()
    {
        global $conn;
        $sql = "SELECT COUNT(*) AS total
                FROM `user`
                WHERE role='customer'";
        return mysqli_query($conn, $sql);
    }
    // Lấy thông tin một khách hàng
    public function select_user_id($username)
    {
        global $conn;
        $username = mysqli_real_escape_string($conn, $username);
        $sql = "SELECT * FROM `user`
                WHERE username='$username'
                  AND role='customer'
                LIMIT 1";

        return mysqli_query($conn, $sql);
    }
}
?>

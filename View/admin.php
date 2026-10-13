<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
        alert('Bạn không có quyền truy cập trang quản trị. Phần này chỉ dành cho Admin!');
        window.location='Info.php';
    </script>";
    exit();
}
// Nhúng Model để lấy dữ liệu sản phẩm thực tế từ Database
include('../Model/Model.php');
$get_data = new data_shop();
$get_data_user = new data_user();
$page = isset($_GET['page']) ? $_GET['page'] : 'tongquan';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị - MOCHI PET</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { display: flex; min-height: 100vh; background-color: #f4f6f9; color: #333; }
        /* 1. Thanh Sidebar bên trái */
        .sidebar { width: 250px; background-color: #1c4b68; color: #fff; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; text-align: center; font-size: 20px; font-weight: bold; background: #14374d; letter-spacing: 1px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { list-style: none; padding: 20px 0; flex: 1; }
        .sidebar-menu li a { display: block; padding: 14px 25px; color: #cbd5e1; text-decoration: none; font-size: 15px; font-weight: 500; transition: all 0.3s; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background-color: #33a8b8; color: #fff; border-left: 4px solid #fff; }
        
        /* Nút đăng xuất ở chân Sidebar */
        .sidebar-footer { padding: 15px 20px; border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-footer a { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 11px; background: rgba(231, 76, 60, 0.2); color: #ff7675; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; border: 1px solid rgba(231, 76, 60, 0.4); transition: all 0.2s; }
        .sidebar-footer a:hover { background: #e74c3c; color: #fff; }

        /* 2. Phần nội dung bên phải */
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .topbar { background-color: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .topbar .user-info { font-weight: bold; color: #555; }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .topbar a.btn-home { text-decoration: none; color: #33a8b8; border: 1px solid #33a8b8; padding: 6px 14px; border-radius: 4px; font-size: 13px; font-weight: bold; transition: 0.2s; }
        .topbar a.btn-home:hover { background-color: #33a8b8; color: #fff; }
        .topbar a.btn-logout { text-decoration: none; color: #e74c3c; border: 1px solid #e74c3c; padding: 6px 14px; border-radius: 4px; font-size: 13px; font-weight: bold; transition: 0.2s; }
        .topbar a.btn-logout:hover { background-color: #e74c3c; color: #fff; }

        .content-body { padding: 30px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 25px; }
        /* Tiêu đề & Nút Thêm Mới */
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; }
        .card-title { font-size: 20px; font-weight: bold; color: #1c4b68; }
        .btn-add { background-color: #27ae60; color: #fff; text-decoration: none; padding: 9px 18px; border-radius: 5px; font-size: 14px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; }
        .btn-add:hover { background-color: #219150; }
        /* Bảng hiển thị bản ghi */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        table th { background-color: #f8f9fa; color: #555; font-weight: bold; }
        table tr:hover { background-color: #f1f5f9; }
        .img-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        /* Nút thao tác */
        .btn-action { text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; display: inline-block; margin-right: 4px; }
        .btn-edit { background-color: #f39c12; }
        .btn-delete { background-color: #e74c3c; }
        .btn-view { text-decoration: none; padding: 6px 14px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; background-color: #3498db; }
        /* DASHBOARD */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 8px;
            background: #eaf8fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }
        .stat-number {
            font-size: 25px;
            font-weight: bold;
            color: #1c4b68;
        }
        .stat-title {
            margin-top: 5px;
            color: #777;
            font-size: 14px;
        }
        .dashboard-section {
            margin-top: 25px;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #eee;
        }
        .dashboard-section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1c4b68;
            margin-bottom: 15px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            background: #eef6ff;
            color: #2980b9;
            font-size: 12px;
            font-weight: bold;
        }
        @media (max-width: 1000px) {
            .dashboard-stats { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            .dashboard-stats { grid-template-columns: 1fr; }
        }
    </style>
<link rel="stylesheet" href="../assets/legacy.css">
</head>
<body>
    <!-- 1. Thanh Sidebar Điều Hướng -->
    <div class="sidebar">
        <div class="sidebar-header">MOCHI PET</div>
        <ul class="sidebar-menu">
            <li>
                <a href="admin.php?page=tongquan" class="<?php echo ($page == 'tongquan') ? 'active' : ''; ?>">
                    📊 Tổng Quan
                </a>
            </li>
            <li>
                <a href="admin.php?page=sanpham" class="<?php echo ($page == 'sanpham') ? 'active' : ''; ?>">
                    📦 Quản lý Sản Phẩm
                </a>
            </li>
            <li>
                <a href="admin.php?page=donhang" class="<?php echo ($page == 'donhang') ? 'active' : ''; ?>">
                    🛒 Quản lý Đơn Hàng
                </a>
            </li>
            <li>
                <a href="admin.php?page=khachhang" class="<?php echo ($page == 'khachhang') ? 'active' : ''; ?>">
                    👥 Quản lý Khách Hàng
                </a>
            </li>
            <li>
                <a href="admin.php?page=danhmuc" class="<?php echo ($page == 'danhmuc') ? 'active' : ''; ?>">
                    🏷️ Quản lý Danh Mục
                </a>
            </li>
        </ul>

        <!-- Nút Đăng Xuất ở chân Sidebar -->
        <div class="sidebar-footer">
            <a href="logout.php" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất tài khoản quản trị?');">
                🚪 Đăng Xuất
            </a>
        </div>
    </div>

    <!-- 2. Khung Nội Dung Chính -->
    <div class="main-content">
        <div class="topbar">
            <span class="user-info">Xin chào, Admin!</span>
            <div class="topbar-actions">
                <a href="Info.php" class="btn-home">🌐 Xem Trang Chủ</a>
                <a href="logout.php" class="btn-logout" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?');">🚪 Đăng Xuất</a>
            </div>
        </div>
        <div class="content-body">
            <div class="card">
<?php if ($page == 'tongquan'): ?>
    <?php
    $result_sp = $get_data->count_sanpham();
    $row_sp = mysqli_fetch_assoc($result_sp);
    $tong_sanpham = $row_sp['total'];

    $result_dh = $get_data->count_donhang();
    $row_dh = mysqli_fetch_assoc($result_dh);
    $tong_donhang = $row_dh['total'];

    $result_kh = $get_data_user->count_khachhang();
    $row_kh = mysqli_fetch_assoc($result_kh);
    $tong_khachhang = $row_kh['total'];

    $result_dm = $get_data->count_danhmuc();
    $row_dm = mysqli_fetch_assoc($result_dm);
    $tong_danhmuc = $row_dm['total'];

    $result_doanhthu = $get_data->count_tongdoanhthu();
    $row_doanhthu = mysqli_fetch_assoc($result_doanhthu);
    $tong_doanhthu = $row_doanhthu['total'];

    $result_cho_xu_ly = $get_data->count_donhang_trangthai('Chờ xử lý');
    $row_cho_xu_ly = mysqli_fetch_assoc($result_cho_xu_ly);
    $tong_cho_xu_ly = $row_cho_xu_ly['total'];

    $result_da_xac_nhan = $get_data->count_donhang_trangthai('Đã xác nhận');
    $row_da_xac_nhan = mysqli_fetch_assoc($result_da_xac_nhan);
    $tong_da_xac_nhan = $row_da_xac_nhan['total'];

    $result_cho_ship = $get_data->count_donhang_trangthai('Chờ ship');
    $row_cho_ship = mysqli_fetch_assoc($result_cho_ship);
    $tong_cho_ship = $row_cho_ship['total'];

    $result_dang_giao = $get_data->count_donhang_trangthai('Đang giao');
    $row_dang_giao = mysqli_fetch_assoc($result_dang_giao);
    $tong_dang_giao = $row_dang_giao['total'];

    $result_da_giao = $get_data->count_donhang_trangthai('Đã giao');
    $row_da_giao = mysqli_fetch_assoc($result_da_giao);
    $tong_da_giao = $row_da_giao['total'];

    $result_da_huy = $get_data->count_donhang_trangthai('Đã hủy');
    $row_da_huy = mysqli_fetch_assoc($result_da_huy);
    $tong_da_huy = $row_da_huy['total'];
    ?>
    <div class="card-header">
        <div>
            <div class="card-title">Tổng Quan</div>
            <p style="margin-top:8px; color:#777;">
                Tổng quan hoạt động của Mochi Pet.
            </p>
        </div>
    </div>
    <div class="dashboard-stats">
        <div class="stat-box">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <div class="stat-number"><?php echo $tong_sanpham; ?></div>
                <div class="stat-title">Tổng sản phẩm</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon">🛒</div>
            <div class="stat-info">
                <div class="stat-number"><?php echo $tong_donhang; ?></div>
                <div class="stat-title">Tổng đơn hàng</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <div class="stat-number"><?php echo $tong_khachhang; ?></div>
                <div class="stat-title">Khách hàng</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon">🏷️</div>
            <div class="stat-info">
                <div class="stat-number"><?php echo $tong_danhmuc; ?></div>
                <div class="stat-title">Danh mục</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <div class="stat-number" style="font-size:20px;">
                    <?php echo number_format($tong_doanhthu, 0, ',', '.'); ?> đ
                </div>
                <div class="stat-title">Tổng doanh thu</div>
            </div>
        </div>
    </div>

    <div class="dashboard-section">
        <div class="dashboard-section-title">📦 Thống kê đơn hàng</div>
        <div style="display:grid; grid-template-columns:repeat(6, 1fr); gap:12px;">
            <div class="stat-box">
                <div class="stat-info">
                    <div class="stat-number"><?php echo $tong_cho_xu_ly; ?></div>
                    <div class="stat-title">Chờ xử lý</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-info">
                    <div class="stat-number"><?php echo $tong_da_xac_nhan; ?></div>
                    <div class="stat-title">Đã xác nhận</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-info">
                    <div class="stat-number"><?php echo $tong_cho_ship; ?></div>
                    <div class="stat-title">Chờ ship</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-info">
                    <div class="stat-number"><?php echo $tong_dang_giao; ?></div>
                    <div class="stat-title">Đang giao</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-info">
                    <div class="stat-number"><?php echo $tong_da_giao; ?></div>
                    <div class="stat-title">Đã giao</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-info">
                    <div class="stat-number"><?php echo $tong_da_huy; ?></div>
                    <div class="stat-title">Đã hủy</div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-section">
        <div class="dashboard-section-title">🛒 Đơn hàng gần đây</div>
        <?php $donhang_moi = $get_data->select_donhang(); ?>
        <table>
            <thead>
                <tr>
                    <th>Mã đơn hàng</th>
                    <th>Thông tin khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($donhang_moi && mysqli_num_rows($donhang_moi) > 0):
                $i = 0;
                while ($dh = mysqli_fetch_assoc($donhang_moi)):
                    if ($i >= 5) break;
                    $i++;
            ?>
                <tr>
                    <td><strong>#<?php echo $dh['Id_donhang']; ?></strong></td>
                    <td><?php echo isset($dh['hoten']) ? htmlspecialchars($dh['hoten']) : 'Khách hàng'; ?></td>
                    <td><?php echo isset($dh['ngaydat']) ? htmlspecialchars($dh['ngaydat']) : '---'; ?></td>
                    <td>
                        <span class="status-badge">
                            <?php echo isset($dh['trangthai']) ? htmlspecialchars($dh['trangthai']) : 'Chưa cập nhật'; ?>
                        </span>
                    </td>
                </tr>
            <?php
                endwhile;
            else:
            ?>
                <tr><td colspan="4" style="text-align:center;">Chưa có đơn hàng.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($page == 'sanpham'): ?>
    <div class="card-header">
        <h2 class="card-title">Danh Sách Sản Phẩm</h2>
        <a href="ngockhanhsport.php" class="btn-add">➕ Thêm Sản Phẩm Mới</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Hình Ảnh</th>
                <th>Tên Sản Phẩm</th>
                <th>Mã SP</th>
                <th>Danh Mục</th>
                <th>Giá Bán</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $list_sp = $get_data->select_shop();
            if ($list_sp) {
                $i = 1;
                foreach ($list_sp as $sp) {
            ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><img src="../Upload/<?php echo $sp['Hinhanh']; ?>" class="img-thumb" alt=""></td>
                    <td><b><?php echo $sp['Tensanpham']; ?></b></td>
                    <td><?php echo $sp['Masanpham']; ?></td>
                    <td><?php echo $sp['Danhmuc']; ?></td>
                    <td><?php echo number_format($sp['Sotien']); ?> VNĐ</td>
                    <td>
                        <a href="update.php?sua=<?php echo $sp['Id_shop']; ?>" class="btn-action btn-edit">Sửa</a>
                        <a href="../Controller/Controller.php?xoa=<?php echo $sp['Id_shop']; ?>" class="btn-action btn-delete" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                    </td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='7' align='center'>Chưa có dữ liệu sản phẩm.</td></tr>";
            }
            ?>
        </tbody>
    </table>

<?php elseif ($page == 'donhang'): ?>
    <div class="card-header">
        <div>
            <h2 class="card-title">Quản Lý Đơn Hàng</h2>
            <p style="margin-top:6px;color:#64748b;font-size:14px;">
                Danh sách đơn hàng từ website Mochi Pet
            </p>
        </div>
    </div>
    <div style="background:#f5f9ff; border:1px solid #dbeafe; border-radius:12px; padding:18px; margin-bottom:20px;">
        <strong style="color:#123b78;font-size:16px;">🛒 Đơn hàng từ website</strong>
        <p style="margin-top:6px; color:#64748b; font-size:14px;">
            Các đơn hàng khách tạo từ giỏ hàng và thanh toán trên trang chủ sẽ được hiển thị tại đây.
        </p>
    </div>
    <?php $list_donhang = $get_data->select_donhang(); ?>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Thông tin đơn hàng</th>
                <th>Khách hàng</th>
                <th>Số điện thoại</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($list_donhang)) { ?>
                <?php $stt = 1; ?>
                <?php foreach ($list_donhang as $donhang) { ?>
                    <tr>
                        <td><?= $stt++ ?></td>
                        <td>
                            <strong>Đơn hàng #<?= htmlspecialchars($donhang['Id_donhang']) ?></strong><br>
                            <small>Ngày đặt: <?= htmlspecialchars($donhang['ngaydat']) ?></small><br>
                            <small>Địa chỉ: <?= htmlspecialchars($donhang['diachi']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($donhang['hoten']) ?></td>
                        <td><a href="tel:<?= htmlspecialchars($donhang['sdt']) ?>"><?= htmlspecialchars($donhang['sdt']) ?></a></td>
                        <td><strong><?= number_format($donhang['tongtien'], 0, ',', '.') ?> đ</strong></td>
                        <td>
                            <form action="../Controller/Controller.php" method="POST" style="display:flex; align-items:center; gap:8px;">
                                <input type="hidden" name="id_donhang" value="<?= $donhang['Id_donhang'] ?>">
                                <select name="trangthai" style="padding:7px 10px; border:1px solid #d1d5db; border-radius:5px; font-size:13px; min-width:145px;">
                                    <option value="Chờ xử lý" <?= ($donhang['trangthai'] == 'Chờ xử lý') ? 'selected' : '' ?>>Chờ xử lý</option>
                                    <option value="Đã xác nhận" <?= ($donhang['trangthai'] == 'Đã xác nhận') ? 'selected' : '' ?>>Đã xác nhận</option>
                                    <option value="Chờ ship" <?= ($donhang['trangthai'] == 'Chờ ship') ? 'selected' : '' ?>>Chờ ship</option>
                                    <option value="Đang giao" <?= ($donhang['trangthai'] == 'Đang giao') ? 'selected' : '' ?>>Đang giao</option>
                                    <option value="Đã giao" <?= ($donhang['trangthai'] == 'Đã giao') ? 'selected' : '' ?>>Đã giao</option>
                                    <option value="Đã hủy" <?= ($donhang['trangthai'] == 'Đã hủy') ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                                <button type="submit" name="capnhat_trangthai" style="border:none; background:#1c4b68; color:white; padding:7px 10px; border-radius:5px; cursor:pointer; font-size:12px; font-weight:bold;">Cập nhật</button>
                            </form>
                        </td>
                        <td>
                            <a href="admin.php?page=chitietdonhang&id=<?= $donhang['Id_donhang'] ?>" class="btn-view">Xem</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="7" style="text-align:center; padding:30px;">Chưa có đơn hàng nào.</td></tr>
            <?php } ?>
        </tbody>
    </table>

<?php elseif ($page == 'chitietdonhang'): ?>
    <?php
    $id_donhang = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $list_donhang = $get_data->select_donhang();
    $donhang_chitiet = null;
    if (!empty($list_donhang)) {
        foreach ($list_donhang as $dh) {
            if (isset($dh['Id_donhang']) && intval($dh['Id_donhang']) == $id_donhang) {
                $donhang_chitiet = $dh;
                break;
            }
        }
    }
    ?>
    <div class="card-header">
        <div>
            <h2 class="card-title">Chi Tiết Đơn Hàng</h2>
            <p style="margin-top:6px;color:#64748b;font-size:14px;">
                Thông tin đơn hàng từ website Mochi Pet
            </p>
        </div>
        <a href="admin.php?page=donhang" style="text-decoration:none; background:#64748b; color:#fff; padding:9px 16px; border-radius:5px; font-weight:bold;">← Quay lại</a>
    </div>
    <?php if ($donhang_chitiet): ?>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:20px; margin-top:20px;">
            <h3 style="color:#1c4b68;margin-bottom:20px;">Đơn hàng #<?= htmlspecialchars($donhang_chitiet['Id_donhang']) ?></h3>
            <table style="margin-top:0;">
                <tr><th style="width:220px;">Mã đơn hàng</th><td>#<?= htmlspecialchars($donhang_chitiet['Id_donhang']) ?></td></tr>
                <tr><th>Khách hàng</th><td><?= htmlspecialchars($donhang_chitiet['hoten']) ?></td></tr>
                <tr><th>Số điện thoại</th><td><a href="tel:<?= htmlspecialchars($donhang_chitiet['sdt']) ?>"><?= htmlspecialchars($donhang_chitiet['sdt']) ?></a></td></tr>
                <tr><th>Địa chỉ</th><td><?= htmlspecialchars($donhang_chitiet['diachi']) ?></td></tr>
                <tr><th>Tổng tiền</th><td><strong style="color:#123b78;"><?= number_format($donhang_chitiet['tongtien'], 0, ',', '.') ?> đ</strong></td></tr>
                <tr><th>Phương thức thanh toán</th><td><?= htmlspecialchars($donhang_chitiet['phuongthucthanhtoan']) ?></td></tr>
                <tr><th>Trạng thái</th><td><?= htmlspecialchars($donhang_chitiet['trangthai']) ?></td></tr>
                <tr><th>Ngày đặt</th><td><?= htmlspecialchars($donhang_chitiet['ngaydat']) ?></td></tr>
                <tr><th>Ghi chú</th><td><?= !empty($donhang_chitiet['ghichu']) ? htmlspecialchars($donhang_chitiet['ghichu']) : 'Không có ghi chú' ?></td></tr>
            </table>
        </div>
    <?php else: ?>
        <div style="margin-top:20px; padding:30px; text-align:center; background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; color:#9a3412;">
            Không tìm thấy đơn hàng.
        </div>
    <?php endif; ?>

<?php elseif ($page == 'khachhang'): ?>
    <?php
    $list_user = $get_data_user->select_user();
    $result_count_user = $get_data_user->count_khachhang();
    $row_count_user = mysqli_fetch_assoc($result_count_user);
    $tong_khachhang = $row_count_user['total'];
    ?>
    <div class="card-header">
        <div>
            <h2 class="card-title">Quản Lý Khách Hàng</h2>
            <p style="margin-top:8px; color:#777;">Danh sách khách hàng đã đăng ký tài khoản.</p>
        </div>
        <div style="background:#eaf8fa; color:#1c4b68; padding:10px 16px; border-radius:6px; font-weight:bold;">
            👥 <?php echo $tong_khachhang; ?> khách hàng
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tài khoản</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($list_user && mysqli_num_rows($list_user) > 0): ?>
                <?php $i = 1; while ($user = mysqli_fetch_assoc($list_user)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['hoten']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['sdt']); ?></td>
                        <td><?php echo htmlspecialchars($user['diachi']); ?></td>
                        <td>
                            <a href="admin.php?page=chitietkhachhang&username=<?php echo urlencode($user['username']); ?>" class="btn-action btn-edit">👁 Xem</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center; padding:25px;">Chưa có khách hàng đăng ký.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php elseif ($page == 'chitietkhachhang'): ?>
    <?php
    $username = isset($_GET['username']) ? $_GET['username'] : '';
    $result_user = $get_data_user->select_user_id($username);
    $khachhang = false;
    if ($result_user && mysqli_num_rows($result_user) > 0) {
        $khachhang = mysqli_fetch_assoc($result_user);
    }
    $list_donhang_khach = false;
    if ($khachhang) {
        $list_donhang_khach = $get_data->select_donhang_sdt($khachhang['sdt']);
    }
    $thongke_khach = false;
    if ($khachhang) {
        $result_thongke = $get_data->thongke_donhang_khach($khachhang['sdt']);
        if ($result_thongke) {
            $thongke_khach = mysqli_fetch_assoc($result_thongke);
        }
    }
    ?>
    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:18px; margin-top:25px; margin-bottom:25px;">
        <div style="background:#eff6ff; border:1px solid #dbeafe; border-radius:10px; padding:20px;">
            <div style="font-size:14px; color:#64748b; margin-bottom:8px;">🛒 Tổng đơn hàng</div>
            <div style="font-size:28px; font-weight:bold; color:#1c4b68;"><?php echo $thongke_khach ? $thongke_khach['tongdon'] : 0; ?></div>
            <div style="font-size:13px; color:#64748b; margin-top:5px;">đơn hàng</div>
        </div>
        <div style="background:#f0fdf4; border:1px solid #dcfce7; border-radius:10px; padding:20px;">
            <div style="font-size:14px; color:#64748b; margin-bottom:8px;">💰 Tổng tiền đã mua</div>
            <div style="font-size:28px; font-weight:bold; color:#15803d;"><?php echo $thongke_khach ? number_format($thongke_khach['tongtien'], 0, ',', '.') : '0'; ?> đ</div>
            <div style="font-size:13px; color:#64748b; margin-top:5px;">tổng giá trị đơn hàng</div>
        </div>
        <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; padding:20px;">
            <div style="font-size:14px; color:#64748b; margin-bottom:8px;">📅 Đơn hàng gần nhất</div>
            <div style="font-size:18px; font-weight:bold; color:#c2410c;">
                <?php echo ($thongke_khach && !empty($thongke_khach['donhang_gannhat'])) ? htmlspecialchars($thongke_khach['donhang_gannhat']) : 'Chưa có'; ?>
            </div>
            <div style="font-size:13px; color:#64748b; margin-top:8px;">thời gian đặt hàng gần nhất</div>
        </div>
    </div>

    <div class="card-header">
        <div>
            <h2 class="card-title">Chi Tiết Khách Hàng</h2>
            <p style="margin-top:8px; color:#777;">Thông tin chi tiết tài khoản khách hàng.</p>
        </div>
        <a href="admin.php?page=khachhang" style="text-decoration:none; background:#1c4b68; color:#fff; padding:9px 16px; border-radius:5px; font-weight:bold;">← Quay lại</a>
    </div>
    <?php if ($khachhang): ?>
        <div style="margin-top:20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:25px;">
            <h3 style="color:#1c4b68; margin-bottom:20px;">Thông tin tài khoản</h3>
            <table style="margin-top:0;">
                <tr><th style="width:220px;">Tài khoản</th><td><strong><?php echo htmlspecialchars($khachhang['username']); ?></strong></td></tr>
                <tr><th>Họ tên</th><td><?php echo htmlspecialchars($khachhang['hoten']); ?></td></tr>
                <tr><th>Email</th><td><?php echo htmlspecialchars($khachhang['email']); ?></td></tr>
                <tr><th>Số điện thoại</th><td><a href="tel:<?php echo htmlspecialchars($khachhang['sdt']); ?>"><?php echo htmlspecialchars($khachhang['sdt']); ?></a></td></tr>
                <tr><th>Địa chỉ</th><td><?php echo htmlspecialchars($khachhang['diachi']); ?></td></tr>
                <tr><th>Quyền tài khoản</th><td><span style="background:#eaf8fa; color:#1c4b68; padding:5px 10px; border-radius:15px; font-weight:bold;"><?php echo htmlspecialchars($khachhang['role']); ?></span></td></tr>
            </table>
        </div>

        <div style="margin-top:25px; background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:20px;">
            <h3 style="color:#1c4b68; margin-bottom:18px;">🛒 Lịch Sử Đơn Hàng</h3>
            <?php if ($list_donhang_khach && mysqli_num_rows($list_donhang_khach) > 0): ?>
                <table style="margin-top:0;">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($donhang_khach = mysqli_fetch_assoc($list_donhang_khach)): ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($donhang_khach['Id_donhang']); ?></strong></td>
                                <td><?php echo htmlspecialchars($donhang_khach['ngaydat']); ?></td>
                                <td><strong><?php echo number_format($donhang_khach['tongtien'], 0, ',', '.'); ?> đ</strong></td>
                                <td><span class="status-badge"><?php echo htmlspecialchars($donhang_khach['trangthai']); ?></span></td>
                                <td>
                                    <a href="admin.php?page=chitietdonhang&id=<?php echo $donhang_khach['Id_donhang']; ?>" class="btn-view">Xem</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding:25px; text-align:center; background:#f8fafc; border-radius:8px; color:#64748b;">Khách hàng chưa có đơn hàng nào.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php elseif ($page == 'danhmuc'): ?>
    <div class="card-header">
        <div>
            <div class="card-title">Quản Lý Danh Mục</div>
            <p style="margin-top: 8px; color: #777;">Quản lý danh mục và các sản phẩm thuộc từng danh mục.</p>
        </div>
    </div>
    <?php $list_danhmuc = $get_data->select_danhmuc(); ?>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên danh mục</th>
                <th>Số sản phẩm</th>
                <th>Sản phẩm thuộc danh mục</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($list_danhmuc && mysqli_num_rows($list_danhmuc) > 0):
            $i = 1;
            while ($dm = mysqli_fetch_assoc($list_danhmuc)):
                $ten_danhmuc = $dm['Danhmuc'];
                $list_sp_dm = $get_data->select_sanpham_danhmuc($ten_danhmuc);
                $so_sanpham = 0;
                $ten_sanpham = array();
                if ($list_sp_dm) {
                    while ($sp_dm = mysqli_fetch_assoc($list_sp_dm)) {
                        $so_sanpham++;
                        $ten_sanpham[] = $sp_dm['Tensanpham'];
                    }
                }
        ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><strong><?php echo htmlspecialchars($ten_danhmuc); ?></strong></td>
                <td>
                    <span style="background:#e8f5e9; color:#27ae60; padding:5px 10px; border-radius:15px; font-weight:bold;">
                        <?php echo $so_sanpham; ?> sản phẩm
                    </span>
                </td>
                <td>
                    <?php
                    if ($so_sanpham > 0) {
                        echo htmlspecialchars(implode(', ', $ten_sanpham));
                    } else {
                        echo '<span style="color:#999;">Chưa có sản phẩm</span>';
                    }
                    ?>
                </td>
                <td>
                    <a href="admin.php?page=chitietdanhmuc&danhmuc=<?php echo urlencode($ten_danhmuc); ?>" class="btn-action btn-edit">👁 Xem chi tiết</a>
                </td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr><td colspan="5" style="text-align:center;">Chưa có danh mục sản phẩm.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

<?php elseif ($page == 'chitietdanhmuc'): ?>
    <?php
    $danhmuc_chitiet = isset($_GET['danhmuc']) ? $_GET['danhmuc'] : '';
    $list_sp_chitiet = $get_data->select_sanpham_danhmuc($danhmuc_chitiet);
    ?>
    <div class="card-header">
        <div>
            <div class="card-title">Chi Tiết Danh Mục</div>
            <p style="margin-top:8px;color:#777;">
                Danh mục: <strong><?php echo htmlspecialchars($danhmuc_chitiet); ?></strong>
            </p>
        </div>
        <a href="admin.php?page=danhmuc" class="btn-add" style="background:#1c4b68;">← Quay lại</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Mã sản phẩm</th>
                <th>Giá</th>
                <th>Mô tả</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($list_sp_chitiet && mysqli_num_rows($list_sp_chitiet) > 0):
            $i = 1;
            while ($sp = mysqli_fetch_assoc($list_sp_chitiet)):
        ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><img src="../Upload/<?php echo htmlspecialchars($sp['Hinhanh']); ?>" class="img-thumb" alt="Sản phẩm"></td>
                <td><strong><?php echo htmlspecialchars($sp['Tensanpham']); ?></strong></td>
                <td><?php echo htmlspecialchars($sp['Masanpham']); ?></td>
                <td><?php echo number_format($sp['Sotien'], 0, ',', '.'); ?> đ</td>
                <td><?php echo htmlspecialchars($sp['Mota']); ?></td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr><td colspan="6" style="text-align:center;">Danh mục này chưa có sản phẩm.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="card-header">
        <h2 class="card-title">Trang quản trị</h2>
    </div>
    <div style="padding:20px;">Không tìm thấy trang quản trị yêu cầu.</div>
<?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
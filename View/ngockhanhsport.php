<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới - MOCHI PET</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        body { display: flex; min-height: 100vh; background-color: #f4f6f9; color: #333; }

        /* 1. SIDEBAR LEFT (Khung bên trái) */
        .sidebar { width: 240px; background-color: #193548; color: #fff; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-header { padding: 22px 20px; font-size: 18px; font-weight: bold; letter-spacing: 0.5px; }
        .sidebar-menu { list-style: none; margin-top: 10px; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 10px; padding: 14px 20px; color: #b0bec5; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background-color: #2ca5b8; color: #fff; }

        /* 2. MAIN CONTENT (Khu vực nội dung chính bên phải) */
        .main-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        
        /* Topbar */
        .topbar { background-color: #fff; height: 60px; padding: 0 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eef2f5; }
        .topbar .user-info { font-weight: 600; color: #444; font-size: 14px; }
        .topbar a.btn-home { text-decoration: none; color: #3babc0; border: 1px solid #3babc0; padding: 6px 14px; border-radius: 4px; font-size: 13px; font-weight: bold; transition: 0.2s; }
        .topbar a.btn-home:hover { background-color: #3babc0; color: #fff; }

        /* Content Body */
        .content-body { padding: 30px; }
        
        /* Card Container (Khung trắng chứa Form) */
        .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); padding: 25px 30px; }
        
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .card-title { font-size: 18px; font-weight: bold; color: #222; }
        
        /* Nút quay lại danh sách */
        .btn-back { background-color: #28a745; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 5px; font-size: 13px; font-weight: bold; transition: background 0.2s; display: inline-flex; align-items: center; gap: 5px; }
        .btn-back:hover { background-color: #218838; }

        /* Form Layout */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group.full-width { grid-column: span 2; }
        
        .form-group label { font-weight: 600; font-size: 13px; color: #444; margin-bottom: 8px; }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; outline: none; transition: border-color 0.2s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #2ca5b8; }
        .form-group textarea { height: 110px; resize: vertical; }
        .form-group input[type="file"] { padding: 8px 0; font-size: 13px; }

        /* Form Actions */
        .form-actions { grid-column: span 2; display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px; }
        .btn-submit { background-color: #2ca5b8; color: #fff; border: none; padding: 10px 24px; border-radius: 5px; font-size: 14px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-submit:hover { background-color: #238797; }
        .btn-reset { background-color: #6c757d; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; font-size: 14px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-reset:hover { background-color: #5a6268; }
        .image-upload-area {
    display: flex;
    align-items: center;
    gap: 25px;
    margin-top: 10px;
}

.upload-box {
    display: flex;
    align-items: center;
}

.preview-box {
    width: 180px;
    height: 180px;
    border: 2px dashed #ccc;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #f8f9fa;
    position: relative;
}

.preview-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: none;
}

#previewText {
    color: #999;
    font-size: 14px;
}
    </style>
<link rel="stylesheet" href="../assets/legacy.css">
</head>

<body>

    <!-- SIDEBAR LEFT -->
    <div class="sidebar">
        <div class="sidebar-header">MOCHI PET</div>
        <ul class="sidebar-menu">
            <li><a href="admin.php?page=sanpham" class="active">📦 Quản lý Sản Phẩm</a></li>
            <li><a href="admin.php?page=khachhang">👥 Quản lý Khách Hàng</a></li>
            <li><a href="admin.php?page=danhmuc">🏷️ Quản lý Chủng Loại</a></li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- TOPBAR -->
        <div class="topbar">
            <span class="user-info">Xin chào, Admin!</span>
            <a href="index.php" class="btn-home">🌐 Xem Trang Chủ</a>
        </div>

        <!-- CONTENT BODY -->
        <div class="content-body">
            <div class="card">
                
                <!-- CARD HEADER: Tiêu đề & Nút quay lại -->
                <div class="card-header">
                    <h2 class="card-title">Thêm Sản Phẩm Mới</h2>
                    <a href="admin.php?page=sanpham" class="btn-back">📋 Danh Sách Sản Phẩm</a>
                </div>

                <!-- CARD BODY: FORM THÊM SẢN PHẨM -->
                <form action="../Controller/Controller.php" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label>Tên Sản Phẩm:</label>
                            <input type="text" name="txttensp" placeholder="Nhập tên sản phẩm (VD: Nike Air Zoom Pegasus 41)..." required>
                        </div>

                        <div class="form-group">
                            <label>Mã Sản Phẩm (SKU):</label>
                            <input type="text" name="txtmasp" placeholder="Nhập mã sản phẩm (VD: MK001)..." required>
                        </div>

                        <div class="form-group">
                            <label>Danh Mục Sản Phẩm:</label>
                            <select name="txtdanhmuc" required>
                                <option value="">-- Chọn danh mục --</option>
                                <option value="Giày chạy bộ">Giày chạy bộ</option>
                                <option value="Giày bóng đá">Giày bóng đá</option>
                                <option value="Giày cầu lông">Giày cầu lông</option>
                                <option value="Quần áo thể thao">Quần áo thể thao</option>
                                <option value="Balo thể thao">Balo thể thao</option>
                                <option value="Đồ tập gym">Đồ tập gym</option>
                                <option value="Phụ kiện thể thao">Phụ kiện thể thao</option>
                                <option value="Dụng cụ thể thao">Dụng cụ thể thao</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Giá Bán (VNĐ):</label>
                            <input type="number" name="txtsotien" placeholder="Nhập giá bán (VD: 320000)..." required>
                        </div>

                       <div class="form-group">
    <label>Hình Ảnh Sản Phẩm:</label>

    <div class="image-upload-area">

        <!-- Chọn ảnh -->
        <div class="upload-box">
 <input 
    type="file" 
    name="txtfile" 
    id="HinhAnh"
    accept="image/*"
    onchange="previewImage(event)"
>
        </div>

        <!-- Khung xem trước -->
        <div class="preview-box">
            <img 
                id="imagePreview" 
                src="" 
                alt="Ảnh xem trước"
            >

            <span id="previewText">
                Chưa chọn ảnh
            </span>
        </div>

    </div>
</div>

                        <div class="form-group full-width">
                            <label>Mô Tả Sản Phẩm:</label>
                            <textarea name="txtmota" placeholder="Nhập thông tin chi tiết về sản phẩm..."></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="reset" class="btn-reset">Nhập lại</button>
                            <button type="submit" name="txtsub" class="btn-submit">➕ Thêm Sản Phẩm</button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
<script>
function previewImage(event) {

    const file = event.target.files[0];

    const imagePreview = document.getElementById('imagePreview');
    const previewText = document.getElementById('previewText');

    if (file) {

        const reader = new FileReader();

        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
            previewText.style.display = 'none';
        };

        reader.readAsDataURL(file);

    } else {

        imagePreview.src = '';
        imagePreview.style.display = 'none';
        previewText.style.display = 'block';

    }
}
</script>
</body>
</html>
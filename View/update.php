<?php
include("../Model/Model.php");

$data = new data_shop();
$id = isset($_GET['SUA']) ? intval($_GET['SUA']) : 0;

$select_sp = $data->select_shop_id($id);

if ($id > 0 && $select_sp && mysqli_num_rows($select_sp) > 0) {
    $se_sp = mysqli_fetch_assoc($select_sp);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật sản phẩm - MOCHI PET</title>

    <style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background:
        radial-gradient(circle at top left, rgba(37, 99, 235, 0.08), transparent 35%),
        #f1f5f9;
    color: #172033;
}

/* =========================
   PAGE CONTAINER
========================= */

.page-container {
    width: min(1180px, calc(100% - 40px));
    margin: 45px auto 60px;
}

/* =========================
   HEADER
========================= */

.page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 25px;
    margin-bottom: 28px;
}

.page-title {
    position: relative;
}

.page-title::before {
    content: "";
    position: absolute;
    left: 0;
    top: -10px;
    width: 55px;
    height: 5px;
    border-radius: 10px;
    background: linear-gradient(90deg, #2563eb, #38bdf8);
}

.page-title h1 {
    margin: 0;
    padding-top: 10px;
    font-size: 34px;
    font-weight: 750;
    letter-spacing: -0.8px;
    color: #172033;
}

.page-title p {
    margin: 9px 0 0;
    font-size: 15px;
    color: #64748b;
}

/* Nút danh sách */

.btn-list {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;

    padding: 13px 20px;

    background: #ffffff;
    color: #2563eb;

    border: 1px solid #dbe4f0;
    border-radius: 12px;

    text-decoration: none;
    font-size: 14px;
    font-weight: 700;

    box-shadow: 0 5px 15px rgba(15, 23, 42, 0.06);

    transition: all 0.2s ease;
}

.btn-list:hover {
    color: #ffffff;
    background: #2563eb;
    border-color: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.22);
}

/* =========================
   FORM CARD
========================= */

.update-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 22px;

    padding: 34px;

    box-shadow:
        0 15px 45px rgba(15, 23, 42, 0.08),
        0 2px 8px rgba(15, 23, 42, 0.03);
}

/* =========================
   SECTION TITLE
========================= */

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;

    padding-bottom: 18px;
    margin-bottom: 25px;

    border-bottom: 1px solid #e8edf3;

    font-size: 20px;
    font-weight: 750;
    color: #172033;
}

.section-title::before {
    content: "";
    width: 5px;
    height: 25px;

    border-radius: 10px;

    background: linear-gradient(
        180deg,
        #2563eb,
        #38bdf8
    );
}

/* =========================
   FORM GRID
========================= */

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px 24px;
}

/* =========================
   FORM GROUP
========================= */

.form-group {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.form-group.full {
    grid-column: 1 / -1;
}

.form-group label {
    margin-bottom: 9px;

    font-size: 13px;
    font-weight: 750;

    color: #334155;
}

/* =========================
   INPUT
========================= */

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;

    border: 1px solid #dbe3ec;
    border-radius: 12px;

    padding: 13px 15px;

    background: #f8fafc;

    color: #172033;

    font-size: 14px;

    outline: none;

    transition:
        border-color 0.2s ease,
        background 0.2s ease,
        box-shadow 0.2s ease,
        transform 0.2s ease;
}

.form-group input,
.form-group select {
    height: 48px;
}

.form-group textarea {
    min-height: 135px;
    resize: vertical;
    line-height: 1.6;
}

/* Hover */

.form-group input:hover,
.form-group select:hover,
.form-group textarea:hover {
    border-color: #b9c7d8;
    background: #ffffff;
}

/* Focus */

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #3b82f6;

    background: #ffffff;

    box-shadow:
        0 0 0 4px rgba(59, 130, 246, 0.10),
        0 5px 15px rgba(37, 99, 235, 0.05);
}

/* =========================
   IMAGE SECTION
========================= */

.image-section {
    margin-top: 34px;
    padding-top: 30px;

    border-top: 1px solid #e5eaf0;
}

.image-section-title {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-bottom: 20px;

    font-size: 20px;
    font-weight: 750;

    color: #172033;
}

.image-section-title::before {
    content: "▣";

    display: flex;
    align-items: center;
    justify-content: center;

    width: 34px;
    height: 34px;

    border-radius: 10px;

    background: #eff6ff;
    color: #2563eb;

    font-size: 17px;
}

/* =========================
   IMAGE GRID
========================= */

.image-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

/* =========================
   IMAGE CARD
========================= */

.image-card {
    position: relative;

    padding: 20px;

    background: #f8fafc;

    border: 1px solid #e2e8f0;
    border-radius: 17px;

    transition: all 0.2s ease;
}

.image-card:hover {
    background: #ffffff;

    border-color: #cbd5e1;

    box-shadow:
        0 10px 25px rgba(15, 23, 42, 0.07);

    transform: translateY(-2px);
}

.image-card-title {
    display: flex;
    align-items: center;
    gap: 8px;

    margin-bottom: 15px;

    font-size: 14px;
    font-weight: 750;

    color: #334155;
}

/* Badge */

.image-card:first-child .image-card-title::after {
    content: "ĐANG SỬ DỤNG";

    margin-left: auto;

    padding: 5px 8px;

    border-radius: 6px;

    background: #dcfce7;
    color: #15803d;

    font-size: 9px;
    font-weight: 800;
}

.image-card:nth-child(2) .image-card-title::after {
    content: "THAY ĐỔI";

    margin-left: auto;

    padding: 5px 8px;

    border-radius: 6px;

    background: #eff6ff;
    color: #2563eb;

    font-size: 9px;
    font-weight: 800;
}

/* =========================
   IMAGE PREVIEW
========================= */

.image-preview {
    position: relative;

    width: 100%;
    height: 320px;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    background:
        linear-gradient(
            135deg,
            #ffffff,
            #f8fafc
        );

    border: 1px dashed #cbd5e1;
    border-radius: 13px;
}

.image-preview img {
    max-width: 94%;
    max-height: 94%;

    object-fit: contain;

    transition: transform 0.3s ease;
}

.image-card:hover .image-preview img {
    transform: scale(1.025);
}

/* =========================
   FILE INPUT
========================= */

.image-card input[type="file"] {
    width: 100%;

    margin-top: 15px;

    padding: 11px;

    border: 1px solid #dbe3ec;
    border-radius: 10px;

    background: #ffffff;

    color: #475569;

    font-size: 13px;

    cursor: pointer;
}

.image-card input[type="file"]:hover {
    border-color: #93c5fd;
    background: #f8fbff;
}

/* =========================
   BUTTON AREA
========================= */

.button-area {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;

    margin-top: 30px;
    padding-top: 25px;

    border-top: 1px solid #e5eaf0;
}

/* =========================
   BUTTON
========================= */

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 46px;

    padding: 0 22px;

    border-radius: 11px;

    font-size: 14px;
    font-weight: 750;

    text-decoration: none;

    cursor: pointer;

    transition: all 0.2s ease;
}

/* Quay lại */

.btn-back {
    background: #f1f5f9;
    color: #475569;

    border: 1px solid #e2e8f0;
}

.btn-back:hover {
    background: #e2e8f0;
    color: #1e293b;
    transform: translateY(-2px);
}

/* Cập nhật */

.btn-update {
    border: none;

    color: #ffffff;

    background:
        linear-gradient(
            135deg,
            #2563eb,
            #1d4ed8
        );

    box-shadow:
        0 8px 18px rgba(37, 99, 235, 0.25);
}

.btn-update:hover {
    transform: translateY(-2px);

    box-shadow:
        0 12px 25px rgba(37, 99, 235, 0.35);
}

.btn-update:active {
    transform: translateY(0);
}

/* =========================
   RESPONSIVE
========================= */

@media (max-width: 800px) {

    .page-container {
        width: calc(100% - 25px);
        margin: 25px auto 40px;
    }

    .page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .page-title h1 {
        font-size: 28px;
    }

    .update-card {
        padding: 22px;
        border-radius: 17px;
    }

    .form-grid,
    .image-grid {
        grid-template-columns: 1fr;
    }

    .form-group.full {
        grid-column: auto;
    }

    .image-section {
        margin-top: 25px;
    }

    .image-preview {
        height: 260px;
    }

    .button-area {
        flex-direction: column-reverse;
    }

    .button-area .btn {
        width: 100%;
    }
}
</style>
<link rel="stylesheet" href="../assets/legacy.css">
</head>
<body>
<div class="page-container">

    <div class="page-header">

        <div class="page-title">
            <h1>✎ Cập nhật sản phẩm</h1>
            <p>Chỉnh sửa thông tin và hình ảnh sản phẩm</p>
        </div>

        <a href="select.php" class="btn-list">
            ☷ Danh sách sản phẩm
        </a>

    </div>
    <form method="POST" action="../Controller/Controller.php?SUA=<?php echo $id; ?>" enctype="multipart/form-data">

<div class="form-grid">

    <!-- Danh mục sản phẩm -->
    <div class="form-group">
        <label>Danh mục sản phẩm</label>

        <select name="txtdanhmuc">
            <option value="<?php echo htmlspecialchars($se_sp['Danhmuc']); ?>">
                <?php echo htmlspecialchars($se_sp['Danhmuc']); ?>
            </option>

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


    <!-- Tên sản phẩm -->
    <div class="form-group">
        <label>Tên sản phẩm</label>

        <input
            type="text"
            name="txttensp"
            value="<?php echo htmlspecialchars($se_sp['Tensanpham']); ?>"
        >
    </div>


    <!-- Mã sản phẩm -->
    <div class="form-group">
        <label>Mã sản phẩm</label>

        <input
            type="text"
            name="txtmasp"
            value="<?php echo htmlspecialchars($se_sp['Masanpham']); ?>"
        >
    </div>


    <!-- Số tiền -->
    <div class="form-group">
        <label>Số tiền</label>

        <input
            type="number"
            name="txtsotien"
            value="<?php echo htmlspecialchars($se_sp['Sotien']); ?>"
        >
    </div>


    <!-- Mô tả -->
    <div class="form-group full">
        <label>Mô tả sản phẩm</label>

        <textarea name="txtmota"><?php echo htmlspecialchars($se_sp['Mota']); ?></textarea>
    </div>

</div>
    <div class="image-section">

    <div class="image-section-title">
        🖼 Hình ảnh sản phẩm
    </div>

    <div class="image-grid">

        <div class="image-card">

            <div class="image-card-title">
                Ảnh hiện tại
            </div>

            <div class="image-preview">
                <img src="../Upload/<?php echo $se_sp['Hinhanh']; ?>">
            </div>

        </div>


        <div class="image-card">

            <div class="image-card-title">
                Chọn ảnh mới
            </div>

            <div class="image-preview">
                <img
                    id="previewImage"
                    src="../Upload/<?php echo $se_sp['Hinhanh']; ?>"
                >
            </div>

            <input
                type="file"
                name="txtfile"
                id="txtfile"
                accept="image/*"
            >

        </div>

    </div>

</div>

</div>

<div class="button-area">

    <a href="select.php" class="btn btn-back">
        ← Quay lại
    </a>

    <button type="submit" name="txt_sub_update" class="btn btn-update">
        ✓ Cập nhật sản phẩm
    </button>

</div>

</form>

</body>
</html>
<script>
document.getElementById("txtfile").addEventListener("change", function(event) {

    const file = event.target.files[0];

    if (file) {

        const reader = new FileReader();

        reader.onload = function(e) {

            document.getElementById("previewImage").src = e.target.result;

        };

        reader.readAsDataURL(file);
    }

});
</script>

<?php
} else {
    echo "<h3 align='center'>Không tìm thấy sản phẩm cần cập nhật!</h3>";
}
?>
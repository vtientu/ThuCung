<?php
session_start();

// Chỉ cho phép thành viên vừa đăng ký/đã đăng nhập xem trang này.
if (!isset($_SESSION['user'])) {
    header('Location: Info.php');
    exit();
}

$username = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào mừng thành viên mới - Mochi Pet</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #f5f8ff 0%, #ffffff 55%, #fff7e8 100%);
            color: #102f63;
        }
        .page {
            min-height: 100vh;
            padding: 35px 18px 55px;
        }
        .wrap {
            width: min(1050px, 100%);
            margin: 0 auto;
        }
        .brand {
            text-align: center;
            font-weight: 800;
            letter-spacing: 1px;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .brand span { color: #f28c00; }
        .card {
            background: rgba(255,255,255,.97);
            border-radius: 26px;
            box-shadow: 0 18px 55px rgba(16,47,99,.12);
            overflow: hidden;
            border: 1px solid #e7edf7;
        }
        .hero {
            text-align: center;
            padding: 48px 25px 32px;
            background: linear-gradient(135deg, #0e3978, #164d98);
            color: white;
        }
        .badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 999px;
            background: #f7a51b;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 16px;
        }
        .hero h1 {
            margin: 0 0 10px;
            font-size: clamp(30px, 5vw, 48px);
            line-height: 1.15;
        }
        .hero p {
            margin: 0 auto;
            max-width: 680px;
            font-size: 17px;
            line-height: 1.6;
            opacity: .94;
        }
        .content { padding: 35px; }
        .welcome {
            text-align: center;
            font-size: 18px;
            color: #53657e;
            margin: 0 0 28px;
        }
        .welcome strong { color: #123f7b; }
        .offer {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 25px;
            align-items: stretch;
        }
        .benefits {
            border: 1px solid #e6ebf3;
            border-radius: 20px;
            padding: 25px;
            background: #fbfcff;
        }
        .benefits h2, .coupon h2 {
            margin-top: 0;
            color: #123f7b;
        }
        .benefit {
            display: flex;
            gap: 12px;
            margin: 17px 0;
            line-height: 1.5;
            color: #44546b;
        }
        .icon {
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: #fff0d2;
            font-size: 20px;
        }
        .coupon {
            border-radius: 22px;
            padding: 30px;
            background: linear-gradient(145deg, #fff5df, #fffaf0);
            border: 2px dashed #f2a01b;
            text-align: center;
        }
        .coupon .small {
            color: #7a6a4e;
            margin-bottom: 8px;
        }
        .coupon .discount {
            font-size: 42px;
            font-weight: 900;
            color: #ed8500;
            margin: 5px 0;
        }
        .coupon-code {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
            padding: 13px 16px;
            border: 2px solid #123f7b;
            border-radius: 12px;
            background: #fff;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #123f7b;
        }
        .copy {
            border: 0;
            border-radius: 9px;
            padding: 8px 11px;
            cursor: pointer;
            background: #123f7b;
            color: white;
            font-weight: 700;
        }
        .note {
            font-size: 13px;
            color: #7b6b52;
            line-height: 1.5;
            margin: 5px auto 20px;
            max-width: 470px;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 32px;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            border-radius: 12px;
            padding: 13px 24px;
            font-weight: 800;
            transition: .2s;
        }
        .btn-primary {
            background: #123f7b;
            color: white;
        }
        .btn-secondary {
            background: white;
            color: #123f7b;
            border: 1px solid #cbd7e8;
        }
        .btn:hover { transform: translateY(-2px); }
        .footer-note {
            text-align: center;
            margin-top: 22px;
            color: #75849a;
            font-size: 13px;
        }
        @media (max-width: 760px) {
            .content { padding: 24px 18px; }
            .offer { grid-template-columns: 1fr; }
            .hero { padding: 38px 18px 28px; }
            .coupon-code { font-size: 18px; }
        }
    </style>
<link rel="stylesheet" href="../assets/legacy.css">
</head>
<body>
<div class="page">
    <div class="wrap">
        <div class="brand">MOCHI <span>PET</span></div>

        <div class="card">
            <section class="hero">
                <div class="badge">🎉 THÀNH VIÊN MỚI</div>
                <h1>Chào mừng bạn đến với<br>Mochi Pet!</h1>
                <p>
                    Cảm ơn bạn đã đăng ký thành viên. Đây là ưu đãi đặc biệt dành riêng
                    cho khách hàng mới của Mochi Pet.
                </p>
            </section>

            <section class="content">
                <p class="welcome">
                    Xin chào <strong><?php echo htmlspecialchars($username); ?></strong> 👋<br>
                    Tài khoản của bạn đã được tạo thành công.
                </p>

                <div class="offer">
                    <div class="benefits">
                        <h2>🎁 Quyền lợi thành viên mới</h2>

                        <div class="benefit">
                            <div class="icon">🏷️</div>
                            <div><strong>Ưu đãi đơn hàng đầu tiên</strong><br>Nhận mã giảm giá đặc biệt ngay sau khi đăng ký.</div>
                        </div>

                        <div class="benefit">
                            <div class="icon">⭐</div>
                            <div><strong>Thành viên Mochi Pet</strong><br>Thông tin tài khoản được lưu để hỗ trợ các lần mua hàng tiếp theo.</div>
                        </div>

                        <div class="benefit">
                            <div class="icon">🛍️</div>
                            <div><strong>Mua sắm thuận tiện hơn</strong><br>Quay lại trang chủ để xem các sản phẩm thể thao đang có.</div>
                        </div>
                    </div>

                    <div class="coupon">
                        <div class="small">MÃ ƯU ĐÃI DÀNH RIÊNG CHO BẠN</div>
                        <div class="discount">GIẢM 10%</div>
                        <div>Cho đơn hàng đầu tiên</div>

                        <div class="coupon-code">
                            <span id="coupon">NKSNEW10</span>
                            <button class="copy" type="button" onclick="copyCoupon()">Sao chép</button>
                        </div>

                        <div class="note">
                            Nhập mã <strong>NKSNEW10</strong> khi đặt đơn để nhận ưu đãi.
                            Mã dành cho khách hàng mới và áp dụng theo chính sách của cửa hàng.
                        </div>
                    </div>
                </div>

                <div class="actions">
                    <a class="btn btn-primary" href="Info.php">🏠 Quay lại trang chủ</a>
                    <a class="btn btn-secondary" href="Info.php#products">🛍️ Xem sản phẩm</a>
                </div>

                <div class="footer-note">
                    Mochi Pet · LITTLE PAWS · BIG LOVE
                </div>
            </section>
        </div>
    </div>
</div>

<script>
function copyCoupon() {
    const code = document.getElementById('coupon').innerText;
    navigator.clipboard.writeText(code).then(function () {
        const btn = document.querySelector('.copy');
        btn.innerText = 'Đã sao chép ✓';
        setTimeout(function () { btn.innerText = 'Sao chép'; }, 1800);
    }).catch(function () {
        alert('Mã ưu đãi: ' + code);
    });
}
</script>
</body>
</html>

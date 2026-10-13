<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - MOCHI PET</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0b2e78 0%, #1e3a8a 50%, #0f172a 100%);
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 20px;
            padding: 40px 32px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
            position: relative;
        }

        /* LOGO & TIÊU ĐỀ */
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-badge {
            width: 52px;
            height: 52px;
            background: #0b2e78;
            color: #ffffff;
            font-weight: 900;
            font-size: 18px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            margin: 0 auto 12px;
            box-shadow: 0 6px 16px rgba(11, 46, 120, 0.3);
        }

        .brand-header h2 {
            font-size: 22px;
            font-weight: 900;
            color: #0b2e78;
            letter-spacing: -0.5px;
        }

        .brand-header p {
            color: #64748b;
            font-size: 13px;
            margin-top: 4px;
        }

        /* FORM */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .form-group input {
            width: 100%;
            height: 46px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 14px;
            font-size: 14px;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-group input:focus {
            border-color: #0b2e78;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(11, 46, 120, 0.1);
        }

        /* NÚT ĐĂNG NHẬP */
        .btn-submit {
            height: 46px;
            background: #0b2e78;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(11, 46, 120, 0.25);
        }

        .btn-submit:hover {
            background: #174ea6;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(11, 46, 120, 0.35);
        }

        /* ĐƯỜNG DẪN DƯỚI */
        .form-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #f1f5f9;
            padding-top: 16px;
        }

        .form-footer a {
            color: #0b2e78;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }

        .form-footer a:hover {
            color: #174ea6;
            text-decoration: underline;
        }
    </style>
<link rel="stylesheet" href="../assets/legacy.css">
</head>
<body>

    <div class="login-wrapper">
        <div class="brand-header">
            <div class="brand-badge">M</div>
            <h2>ĐĂNG NHẬP</h2>
            <p>Mochi Pet · Quản trị & Mua sắm</p>
        </div>

        <form method="POST" action="../Controller/Controller.php" class="login-form">
            <div class="form-group">
                <label for="username">Tài khoản (Username):</label>
                <input type="text" id="username" name="username" placeholder="Nhập tài khoản của bạn..." required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu (Password):</label>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu..." required>
            </div>

            <input type="submit" name="txtsub_login" value="Đăng Nhập" class="btn-submit">
        </form>

        <div class="form-footer">
            <a href="Info.php">← Quay lại trang chủ</a>
            <a href="Info.php">Tạo tài khoản mới</a>
        </div>
    </div>

</body>
</html>
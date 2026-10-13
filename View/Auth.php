<!DOCTYPE html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $mode === 'register' ? 'Đăng ký' : 'Đăng nhập' ?> · Mochi Pet</title><link rel="stylesheet" href="assets/style.css"><link rel="stylesheet" href="assets/pet-club.css"><link rel="icon" href="assets/images/favicon.svg"></head>
<body class="auth-body">
<main class="auth-layout">
    <div class="auth-art"><a href="index.php" class="logo">mochi<span class="logo-pet">pet</span><span class="logo-dot">.</span></a><h1>Những bàn chân nhỏ.<br>Tình yêu thật lớn.</h1><img src="assets/images/hero.png" alt="Những người bạn nhỏ của Mochi"><p>Một chút yêu thương, mỗi ngày.</p></div>
    <section class="auth-card"><a class="text-link" href="index.php">← Về cửa hàng</a><span class="eyebrow">WELCOME TO MOCHI</span><h1><?= $mode === 'register' ? 'Làm quen nhé!' : 'Mừng bạn trở lại!' ?></h1><p><?= $mode === 'register' ? 'Tạo tài khoản để lưu giỏ hàng và theo dõi đơn của bé.' : 'Đăng nhập để tiếp tục chăm chút cho người bạn nhỏ.' ?></p>
    <?php if ($error): ?><p class="form-error" role="alert"><?= h($error) ?></p><?php endif; ?>
    <form method="post" class="checkout-form"><?php csrf(); ?>
    <?php if ($mode === 'register'): ?><label>Họ và tên<input name="name" autocomplete="name" maxlength="100" value="<?= h($_POST['name'] ?? '') ?>" required></label><?php endif; ?>
    <label>Email<input type="email" name="email" autocomplete="email" maxlength="190" value="<?= h($_POST['email'] ?? '') ?>" required></label>
    <label>Mật khẩu<input type="password" name="password" autocomplete="<?= $mode === 'register' ? 'new-password' : 'current-password' ?>" <?= $mode === 'register' ? 'minlength="10"' : '' ?> maxlength="72" required></label>
    <?php if ($mode === 'register'): ?><label>Nhập lại mật khẩu<input type="password" name="confirm_password" autocomplete="new-password" minlength="10" maxlength="72" required></label><?php endif; ?>
    <button class="button"><?= $mode === 'register' ? 'Tạo tài khoản' : 'Đăng nhập' ?> →</button></form>
    <p class="auth-switch"><?= $mode === 'register' ? 'Đã có tài khoản?' : 'Bạn mới đến Mochi?' ?> <a href="auth.php?mode=<?= $mode === 'register' ? 'login' : 'register' ?>"><?= $mode === 'register' ? 'Đăng nhập' : 'Tạo tài khoản' ?></a></p></section>
</main></body></html>

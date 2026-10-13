<?php
// Đăng xuất chỉ qua POST có CSRF trên trang tài khoản.
header('Location: ../index.php?page=account', true, 303);
exit;

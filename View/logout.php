<?php
session_start();
// Xóa session
session_unset();
session_destroy();
// Quay lại trang đăng nhập
header("Location: login.php");
exit();
?>
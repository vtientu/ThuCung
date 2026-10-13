<?php
// Sao chép thành database.php (file này được bỏ qua bởi Git).
// InfinityFree: lấy chính xác host, database, username trong MySQL Databases.
return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'name' => 'mochi_pet',
    'user' => 'root',
    'password' => '',
    'socket' => null,
    // Đặt một chuỗi ngẫu nhiên dài >= 24 ký tự trước khi mở setup.php.
    // Sau khi tạo admin, xóa giá trị này. Không dùng mật khẩu mặc định.
    'setup_token' => '',
];

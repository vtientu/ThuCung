<?php
// Sao chép thành database.php (file này được bỏ qua bởi Git).
// Cấu hình mặc định cho MySQL trong XAMPP local.
return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'name' => 'mochi_pet',
    'user' => 'root',
    'password' => '',
    'socket' => null,
    // Tạo database còn thiếu trên local, sau đó tạo bảng và seed một lần.
    'auto_setup' => true,
    // Đặt một chuỗi ngẫu nhiên dài >= 24 ký tự trước khi mở setup.php.
    // Sau khi tạo admin, xóa giá trị này. Không dùng mật khẩu mặc định.
    'setup_token' => '',
];

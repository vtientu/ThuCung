# Mochi Pet — chạy local bằng XAMPP

Dự án PHP MVC + MySQL cho cửa hàng thú cưng. Giao diện concept store tím–trắng; danh mục, tài khoản, giỏ hàng, đơn hàng và quản trị dùng cùng một database.

## Chạy dự án

Yêu cầu **XAMPP với PHP 8.0+**, PDO MySQL và MySQL/MariaDB. Không cần Node, Composer hoặc Internet để chạy web.

1. Chép dự án vào `C:\xampp\htdocs\ThuCung`.
2. Bật **Apache** và **MySQL** trong XAMPP Control Panel.
3. Mở `http://localhost/ThuCung/`. Với XAMPP mặc định, web tự tạo database `mochi_pet`, bảng và dữ liệu mẫu; không cần cấu hình hay import SQL.
4. Chỉ khi máy bạn đã đặt mật khẩu/port MySQL khác mặc định: sao chép `config/database.example.php` thành `config/database.php` rồi sửa thông tin kết nối. Mặc định là `127.0.0.1:3306`, username `root`, password rỗng.
5. Để tạo tài khoản quản trị, sao chép file cấu hình nếu chưa có và đặt `setup_token` thành một chuỗi ngẫu nhiên dài ít nhất 24 ký tự trong cấu hình riêng.
6. Mở `http://localhost/ThuCung/`. Web tự tạo **8 bảng, 5 danh mục và 8 sản phẩm** ở lần truy cập đầu, không cần import SQL.
7. Mở `http://localhost/ThuCung/setup.php`, nhập mã thiết lập và tạo admin với email/mật khẩu bạn chọn. Xóa giá trị `setup_token` sau khi tạo; trang thiết lập tự khóa trong DB.
8. Đăng nhập tại `auth.php`, quản trị tại `admin.php`.

Không có mật khẩu admin mặc định. `config/database.php` và các ảnh do bạn tải lên được Git bỏ qua; không thay đè chúng khi cập nhật mã nguồn.

## Chức năng

- Tìm kiếm, lọc chó/mèo và danh mục, sắp xếp giá, chi tiết sản phẩm.
- Đăng ký/đăng nhập, cập nhật hồ sơ, đổi mật khẩu và lịch sử đơn riêng của khách.
- Giỏ hàng MySQL; gộp giỏ khách vào giỏ thành viên khi đăng nhập.
- Đặt COD với giá máy chủ, snapshot tên/giá lúc mua, transaction ghi đơn/trừ kho/xóa giỏ và chống gửi trùng.
- Admin quản lý sản phẩm, ảnh, tồn kho, danh mục, khách hàng và trạng thái đơn.
- Chỉ hủy đơn trước khi giao; hoàn tồn kho đúng một lần. Giao hàng 30.000 đ, miễn phí từ 399.000 đ.
- Yêu thích lưu trên trình duyệt. Chưa tích hợp thanh toán online, hãng vận chuyển hoặc email tự động.

## Cấu trúc

```text
index.php / auth.php / admin.php / setup.php
Controller/     xử lý request, session, CSRF và phân quyền
Model/          PDO, auto seed, danh mục, giỏ hàng và đơn
View/           giao diện cửa hàng, tài khoản và quản trị
assets/app.css  CSS dùng chung đã bỏ selector cũ và khai báo trùng
assets/app.js   tương tác tìm kiếm, menu, yêu thích và dialog
assets/images/  ảnh và SVG đang được sử dụng
assets/fonts/   Manrope và giấy phép OFL
Upload/         ảnh tải lên từ quản trị
config/         mẫu cấu hình và cấu hình riêng của máy
database/      schema SQL cho auto seed
tests/         kiểm tra MySQL và khởi tạo dữ liệu
```

Chỉnh sản phẩm và danh mục qua **admin**, không cần sửa mã PHP. Các file View/Controller chuyển hướng của dự án cũ, ảnh thể thao, CSS giao diện cũ và công cụ triển khai đã được bỏ. Nếu có `storage/` từ bản lưu đơn bằng file trước đây, dữ liệu đó được giữ riêng và không được ứng dụng này đọc/ghi.

## Auto seed

`auto_setup` mặc định bật. Lần kết nối đầu chạy `database/schema.sql`, sau đó lưu `settings.schema_version=3`. Bản nâng cấp lên phiên bản 3 cập nhật tên ảnh của tám sản phẩm mẫu theo bộ ảnh trong `assets/images/`, đồng thời giữ nguyên các thông tin khác. Những lần tải trang tiếp theo không seed lại hoặc khôi phục sản phẩm bạn đã xóa.

Nếu database chưa tồn tại (MySQL 1049), ứng dụng tự tạo database khi dùng host local hoặc Unix socket. Tài khoản MySQL cần quyền CREATE; XAMPP mặc định dùng root có quyền này. Với host từ xa hoặc `auto_setup => false`, hãy tạo database thủ công.

Khởi tạo dùng `CREATE TABLE IF NOT EXISTS` và `INSERT IGNORE`, không DROP/TRUNCATE hoặc ghi đè các dòng đã có. Khóa MySQL tránh hai lượt truy cập đầu cùng khởi tạo. Nếu DB Mochi cũ chưa có dấu phiên bản, lượt cập nhật đầu có thể thêm các dòng mẫu còn thiếu, nhưng giữ tên/giá/tồn kho, tài khoản và đơn đã tồn tại. Đây không phải công cụ chuyển đổi schema của dự án khác.

Có thể đặt `auto_setup => false` và import `database/schema.sql` bằng phpMyAdmin nếu muốn làm thủ công. Giữ thư mục `database/` khi bật tự khởi tạo.

## Kiểm tra

Test chỉ chạy trên database riêng có tên kết thúc `_test`; không dùng `mochi_pet` đang chứa dữ liệu của bạn. Tạo file cấu hình test riêng, cùng cấu trúc với file example.

PowerShell, chạy từ thư mục dự án:

```powershell
$env:MOCHI_CONFIG_FILE = 'C:\duong-dan\database-test.php'
C:\xampp\php\php.exe tests\mysql.php
```

`tests/mysql.php` kiểm tra giỏ, checkout, rollback khi thiếu hàng, snapshot giá, chống đơn trùng và hoàn kho. Test tự xóa fixture của nó.

`tests/install.php` yêu cầu database test **trống**, kiểm tra auto seed và bảo toàn dữ liệu; giữ fixture để đối chiếu. Dùng một database/config test khác:

```powershell
$env:MOCHI_CONFIG_FILE = 'C:\duong-dan\install-test.php'
C:\xampp\php\php.exe tests\install.php
```

Với PHP CLI trên Linux/macOS: `MOCHI_CONFIG_FILE=/duong-dan/database-test.php php tests/mysql.php`.

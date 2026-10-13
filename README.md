# Mochi Pet — PHP MVC / XAMPP

Cửa hàng đồ thú cưng ưu tiên giao diện: tông kem, cam đất và xanh sage; ảnh hero riêng, minh họa sản phẩm đồng bộ; responsive cho desktop và điện thoại. Font và ảnh có sẵn trong dự án, không cần Internet khi chạy.

## Chạy trên XAMPP

1. Dùng XAMPP với PHP 8.0 trở lên (khuyến nghị 8.2).
2. Chép toàn bộ thư mục `ThuCung` vào `C:\xampp\htdocs\`.
3. Mở XAMPP Control Panel, bật **Apache**.
4. Truy cập `http://localhost/ThuCung/` trên máy của bạn.

Cửa hàng mới không cần MySQL hoặc Composer. Đảm bảo Apache có quyền ghi thư mục `storage/` để lưu đơn. Có thể dùng tên thư mục khác; tất cả liên kết của cửa hàng dùng đường dẫn tương đối.

## Các chức năng đã có

- Trang chủ, cửa hàng, giới thiệu và chi tiết sản phẩm.
- Tìm kiếm theo tên/nhãn hàng, lọc danh mục hoặc chó/mèo, sắp xếp theo giá.
- Đánh dấu yêu thích bằng localStorage (ghi nhớ trên trình duyệt).
- Giỏ hàng PHP session: thêm, chỉnh số lượng, xóa sản phẩm.
- Đặt hàng COD, kiểm tra thông tin nhận hàng, tính tổng tiền phía máy chủ.
- Giao hàng 30.000 đ; miễn phí từ 399.000 đ.
- Lưu đơn cục bộ, xác nhận mã đơn và xóa giỏ sau khi lưu thành công.
- CSRF token, escape nội dung HTML và khóa file khi ghi đơn.

Đây là bản chạy cục bộ cho dự án học tập. Sản phẩm, nhãn hàng, giá, ngôi sao và ảnh minh họa là dữ liệu mẫu; chưa tích hợp thanh toán, vận chuyển, đánh giá thật hoặc quản trị đơn hàng mới.

## Cấu trúc

- `index.php`: điểm vào ứng dụng.
- `Controller/StoreController.php`: session, giỏ hàng, kiểm tra form, đặt hàng.
- `Model/PetCatalog.php`: dữ liệu mẫu, tra cứu sản phẩm và định dạng.
- `Model/OrderStore.php`: ghi đơn hàng có khóa file.
- `View/Store.php`: giao diện cửa hàng và các trang.
- `assets/style.css`, `assets/app.js`: giao diện responsive và tương tác.
- `assets/images/`: ảnh hero được tạo riêng và minh họa SVG.
- `assets/fonts/`: Be Vietnam Pro và giấy phép OFL.
- `storage/orders.php`: đơn cục bộ, được tạo sau lần đặt hàng đầu tiên, không đưa vào Git.

Sửa tên/giá/mô tả sản phẩm tại `Model/PetCatalog.php`. Thay ảnh minh họa tại `assets/images/` và cập nhật trường `image` tương ứng. Đổi màu chủ đạo trong `:root` ở `assets/style.css`.

## Đọc đơn cục bộ

Mỗi dòng chú thích trong `storage/orders.php` chứa một đơn JSON mã hóa Base64; mã hóa này là cách lưu dữ liệu, không phải mã hóa bảo mật. Dùng `php tools/read-orders.php` trên máy chủ để đọc. Trên Windows có thể chạy `C:\xampp\php\php.exe tools\read-orders.php` từ thư mục dự án. Không mở quyền truy cập HTTP cho thư mục storage. File `.htaccess` đi kèm chặn truy cập trên Apache; file dữ liệu `.php` cũng không xuất nội dung khi thực thi.

## Mã nguồn MVC cũ

Các file `Model/Connect.php`, `Model/Model.php`, `Controller/Controller.php` và các trang quản trị/đăng nhập trong `View/` được giữ để tham khảo cấu trúc dự án gốc; đã đổi tên hiển thị sang Mochi Pet. `View/Info.php` và `View/chitietsanpham.php` chuyển đến cửa hàng mới; `View/select.php` chuyển đến trang quản trị để thay phần bị lỗi cú pháp trong ZIP gốc.

Phần quản trị cũ yêu cầu database `webphp` với các bảng `ngockhanhsport`, `donhang`, `user`. ZIP nguồn không có schema/database nên phần này chưa được kiểm thử chức năng hoặc nối vào danh mục/đơn hàng mới. Các form xác thực/upload cũ chưa được gia cố để triển khai công khai; không dùng làm hệ thống quản trị production. Không cần truy cập phần này để dùng cửa hàng mới.

## Kiểm tra đã thực hiện

Chạy trên PHP 8.4 CLI và Chromium: kiểm tra cú pháp PHP, desktop/mobile, tìm kiếm, lọc, sắp xếp, yêu thích, chi tiết, giỏ hàng, phí giao hàng, lưu đơn COD, CSRF, không lộ dữ liệu đơn qua HTTP, trạng thái 404 và lỗi tài nguyên/JavaScript. Chưa chạy trực tiếp bản XAMPP trên Windows; hướng dẫn trên dùng Apache/PHP chuẩn tương thích XAMPP.

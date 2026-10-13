# Mochi Pet Concept Store — PHP MVC + MySQL

Cửa hàng đồ thú cưng với giao diện concept store tím–trắng, hero bento và danh mục dạng directory, hoạt động trên XAMPP và shared hosting InfinityFree. Sản phẩm, danh mục, tài khoản, giỏ hàng, đơn và chi tiết đơn đều dùng **cùng một MySQL database**. Không còn chế độ dữ liệu mẫu chạy độc lập hoặc lưu đơn bằng file.

## Chức năng

- Cửa hàng: tìm kiếm Unicode, lọc danh mục/chó/mèo, sắp xếp giá, xem chi tiết và tồn kho.
- Navbar chọn đúng Trang chủ / Cửa hàng / Cho cún / Cho mèo / Về Mochi.
- Tài khoản: đăng ký, đăng nhập, đăng xuất bằng POST, sửa hồ sơ, đổi mật khẩu, lịch sử và chi tiết đơn của riêng khách.
- Giỏ hàng: lưu MySQL cho cả khách vãng lai và thành viên; gộp giỏ khi đăng nhập; giỏ thành viên giữ qua lần đăng nhập sau.
- Đặt COD: tính giá ở máy chủ, lưu tên/giá sản phẩm tại thời điểm mua, ghi đơn + chi tiết + trừ kho + xóa giỏ trong một transaction. Gửi lại cùng form không tạo đơn trùng.
- Admin: thống kê, thêm/sửa/ẩn sản phẩm, tải ảnh JPG/PNG/WebP, chỉnh tồn kho, quản lý danh mục, xem đơn/chi tiết, chuyển trạng thái và khóa/mở khách hàng.
- Luồng đơn: Chờ xử lý → Đã xác nhận → Đang giao → Đã giao. Chỉ hủy trước khi giao; hủy đơn hoàn tồn kho đúng một lần.
- PDO prepared statements, utf8mb4, password_hash/password_verify, CSRF, kiểm tra quyền admin, giới hạn thử mật khẩu, kiểm tra nội dung ảnh và tên file ngẫu nhiên.
- Giao hàng 30.000 đ, miễn phí từ 399.000 đ. Ảnh và font có sẵn, không cần CDN.

Yêu cầu PHP **8.0+**, extension **PDO MySQL**, MySQL **5.7+** hoặc MariaDB **10.4+**, bảng InnoDB. Thời gian ứng dụng hiển thị theo Việt Nam (UTC+7). Không cần Node, Composer hay cron để chạy web.

## XAMPP

1. Chép thư mục dự án vào `C:\xampp\htdocs\ThuCung`.
2. Bật **Apache** và **MySQL** trong XAMPP Control Panel.
3. Vào phpMyAdmin, tạo database `mochi_pet` với `utf8mb4_unicode_ci`. Không cần import SQL: web tự tạo bảng và seed 8 sản phẩm ở lần truy cập đầu. Giữ thư mục `database/` trong mã nguồn.
4. Sao chép `config/database.example.php` thành `config/database.php`. XAMPP mặc định dùng `host=127.0.0.1`, `name=mochi_pet`, `user=root`, `password=''`; nếu máy bạn đã đặt mật khẩu MySQL thì điền mật khẩu thực tế vào file riêng này.
5. Đặt `setup_token` thành một chuỗi ngẫu nhiên ít nhất 24 ký tự (khuyến nghị 32–64 ký tự từ trình quản lý mật khẩu). Giữ riêng; không commit hoặc gửi lên chat.
6. Mở `http://localhost/ThuCung/setup.php`, nhập mã vừa đặt, tên/email và mật khẩu admin mới (ít nhất 10 ký tự).
7. Khi thành công, xóa giá trị `setup_token` hoặc xóa `setup.php`. Trang setup tự khóa trong database ngay sau lần tạo đầu tiên.
8. Mở `http://localhost/ThuCung/`; đăng nhập ở `auth.php`, quản trị ở `admin.php`.

Không có tài khoản hoặc mật khẩu admin mặc định trong SQL/GitHub.

## InfinityFree — các bước bắt buộc

1. Trong Control Panel, mở **MySQL Databases** và tạo một database.
2. Ghi nhận **MySQL Hostname**, **Database Name**, **Username** và mật khẩu MySQL theo tài khoản hosting. Host trên InfinityFree thường có dạng `sqlXXX.infinityfree.com`; **không dùng localhost/127.0.0.1/root** như XAMPP. Dùng chính xác các giá trị bảng điều khiển cung cấp, gồm tiền tố tài khoản trong tên database.
3. **Không cần import SQL thủ công.** Sau khi điền cấu hình và mở web, ứng dụng tự tạo 8 bảng, 5 danh mục và 8 sản phẩm mẫu. Tài khoản MySQL cần quyền CREATE TABLE/INSERT (tài khoản hosting thông thường có các quyền này). Database vẫn phải được tạo trong Control Panel trước; web không tự tạo database hoặc đoán thông tin đăng nhập. Nếu hosting hạn chế khởi tạo tự động, chọn đúng database trong phpMyAdmin và Import `database/schema.sql` làm phương án thay thế.
4. Tải nội dung dự án vào **htdocs của tên miền đang dùng**, để `index.php` nằm ngay trong `htdocs`. Giữ các thư mục Controller, Model, View, config, assets, Upload, database cùng cấp. File Manager hoặc FTP đều dùng được.
5. Tạo file `htdocs/config/database.php` từ file example và điền cấu hình hosting:

   ```php
   <?php
   return [
       'host' => 'MYSQL_HOSTNAME_TRONG_CONTROL_PANEL',
       'port' => 3306,
       'name' => 'TEN_DATABASE_DAY_DU_CO_TIEN_TO',
       'user' => 'MYSQL_USERNAME',
       'password' => 'MAT_KHAU_MYSQL_CUA_BAN',
       'socket' => null,
       'auto_setup' => true,
       'setup_token' => 'CHUOI_NGAU_NHIEN_RIENG_CUA_BAN_IT_NHAT_24_KY_TU',
   ];
   ```

   Đây là chỗ để bạn điền giá trị riêng trên hosting, không phải thông tin đăng nhập thật. File `config/database.php` được Git bỏ qua. Không tải cấu hình socket của môi trường cloud lên InfinityFree.

6. Mở `https://ten-mien-cua-ban/setup.php`, tạo admin bằng mã thiết lập và email/mật khẩu bạn chọn. Xóa setup.php hoặc bỏ trống setup_token sau khi hoàn tất.
7. Mở `https://ten-mien-cua-ban/auth.php` để đăng nhập. Admin vào `admin.php`; khách vào trang tài khoản.
8. Thử đăng ký khách → thêm sản phẩm → đặt COD → kiểm tra đơn trong admin → đổi trạng thái. Tải một ảnh sản phẩm để kiểm tra quyền ghi `Upload/`.

Thông thường thư mục `755`, file `644`; không cần quyền `777`. Giữ các file `.htaccess`. `Upload/` cần ghi được nếu dùng tính năng tải ảnh. Kiểm tra bản PHP hosting là 8.0+ và bật HTTPS theo hướng dẫn SSL của InfinityFree. Không cần terminal hoặc SSH trên hosting.

## Tự tạo bảng và seed

`auto_setup` mặc định bật, kể cả khi file cấu hình cũ chưa có khóa này. Kết nối đầu chạy schema và dữ liệu khởi tạo từ `database/schema.sql`, rồi lưu `settings.schema_version=1`. Các lượt tải trang sau chỉ kiểm tra phiên bản; không chạy lại seed, không khôi phục sản phẩm bạn đã xóa sau khi khởi tạo.

Khởi tạo dùng `CREATE TABLE IF NOT EXISTS` và `INSERT IGNORE`: giữ các dòng đã tồn tại, không DROP/TRUNCATE, không ghi đè tên/giá/tồn kho đã sửa, tài khoản hoặc đơn hàng. Không tạo tài khoản admin mặc định. Nếu hai lượt truy cập đầu đến cùng lúc, khóa MySQL bảo đảm chỉ một lượt thực hiện khởi tạo. Khởi tạo chưa hoàn tất sẽ được thử lại ở lượt truy cập tiếp theo; chỉ đánh dấu phiên bản sau khi toàn bộ schema/seed thành công.

Đây là bootstrap cho schema Mochi, không phải công cụ chuyển đổi database của dự án khác hoặc migration schema tùy ý. Nếu database cũ chưa có dấu phiên bản, lần cập nhật đầu có thể thêm các dòng mẫu còn thiếu nhưng giữ nguyên những dòng đã có. Không tự seed lại sau khi đã có dấu phiên bản. Có thể đặt `auto_setup => false` nếu muốn quản lý việc import SQL hoàn toàn thủ công. Giữ `database/schema.sql` trên hosting khi dùng auto setup.

## Cấu trúc MVC

- `index.php`, `auth.php`, `admin.php`: các điểm vào ứng dụng.
- `Controller/Bootstrap.php`: session, CSRF, phân quyền, phản hồi lỗi cấu hình.
- `Controller/StoreController.php`: giỏ hàng, đặt đơn, tài khoản.
- `Controller/AuthController.php`: đăng ký/đăng nhập/đăng xuất.
- `Controller/AdminController.php`: quản trị và kiểm tra upload.
- `Model/Database.php`: kết nối PDO tập trung.
- `Model/Install.php`: tự tạo bảng/seed một lần, khóa MySQL để tránh hai lượt khởi tạo cùng lúc.
- `View/Header.php`, `Home.php`, `Footer.php`: cấu trúc concept store mới; không dùng lại layout hero hai cột/sticker và banner pastel.
- `Model/PetCatalog.php`, `Cart.php`, `OrderStore.php`: dữ liệu và giao dịch MySQL.
- `View/Store.php`, `Account.php`, `Auth.php`, `Admin.php`: giao diện.
- `database/schema.sql`: schema và 8 sản phẩm khởi tạo. Không chứa tài khoản admin.
- `assets/concept-store.css`: nhận diện tím–trắng mới, header hai tầng, hero bento, bộ lọc bên cạnh và footer wordmark; `style.css` là bố cục/tương tác dùng chung, `admin.css` là quản trị.
- `config/database.example.php`: mẫu cấu hình; `config/database.php` là file riêng không commit.

Các đường dẫn View/Controller cũ chuyển sang ứng dụng mới; không còn form xác thực plaintext hoặc thao tác xóa qua GET từ mã nguồn gốc. Các ảnh thể thao trong Upload được giữ từ ZIP nguồn, không dùng trong danh mục pet mặc định.

`storage/orders.php` của bản cũ không còn được đọc/ghi. Nếu bạn có đơn cũ trong file này, hãy giữ bản sao riêng để đối chiếu; bản này không tự nhập chúng hoặc xóa dữ liệu đó. Không thay đè `config/database.php` và các ảnh `Upload/pet-*` khi cập nhật mã nguồn.

## Kiểm tra và phát triển

Đã chạy trên PHP 8.4 + MariaDB 11.8 + Chromium: luồng đăng ký/đăng nhập, gộp giỏ, đặt đơn, giá máy chủ, snapshot sản phẩm, chống gửi trùng, rollback khi thiếu hàng, hoàn kho khi hủy, phân quyền, CSRF, CRUD sản phẩm/danh mục, upload ảnh và responsive/navbar desktop/mobile. Chưa triển khai trực tiếp vào tài khoản InfinityFree của bạn; cần điền cấu hình riêng ở trên.

Có test giao dịch MySQL tại `tests/mysql.php`. Tạo **database riêng có tên kết thúc `_test`** và dùng một file cấu hình riêng qua `MOCHI_CONFIG_FILE`; bảng và dữ liệu sẽ tự khởi tạo khi test kết nối. Test từ chối chạy trên database không có hậu tố này:

```sh
MOCHI_CONFIG_FILE=/duong-dan/config-test.php php tests/mysql.php
```

Windows PowerShell:

```powershell
$env:MOCHI_CONFIG_FILE = 'C:\duong-dan\config-test.php'
C:\xampp\php\php.exe tests\mysql.php
```

Test tạo/xóa fixture của nó; không sử dụng database production. `php tools/read-orders.php` đọc đơn từ CLI; không trả dữ liệu khách hàng qua HTTP.

Các file `tools/cloud-setup.sh` và `tools/cloud-start.sh` chỉ dành cho môi trường Codex Debian, không cần chạy trên XAMPP/InfinityFree. Chúng sử dụng checkout hiện có; không tạo Git worktree.

## Phạm vi

Thanh toán hiện là COD; chưa tích hợp cổng thanh toán, hãng vận chuyển hay gửi email tự động. Yêu thích lưu trên trình duyệt. Danh mục/sản phẩm/giá và hình minh họa trong SQL là dữ liệu khởi tạo; có thể thay toàn bộ bằng quản trị. Bản này không cung cấp đánh giá khách hàng giả, không cần khóa API để chạy các chức năng đã liệt kê.

Test tự khởi tạo ở `tests/install.php` yêu cầu **database test trống** và giữ fixture để đối chiếu. Test kiểm tra dữ liệu đã chỉnh không bị ghi đè, đơn hàng/tài khoản còn nguyên và seed không chạy lại sau lần khởi tạo đầu:

```sh
MOCHI_CONFIG_FILE=/duong-dan/config-install-test.php php tests/install.php
```

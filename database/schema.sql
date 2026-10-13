-- Mochi Pet: web tự chạy schema/seed ở lần kết nối đầu. Có thể Import thủ công.
-- Không có CREATE DATABASE/USE để tương thích tài khoản shared hosting InfinityFree.
-- Import vào database trống. Không dùng file này để reset database đang có dữ liệu.
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(64) PRIMARY KEY,
    setting_value VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO settings VALUES ('admin_initialized', '0');
CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL DEFAULT '',
    address VARCHAR(500) NOT NULL DEFAULT '',
    role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    pet ENUM('all','dog','cat') NOT NULL DEFAULT 'all',
    price DECIMAL(12,0) NOT NULL,
    `old` DECIMAL(12,0) NOT NULL DEFAULT 0,
    image VARCHAR(100) NOT NULL DEFAULT 'food',
    color ENUM('peach','green','yellow','pink','lavender') NOT NULL DEFAULT 'peach',
    badge VARCHAR(30) NOT NULL DEFAULT '',
    description TEXT NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 50,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS cart_items (
    cart_key VARCHAR(64) NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (cart_key,product_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(32) NOT NULL UNIQUE,
    checkout_token CHAR(64) NOT NULL UNIQUE,
    cart_key VARCHAR(64) NOT NULL,
    user_id INT UNSIGNED NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(500) NOT NULL,
    subtotal DECIMAL(12,0) NOT NULL,
    shipping DECIMAL(12,0) NOT NULL,
    total DECIMAL(12,0) NOT NULL,
    payment VARCHAR(10) NOT NULL DEFAULT 'COD',
    status ENUM('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (user_id,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NULL,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(12,0) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS login_attempts (
    attempt_key CHAR(64) PRIMARY KEY,
    failures INT UNSIGNED NOT NULL DEFAULT 0,
    last_attempt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO categories(id,slug,name) VALUES
(1,'food','Thức ăn'),(2,'accessories','Phụ kiện'),(3,'toys','Đồ chơi'),(4,'beds','Nhà & nệm'),(5,'care','Chăm sóc');
INSERT IGNORE INTO products (id,category_id,name,brand,pet,price,`old`,image,color,badge,description) VALUES (1,1,'Hạt dinh dưỡng cho mèo','MOCHI NUTRITION','cat',185000,220000,'food','peach','Bán chạy','Công thức cá hồi và rau củ, dành cho mèo trưởng thành. Túi 1 kg, tiện lợi cho bữa ăn hằng ngày.');
INSERT IGNORE INTO products (id,category_id,name,brand,pet,price,`old`,image,color,badge,description) VALUES (2,2,'Bát ăn gốm Pastel','MOCHI HOME','all',129000,0,'bowl','green','','Bát gốm tráng men màu xanh sage, dung tích 400 ml. Đế rộng vững chãi, dễ vệ sinh sau mỗi bữa ăn.');
INSERT IGNORE INTO products (id,category_id,name,brand,pet,price,`old`,image,color,badge,description) VALUES (3,3,'Đồ chơi xương mềm','MOCHI PLAY','dog',89000,110000,'bone','yellow','-19%','Đồ chơi vải mềm hình khúc xương, dài 22 cm. Một người bạn nhỏ cho những giờ chơi cùng cún. Giám sát thú cưng khi sử dụng.');
INSERT IGNORE INTO products (id,category_id,name,brand,pet,price,`old`,image,color,badge,description) VALUES (4,2,'Vòng cổ Everyday','MOCHI WALK','dog',159000,0,'collar','pink','Mới','Vòng cổ màu đất nung với khóa kim loại. Kích thước điều chỉnh 25–40 cm, phù hợp cho những chuyến đi dạo mỗi ngày.');
INSERT IGNORE INTO products (id,category_id,name,brand,pet,price,`old`,image,color,badge,description) VALUES (5,4,'Nệm êm Cloud Bed','MOCHI HOME','all',349000,420000,'bed','peach','-17%','Chiếc nệm tròn mềm mại đường kính 55 cm. Vỏ tháo rời để giặt, góc nghỉ ngơi ấm áp dành riêng cho bé.');
INSERT IGNORE INTO products (id,category_id,name,brand,pet,price,`old`,image,color,badge,description) VALUES (6,5,'Sữa tắm dịu nhẹ Oat','MOCHI CARE','all',145000,0,'shampoo','green','','Sữa tắm hương yến mạch nhẹ nhàng, chai 300 ml. Dành cho chó và mèo; tránh tiếp xúc với mắt và rửa sạch sau khi dùng.');
INSERT IGNORE INTO products (id,category_id,name,brand,pet,price,`old`,image,color,badge,description) VALUES (7,3,'Cần câu lông vũ','MOCHI PLAY','cat',65000,0,'feather','pink','','Cần câu đồ chơi với lông vũ và chuông nhỏ. Cùng bé vận động và tạo thêm những khoảnh khắc vui vẻ.');
INSERT IGNORE INTO products (id,category_id,name,brand,pet,price,`old`,image,color,badge,description) VALUES (8,1,'Snack thưởng cho cún','MOCHI NUTRITION','dog',79000,95000,'snack','yellow','-17%','Snack thưởng vị gà, túi 100 g. Dùng làm phần thưởng khi huấn luyện, bổ sung bên cạnh chế độ ăn chính.');

-- Dấu mốc cuối cùng: không seed lại mỗi khi tải trang.
INSERT IGNORE INTO settings (setting_key,setting_value) VALUES ('schema_version','1');

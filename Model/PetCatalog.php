<?php
/** Danh mục mẫu dùng chung cho giao diện và giỏ hàng. */
function pet_products(): array
{
    return [
        ['id' => 1, 'name' => 'Hạt dinh dưỡng cho mèo', 'brand' => 'MOCHI NUTRITION', 'category' => 'food', 'pet' => 'cat', 'price' => 185000, 'old' => 220000, 'image' => 'food', 'color' => 'peach', 'badge' => 'Bán chạy', 'description' => 'Công thức cá hồi và rau củ, dành cho mèo trưởng thành. Túi 1 kg, tiện lợi cho bữa ăn hằng ngày.'],
        ['id' => 2, 'name' => 'Bát ăn gốm Pastel', 'brand' => 'MOCHI HOME', 'category' => 'accessories', 'pet' => 'all', 'price' => 129000, 'old' => 0, 'image' => 'bowl', 'color' => 'green', 'badge' => '', 'description' => 'Bát gốm tráng men màu xanh sage, dung tích 400 ml. Đế rộng vững chãi, dễ vệ sinh sau mỗi bữa ăn.'],
        ['id' => 3, 'name' => 'Đồ chơi xương mềm', 'brand' => 'MOCHI PLAY', 'category' => 'toys', 'pet' => 'dog', 'price' => 89000, 'old' => 110000, 'image' => 'bone', 'color' => 'yellow', 'badge' => '-19%', 'description' => 'Đồ chơi vải mềm hình khúc xương, dài 22 cm. Một người bạn nhỏ cho những giờ chơi cùng cún. Giám sát thú cưng khi sử dụng.'],
        ['id' => 4, 'name' => 'Vòng cổ Everyday', 'brand' => 'MOCHI WALK', 'category' => 'accessories', 'pet' => 'dog', 'price' => 159000, 'old' => 0, 'image' => 'collar', 'color' => 'pink', 'badge' => 'Mới', 'description' => 'Vòng cổ màu đất nung với khóa kim loại. Kích thước điều chỉnh 25–40 cm, phù hợp cho những chuyến đi dạo mỗi ngày.'],
        ['id' => 5, 'name' => 'Nệm êm Cloud Bed', 'brand' => 'MOCHI HOME', 'category' => 'beds', 'pet' => 'all', 'price' => 349000, 'old' => 420000, 'image' => 'bed', 'color' => 'peach', 'badge' => '-17%', 'description' => 'Chiếc nệm tròn mềm mại đường kính 55 cm. Vỏ tháo rời để giặt, góc nghỉ ngơi ấm áp dành riêng cho bé.'],
        ['id' => 6, 'name' => 'Sữa tắm dịu nhẹ Oat', 'brand' => 'MOCHI CARE', 'category' => 'care', 'pet' => 'all', 'price' => 145000, 'old' => 0, 'image' => 'shampoo', 'color' => 'green', 'badge' => '', 'description' => 'Sữa tắm hương yến mạch nhẹ nhàng, chai 300 ml. Dành cho chó và mèo; tránh tiếp xúc với mắt và rửa sạch sau khi dùng.'],
        ['id' => 7, 'name' => 'Cần câu lông vũ', 'brand' => 'MOCHI PLAY', 'category' => 'toys', 'pet' => 'cat', 'price' => 65000, 'old' => 0, 'image' => 'feather', 'color' => 'pink', 'badge' => '', 'description' => 'Cần câu đồ chơi với lông vũ và chuông nhỏ. Cùng bé vận động và tạo thêm những khoảnh khắc vui vẻ.'],
        ['id' => 8, 'name' => 'Snack thưởng cho cún', 'brand' => 'MOCHI NUTRITION', 'category' => 'food', 'pet' => 'dog', 'price' => 79000, 'old' => 95000, 'image' => 'snack', 'color' => 'yellow', 'badge' => '-17%', 'description' => 'Snack thưởng vị gà, túi 100 g. Dùng làm phần thưởng khi huấn luyện, bổ sung bên cạnh chế độ ăn chính.'],
    ];
}
function pet_product(int $id): ?array
{
    foreach (pet_products() as $product) {
        if ($product['id'] === $id) { return $product; }
    }
    return null;
}
function h($value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function money($value): string { return number_format((float) $value, 0, ',', '.') . ' ₫'; }

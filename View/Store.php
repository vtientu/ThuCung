<?php
$page = $_GET['page'] ?? 'home';
if (!in_array($page, ['home', 'shop', 'product', 'cart', 'success', 'about', 'account'], true)) {
    http_response_code(404);
    $page = '404';
}
$detailProduct = $page === 'product' ? pet_product((int) ($_GET['id'] ?? 0)) : null;
if ($page === 'product' && !$detailProduct) {
    http_response_code(404);
}
$categories = in_array($page, ['home', 'shop', 'product'], true) ? category_labels() : [];
$category = (string) ($_GET['category'] ?? 'all');
$pet = (string) ($_GET['pet'] ?? 'all');
$query = trim((string) ($_GET['q'] ?? ''));
$products = in_array($page, ['home', 'shop'], true)
    ? search_products($category, $pet, $query, (string) ($_GET['sort'] ?? ''))
    : [];
function icon(string $name): string
{
    $paths = [
        'bag' => '<path d="M5 7h14l1 14H4L5 7Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/>',
        'arrow' => '<path d="M4 12h16m-6-6 6 6-6 6"/>',
        'heart' => '<path d="M20 5c-3-3-6-1-8 1-2-2-5-4-8-1-4 4 0 8 8 14 8-6 12-10 8-14Z"/>',
        'user' => '<circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/>',
        'truck' =>
            '<path d="M2 5h12v12H2V5Zm12 4h4l4 5v3h-8"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
        'shield' =>
            '<path d="m12 2 9 4v6c0 5-9 10-9 10S3 17 3 12V6l9-4Z"/><path d="m8 12 3 3 5-6"/>',
        'paw' =>
            '<ellipse cx="12" cy="16" rx="6" ry="5"/><ellipse cx="5" cy="9" rx="2" ry="3"/><ellipse cx="10" cy="5" rx="2" ry="3"/><ellipse cx="16" cy="5" rx="2" ry="3"/><ellipse cx="21" cy="10" rx="2" ry="3"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'check' => '<path d="m5 12 4 4L20 5"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' .
        ($paths[$name] ?? $paths['paw']) .
        '</svg>';
}

function product_card(array $p, string $return): void
{
    ?>
<article class="product-card">
    <div class="product-image <?= h($p['color']) ?>">
        <?php if ($p['badge']): ?>
        <span class="badge <?= $p['badge'] === 'Mới' ? 'new' : '' ?>"><?= h($p['badge']) ?></span>
        <?php endif; ?>
        <button
            class="favorite"
            type="button"
            data-favorite="<?= $p['id'] ?>"
            aria-label="Yêu thích <?= h($p['name']) ?>"
            aria-pressed="false"
        >
            <?= icon('heart') ?>
        </button>
        <a href="index.php?page=product&id=<?= $p['id'] ?>">
            <img
                src="<?= h(product_image($p)) ?>"
                alt="Minh họa <?= h($p['name']) ?>"
                loading="lazy"
            />
        </a>
    </div>
    <div class="product-info">
        <span class="brand-label"><?= h($p['brand']) ?></span>
        <a href="index.php?page=product&id=<?= $p['id'] ?>">
            <h3><?= h($p['name']) ?></h3>
        </a>
        <div class="rating">
            <span class="stock-dot"></span>
            <small><?= $p['stock'] > 0 ? 'Sẵn sàng cho bé · ' . (int) $p['stock'] . ' còn lại' : 'Tạm hết hàng' ?></small>
        </div>
        <div class="product-bottom">
            <div>
                <strong><?= money($p['price']) ?></strong>
                <?php if ($p['old']): ?>
                <del><?= money($p['old']) ?></del>
                <?php endif; ?>
            </div>
            <form method="post">
                <?php csrf(); ?>
                <input type="hidden" name="action" value="add" />
                <input type="hidden" name="id" value="<?= $p['id'] ?>" />
                <input type="hidden" name="return" value="<?= h($return) ?>" />
                <button
                    class="add-btn"
                    <?= $p['stock'] < 1 ? 'disabled' : '' ?>
                    aria-label="Thêm <?= h($p['name']) ?> vào giỏ"
                >
                    <?= icon('bag') ?>
                    <span>Thêm vào giỏ</span>
                </button>
            </form>
        </div>
    </div>
</article>
<?php
}
?>
<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="theme-color" content="#6637ce" />
        <meta
            name="description"
            content="Mochi Pet — Những điều nhỏ xinh cho người bạn bốn chân. Thức ăn, đồ chơi và phụ kiện thú cưng."
        />
        <title>Mochi Pet · Đồ xinh cho bốn chân.</title>
        <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml" />
        <script src="assets/app.js" defer></script>
        <link rel="stylesheet" href="assets/app.css" />
    </head>
    <body class="page-<?= h($page) ?>">
        <?php require __DIR__ . '/Header.php'; ?> <?php if ($notice): ?>
        <div class="toast" role="status"><?= icon('check') ?> <?= h($notice) ?></div>
        <?php endif; ?>
        <main>
            <?php if (
    $checkoutError &&
    $page !== 'cart'
): ?>
            <div class="container form-error" role="alert"><?= h($checkoutError) ?></div>
            <?php endif; ?> <?php if ($page === 'home') {
    require __DIR__ . '/Home.php';
} ?> <?php if (in_array($page, ['home', 'shop'])): ?>
            <section class="section container" id="products">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow"><?= $page === 'home' ? '02 / CURATED FOR LITTLE ROOMMATES' : 'NHỮNG ĐIỀU TỐT ĐẸP DÀNH CHO BÉ' ?></span>
                        <h2><?= $page === 'home' ? 'Có gu. Có cả yêu thương.' : ($pet === 'dog' ? 'Thế giới nhỏ của cún' : ($pet === 'cat' ? 'Món xinh dành cho mèo' : 'Chọn món cho bé.')) ?></h2>
                    </div>
                    <?php if (
    $page === 'home'
): ?>
                    <a class="text-link" href="index.php?page=shop">
                        Xem tất cả sản phẩm <?= icon('arrow') ?>
                    </a>
                    <?php else: ?>
                    <p><?= count($products) ?> món quà đang chờ bé</p>
                    <?php endif; ?>
                </div>
                <div class="catalog-layout">
                    <form class="product-filters" method="get">
                        <input type="hidden" name="page" value="<?= h($page) ?>" />
                        <input type="hidden" name="pet" value="<?= h($pet) ?>" />
                        <input type="hidden" name="category" value="<?= h($category) ?>" />
                        <input type="hidden" name="q" value="<?= h($query) ?>" />
                        <span class="filter-heading">DANH MỤC</span>
                        <div class="filter-tabs">
                            <?php foreach (
    $categories
    as $key => $label
): ?>
                            <button
                                name="category"
                                value="<?= $key ?>"
                                class="<?= $category === $key ? 'selected' : '' ?>"
                            >
                                <?= h($label) ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                        <select name="sort" aria-label="Sắp xếp sản phẩm" id="sort-select">
                            <option value="">Mochi đề xuất</option>
                            <option value="price-asc" <?= ($_GET['sort'] ?? '') === 'price-asc' ? 'selected' : '' ?>>
                                Giá thấp đến cao
                            </option>
                            <option value="price-desc" <?= ($_GET['sort'] ?? '') === 'price-desc' ? 'selected' : '' ?>>
                                Giá cao đến thấp
                            </option>
                        </select>
                        <noscript><button>Sắp xếp</button></noscript>
                    </form>
                    <div class="catalog-content">
                        <?php if ($query): ?>
                        <p class="search-result">
                            Kết quả cho “<?= h($query) ?>” ·
                            <a href="index.php?page=shop">Xóa tìm kiếm</a>
                        </p>
                        <?php endif; ?>
                        <div class="product-grid"><?php foreach (
    $page === 'home' ? array_slice($products, 0, 6) : $products
    as $p
) {
    product_card($p, $page);
} ?></div>
                        <?php if (!$products): ?>
                        <div class="empty-state">
                            <?= icon('search') ?>
                            <h3>Chưa tìm thấy món phù hợp</h3>
                            <p>Thử một từ khóa khác hoặc xem tất cả món quà của Mochi nhé.</p>
                            <a class="button" href="index.php?page=shop">Xem tất cả</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php if ($page === 'home'): ?>
            <section class="container concept-manifesto">
                <div class="manifesto-symbol"><?= icon('paw') ?></div>
                <span class="eyebrow">THE MOCHI WAY</span>
                <h2>
                    Không chỉ mua đồ.
                    <br />
                    <span>Chọn cách cưng bé.</span>
                </h2>
                <div class="manifesto-bottom">
                    <p>
                        Một món đồ dễ dùng. Một thiết kế vừa mắt.
                        <br />
                        Một ngày cùng bé vui hơn một chút.
                    </p>
                    <a class="text-link" href="index.php?page=about">
                        Vì sao có Mochi <?= icon('arrow') ?>
                    </a>
                </div>
            </section>
            <?php endif; ?> <?php elseif ($page === 'product'):

    $p = $detailProduct;
    if (!$p): ?>
            <div class="container empty-state">
                <h1>Không tìm thấy sản phẩm</h1>
                <a class="button" href="index.php?page=shop">Về cửa hàng</a>
            </div>
            <?php else: ?>
            <section class="container detail-section">
                <div class="breadcrumb">
                    <a href="index.php">Trang chủ</a>
                    /
                    <a href="index.php?page=shop">Cửa hàng</a>
                    / <?= h($p['name']) ?>
                </div>
                <div class="detail-grid">
                    <div class="detail-image <?= h($p['color']) ?>">
                        <img
                            src="<?= h(product_image($p)) ?>"
                            alt="Minh họa <?= h($p['name']) ?>"
                        />
                    </div>
                    <div class="detail-copy">
                        <span class="eyebrow"><?= h($p['brand']) ?></span>
                        <h1><?= h($p['name']) ?></h1>
                        <div class="rating">
                            <span class="stock-dot"></span>
                            <small><?= (int) $p['stock'] ?> sản phẩm có sẵn</small>
                        </div>
                        <div class="detail-price">
                            <?= money($p['price']) ?> <?php if (
     $p['old']
 ): ?>
                            <del><?= money($p['old']) ?></del>
                            <?php endif; ?>
                        </div>
                        <p><?= h($p['description']) ?></p>
                        <form method="post" class="detail-buy">
                            <?php csrf(); ?>
                            <input type="hidden" name="action" value="add" />
                            <input type="hidden" name="id" value="<?= $p['id'] ?>" />
                            <input type="hidden" name="return" value="cart" />
                            <label>
                                Số lượng
                                <input
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="20"
                                    value="1"
                                    required
                                />
                            </label>
                            <button class="button" <?= $p['stock'] < 1 ? 'disabled' : '' ?>>
                                <?= icon('bag') ?> <?= $p['stock'] < 1 ? 'Tạm hết hàng' : 'Thêm vào giỏ hàng' ?>
                            </button>
                        </form>
                        <div class="detail-perks">
                            <p><?= icon('truck') ?> Miễn phí giao hàng cho đơn từ 399K</p>
                            <p><?= icon('shield') ?> Thanh toán khi nhận hàng (COD)</p>
                        </div>
                        <details open>
                            <summary>Thông tin sản phẩm</summary>
                            <p><?= h($p['description']) ?></p>
                            <p>
                                Danh mục: <?= h($categories[$p['category']]) ?> · Mã:
                                MP-00<?= $p['id'] ?>
                            </p>
                        </details>
                        <details>
                            <summary>Giao hàng & đổi trả</summary>
                            <p>
                                Phí giao hàng 30.000 ₫ cho đơn dưới 399.000 ₫. Kiểm tra thông tin và
                                tình trạng sản phẩm trước khi sử dụng. Liên hệ cửa hàng để được hỗ
                                trợ nếu hàng bị lỗi.
                            </p>
                        </details>
                    </div>
                </div>
            </section>
            <section class="container section">
                <div class="section-heading">
                    <h2>Thêm một chút yêu thương</h2>
                    <a class="text-link" href="index.php?page=shop">Xem cửa hàng →</a>
                </div>
                <div class="product-grid"><?php foreach (
    related_products((int) $p['id'])
    as $other
) {
    product_card($other, 'shop');
} ?></div>
            </section>
            <?php endif;
    ?> <?php
elseif ($page === 'cart'): ?>
            <section class="container section">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">MỘT GIỎ ĐẦY YÊU THƯƠNG</span>
                        <h1>Giỏ hàng của bạn</h1>
                    </div>
                    <a class="text-link" href="index.php?page=shop">Tiếp tục mua sắm →</a>
                </div>
                <?php if (!$_SESSION['cart']): ?>
                <div class="empty-state">
                    <?= icon('bag') ?>
                    <h2>Giỏ hàng đang chờ những món xinh</h2>
                    <p>Chọn một món quà nhỏ để ngày của bé vui hơn nhé.</p>
                    <a class="button" href="index.php?page=shop">
                        Khám phá cửa hàng <?= icon('arrow') ?>
                    </a>
                </div>
                <?php else: ?>
                <div class="cart-layout">
                    <div class="cart-items">
                        <?php foreach (
    $_SESSION['cart']
    as $id => $quantity
):

    $p = $cartProducts[$id] ?? null;
    if (!$p) {
        continue;
    }
    ?>
                        <article class="cart-item">
                            <a
                                class="cart-thumb <?= $p['color'] ?>"
                                href="index.php?page=product&id=<?= $id ?>"
                            >
                                <img
                                    src="<?= h(product_image($p)) ?>"
                                    alt="<?= h($p['name']) ?>"
                                />
                            </a>
                            <div class="cart-item-info">
                                <span class="brand-label"><?= h($p['brand']) ?></span>
                                <h3>
                                    <a href="index.php?page=product&id=<?= $id ?>">
                                        <?= h($p['name']) ?>
                                    </a>
                                </h3>
                                <p><?= money($p['price']) ?></p>
                                <form method="post" class="quantity-form">
                                    <?php csrf(); ?>
                                    <input type="hidden" name="action" value="update" />
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $id ?>"
                                    />
                                    <input type="hidden" name="return" value="cart" />
                                    <label class="sr-only" for="qty-<?= $id ?>">
                                        Số lượng <?= h($p['name']) ?>
                                    </label>
                                    <input
                                        id="qty-<?= $id ?>"
                                        type="number"
                                        name="quantity"
                                        min="0"
                                        max="20"
                                        value="<?= $quantity ?>"
                                        required
                                    />
                                    <button>Cập nhật</button>
                                    <button name="quantity" value="0" class="remove">Xóa</button>
                                </form>
                            </div>
                            <strong><?= money($p['price'] * $quantity) ?></strong>
                        </article>
                        <?php
endforeach; ?>
                        <div class="shipping-note">
                            <?= icon('truck') ?> <?= $cartTotal >= 399000 ? 'Tuyệt vời! Đơn của bạn được miễn phí vận chuyển.' : 'Thêm ' . money(399000 - $cartTotal) . ' để được miễn phí vận chuyển.' ?>
                        </div>
                    </div>
                    <aside class="checkout-card">
                        <h2>Món quà sắp đến rồi!</h2>
                        <div class="totals">
                            <p>
                                <span>Tạm tính</span>
                                <strong><?= money($cartTotal) ?></strong>
                            </p>
                            <p>
                                <span>Giao hàng</span>
                                <strong><?= $cartTotal >= 399000 ? 'Miễn phí' : money(30000) ?></strong>
                            </p>
                            <p class="total">
                                <span>Tổng cộng</span>
                                <strong><?= money($cartTotal + ($cartTotal >= 399000 ? 0 : 30000)) ?></strong>
                            </p>
                        </div>
                        <form method="post" class="checkout-form">
                            <?php csrf(); ?>
                            <input type="hidden" name="action" value="checkout" />
                            <input
                                type="hidden"
                                name="checkout_token"
                                value="<?= h($_SESSION['checkout_token']) ?>"
                            />
                            <input type="hidden" name="return" value="cart" />
                            <h3>Thông tin nhận hàng</h3>
                            <?php if (
    $checkoutError
): ?>
                            <p class="form-error" role="alert"><?= h($checkoutError) ?></p>
                            <?php endif; ?>
                            <label>
                                Họ và tên
                                <input
                                    name="name"
                                    autocomplete="name"
                                    required
                                    maxlength="100"
                                    value="<?= h($_POST['name'] ?? ($user['name'] ?? '')) ?>"
                                    placeholder="Tên của bạn"
                                />
                            </label>
                            <label>
                                Email nhận xác nhận đơn hàng
                                <input type="email" name="email" autocomplete="email" required maxlength="254"
                                    value="<?= h($_POST['email'] ?? ($user['email'] ?? '')) ?>" placeholder="ban@example.com" />
                            </label>
                            <label>
                                Số điện thoại
                                <input
                                    type="tel"
                                    name="phone"
                                    autocomplete="tel"
                                    required
                                    pattern="(0|\+84)[0-9]{9,10}"
                                    value="<?= h($_POST['phone'] ?? ($user['phone'] ?? '')) ?>"
                                    placeholder="090 123 4567"
                                />
                            </label>
                            <label>
                                Địa chỉ nhận hàng
                                <textarea
                                    name="address"
                                    autocomplete="street-address"
                                    required
                                    minlength="10"
                                    maxlength="500"
                                    placeholder="Số nhà, đường, phường/xã, tỉnh/thành"
                                >
<?= h($_POST['address'] ?? ($user['address'] ?? '')) ?></textarea>
                            </label>
                            <p class="payment-note">
                                <?= icon('shield') ?> Thanh toán khi nhận hàng (COD)
                            </p>
                            <button class="button">Đặt hàng <?= icon('arrow') ?></button>
                            <small class="demo-note">
                                Đơn được ghi nhận trực tiếp tại Mochi. Bạn thanh toán khi nhận hàng;
                                phí giao hàng đã bao gồm trong tổng tiền.
                            </small>
                        </form>
                    </aside>
                </div>
                <?php endif; ?>
            </section>
            <?php elseif ($page === 'success'):
    $order =
        $_SESSION['last_order'] ?? null; ?>
            <section class="container empty-state success">
                <?= icon('check') ?>
                <span class="eyebrow">MỘT CHÚT YÊU THƯƠNG ĐANG TRÊN ĐƯỜNG</span>
                <h1><?= $order ? 'Đã ghi nhận đơn hàng của bạn!' : 'Chưa có đơn hàng' ?></h1>
                <?php if ($order): ?>
                <p>Cảm ơn <?= h($order['name']) ?> đã chọn Mochi cho người bạn nhỏ.</p>
                <div class="order-summary">
                    <strong>Mã đơn: <?= h($order['code']) ?></strong>
                    <p>Tổng cộng: <?= money($order['total']) ?> · Thanh toán COD</p>
                    <p>Trạng thái: <?= h(order_statuses()[$order['status']]) ?> · Đơn đã được ghi nhận</p>
                </div>
                <?php endif; ?>
                <a class="button" href="index.php?page=shop">
                    Về cửa hàng <?= icon('arrow') ?>
                </a>
            </section>
            <?php
elseif ($page === 'account'):
    require __DIR__ . '/Account.php'; ?> <?php
elseif (
    $page === 'about'
): ?>
            <section class="container about-section">
                <span class="eyebrow">HELLO, WE'RE MOCHI.</span>
                <h1>
                    Những bàn chân nhỏ.
                    <br />
                    <span>Tình yêu thật lớn.</span>
                </h1>
                <p class="about-lead">
                    Có những người bạn chẳng bao giờ nói “yêu bạn”,
                    <br />
                    nhưng luôn chạy đến đầu tiên khi bạn về nhà.
                </p>
                <div class="about-grid">
                    <img src="assets/images/hero.png" alt="Hai người bạn nhỏ của Mochi" />
                    <div>
                        <h2>Vì bé xứng đáng được cưng chiều.</h2>
                        <p>
                            Mochi bắt đầu từ một ý tưởng giản dị: mang những món đồ đẹp, dễ dùng và
                            đầy quan tâm vào cuộc sống hằng ngày của thú cưng.
                        </p>
                        <p>
                            Chúng mình thích một chiếc bát thật xinh, một góc ngủ thật êm và những
                            giờ chơi không muốn kết thúc. Chọn món cho bé cũng là một cách nói: “Bạn
                            là gia đình”.
                        </p>
                        <a class="button" href="index.php?page=shop">
                            Chọn một món cho bé <?= icon('heart') ?>
                        </a>
                    </div>
                </div>
            </section>
            <?php else: ?>
            <section class="container empty-state">
                <h1>Trang này đi lạc mất rồi.</h1>
                <a class="button" href="index.php">Về nhà Mochi</a>
            </section>
            <?php endif; ?>
        </main>
        <?php require __DIR__ . '/Footer.php'; ?>
        <dialog id="policy-dialog">
            <button class="dialog-close" aria-label="Đóng">×</button>
            <span class="eyebrow">MOCHI & BẠN</span>
            <h2>Giao hàng & thanh toán</h2>
            <p>
                Miễn phí giao hàng cho đơn từ 399.000 ₫. Đơn dưới mức này có phí giao hàng 30.000 ₫.
            </p>
            <p>
                Thanh toán khi nhận hàng (COD). Đơn hàng được ghi nhận tại cửa hàng. Mochi sẽ xử lý
                đơn trước khi giao; hiện chưa có thanh toán trực tuyến.
            </p>
            <button class="button dialog-close">Đã hiểu <?= icon('check') ?></button>
        </dialog>
        <div id="live-message" class="live-message" role="status" aria-live="polite"></div>
    </body>
</html>

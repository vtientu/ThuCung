<header class="header concept-header">
    <div class="container header-inner">
        <a class="logo" href="index.php" aria-label="Mochi Pet trang chủ"><span class="logo-mark"><?= icon('paw') ?></span>mochi<span class="logo-pet">pet</span><span class="logo-dot">.</span></a>
        <span class="brand-motto">ĐỒ XINH CHO BỐN CHÂN.<span>GOOD DESIGN. GREAT COMPANY.</span></span>
        <div class="header-actions">
            <button type="button" id="search-toggle" aria-label="Mở tìm kiếm" aria-expanded="false"><?= icon('search') ?></button>
            <a class="account-link" href="<?= $user ? 'index.php?page=account' : 'auth.php' ?>" aria-label="<?= $user ? 'Tài khoản của bạn' : 'Đăng nhập' ?>"><?= icon('user') ?></a>
            <a class="cart-link" href="index.php?page=cart" aria-label="Giỏ hàng, <?= $cartCount ?> sản phẩm"><?= icon('bag') ?><span class="cart-caption">Giỏ hàng</span><span id="cart-count"><?= $cartCount ?></span></a>
            <button class="mobile-menu" type="button" id="menu-toggle" aria-label="Mở menu" aria-expanded="false"><?= icon('menu') ?></button>
        </div>
    </div>
    <div class="navigation-row container">
        <nav aria-label="Điều hướng chính" id="navigation">
            <?php foreach ([['Trang chủ','index.php',$page==='home'],['Cửa hàng','index.php?page=shop',$page==='shop'&&$pet==='all'],['Cho cún','index.php?page=shop&pet=dog',$page==='shop'&&$pet==='dog'],['Cho mèo','index.php?page=shop&pet=cat',$page==='shop'&&$pet==='cat'],['Về Mochi','index.php?page=about',$page==='about']] as [$label,$url,$active]): ?>
            <a href="<?= h($url) ?>" class="<?= $active ? 'active' : '' ?>" <?= $active ? 'aria-current="page"' : '' ?>><?= h($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <span class="nav-service"><?= icon('truck') ?> FREESHIP TỪ 399K <span>·</span> THANH TOÁN COD</span>
    </div>
    <form class="search-bar container" id="search-bar" action="index.php" hidden><input type="hidden" name="page" value="shop"><input type="search" name="q" placeholder="Bé đang tìm món gì?" aria-label="Tìm sản phẩm" value="<?= h($query) ?>"><button class="button small">Tìm kiếm <?= icon('search') ?></button></form>
</header>

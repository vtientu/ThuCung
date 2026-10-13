<section class="concept-hero container">
    <div class="concept-intro">
        <div>
            <span class="concept-kicker">MOCHI / PET CONCEPT STORE</span>
            <h1>Cưng hết mình<span>.</span></h1>
        </div>
        <p>
            Ở cùng bạn. Chơi cùng bạn.
            <br />
            Và xứng đáng với những món đồ thật xinh.
        </p>
    </div>
    <div class="hero-bento">
        <div class="scene-photo">
            <img
                src="assets/images/hero.png"
                alt="Cún Golden và mèo nhỏ, những người bạn cùng nhà của bạn"
                fetchpriority="high"
            />
            <span class="photo-topline">THE BEST ROOMMATES HAVE PAWS.</span>
            <div class="photo-caption">
                <span>
                    FOR YOUR
                    <br />
                    FAVORITE ROOMMATE.
                </span>
                <a href="index.php?page=shop" aria-label="Khám phá cửa hàng">
                    <?= icon('arrow') ?>
                </a>
            </div>
        </div>
        <div class="pet-selector">
            <span class="concept-kicker">CHỌN TEAM CỦA BÉ</span>
            <h2>
                Bạn ở cùng
                <br />
                một bé…
            </h2>
            <a class="pet-choice dog-choice" href="index.php?page=shop&pet=dog">
                <span class="pet-choice-icon"><?= icon('paw') ?></span>
                <span>
                    <strong>Cún đáng yêu</strong>
                    <small>Đồ xinh cho team vẫy đuôi</small>
                </span>
                <?= icon('arrow') ?>
            </a>
            <a class="pet-choice cat-choice" href="index.php?page=shop&pet=cat">
                <span class="pet-choice-icon"><?= icon('heart') ?></span>
                <span>
                    <strong>Mèo cá tính</strong>
                    <small>Những món boss sẽ duyệt</small>
                </span>
                <?= icon('arrow') ?>
            </a>
            <a class="selector-all" href="index.php?page=shop">Hoặc xem mọi món xinh ↗</a>
        </div>
    </div>
    <div class="hero-footnote">
        <span>ĐỒ XINH. BỮA NGON. NGÀY VUI.</span>
        <span><?= icon('shield') ?> Thông tin rõ ràng · Mua sắm an tâm</span>
    </div>
</section>
<section class="container category-directory">
    <div class="directory-intro">
        <span class="eyebrow">01 / THE DAILY EDIT</span>
        <h2>
            Mọi điều bé cần.
            <br />
            Trong một chỗ.
        </h2>
        <p>
            Từ giờ ăn đến giờ ngủ.
            <br />
            Từ chiếc vòng cổ đến món đồ chơi ruột.
        </p>
    </div>
    <div class="directory-links">
        <?php
        $categoryImages = [
            'food' => 'food',
            'accessories' => 'collar',
            'toys' => 'bone',
            'beds' => 'bed',
            'care' => 'shampoo',
        ];
        foreach (pet_categories() as $i => $cat): ?>
        <a href="index.php?page=shop&category=<?= h($cat['slug']) ?>">
            <span class="directory-number">0<?= $i + 1 ?></span>
            <img src="assets/images/<?= h($categoryImages[$cat['slug']] ?? 'bowl') ?>.svg" alt="" loading="lazy" />
            <strong><?= h($cat['name']) ?></strong>
            <?= icon('arrow') ?>
        </a>
        <?php endforeach;
        ?>
    </div>
</section>

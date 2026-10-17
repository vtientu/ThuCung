<?php
require_once __DIR__ . '/Database.php';

function pet_product(int $id, bool $lock = false): ?array
{
    $sql =
        'SELECT p.*, c.slug AS category FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ? AND p.active = 1 AND c.active = 1';
    $product = query_db($sql . ($lock ? ' FOR UPDATE' : ''), [$id])->fetch();
    return $product ?: null;
}

function pet_categories(): array
{
    static $categories;
    return $categories ??= query_db(
        'SELECT * FROM categories WHERE active = 1 ORDER BY id',
    )->fetchAll();
}

function category_labels(): array
{
    $labels = ['all' => 'Tất cả'];
    foreach (pet_categories() as $category) {
        $labels[$category['slug']] = $category['name'];
    }
    return $labels;
}

function product_image(array $product): string
{
    $image = (string) $product['image'];
    if (preg_match('/^pet-[a-f0-9]{32}\.(jpg|png|webp)$/', $image)) {
        return 'Upload/' . $image;
    }
    $staticImages = [
        'hat-dinh-duong-meo.png',
        'bat-gom-pastel.png',
        'do-choi-xuong-mem.png',
        'vong-co.png',
        'nem-cloud.png',
        'sua-tam.png',
        'can-cau-long-vu.png',
        'snack-cun.png',
    ];
    if (in_array($image, $staticImages, true)) {
        return 'assets/images/' . $image;
    }
    $allowed = ['food', 'bowl', 'bone', 'collar', 'bed', 'shampoo', 'feather', 'snack'];
    return 'assets/images/' . (in_array($image, $allowed, true) ? $image : 'food') . '.svg';
}

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money($value): string
{
    return number_format((float) $value, 0, ',', '.') . ' ₫';
}

function text_length(string $value): int
{
    return (int) preg_match_all('/./us', $value);
}

function search_products(string $category, string $pet, string $search, string $sort): array
{
    $where = ['p.active = 1', 'c.active = 1'];
    $parameters = [];
    if ($category !== 'all') {
        $where[] = 'c.slug = ?';
        $parameters[] = $category;
    }
    if ($pet !== 'all') {
        $where[] = "(p.pet = ? OR p.pet = 'all')";
        $parameters[] = $pet;
    }
    if ($search !== '') {
        $where[] = '(p.name LIKE ? OR p.brand LIKE ?)';
        $parameters[] = '%' . $search . '%';
        $parameters[] = '%' . $search . '%';
    }
    $order =
        ['price-asc' => 'p.price ASC, p.id ASC', 'price-desc' => 'p.price DESC, p.id ASC'][$sort] ??
        'p.id ASC';
    return query_db(
        'SELECT p.*, c.slug AS category FROM products p JOIN categories c ON c.id=p.category_id WHERE ' .
            implode(' AND ', $where) .
            ' ORDER BY ' .
            $order,
        $parameters,
    )->fetchAll();
}

function related_products(int $id): array
{
    return query_db(
        'SELECT p.*, c.slug AS category FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id<>? AND p.active=1 AND c.active=1 ORDER BY p.id LIMIT 4',
        [$id],
    )->fetchAll();
}
